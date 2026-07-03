<?php
// app/Services/CurrencyService.php
namespace App\Services;

use App\Config\Database;

/**
 * Handles multi-currency display and ZAR conversion.
 *
 * Supported display currencies: EUR, GBP, USD, ZAR
 * All transactions are stored and processed in ZAR (PayFast requirement).
 *
 * Exchange rates are cached in the `settings` table and should be
 * updated daily via cron (see app/Cron/update_rates.php) or manually
 * via the admin panel.
 */
class CurrencyService
{
    // Supported display currencies with their symbols and locales
    public const CURRENCIES = [
        'EUR' => ['symbol' => '€', 'name' => 'Euro',           'locale' => 'en_EU'],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound',  'locale' => 'en_GB'],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar',       'locale' => 'en_US'],
        'ZAR' => ['symbol' => 'R', 'name' => 'South African Rand', 'locale' => 'en_ZA'],
    ];

    // Default fallback rates if DB has none yet
    // Update these to current rates before going live
    private const FALLBACK_RATES = [
        'EUR' => 20.50,   // 1 EUR = ~20.50 ZAR
        'GBP' => 24.00,   // 1 GBP = ~24.00 ZAR
        'USD' => 18.50,   // 1 USD = ~18.50 ZAR
        'ZAR' => 1.00,
    ];

    private static ?array $rates = null;

    // ── Currency detection ────────────────────────────────

    /**
     * Detect the user's preferred display currency.
     * Priority: session override → lang-based guess → EUR default
     */
    public static function detectCurrency(): string
    {
        // 1. Explicit session override (user clicked a currency switcher)
        if (!empty($_SESSION['currency']) && isset(self::CURRENCIES[$_SESSION['currency']])) {
            return $_SESSION['currency'];
        }

        // 2. Guess from site locale
        $locale = $_SESSION['locale'] ?? 'en';
        $map = [
            'en' => 'EUR',   // English default for EU site
            'fr' => 'EUR',
            'de' => 'EUR',
            'es' => 'EUR',
            'it' => 'EUR',
            'nl' => 'EUR',
            'pt' => 'EUR',
            'pl' => 'EUR',
        ];

        return $map[$locale] ?? 'EUR';
    }

    /**
     * Set currency preference in session.
     */
    public static function setCurrency(string $code): void
    {
        if (isset(self::CURRENCIES[$code])) {
            $_SESSION['currency'] = $code;
        }
    }

    // ── Conversion ────────────────────────────────────────

    /**
     * Convert a ZAR amount to the display currency.
     */
    public static function fromZAR(float $zar, string $toCurrency): float
    {
        if ($toCurrency === 'ZAR') return $zar;
        $rate = self::getRate($toCurrency); // rate = 1 foreign = X ZAR
        return round($zar / $rate, 2);
    }

    /**
     * Convert a display currency amount to ZAR.
     */
    public static function toZAR(float $amount, string $fromCurrency): float
    {
        if ($fromCurrency === 'ZAR') return $amount;
        $rate = self::getRate($fromCurrency);
        return round($amount * $rate, 2);
    }

    /**
     * Get the ZAR rate for a currency (1 unit of currency = X ZAR).
     */
    public static function getRate(string $currency): float
    {
        $rates = self::loadRates();
        return $rates[$currency] ?? self::FALLBACK_RATES[$currency] ?? 1.0;
    }

    // ── Formatting ────────────────────────────────────────

    /**
     * Format an amount with the correct currency symbol.
     * e.g. format(18.50, 'EUR') → '€18.50'
     */
    public static function format(float $amount, string $currency): string
    {
        $symbol = self::CURRENCIES[$currency]['symbol'] ?? $currency . ' ';
        return $symbol . number_format($amount, 2);
    }

    /**
     * Format a ZAR amount in the user's display currency.
     */
    public static function formatFromZAR(float $zar, string $displayCurrency): string
    {
        return self::format(self::fromZAR($zar, $displayCurrency), $displayCurrency);
    }

    /**
     * Get symbol for a currency code.
     */
    public static function symbol(string $currency): string
    {
        return self::CURRENCIES[$currency]['symbol'] ?? $currency;
    }

    // ── Package pricing ───────────────────────────────────

    /**
     * Get the display price for a package in the user's currency.
     * Always converts from ZAR base price dynamically using live exchange rates.
     */
    public static function packagePrice(array $package, string $currency): float
    {
        if ($currency === 'ZAR') {
            return (float)$package['price_zar'];
        }
        return self::fromZAR((float)$package['price_zar'], $currency);
    }

    /**
     * Get the ZAR amount for a package purchase.
     * PayFast always processes payments in ZAR, so this is always the ZAR base price.
     */
    public static function packagePriceZAR(array $package, string $displayCurrency): float
    {
        return (float)$package['price_zar'];
    }

    // ── Rate management ───────────────────────────────────

    /**
     * Load rates from DB settings table (cached in static property).
     */
    private static function loadRates(): array
    {
        if (self::$rates !== null) {
            return self::$rates;
        }

        self::$rates = self::FALLBACK_RATES;
        $needsUpdate = false;
        $loadedCount = 0;

        try {
            $db   = Database::getInstance();
            $stmt = $db->query("
                SELECT `key`, `value`, `updated_at` FROM settings
                WHERE `key` IN ('eur_to_zar', 'gbp_to_zar', 'usd_to_zar')
            ");
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $map = [
                    'eur_to_zar' => 'EUR',
                    'gbp_to_zar' => 'GBP',
                    'usd_to_zar' => 'USD',
                ];
                $code = $map[$row['key']] ?? null;
                if ($code && (float)$row['value'] > 0) {
                    self::$rates[$code] = (float)$row['value'];
                    $loadedCount++;

                    // Check if rate is older than 24 hours
                    $updatedAt = $row['updated_at'] ? strtotime($row['updated_at']) : 0;
                    if (time() - $updatedAt > 86400) {
                        $needsUpdate = true;
                    }
                }
            }

            if ($loadedCount < 3) {
                $needsUpdate = true;
            }

            if ($needsUpdate && !isset($GLOBALS['__updating_rates'])) {
                $GLOBALS['__updating_rates'] = true;
                self::fetchAndUpdateLiveRates();
                unset($GLOBALS['__updating_rates']);
            }
        } catch (\Exception $e) {
            // DB not ready — use fallback rates
        }

        return self::$rates;
    }

    /**
     * Update a rate in the DB settings table.
     * Called by the cron job or admin panel.
     */
    public static function updateRate(string $currency, float $zarRate): void
    {
        $keyMap = ['EUR' => 'eur_to_zar', 'GBP' => 'gbp_to_zar', 'USD' => 'usd_to_zar'];
        $key    = $keyMap[$currency] ?? null;
        if (!$key) return;

        try {
            $db = Database::getInstance();
            $db->prepare("
                INSERT INTO settings (`key`, `value`, `type`, `description`)
                VALUES (?, ?, 'string', ?)
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW()
            ")->execute([
                $key,
                (string)$zarRate,
                "1 {$currency} in ZAR (auto-updated)",
            ]);
        } catch (\Exception $e) {
            error_log('[CurrencyService] Rate update failed: ' . $e->getMessage());
        }

        // Bust the static cache
        self::$rates = null;
    }

    /**
     * Fetch live rates from exchangerate-api.com (free tier, no key needed for basic).
     * Call this from a daily cron job.
     */
    public static function fetchAndUpdateLiveRates(): bool
    {
        // Using open.er-api.com — free, no API key required, updates daily
        $url = 'https://open.er-api.com/v6/latest/ZAR';

        $ctx = stream_context_create(['http' => [
            'timeout'       => 10,
            'ignore_errors' => true,
        ]]);

        $json = @file_get_contents($url, false, $ctx);
        if (!$json) {
            error_log('[CurrencyService] Failed to fetch live rates');
            return false;
        }

        $data = json_decode($json, true);
        if (($data['result'] ?? '') !== 'success') {
            error_log('[CurrencyService] Rate API error: ' . ($data['error-type'] ?? 'unknown'));
            return false;
        }

        // Rates are "1 ZAR = X foreign" — we need "1 foreign = X ZAR"
        $rates = $data['rates'] ?? [];
        foreach (['EUR', 'GBP', 'USD'] as $code) {
            if (!empty($rates[$code]) && $rates[$code] > 0) {
                $zarPerUnit = round(1 / $rates[$code], 4);
                self::updateRate($code, $zarPerUnit);
            }
        }

        error_log('[CurrencyService] Rates updated: EUR=' . (1 / ($rates['EUR'] ?? 1))
            . ' GBP=' . (1 / ($rates['GBP'] ?? 1))
            . ' USD=' . (1 / ($rates['USD'] ?? 1)));

        return true;
    }
}

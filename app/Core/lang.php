<?php
// app/Core/Lang.php
namespace App\Core;

class Lang
{
    public static array $supported = [
        'en' => 'English',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'es' => 'Español',
        'it' => 'Italiano',
        'pl' => 'Polski',
        'nl' => 'Nederlands',
        'pt' => 'Português',
    ];

    private static string $locale   = 'en';
    private static array  $strings  = [];
    private static array  $fallback = [];

    /** Called once from index.php after locale is resolved */
    public static function init(string $locale): void
    {
        self::$locale = array_key_exists($locale, self::$supported) ? $locale : 'en';

        // Always load English as fallback
        $enFile = APP_ROOT . '/Lang/en.php';
        if (file_exists($enFile)) {
            self::$fallback = require $enFile;
        }

        if (self::$locale !== 'en') {
            $file = APP_ROOT . '/Lang/' . self::$locale . '.php';
            self::$strings = file_exists($file) ? require $file : [];
        } else {
            self::$strings = self::$fallback;
        }
    }

    /** Translate a dot-notation key, e.g. t('nav.sign_in') */
    public static function t(string $key, array $replace = []): string
    {
        $value = self::get($key, self::$strings)
              ?? self::get($key, self::$fallback)
              ?? $key;

        foreach ($replace as $k => $v) {
            $value = str_replace(':' . $k, $v, $value);
        }

        return $value;
    }

    /** Current locale code */
    public static function locale(): string { return self::$locale; }

    /** URL prefix — empty string for English, '/fr' for French etc. */
    public static function prefix(): string
    {
        return self::$locale === 'en' ? '' : '/' . self::$locale;
    }

    /** Full base path including locale prefix */
    public static function base(): string
    {
        return BASE_PATH . self::prefix();
    }

    /** Build a hreflang URL for a given locale and current path */
    public static function hreflangUrl(string $locale, string $currentPath): string
    {
        // Strip any existing locale prefix from the path
        $path = preg_replace('#^/(' . implode('|', array_keys(self::$supported)) . ')(/|$)#', '/', $currentPath);
        $path = '/' . ltrim($path, '/');

        $prefix = ($locale === 'en') ? '' : '/' . $locale;
        $url = BASE_URL . BASE_PATH . $prefix . ($path === '/' ? '' : $path);
        
        // Append set_lang parameter so explicit language selection is saved
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . 'set_lang=' . $locale;
    }

    /** Resolve locale from the URL segment */
    public static function resolveFromUri(string $uri): array
    {
        $parts  = explode('/', ltrim($uri, '/'), 2);
        $first  = $parts[0] ?? '';

        if (array_key_exists($first, self::$supported)) {
            self::setPreference($first);
            return [
                'locale'    => $first,
                'remainder' => $parts[1] ?? '',
            ];
        }

        return [
            'locale'    => 'en',
            'remainder' => $uri,
        ];
    }

    /** Save preferred language to session and cookie */
    public static function setPreference(string $locale): void
    {
        if (array_key_exists($locale, self::$supported)) {
            $_SESSION['lang_pref'] = $locale;
            if (!headers_sent()) {
                setcookie('lang_pref', $locale, [
                    'expires' => time() + 30 * 24 * 60 * 60,
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
        }
    }

    /** Detect language based on session, cookie, CF country, or Accept-Language header */
    public static function detectLanguage(): string
    {
        // 1. Session / Cookie preference
        if (!empty($_SESSION['lang_pref']) && array_key_exists($_SESSION['lang_pref'], self::$supported)) {
            return $_SESSION['lang_pref'];
        }
        if (!empty($_COOKIE['lang_pref']) && array_key_exists($_COOKIE['lang_pref'], self::$supported)) {
            return $_COOKIE['lang_pref'];
        }

        // 2. Cloudflare Country Header
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $country = strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']);
            $countryMap = [
                'FR' => 'fr', 'BE' => 'fr', 'MC' => 'fr', 'LU' => 'fr',
                'DE' => 'de', 'AT' => 'de', 'CH' => 'de',
                'ES' => 'es', 'MX' => 'es', 'AR' => 'es', 'CO' => 'es', 'CL' => 'es', 'PE' => 'es', 'VE' => 'es',
                'IT' => 'it',
                'PL' => 'pl',
                'NL' => 'nl',
                'PT' => 'pt', 'BR' => 'pt'
            ];
            if (isset($countryMap[$country])) {
                return $countryMap[$country];
            }
        }

        // 3. Accept-Language Header
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($langs as $langRange) {
                $parts = explode(';', $langRange);
                $langTag = trim($parts[0]);
                $subtag = strtolower(explode('-', $langTag)[0]);
                if (array_key_exists($subtag, self::$supported)) {
                    return $subtag;
                }
            }
        }

        return 'en';
    }

    // ── Private helpers ──────────────────────────────────

    private static function get(string $key, array $arr): ?string
    {
        $segments = explode('.', $key);
        $current  = $arr;
        foreach ($segments as $seg) {
            if (!is_array($current) || !array_key_exists($seg, $current)) {
                return null;
            }
            $current = $current[$seg];
        }
        return is_string($current) ? $current : null;
    }
}

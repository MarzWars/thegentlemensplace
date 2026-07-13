<?php
// app/Controllers/SitemapController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;

class SitemapController extends Controller
{
    /**
     * GET /sitemap.xml
     * Master Sitemap Index — references all child sitemaps across domains.
     */
    public function index(): void
    {
        header('Content-Type: application/xml; charset=UTF-8');

        $today = date('Y-m-d');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // ── Main site content sitemap ─────────────────────────────────────
        echo "  <sitemap>\n";
        echo "    <loc>https://thegentlemensplace.eu/sitemap-main.xml</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "  </sitemap>\n";

        // ── Independent Performer Portal (client subdomain) sitemap ───────
        echo "  <sitemap>\n";
        echo "    <loc>https://client.thegentlemensplace.eu/sitemap.xml</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "  </sitemap>\n";

        echo '</sitemapindex>';
        exit;
    }

    /**
     * GET /sitemap-main.xml
     * Full sitemap for thegentlemensplace.eu — all static pages and performer profiles.
     */
    public function main(): void
    {
        $baseUrl = 'https://thegentlemensplace.eu';

        // Supported locales for hreflang (must match Lang::$supported)
        $locales = ['en', 'de', 'fr', 'nl', 'es', 'pt', 'it', 'pl'];

        // Fetch all active performers
        $performers = [];
        try {
            $db   = Database::getInstance();
            $stmt = $db->query("
                SELECT slug, updated_at
                FROM performers
                WHERE status = 'active'
                ORDER BY updated_at DESC
            ");
            $performers = $stmt->fetchAll();
        } catch (\Exception $e) {
            // DB unavailable — sitemap will only contain static pages
        }

        header('Content-Type: application/xml; charset=UTF-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        // ── Static pages ─────────────────────────────────────────────
        $staticPages = [
            // [path, priority, changefreq]
            ['/',           '1.0', 'daily'],
            ['/performers', '0.9', 'hourly'],
            ['/terms',      '0.2', 'monthly'],
            ['/privacy',    '0.2', 'monthly'],
            ['/2257',       '0.2', 'monthly'],
        ];

        // Category filter variations for performers listing
        $categories = ['chat', 'roleplay', 'fantasy', 'couples', 'mature', 'fetish'];
        foreach ($categories as $cat) {
            $staticPages[] = ['/performers/category/' . urlencode($cat), '0.7', 'daily'];
        }

        foreach ($staticPages as [$path, $priority, $changefreq]) {
            $isFilterPage = strpos($path, '?') !== false;
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . $path) . "</loc>\n";
            echo "    <changefreq>{$changefreq}</changefreq>\n";
            echo "    <priority>{$priority}</priority>\n";
            // Hreflang alternates (only for clean paths, not filter pages)
            if (!$isFilterPage) {
                foreach ($locales as $locale) {
                    $prefix = ($locale === 'en') ? '' : '/' . $locale;
                    echo '    <xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . htmlspecialchars($baseUrl . $prefix . $path) . "\" />\n";
                }
                echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($baseUrl . $path) . "\" />\n";
            }
            echo "  </url>\n";
        }

        // ── Performer profile pages ───────────────────────────────────
        foreach ($performers as $p) {
            $slug      = htmlspecialchars($p['slug']);
            $path      = '/performer/' . $slug;
            $lastmod   = date('Y-m-d', strtotime($p['updated_at'] ?? 'now'));
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . $path) . "</loc>\n";
            echo "    <lastmod>{$lastmod}</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            // Hreflang alternates for each locale
            foreach ($locales as $locale) {
                $prefix = ($locale === 'en') ? '' : '/' . $locale;
                echo '    <xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . htmlspecialchars($baseUrl . $prefix . $path) . "\" />\n";
            }
            echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($baseUrl . $path) . "\" />\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }
}

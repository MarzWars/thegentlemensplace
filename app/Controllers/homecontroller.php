<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        // Try to pull top 5 featured/active performers from DB
        $featured = [];
        try {
            $db   = Database::getInstance();
            $stmt = $db->query("
                SELECT id, display_name, slug, age, bio, rate_per_minute,
                       rating_avg, rating_count, total_calls, online_status,
                       profile_photo, category, languages
                FROM performers
                WHERE status = 'active'
                ORDER BY online_status DESC, rating_avg DESC, total_calls DESC
                LIMIT 5
            ");
            $featured = $stmt->fetchAll();
        } catch (\Exception $e) {
            // DB not ready yet — demo data used in view
        }

        $this->view('home/index', [
            'title'            => \App\Core\Lang::t('meta.home_title'),
            'metaDesc'         => \App\Core\Lang::t('meta.home_desc'),
            'metaKeywords'     => \App\Core\Lang::t('meta.home_keywords'),
            'layout'           => 'home',
            'skipLayoutFooter' => true,
            'featured'         => $featured,
        ]);
    }

    public function terms(): void
    {
        $this->view('legal/terms', [
            'title'       => \App\Core\Lang::t('meta.terms_title'),
            'metaDesc'    => 'Terms of Service for The Gentleman\'s Place. By accessing the site you agree to these terms.',
            'metaRobots'  => 'index, follow',
            'layout'      => 'home',
        ]);
    }

    public function privacy(): void
    {
        $this->view('legal/privacy', [
            'title'       => \App\Core\Lang::t('meta.privacy_title'),
            'metaDesc'    => 'Privacy Policy for The Gentleman\'s Place. Learn how we protect your personal data.',
            'metaRobots'  => 'index, follow',
            'layout'      => 'home',
        ]);
    }

    public function usc2257(): void
    {
        $this->view('legal/2257', [
            'title'       => \App\Core\Lang::t('meta.usc2257_title'),
            'metaDesc'    => '18 U.S.C. 2257 record-keeping requirements compliance statement for The Gentleman\'s Place.',
            'metaRobots'  => 'index, follow',
            'layout'      => 'home',
        ]);
    }
}

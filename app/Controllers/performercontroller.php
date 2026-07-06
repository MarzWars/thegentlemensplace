<?php
// app/Controllers/PerformerController.php
namespace App\Controllers;

use App\Core\{Controller, Lang};
use App\Models\Performer;

class PerformerController extends Controller
{
    public function index(?string $category = null): void
    {
        // 301 Redirect old query string
        if (isset($_GET['category'])) {
            $cat = $_GET['category'];
            unset($_GET['category']);
            unset($_GET['url']); // Remove router's url param if present
            $query = http_build_query($_GET);
            $redirectUrl = BASE_URL . BASE_PATH . '/performers/category/' . urlencode($cat) . ($query ? '?' . $query : '');
            header("Location: " . $redirectUrl, true, 301);
            exit;
        }

        $model = new Performer();

        // Get filter params safely
        $filters = [
            'category'   => $category,
            'online_only'=> isset($_GET['online']) ? 1 : null,
            'sort'        => in_array($_GET['sort'] ?? '', ['rating', 'newest', 'popular']) ? $_GET['sort'] : 'popular',
            'page'        => max(1, (int)($_GET['page'] ?? 1)),
        ];

        $performers  = $model->getAll($filters);
        $totalCount  = $model->countAll($filters);
        $totalPages  = ceil($totalCount / 12);  // 12 per page

        // Build dynamic category-aware title & description
        $cat = $filters['category'];
        if ($cat) {
            $catLabel = ucfirst($cat);
            $title    = "{$catLabel} Adult Performers — Private Phone Sex & Chat | " . Lang::t('meta.site_name');
            $metaDesc = "Browse {$catLabel} adult performers available for private phone sex and voice or video chat. Connect discreetly, pay per minute. No subscription required.";
        } else {
            $title    = Lang::t('meta.performers_title');
            $metaDesc = Lang::t('meta.performers_desc');
        }

        $this->view('performers/index', [
            'title'           => $title,
            'metaDesc'        => $metaDesc,
            'metaKeywords'    => Lang::t('meta.performers_keywords'),
            'layout'          => 'home',
            'performers'      => $performers,
            'filters'         => $filters,
            'totalPages'      => $totalPages,
        ]);
    }

    public function show(string $slug): void
    {
        $model     = new Performer();
        $performer = $model->findBySlug($slug);

        // ── Demo fallback ─────────────────────────────────────────────
        // When the DB has no performers yet, serve demo data so
        // profile pages work during development/testing.
        if (!$performer || $performer['status'] !== 'active') {
            $performer = $this->getDemoPerformer($slug);
            if (!$performer) {
                http_response_code(404);
                (new \App\Core\View())->render('errors/404', ['layout' => false]);
                return;
            }
        }

        // For real DB performers fetch reviews/photos; demo gets empty arrays
        $reviews = isset($performer['_demo'])
            ? []
            : $model->getReviews($performer['id'], 10);

        $photos = isset($performer['_demo'])
            ? []
            : $model->getPhotos($performer['id']);

        // ── Build rich SEO meta ──────────────────────────────────────
        $name      = $performer['display_name'];
        $cats      = array_filter(array_map('trim', explode(',', $performer['category'] ?? '')));
        $catLabel  = !empty($cats) ? ucfirst(implode(' & ', array_slice($cats, 0, 2))) : 'Adult Chat';
        $siteName  = Lang::t('meta.site_name');
        $siteUrl   = BASE_URL . BASE_PATH;
        $profileUrl = $siteUrl . '/performer/' . $slug;

        // Title: "{Name} - Premium {Category} | The Gentleman's Place"
        $title = "{$name} - Premium {$catLabel} | {$siteName}";

        // Description: bio-based or template
        $bioSnippet = mb_strimwidth(strip_tags($performer['bio'] ?? ''), 0, 100, '');
        if ($bioSnippet) {
            $metaDesc = "Experience exclusive {$catLabel} with {$name} at {$siteName}. {$bioSnippet}... Connect privately for 1-on-1 voice and video chat.";
        } else {
            $metaDesc = "Experience exclusive {$catLabel} with {$name} at {$siteName}. Connect privately for 1-on-1 voice and video chat with no subscriptions.";
        }
        $metaDesc = mb_strimwidth($metaDesc, 0, 155, '...');

        // Keywords
        $metaKeywords = "{$name}, private adult chat, phone sex performer, {$catLabel} phone sex, adult voice call, erotic phone chat, " . strtolower($siteName);

        // OG Image: prefer profile photo, else default
        $ogImageUrl = !empty($performer['profile_photo'])
            ? $siteUrl . '/' . ltrim($performer['profile_photo'], '/')
            : null; // layout will fall back to og-default.jpg

        // Canonical
        $canonicalUrl = $profileUrl;

        // ── JSON-LD: ProfilePage + AggregateRating + BreadcrumbList ──
        $ratingAvg   = number_format((float)($performer['rating_avg'] ?? 0), 1);
        $ratingCount = (int)($performer['rating_count'] ?? 0);
        $jsonLdData  = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'       => 'ProfilePage',
                    '@id'         => $profileUrl . '#profile',
                    'name'        => $name,
                    'description' => strip_tags($performer['bio'] ?? ''),
                    'url'         => $profileUrl,
                    'image'       => !empty($performer['profile_photo']) ? ($siteUrl . '/' . ltrim($performer['profile_photo'], '/')) : null,
                    'mainEntity'  => [
                        '@type'           => 'Person',
                        'name'            => $name,
                        'description'     => strip_tags($performer['bio'] ?? ''),
                        'knowsAbout'      => $cats,
                        'knowsLanguage'   => array_filter(array_map('trim', explode(',', $performer['languages'] ?? 'English'))),
                    ] + ($ratingCount > 0 ? [
                        'aggregateRating' => [
                            '@type'       => 'AggregateRating',
                            'ratingValue' => $ratingAvg,
                            'reviewCount' => $ratingCount,
                            'bestRating'  => '5',
                            'worstRating' => '1',
                        ],
                    ] : []),
                ],
                [
                    '@type'           => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Performers', 'item' => $siteUrl . '/performers'],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => $name,       'item' => $profileUrl],
                    ],
                ],
            ],
        ];
        $jsonLd = json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->view('performers/profile', [
            'title'            => $title,
            'metaDesc'         => $metaDesc,
            'metaKeywords'     => $metaKeywords,
            'ogImageUrl'       => $ogImageUrl,
            'jsonLd'           => $jsonLd,
            'layout'           => 'home',
            'skipLayoutFooter' => true,
            'performer'        => $performer,
            'reviews'          => $reviews,
            'photos'           => $photos,
            'canCall'          => !empty($_SESSION['user_id']) && ($_SESSION['credits'] ?? 0) >= 5,
        ]);
    }

    // ── Demo performer data (mirrors performers/index.php) ────
    private function getDemoPerformer(string $slug): ?array
    {
        $demos = [
            'isabella-rose' => [
                'id'             => 0,
                'slug'           => 'isabella-rose',
                'display_name'   => 'Isabella Rose',
                'age'            => 24,
                'bio'            => "Sophisticated, playful and endlessly curious. I love deep conversations that go wherever the night takes us.\n\nI'm fluent in English and French, and I bring warmth, wit and genuine attention to every call. Whether you want to talk, laugh, or explore something more intimate — I'm here for all of it.",
                'category'       => 'chat,roleplay',
                'languages'      => 'English, French',
                'rate_per_minute'=> '2.00',
                'rating_avg'     => '4.9',
                'rating_count'   => 312,
                'total_calls'    => 1840,
                'online_status'  => 1,
                'profile_photo'  => null,
                'status'         => 'active',
                '_demo'          => true,
            ],
            'sophia-lane' => [
                'id'             => 0,
                'slug'           => 'sophia-lane',
                'display_name'   => 'Sophia Lane',
                'age'            => 27,
                'bio'            => "Warm, witty and wonderfully unpredictable. Every call is a new adventure — I promise you won't be bored.\n\nI love getting to know what makes you tick. Conversation, fantasy, roleplay — I'm comfortable with all of it. Come as you are.",
                'category'       => 'fantasy,mature',
                'languages'      => 'English',
                'rate_per_minute'=> '3.00',
                'rating_avg'     => '4.8',
                'rating_count'   => 204,
                'total_calls'    => 1120,
                'online_status'  => 1,
                'profile_photo'  => null,
                'status'         => 'active',
                '_demo'          => true,
            ],
            'victoria-black' => [
                'id'             => 0,
                'slug'           => 'victoria-black',
                'display_name'   => 'Victoria Black',
                'age'            => 29,
                'bio'            => "Dominant energy, velvet voice. I set the tone and you follow. If that sounds like your kind of evening, let's talk.\n\nI specialise in roleplay and power dynamics. Fluent in English and German. I'm selective — which means when I give you my attention, it's entirely yours.",
                'category'       => 'roleplay,fetish',
                'languages'      => 'English, German',
                'rate_per_minute'=> '4.00',
                'rating_avg'     => '5.0',
                'rating_count'   => 98,
                'total_calls'    => 540,
                'online_status'  => 0,
                'profile_photo'  => null,
                'status'         => 'active',
                '_demo'          => true,
            ],
            'amara-gold' => [
                'id'             => 0,
                'slug'           => 'amara-gold',
                'display_name'   => 'Amara Gold',
                'age'            => 23,
                'bio'            => "Sweet on the surface, fire underneath. I love making you laugh before I make you breathless.\n\nI'm bilingual in English and Afrikaans, and I bring a natural, easy energy to every conversation. No scripts, no pretence — just genuine connection.",
                'category'       => 'chat,couples',
                'languages'      => 'English, Afrikaans',
                'rate_per_minute'=> '2.00',
                'rating_avg'     => '4.7',
                'rating_count'   => 176,
                'total_calls'    => 920,
                'online_status'  => 1,
                'profile_photo'  => null,
                'status'         => 'active',
                '_demo'          => true,
            ],
            'celeste-noir' => [
                'id'             => 0,
                'slug'           => 'celeste-noir',
                'display_name'   => 'Celeste Noir',
                'age'            => 31,
                'bio'            => "Mysterious, poetic, and deeply attentive. I listen as much as I speak — and I remember everything.\n\nI speak English and Spanish, and I have a gift for creating atmosphere. If you want something that feels cinematic and intimate at the same time, you've found the right person.",
                'category'       => 'mature,fantasy',
                'languages'      => 'English, Spanish',
                'rate_per_minute'=> '3.50',
                'rating_avg'     => '4.9',
                'rating_count'   => 267,
                'total_calls'    => 1560,
                'online_status'  => 1,
                'profile_photo'  => null,
                'status'         => 'active',
                '_demo'          => true,
            ],
            'luna-voss' => [
                'id'             => 0,
                'slug'           => 'luna-voss',
                'display_name'   => 'Luna Voss',
                'age'            => 25,
                'bio'            => "Playful, creative and completely in the moment. I bring the fantasy — you just have to show up.\n\nI love building worlds with words. Roleplay, storytelling, or just a really good conversation — I'm all in. Let's see where the night takes us.",
                'category'       => 'roleplay,chat',
                'languages'      => 'English',
                'rate_per_minute'=> '2.50',
                'rating_avg'     => '4.6',
                'rating_count'   => 143,
                'total_calls'    => 780,
                'online_status'  => 0,
                'profile_photo'  => null,
                'status'         => 'active',
                '_demo'          => true,
            ],
        ];

        return $demos[$slug] ?? null;
    }
}
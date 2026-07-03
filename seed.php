<?php
/**
 * ╔══════════════════════════════════════════════════════╗
 * ║  THE GENTLEMAN'S PLACE — Performers Database Seeder  ║
 * ║                                                      ║
 * ║  Run this ONCE in your browser to seed 93 models.    ║
 * ║  DELETE THIS FILE immediately after use.             ║
 * ║                                                      ║
 * ║  URL: http://localhost/public_html/seed.php          ║
 * ╚══════════════════════════════════════════════════════╝
 */

// Safety check — refuse to run on production
$host = $_SERVER['HTTP_HOST'] ?? '';
if (!in_array($host, ['localhost', '127.0.0.1']) && strpos($host, '.local') === false) {
    http_response_code(403);
    die('This seeder can only run on localhost. Delete it before deploying.');
}

define('APP_ROOT', __DIR__ . '/app');
require_once APP_ROOT . '/Config/config.php';
require_once APP_ROOT . '/Config/database.php';

$db = \App\Config\Database::getInstance();

$firstNames = [
    'Bella', 'Chloe', 'Aurora', 'Zara', 'Sophia', 'Emma', 'Luna', 'Daisy', 'Mia', 'Stella', 
    'Ruby', 'Grace', 'Lily', 'Maya', 'Roxy', 'Scarlett', 'Ivy', 'Violet', 'Jasmine', 'Penelope', 
    'Carmen', 'Natalia', 'Nina', 'Nicole', 'Bianca', 'Amber', 'Jade', 'Honey', 'Lexi', 'Brooke', 
    'Angel', 'Sky', 'Paris', 'Sienna', 'Savannah', 'Paige', 'Summer', 'Autumn', 'Serena', 'Elena'
];
$lastNames = [
    'Velvet', 'Rose', 'Thorne', 'Star', 'Stone', 'Brooks', 'Knight', 'Fox', 'Wild', 'Vance', 
    'Summers', 'Winters', 'Hayes', 'Sterling', 'Chase', 'Cole', 'Blair', 'Monroe', 'Pierce', 'Delaney'
];

$categories = ['chat', 'roleplay', 'fantasy', 'couples', 'mature', 'fetish'];
$languages = ['English', 'English, Spanish', 'English, French', 'English, German', 'English, Portuguese', 'English, Italian'];

header('Content-Type: text/plain');
echo "Initializing database seeder...\n";

$db->beginTransaction();
try {
    for ($i = 1; $i <= 93; $i++) {
        $first = $firstNames[array_rand($firstNames)];
        $last = $lastNames[array_rand($lastNames)];
        $displayName = $first . ' ' . $last;
        
        // Ensure uniqueness
        $username = strtolower($first . $last) . $i;
        $email = $username . '@example.com';
        
        // Generate UUIDs
        $userUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        $perfUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        
        // Password hash
        $hash = password_hash('PerformerPassword123!', PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Insert User
        $stmt = $db->prepare("
            INSERT INTO users (uuid, username, email, password_hash, status, email_verified, age_verified)
            VALUES (?, ?, ?, ?, 'active', 1, 1)
        ");
        $stmt->execute([$userUuid, $username, $email, $hash]);
        $userId = $db->lastInsertId();
        
        // Performer details
        $slugBase = strtolower($first . '-' . $last);
        $slug = $slugBase;
        $k = 1;
        while (true) {
            $check = $db->prepare("SELECT COUNT(*) FROM performers WHERE slug = ?");
            $check->execute([$slug]);
            if ((int)$check->fetchColumn() === 0) break;
            $slug = $slugBase . '-' . $i . '-' . $k++;
        }
        
        $age = mt_rand(18, 35);
        $rate = mt_rand(3, 10);
        $phone = '+2782' . mt_rand(1000000, 9999999);
        $cat = $categories[array_rand($categories)];
        $lang = $languages[array_rand($languages)];
        
        // Insert Performer
        $stmt = $db->prepare("
            INSERT INTO performers (uuid, user_id, display_name, slug, age, phone_number, phone_verified, rate_per_minute, status, online_status, category, languages)
            VALUES (?, ?, ?, ?, ?, ?, 1, ?, 'active', 0, ?, ?)
        ");
        $stmt->execute([$perfUuid, $userId, $displayName, $slug, $age, $phone, $rate, $cat, $lang]);
        
        echo "[$i/93] Provisioned: $displayName ($email) · Slug: $slug\n";
    }
    
    $db->commit();
    echo "\nSUCCESS: 93 mock performers and linked user accounts have been created!\n";
    echo "IMPORTANT: Delete this 'seed.php' file from your web directory immediately.\n";
} catch (\Exception $e) {
    $db->rollBack();
    echo "ERROR: Database transaction rolled back. Reason: " . $e->getMessage() . "\n";
}

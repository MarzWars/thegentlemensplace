<?php
// app/Models/Performer.php
namespace App\Models;

use App\Config\Database;

class Performer
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM performers WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM performers WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters): array
    {
        $where  = ["status = 'active'"];
        $params = [];

        if (!empty($filters['category'])) {
            $where[]  = "FIND_IN_SET(?, category) > 0";
            $params[] = $filters['category'];
        }
        if (!empty($filters['online_only'])) {
            $where[] = "online_status = 1";
        }
        if (!empty($filters['search'])) {
            $where[]  = "(display_name LIKE ? OR bio LIKE ?)";
            $term     = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $orderBy = match($filters['sort'] ?? 'popular') {
            'rating'  => 'rating_avg DESC',
            'newest'  => 'created_at DESC',
            'popular' => 'total_calls DESC, rating_avg DESC',
            default   => 'online_status DESC, rating_avg DESC',
        };

        $page   = max(1, (int)($filters['page'] ?? 1));
        $offset = ($page - 1) * 12;
        $sql    = "SELECT id, uuid, display_name, slug, bio, age, rate_per_minute, status,
                          online_status, profile_photo, category, languages, rating_avg, rating_count, total_calls
                   FROM performers
                   WHERE " . implode(' AND ', $where) . "
                   ORDER BY {$orderBy}
                   LIMIT 12 OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll(array $filters): int
    {
        $where  = ["status = 'active'"];
        $params = [];

        if (!empty($filters['category'])) {
            $where[]  = "FIND_IN_SET(?, category) > 0";
            $params[] = $filters['category'];
        }
        if (!empty($filters['online_only'])) {
            $where[] = "online_status = 1";
        }
        if (!empty($filters['search'])) {
            $where[]  = "(display_name LIKE ? OR bio LIKE ?)";
            $term     = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM performers WHERE " . implode(' AND ', $where)
        );
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getReviews(int $performerId, int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT r.rating, r.comment, r.created_at, u.username
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.performer_id = ? AND r.is_approved = 1
            ORDER BY r.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$performerId, $limit]);
        return $stmt->fetchAll();
    }

    public function getPhotos(int $performerId): array
    {
        $stmt = $this->db->prepare("
            SELECT file_path, thumbnail_path, sort_order
            FROM performer_photos
            WHERE performer_id = ? AND approved = 1
            ORDER BY sort_order ASC
        ");
        $stmt->execute([$performerId]);
        return $stmt->fetchAll();
    }

    public function addEarnings(int $performerId, float $amount): void
    {
        $this->db->prepare("
            UPDATE performers
            SET earnings_balance = earnings_balance + ?,
                earnings_total   = earnings_total + ?
            WHERE id = ?
        ")->execute([$amount, $amount, $performerId]);
    }

    public function incrementCallStats(int $performerId, int $durationSeconds): void
    {
        $this->db->prepare("
            UPDATE performers
            SET total_calls   = total_calls + 1,
                total_minutes = total_minutes + ?
            WHERE id = ?
        ")->execute([ceil($durationSeconds / 60), $performerId]);
    }
}

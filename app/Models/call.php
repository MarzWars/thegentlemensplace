<?php
// app/Models/Call.php
namespace App\Models;

use App\Config\Database;

class Call
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM calls WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUuid(string $uuid): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM calls WHERE uuid = ? LIMIT 1");
        $stmt->execute([$uuid]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO calls (uuid, type, call_link_id, user_id, performer_id, status, rate_per_minute, min_credits, min_minutes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['uuid'],
            $data['type'] ?? 'voice',
            $data['call_link_id'],
            $data['user_id'],
            $data['performer_id'],
            $data['status'],
            $data['rate_per_minute'],
            $data['min_credits'] ?? 0.00,
            $data['min_minutes'] ?? 0
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateSid(int $id, string $sid): void
    {
        $stmt = $this->db->prepare("UPDATE calls SET telephony_sid = ? WHERE id = ?");
        $stmt->execute([$sid, $id]);
    }

    public function updateStatus(int $id, string $status, ?string $reason = null): void
    {
        $stmt = $this->db->prepare("UPDATE calls SET status = ?, termination_reason = ? WHERE id = ?");
        $stmt->execute([$status, $reason, $id]);
    }

    public function markAnswered(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE calls SET status = 'in_progress', answered_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function finalize(int $id, array $data): void
    {
        $stmt = $this->db->prepare("
            UPDATE calls
            SET status = ?,
                duration_seconds = ?,
                credits_used = ?,
                performer_earnings = ?,
                platform_earnings = ?,
                ended_at = ?,
                termination_reason = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['status'],
            $data['duration_seconds'],
            $data['credits_used'],
            $data['performer_earnings'],
            $data['platform_earnings'],
            $data['ended_at'],
            $data['termination_reason'],
            $id
        ]);
    }

    /**
     * Fetch recent calls for a performer, joined with the username.
     */
    public function getRecentForPerformer(int $performerId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.username
            FROM calls c
            JOIN users u ON c.user_id = u.id
            WHERE c.performer_id = ?
            ORDER BY c.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$performerId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Aggregate call stats for a performer (completed calls only).
     */
    public function getStatsForPerformer(int $performerId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total_calls,
                COALESCE(SUM(duration_seconds), 0) AS total_seconds,
                COALESCE(SUM(performer_earnings), 0) AS total_earned
            FROM calls
            WHERE performer_id = ? AND status = 'completed'
        ");
        $stmt->execute([$performerId]);
        return $stmt->fetch() ?: ['total_calls' => 0, 'total_seconds' => 0, 'total_earned' => 0];
    }
}


<?php
// app/Models/Stream.php
namespace App\Models;

use App\Config\Database;

class Stream
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findActiveByPerformer(int $performerId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM streams WHERE performer_id = ? AND status = 'live' LIMIT 1");
        $stmt->execute([$performerId]);
        return $stmt->fetch() ?: null;
    }

    public function findActiveByUuid(string $uuid): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM streams WHERE uuid = ? AND status = 'live' LIMIT 1");
        $stmt->execute([$uuid]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM streams WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO streams (uuid, performer_id, status, title, channel_name, token, started_at, created_at)
            VALUES (?, ?, 'live', ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $data['uuid'],
            $data['performer_id'],
            $data['title'] ?? 'Live Stream',
            $data['channel_name'],
            $data['token'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function endStream(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE streams SET status = 'ended', ended_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function updateViewerCount(int $id, int $count): void
    {
        $stmt = $this->db->prepare("UPDATE streams SET viewer_count = ? WHERE id = ?");
        $stmt->execute([$count, $id]);
    }
}

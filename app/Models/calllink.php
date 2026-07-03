<?php
// app/Models/CallLink.php
namespace App\Models;

use App\Config\Database;

class CallLink
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO call_links (token, user_id, performer_id, expires_at, ip_created, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['token'],
            $data['user_id'],
            $data['performer_id'],
            $data['expires_at'],
            $data['ip_created']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findValidToken(string $token): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM call_links
            WHERE token = ? AND used_at IS NULL AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function markUsed(int $id, string $ip): void
    {
        $stmt = $this->db->prepare("
            UPDATE call_links
            SET used_at = NOW(), ip_used = ?
            WHERE id = ?
        ");
        $stmt->execute([$ip, $id]);
    }
}

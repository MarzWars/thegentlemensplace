<?php
// app/Core/RateLimit.php
namespace App\Core;

use App\Config\Database;

class RateLimit
{
    // Returns true if allowed, false if rate limited
    public static function check(string $action, string $identifier, int $maxAttempts, int $windowSeconds): bool
    {
        $db  = Database::getInstance();
        $now = time();

        // Clean old entries
        $db->prepare("DELETE FROM rate_limits WHERE UNIX_TIMESTAMP(window_start) < ?")->execute([$now - $windowSeconds]);

        $stmt = $db->prepare("SELECT attempts FROM rate_limits WHERE identifier = ? AND action = ?");
        $stmt->execute([$identifier, $action]);
        $row = $stmt->fetch();

        if (!$row) {
            $db->prepare("INSERT INTO rate_limits (identifier, action, attempts) VALUES (?, ?, 1)")->execute([$identifier, $action]);
            return true;
        }

        if ($row['attempts'] >= $maxAttempts) return false;

        $db->prepare("UPDATE rate_limits SET attempts = attempts + 1 WHERE identifier = ? AND action = ?")->execute([$identifier, $action]);
        return true;
    }
}
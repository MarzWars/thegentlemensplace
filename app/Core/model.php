<?php
// app/Core/Model.php
namespace App\Core;

use App\Config\Database;

abstract class Model
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Safe query helper
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function findById(string $table, int $id): ?array
    {
        $stmt = $this->query("SELECT * FROM {$table} WHERE id = ? LIMIT 1", [$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
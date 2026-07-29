
<?php

use PDO;

require_once __DIR__ . '/../db/database.php';
use Database;

class Model
    {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
 
    public function __construct(?PDO $db = null)
    {
        if ($db instanceof PDO) {
            $this->db = $db;
            return;
        }

        $database = new Database();
        $this->db = $database->getConnection();
    }
 
    public function find($id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    public function all(): array
    {
        return $this->db->query("SELECT * FROM {$this->table}")->fetchAll();
    }
 
    public function where(string $column, $value): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }
 
    /** @return string The inserted row's primary key */
    public function create(array $data): string
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
 
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);
 
        return $this->db->lastInsertId();
    }
 
    public function update($id, array $data): bool
    {
        $set = implode(', ', array_map(fn ($col) => "{$col} = :{$col}", array_keys($data)));
        $data['__id'] = $id;
 
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :__id");
        return $stmt->execute($data);
    }
 
    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
}
 

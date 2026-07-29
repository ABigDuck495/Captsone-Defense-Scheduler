<?php

require_once 'Model.php';
class User extends Model{
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findProfessors(): array
    {
        return $this->where('role', 'professor');
    }

    public function findStudents(): array
    {
        return $this->where('role', 'student');
    }
}
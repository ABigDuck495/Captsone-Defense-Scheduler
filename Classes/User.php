<?php

require_once 'Model.php';
// use Model;
class User extends Model{
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }

        $storedHash = $user['password_hash'] ?? '';
        if ($storedHash === '') {
            return null;
        }

        if (!password_verify($password, $storedHash)) {
            return null;
        }

        return $user;
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
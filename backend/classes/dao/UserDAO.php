<?php

require_once __DIR__ . '/JsonStorage.php';

class UserDAO extends JsonStorage
{
    public function findByEmail(string $email): ?array
    {
        $needle = mb_strtolower(trim($email));

        foreach ($this->all() as $row) {
            if (mb_strtolower(trim((string) ($row['email'] ?? ''))) === $needle) {
                return $row;
            }
        }

        return null;
    }

    public function verifyPassword(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if ($user === null || !password_verify($password, (string) ($user['password'] ?? ''))) {
            return null;
        }

        return $user;
    }

    public function publicView(array $user): array
    {
        unset($user['password']);
        return $user;
    }
}

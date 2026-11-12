<?php

require_once __DIR__ . '/../dao/UserDAO.php';

class AuthService
{
    private UserDAO $users;

    public function __construct(UserDAO $users)
    {
        $this->users = $users;
    }

    public function login(string $email, string $password): array
    {
        if (trim($email) === '' || $password === '') {
            throw new InvalidArgumentException("L'email et le mot de passe sont obligatoires.");
        }
        $user = $this->users->verifyPassword($email, $password);
        if ($user === null) {
            throw new UnauthorizedException('Identifiants invalides. Vérifiez votre email et votre mot de passe.');
        }
        if (($user['role'] ?? '') !== 'admin') {
            throw new ForbiddenException("Accès refusé : ce compte ne dispose pas des privilèges administrateur.");
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        $_SESSION['user_email'] = $user['email'];

        return $this->users->publicView($user);
    }

    public function session(): array
    {
        if (!$this->isAdmin()) {
            return ['authenticated' => false, 'user' => null];
        }
        return [
            'authenticated' => true,
            'user' => [
                'id' => $_SESSION['user_id'] ?? null,
                'role' => $_SESSION['role'] ?? null,
                'name' => $_SESSION['user_name'] ?? null,
                'email' => $_SESSION['user_email'] ?? null,
            ],
        ];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function users(): array
    {
        return array_map(fn(array $user): array => $this->users->publicView($user), $this->users->all());
    }
}

class UnauthorizedException extends RuntimeException {}
class ForbiddenException extends RuntimeException {}

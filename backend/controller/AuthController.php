<?php

require_once __DIR__ . '/../classes/services/AuthService.php';

class AuthController
{
    private AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function login(string $method, array $body): array
    {
        if ($method !== 'POST') {
            throw new MethodNotAllowedException('Méthode non autorisée.');
        }
        return ['status' => 200, 'data' => $this->service->login(
            (string) ($body['email'] ?? ''),
            (string) ($body['password'] ?? '')
        )];
    }

    public function logout(string $method): array
    {
        if ($method !== 'POST') {
            throw new MethodNotAllowedException('Méthode non autorisée.');
        }
        $this->service->logout();
        return ['status' => 200, 'data' => ['message' => 'Déconnexion réussie.']];
    }

    public function session(string $method): array
    {
        if ($method !== 'GET') {
            throw new MethodNotAllowedException('Méthode non autorisée.');
        }
        return ['status' => 200, 'data' => $this->service->session()];
    }

    public function users(string $method): array
    {
        if ($method !== 'GET') {
            throw new MethodNotAllowedException('Méthode non autorisée.');
        }
        return ['status' => 200, 'data' => $this->service->users()];
    }

    public function isAdmin(): bool { return $this->service->isAdmin(); }
}

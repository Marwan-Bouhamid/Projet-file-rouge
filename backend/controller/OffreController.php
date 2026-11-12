<?php

require_once __DIR__ . '/../classes/services/OffreService.php';

class OffreController
{
    private OffreService $service;

    public function __construct(OffreService $service)
    {
        $this->service = $service;
    }

    public function handle(string $method, ?int $id, array $body, array $files): array
    {
        switch ($method) {
            case 'GET':
                if ($id !== null) {
                    $row = $this->service->find($id);
                    if ($row === null) {
                        throw new OutOfBoundsException('Offre introuvable.');
                    }
                    return ['status' => 200, 'data' => $row];
                }
                return ['status' => 200, 'data' => $this->service->list()];
            case 'POST':
                return ['status' => 201, 'data' => $this->service->create($body, $files['image'] ?? null)];
            case 'PUT':
                if ($id === null) {
                    throw new InvalidArgumentException('Paramètre id manquant.');
                }
                return ['status' => 200, 'data' => $this->service->update($id, $body, $files['image'] ?? null)];
            case 'DELETE':
                if ($id === null) {
                    throw new InvalidArgumentException('Paramètre id manquant.');
                }
                $this->service->delete($id);
                return ['status' => 200, 'data' => ['id' => $id]];
            default:
                throw new MethodNotAllowedException('Méthode non autorisée.');
        }
    }
}

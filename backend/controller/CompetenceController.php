<?php

require_once __DIR__ . '/../classes/services/CompetenceService.php';

class CompetenceController
{
    private CompetenceService $service;

    public function __construct(CompetenceService $service)
    {
        $this->service = $service;
    }

    public function handle(string $method, ?int $id, array $body): array
    {
        switch ($method) {
            case 'GET':
                if ($id !== null) {
                    $row = $this->service->find($id);
                    if ($row === null) {
                        throw new OutOfBoundsException('Compétence introuvable.');
                    }
                    return ['status' => 200, 'data' => $row];
                }
                return ['status' => 200, 'data' => $this->service->list()];
            case 'POST':
                return ['status' => 201, 'data' => $this->service->create($body)];
            case 'PUT':
                if ($id === null) {
                    throw new InvalidArgumentException('Paramètre id manquant.');
                }
                return ['status' => 200, 'data' => $this->service->update($id, $body)];
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

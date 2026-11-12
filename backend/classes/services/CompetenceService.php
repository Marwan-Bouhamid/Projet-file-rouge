<?php

require_once __DIR__ . '/../dao/CompetenceDAO.php';

class CompetenceService
{
    private CompetenceDAO $competences;

    public function __construct(CompetenceDAO $competences)
    {
        $this->competences = $competences;
    }

    public function list(): array
    {
        $rows = $this->competences->all();
        usort($rows, static fn(array $a, array $b): int
            => strcasecmp((string) ($a['nom'] ?? ''), (string) ($b['nom'] ?? '')));
        return $rows;
    }

    public function find(int $id): ?array { return $this->competences->find($id); }

    public function create(array $data): array
    {
        $payload = ['nom' => trim((string) ($data['nom'] ?? ''))];
        $this->throwIfInvalid($this->validate($payload));
        return $this->competences->create($payload);
    }

    public function update(int $id, array $data): array
    {
        if ($this->find($id) === null) {
            throw new OutOfBoundsException('Compétence introuvable.');
        }
        $payload = ['nom' => trim((string) ($data['nom'] ?? ''))];
        $this->throwIfInvalid($this->validate($payload, $id));
        $updated = $this->competences->update($id, $payload);
        if ($updated === null) {
            throw new OutOfBoundsException('Compétence introuvable.');
        }
        return $updated;
    }

    public function delete(int $id): bool
    {
        if ($this->find($id) === null) {
            throw new OutOfBoundsException('Compétence introuvable.');
        }
        return $this->competences->delete($id);
    }

    private function validate(array $data, ?int $ignoreId = null): array
    {
        $nom = $data['nom'];
        if ($nom === '') {
            return ['Le nom de la compétence est obligatoire.'];
        }
        if (mb_strlen($nom) > 80) {
            return ['Le nom de la compétence ne doit pas dépasser 80 caractères.'];
        }

        $needle = mb_strtolower($nom);
        foreach ($this->competences->all() as $row) {
            if ($ignoreId !== null && (int) ($row['id'] ?? 0) === $ignoreId) {
                continue;
            }
            if (mb_strtolower(trim((string) ($row['nom'] ?? ''))) === $needle) {
                return ['Cette compétence existe déjà dans le catalogue.'];
            }
        }

        return [];
    }

    private function throwIfInvalid(array $errors): void
    {
        if ($errors !== []) {
            throw new InvalidArgumentException(implode("\n", $errors));
        }
    }
}

<?php

class Competence
{
    private ?int $id;
    private string $nom;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->nom = (string) ($data['nom'] ?? '');
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function toArray(): array
    {
        return ['id' => $this->id, 'nom' => $this->nom];
    }
}

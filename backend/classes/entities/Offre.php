<?php

class Offre
{
    private ?int $id;
    private string $titre;
    private string $description;
    private string $domaine;
    private ?int $yearsRequired;
    private ?string $image;
    private ?int $idRecruteur;
    private ?string $createdAt;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->titre = (string) ($data['titre'] ?? '');
        $this->description = (string) ($data['description'] ?? '');
        $this->domaine = (string) ($data['domaine'] ?? '');
        $this->yearsRequired = isset($data['years_required']) && $data['years_required'] !== ''
            ? (int) $data['years_required']
            : null;
        $this->image = isset($data['image']) ? (string) $data['image'] : null;
        $this->idRecruteur = isset($data['id_recruteur']) && $data['id_recruteur'] !== ''
            ? (int) $data['id_recruteur']
            : null;
        $this->createdAt = isset($data['created_at']) ? (string) $data['created_at'] : null;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function getDomaine(): string { return $this->domaine; }
    public function setDomaine(string $domaine): void { $this->domaine = $domaine; }
    public function getYearsRequired(): ?int { return $this->yearsRequired; }
    public function setYearsRequired(?int $yearsRequired): void { $this->yearsRequired = $yearsRequired; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): void { $this->image = $image; }
    public function getIdRecruteur(): ?int { return $this->idRecruteur; }
    public function setIdRecruteur(?int $idRecruteur): void { $this->idRecruteur = $idRecruteur; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): void { $this->createdAt = $createdAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'domaine' => $this->domaine,
            'years_required' => $this->yearsRequired,
            'image' => $this->image,
            'id_recruteur' => $this->idRecruteur,
            'created_at' => $this->createdAt,
        ];
    }
}

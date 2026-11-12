<?php

class User
{
    private ?int $id;
    private string $lastName;
    private string $firstName;
    private string $email;
    private ?string $password;
    private string $role;
    private ?string $domaine;
    private ?int $yearsExperience;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->lastName = (string) ($data['last_name'] ?? '');
        $this->firstName = (string) ($data['first_name'] ?? '');
        $this->email = (string) ($data['email'] ?? '');
        $this->password = isset($data['password']) ? (string) $data['password'] : null;
        $this->role = (string) ($data['role'] ?? '');
        $this->domaine = isset($data['domaine']) ? (string) $data['domaine'] : null;
        $this->yearsExperience = isset($data['years_experience']) && $data['years_experience'] !== ''
            ? (int) $data['years_experience']
            : null;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): void { $this->lastName = $lastName; }
    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): void { $this->firstName = $firstName; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(?string $password): void { $this->password = $password; }
    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }
    public function getDomaine(): ?string { return $this->domaine; }
    public function setDomaine(?string $domaine): void { $this->domaine = $domaine; }
    public function getYearsExperience(): ?int { return $this->yearsExperience; }
    public function setYearsExperience(?int $yearsExperience): void { $this->yearsExperience = $yearsExperience; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'last_name' => $this->lastName,
            'first_name' => $this->firstName,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'domaine' => $this->domaine,
            'years_experience' => $this->yearsExperience,
        ];
    }
}

<?php

require_once __DIR__ . '/../dao/OffreDAO.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../../helpers/ImageUpload.php';

class OffreService
{
    private OffreDAO $offres;
    private UserDAO $users;
    private ImageUpload $images;

    public function __construct(OffreDAO $offres, UserDAO $users, ImageUpload $images)
    {
        $this->offres = $offres;
        $this->users = $users;
        $this->images = $images;
    }

    public function list(): array
    {
        $rows = $this->offres->all();
        usort($rows, static function (array $a, array $b): int {
            $comparison = strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
            return $comparison !== 0 ? $comparison : ((int) ($b['id'] ?? 0) <=> (int) ($a['id'] ?? 0));
        });

        return $rows;
    }

    public function find(int $id): ?array
    {
        return $this->offres->find($id);
    }

    public function create(array $data, ?array $file): array
    {
        $payload = $this->normalize($data);
        $errors = $this->validate($payload);
        $hasUpload = $this->hasUpload($file);

        if (!$hasUpload) {
            $errors[] = "L'image est obligatoire lors de la création d'une offre.";
        } else {
            $errors = array_merge($errors, $this->images->validate($file));
        }
        $this->throwIfInvalid($errors);

        $image = $this->images->store($file);
        if ($image === null) {
            throw new RuntimeException("Impossible d'enregistrer l'image téléchargée.");
        }
        $payload['image'] = $image;
        $payload['created_at'] = gmdate('Y-m-d\TH:i:s\Z');

        try {
            return $this->offres->create($payload);
        } catch (Throwable $exception) {
            $this->images->remove($image);
            throw $exception;
        }
    }

    public function update(int $id, array $data, ?array $file): array
    {
        $existing = $this->find($id);
        if ($existing === null) {
            throw new OutOfBoundsException('Offre introuvable.');
        }

        $payload = $this->normalize($data);
        $errors = $this->validate($payload);
        $hasUpload = $this->hasUpload($file);
        if ($hasUpload) {
            $errors = array_merge($errors, $this->images->validate($file));
        }
        $this->throwIfInvalid($errors);

        $newImage = null;
        if ($hasUpload) {
            $newImage = $this->images->store($file);
            if ($newImage === null) {
                throw new RuntimeException("Impossible d'enregistrer l'image téléchargée.");
            }
            $payload['image'] = $newImage;
        }

        try {
            $updated = $this->offres->update($id, $payload);
            if ($updated === null) {
                throw new OutOfBoundsException('Offre introuvable.');
            }
        } catch (Throwable $exception) {
            if ($newImage !== null) {
                $this->images->remove($newImage);
            }
            throw $exception;
        }

        if ($newImage !== null) {
            $this->removeImageBestEffort($existing['image'] ?? null);
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $existing = $this->find($id);
        if ($existing === null) {
            throw new OutOfBoundsException('Offre introuvable.');
        }
        $deleted = $this->offres->delete($id);
        if ($deleted) {
            $this->removeImageBestEffort($existing['image'] ?? null);
        }

        return $deleted;
    }

    private function removeImageBestEffort(?string $image): void
    {
        try {
            $this->images->remove($image);
        } catch (RuntimeException $exception) {
            error_log('SmartMatch image cleanup failed: ' . $exception->getMessage());
        }
    }

    private function normalize(array $data): array
    {
        $payload = [
            'titre' => trim((string) ($data['titre'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'domaine' => trim((string) ($data['domaine'] ?? '')),
            'years_required' => $data['years_required'] ?? null,
            'id_recruteur' => $data['id_recruteur'] ?? null,
        ];
        if ($payload['years_required'] !== null && $payload['years_required'] !== ''
            && filter_var($payload['years_required'], FILTER_VALIDATE_INT) !== false) {
            $payload['years_required'] = (int) $payload['years_required'];
        }
        if ($payload['id_recruteur'] !== null && $payload['id_recruteur'] !== '') {
            $payload['id_recruteur'] = (int) $payload['id_recruteur'];
        }

        return $payload;
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['titre'] === '') {
            $errors[] = "Le titre de l'offre est obligatoire.";
        } elseif (mb_strlen($data['titre']) > 150) {
            $errors[] = "Le titre ne doit pas dépasser 150 caractères.";
        }
        if ($data['domaine'] === '') {
            $errors[] = 'Le domaine est obligatoire.';
        } elseif (mb_strlen($data['domaine']) > 100) {
            $errors[] = 'Le domaine ne doit pas dépasser 100 caractères.';
        }
        if ($data['years_required'] === null || $data['years_required'] === '') {
            $errors[] = "Les années d'expérience requises sont obligatoires.";
        } elseif (filter_var($data['years_required'], FILTER_VALIDATE_INT) === false
            || (int) $data['years_required'] < 0) {
            $errors[] = "Les années d'expérience doivent être un entier positif ou nul.";
        }
        if ($data['id_recruteur'] === null || $data['id_recruteur'] === '') {
            $errors[] = 'Le recruteur est obligatoire.';
        } else {
            $recruteur = $this->users->find((int) $data['id_recruteur']);
            if ($recruteur === null) {
                $errors[] = "Le recruteur sélectionné n'existe pas.";
            } elseif (!in_array($recruteur['role'] ?? '', ['recruteur', 'admin'], true)) {
                $errors[] = "L'utilisateur sélectionné n'a pas le rôle recruteur.";
            }
        }

        return $errors;
    }

    private function hasUpload(?array $file): bool
    {
        return is_array($file) && (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE);
    }

    private function throwIfInvalid(array $errors): void
    {
        if ($errors !== []) {
            throw new InvalidArgumentException(implode("\n", $errors));
        }
    }
}

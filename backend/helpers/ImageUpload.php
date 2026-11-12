<?php

class ImageUpload
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = rtrim($directory, DIRECTORY_SEPARATOR);
    }

    public function validate(array $file): array
    {
        $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            return [];
        }
        if ($errorCode !== UPLOAD_ERR_OK) {
            return ["Échec du téléchargement de l'image (code : " . (int) $errorCode . ")."];
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0) {
            return ["Le fichier image est vide."];
        }
        if ($size > 2 * 1024 * 1024) {
            return ["L'image ne doit pas dépasser 2 Mo."];
        }

        $clientExt = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($clientExt, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return ["Format d'image non autorisé. Utilisez .jpg, .jpeg, .png ou .webp."];
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_file($tmp) || $this->extensionFor($tmp) === null) {
            return ["Le fichier téléchargé n'est pas une image valide."];
        }

        return [];
    }

    public function store(array $file): ?string
    {
        $tmp = (string) ($file['tmp_name'] ?? '');
        $extension = $this->extensionFor($tmp);
        if ($extension === null) {
            return null;
        }
        if (!is_dir($this->directory) && !mkdir($this->directory, 0777, true) && !is_dir($this->directory)) {
            return null;
        }

        $name = uniqid('offre_', true) . '.' . $extension;
        $destination = $this->directory . DIRECTORY_SEPARATOR . $name;

        if (is_uploaded_file($tmp)) {
            return move_uploaded_file($tmp, $destination) ? $name : null;
        }
        if (@rename($tmp, $destination)) {
            return $name;
        }
        if (@copy($tmp, $destination)) {
            @unlink($tmp);
            return $name;
        }

        return null;
    }

    public function remove(?string $image): void
    {
        if ($image === null || $image === '') {
            return;
        }

        $path = $this->directory . DIRECTORY_SEPARATOR . basename($image);
        if (is_file($path) && !@unlink($path)) {
            throw new RuntimeException("Impossible de supprimer l'image.");
        }
    }

    private function extensionFor(string $path): ?string
    {
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $info = @getimagesize($path);
        if ($info === false || empty($info['mime'])) {
            return null;
        }

        $mime = $info['mime'];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $detected = finfo_file($finfo, $path);
                finfo_close($finfo);
                if (is_string($detected) && $detected !== '') {
                    $mime = $detected;
                }
            }
        }

        return $allowed[$mime] ?? null;
    }
}

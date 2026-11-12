<?php

class JsonStorage
{
    protected string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
        $directory = dirname($file);

        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException('Impossible de créer le répertoire de stockage.');
        }

        if (!file_exists($file) && file_put_contents($file, '[]') === false) {
            throw new RuntimeException('Impossible d\'initialiser le fichier de stockage.');
        }
    }

    public function all(): array { return $this->read(); }

    public function find(int $id): ?array
    {
        foreach ($this->read() as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                return $row;
            }
        }

        return null;
    }

    public function create(array $data): array
    {
        $rows = $this->read();
        $data['id'] = $this->nextId($rows);
        $rows[] = $data;
        $this->save($rows);

        return $data;
    }

    public function update(int $id, array $data): ?array
    {
        $rows = $this->read();

        foreach ($rows as $index => $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                $rows[$index] = array_merge($row, $data, ['id' => $id]);
                $this->save($rows);

                return $rows[$index];
            }
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $rows = $this->read();
        $kept = [];
        $found = false;

        foreach ($rows as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                $found = true;
                continue;
            }
            $kept[] = $row;
        }

        if ($found) {
            $this->save($kept);
        }

        return $found;
    }

    protected function read(): array
    {
        $handle = fopen($this->file, 'r');
        if ($handle === false) {
            throw new RuntimeException('Impossible de lire le fichier de stockage.');
        }

        if (!flock($handle, LOCK_SH)) {
            fclose($handle);
            throw new RuntimeException('Impossible de verrouiller le fichier de stockage.');
        }

        $contents = stream_get_contents($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        if ($contents === false || trim($contents) === '') {
            return [];
        }

        $decoded = json_decode($contents, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Le fichier de stockage contient un JSON invalide.');
        }

        return $decoded;
    }

    protected function nextId(array $rows): int
    {
        $max = 0;
        foreach ($rows as $row) {
            $max = max($max, (int) ($row['id'] ?? 0));
        }

        return $max + 1;
    }

    protected function save(array $rows): void
    {
        $json = json_encode(
            array_values($rows),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
        $handle = fopen($this->file, 'c');

        if ($handle === false) {
            throw new RuntimeException('Impossible d\'ouvrir le fichier de stockage.');
        }
        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            throw new RuntimeException('Impossible de verrouiller le fichier de stockage.');
        }

        if (!ftruncate($handle, 0) || !rewind($handle) || fwrite($handle, $json) === false || !fflush($handle)) {
            flock($handle, LOCK_UN);
            fclose($handle);
            throw new RuntimeException('Impossible d\'écrire le fichier de stockage.');
        }

        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

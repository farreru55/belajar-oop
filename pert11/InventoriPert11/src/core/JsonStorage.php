<?php

class JsonStorage
{
    private string $filePath;
    private array $data = [];
    private bool $autoSave = true;

    public function __construct(string $filePath, bool $autoSave = true)
    {
        $this->filePath = $filePath;
        $this->autoSave = $autoSave;
        $this->ensureDirectoryExists();
        $this->load();
    }

    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    public function load(): void
    {
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            if ($content !== false) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->data = $decoded;
                }
            }
        }
    }

    public function save(): bool
    {
        $json = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        $result = file_put_contents($this->filePath, $json);
        return $result !== false;
    }

    public function setAutoSave(bool $autoSave): void
    {
        $this->autoSave = $autoSave;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        if ($this->autoSave) {
            $this->save();
        }
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function remove(string $key): bool
    {
        if (!array_key_exists($key, $this->data)) {
            return false;
        }

        unset($this->data[$key]);
        if ($this->autoSave) {
            $this->save();
        }
        return true;
    }

    public function clear(): void
    {
        $this->data = [];
        if ($this->autoSave) {
            $this->save();
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function push(string $key, $value): void
    {
        if (!isset($this->data[$key]) || !is_array($this->data[$key])) {
            $this->data[$key] = [];
        }
        $this->data[$key][] = $value;
        if ($this->autoSave) {
            $this->save();
        }
    }

    public function filter(callable $callback): array
    {
        return array_filter($this->data, $callback, ARRAY_FILTER_USE_BOTH);
    }

    public function map(callable $callback): array
    {
        return array_map($callback, $this->data);
    }

    public function find(callable $callback)
    {
        foreach ($this->data as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }

    public function exists(): bool
    {
        return file_exists($this->filePath);
    }

    public function delete(): bool
    {
        if (file_exists($this->filePath)) {
            return unlink($this->filePath);
        }
        return true;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function backup(string $backupPath = null): bool
    {
        if ($backupPath === null) {
            $backupPath = $this->filePath . '.backup.' . date('Y-m-d_H-i-s');
        }

        if (!file_exists($this->filePath)) {
            return false;
        }

        return copy($this->filePath, $backupPath);
    }

    public function restore(string $backupPath): bool
    {
        if (!file_exists($backupPath)) {
            return false;
        }

        $content = file_get_contents($backupPath);
        if ($content === false) {
            return false;
        }

        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return false;
        }

        $this->data = $decoded;
        return $this->save();
    }
}
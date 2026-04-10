<?php

class JsonDriver implements StorageDriver {
    private string $base;

    public function __construct(string $basePath) {
        $this->base = rtrim($basePath, '/');
    }

    private function keyToPath(string $key): string {
        $key = str_replace(['..', '//'], '', $key);
        $key = trim($key, '/');
        return $this->base . '/' . $key . '.json';
    }

    public function get(string $key): mixed {
        $path = $this->keyToPath($key);
        if (!file_exists($path)) return null;
        $content = file_get_contents($path);
        return json_decode($content, true);
    }

    public function set(string $key, mixed $value): bool {
        $path = $this->keyToPath($key);
        $dir  = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $encoded = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($path, $encoded, LOCK_EX) !== false;
    }

    public function delete(string $key): bool {
        $path = $this->keyToPath($key);
        if (file_exists($path)) {
            return unlink($path);
        }
        return true;
    }

    public function list(string $prefix = ''): array {
        $prefix  = trim($prefix, '/');
        $dir     = $this->base . ($prefix ? '/' . $prefix : '');
        $keys    = [];

        if (!is_dir($dir)) return $keys;

        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iter as $file) {
            if ($file->getExtension() !== 'json') continue;
            $relative = substr($file->getPathname(), strlen($this->base) + 1);
            $keys[]   = substr($relative, 0, -5); // strip .json
        }

        return $keys;
    }
}

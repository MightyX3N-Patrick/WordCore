<?php

class Storage {
    private static ?StorageDriver $driver = null;

    public static function setDriver(StorageDriver $driver): void {
        self::$driver = $driver;
    }

    public static function driver(): StorageDriver {
        if (self::$driver === null) {
            self::$driver = new JsonDriver(WC_ROOT . '/data');
        }
        return self::$driver;
    }

    public static function get(string $key, mixed $default = null): mixed {
        $value = self::driver()->get($key);
        return $value !== null ? $value : $default;
    }

    public static function set(string $key, mixed $value): bool {
        return self::driver()->set($key, $value);
    }

    public static function delete(string $key): bool {
        return self::driver()->delete($key);
    }

    public static function list(string $prefix = ''): array {
        return self::driver()->list($prefix);
    }
}

interface StorageDriver {
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): bool;
    public function delete(string $key): bool;
    public function list(string $prefix): array;
}

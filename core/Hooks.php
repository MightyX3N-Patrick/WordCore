<?php

class Hooks {
    private static array $hooks = [];
    private static array $filters = [];

    public static function on(string $event, callable $callback, int $priority = 10): void {
        self::$hooks[$event][$priority][] = $callback;
    }

    public static function fire(string $event, mixed ...$args): void {
        if (empty(self::$hooks[$event])) return;
        ksort(self::$hooks[$event]);
        foreach (self::$hooks[$event] as $callbacks) {
            foreach ($callbacks as $cb) {
                call_user_func_array($cb, $args);
            }
        }
    }

    public static function filter(string $name, callable $callback, int $priority = 10): void {
        self::$filters[$name][$priority][] = $callback;
    }

    public static function apply(string $name, mixed $value, mixed ...$args): mixed {
        if (empty(self::$filters[$name])) return $value;
        ksort(self::$filters[$name]);
        foreach (self::$filters[$name] as $callbacks) {
            foreach ($callbacks as $cb) {
                $value = call_user_func_array($cb, [$value, ...$args]);
            }
        }
        return $value;
    }
}

<?php

class WordCore {
    public static function base(): string {
        return rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    }

    public static function redirect(string $path): void {
        header('Location: ' . self::base() . $path);
        exit;
    }

    public static function init(): void {
        self::ensureInstalled();
        AddonManager::loadActive();
        Hooks::fire('wordcore_loaded');
    }

    private static function ensureInstalled(): void {
        $settings = Storage::get('core/settings');
        if ($settings !== null) return;

        Storage::set('core/settings', [
            'site_name'    => 'WordCore Site',
            'active_theme' => null,
            'installed_at' => date('c'),
        ]);
        Storage::set('core/addons', []);
        Storage::set('core/themes', []);
        Storage::set('core/repos',  []);
        Storage::set('core/users',  []);

        Auth::createUser('admin', 'admin', 'admin');
    }

    public static function route(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        $base = self::base();
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri    = '/' . ltrim($uri, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        require_once WC_ROOT . '/panel/routes.php';

        Router::dispatch($method, $uri);
    }
}

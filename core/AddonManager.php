<?php

class AddonManager {
    private static array $registry = [];

    public static function loadActive(): void {
        $addons = Storage::get('core/addons', []);
        foreach ($addons as $slug => $meta) {
            if (!empty($meta['active'])) {
                self::loadAddon($slug);
            }
        }
    }

    private static function loadAddon(string $slug): void {
        $entry = WC_ROOT . '/addons/' . $slug . '/addon.php';
        if (file_exists($entry)) {
            require_once $entry;
        }
    }

    public static function getAll(): array {
        $installed = Storage::get('core/addons', []);
        $result    = [];

        foreach (glob(WC_ROOT . '/addons/*/addon.json') as $manifest) {
            $slug = basename(dirname($manifest));
            $meta = json_decode(file_get_contents($manifest), true) ?? [];
            $meta['slug']   = $slug;
            $meta['active'] = !empty($installed[$slug]['active']);
            $result[$slug]  = $meta;
        }

        return $result;
    }

    public static function activate(string $slug): bool {
        $manifest = WC_ROOT . '/addons/' . $slug . '/addon.json';
        if (!file_exists($manifest)) return false;

        $addons = Storage::get('core/addons', []);
        $addons[$slug] = ['active' => true];
        Storage::set('core/addons', $addons);

        self::loadAddon($slug);
        Hooks::fire('addon_activated', $slug);
        return true;
    }

    public static function deactivate(string $slug): bool {
        $addons = Storage::get('core/addons', []);
        if (isset($addons[$slug])) {
            $addons[$slug]['active'] = false;
            Storage::set('core/addons', $addons);
        }
        Hooks::fire('addon_deactivated', $slug);
        return true;
    }

    public static function delete(string $slug): bool {
        self::deactivate($slug);
        $addons = Storage::get('core/addons', []);
        unset($addons[$slug]);
        Storage::set('core/addons', $addons);

        $dir = WC_ROOT . '/addons/' . $slug;
        if (is_dir($dir)) {
            self::rrmdir($dir);
        }
        return true;
    }

    public static function install(string $zipPath): array {
        $tmp = sys_get_temp_dir() . '/wc_addon_' . uniqid();
        mkdir($tmp);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['ok' => false, 'error' => 'Could not open zip file.'];
        }
        $zip->extractTo($tmp);
        $zip->close();

        $manifestFile = self::findManifest($tmp, 'addon.json');
        if (!$manifestFile) {
            self::rrmdir($tmp);
            return ['ok' => false, 'error' => 'No addon.json found in zip.'];
        }

        $meta = json_decode(file_get_contents($manifestFile), true);
        if (empty($meta['slug'])) {
            self::rrmdir($tmp);
            return ['ok' => false, 'error' => 'addon.json missing required "slug" field.'];
        }

        $slug   = preg_replace('/[^a-z0-9\-_]/', '', $meta['slug']);
        $dest   = WC_ROOT . '/addons/' . $slug;
        $srcDir = dirname($manifestFile);

        if (is_dir($dest)) self::rrmdir($dest);
        self::rcopy($srcDir, $dest);
        self::rrmdir($tmp);

        return ['ok' => true, 'slug' => $slug, 'meta' => $meta];
    }

    public static function installFromRepo(string $zipUrl): array {
        $tmp  = tempnam(sys_get_temp_dir(), 'wc_') . '.zip';
        $data = self::httpGet($zipUrl);
        if ($data === null) {
            return ['ok' => false, 'error' => 'Failed to download from repository.'];
        }
        file_put_contents($tmp, $data);
        $result = self::install($tmp);
        unlink($tmp);
        return $result;
    }

    public static function httpGet(string $url): ?string {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_USERAGENT      => 'WordCore/' . WC_VERSION,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($data !== false && $code === 200) ? $data : null;
        }
        // Fallback to file_get_contents
        $ctx  = stream_context_create(['http' => ['timeout' => 15, 'user_agent' => 'WordCore/' . WC_VERSION]]);
        $data = @file_get_contents($url, false, $ctx);
        return $data !== false ? $data : null;
    }

    private static function findManifest(string $dir, string $filename): ?string {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
            if ($file->getFilename() === $filename) return $file->getPathname();
        }
        return null;
    }

    private static function rcopy(string $src, string $dst): void {
        mkdir($dst, 0755, true);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $src . '/' . $item;
            $d = $dst . '/' . $item;
            is_dir($s) ? self::rcopy($s, $d) : copy($s, $d);
        }
    }

    private static function rrmdir(string $dir): void {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? self::rrmdir($path) : unlink($path);
        }
        rmdir($dir);
    }

    public static function registerNavItem(string $label, string $url, int $priority = 50, string $icon = ''): void {
        self::$registry['nav'][] = compact('label', 'url', 'priority', 'icon');
    }

    public static function getNavItems(): array {
        $items = self::$registry['nav'] ?? [];
        usort($items, fn($a, $b) => $a['priority'] <=> $b['priority']);
        return $items;
    }
}

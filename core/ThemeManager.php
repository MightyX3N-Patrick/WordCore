<?php

class ThemeManager {
    public static function getAll(): array {
        $installed = Storage::get('core/themes', []);
        $active    = Storage::get('core/settings', [])['active_theme'] ?? null;
        $result    = [];

        foreach (glob(WC_ROOT . '/themes/*/theme.json') as $manifest) {
            $slug = basename(dirname($manifest));
            $meta = json_decode(file_get_contents($manifest), true) ?? [];
            $meta['slug']   = $slug;
            $meta['active'] = ($slug === $active);
            $result[$slug]  = $meta;
        }

        return $result;
    }

    public static function activate(string $slug): bool {
        $manifest = WC_ROOT . '/themes/' . $slug . '/theme.json';
        if (!file_exists($manifest)) return false;

        $settings = Storage::get('core/settings', []);
        $settings['active_theme'] = $slug;
        Storage::set('core/settings', $settings);

        Hooks::fire('theme_activated', $slug);
        return true;
    }

    public static function delete(string $slug): bool {
        $settings = Storage::get('core/settings', []);
        if (($settings['active_theme'] ?? null) === $slug) {
            return false;
        }

        $dir = WC_ROOT . '/themes/' . $slug;
        if (is_dir($dir)) {
            self::rrmdir($dir);
        }
        return true;
    }

    public static function install(string $zipPath): array {
        $tmp = sys_get_temp_dir() . '/wc_theme_' . uniqid();
        mkdir($tmp);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['ok' => false, 'error' => 'Could not open zip file.'];
        }
        $zip->extractTo($tmp);
        $zip->close();

        $manifestFile = self::findManifest($tmp, 'theme.json');
        if (!$manifestFile) {
            self::rrmdir($tmp);
            return ['ok' => false, 'error' => 'No theme.json found in zip.'];
        }

        $meta = json_decode(file_get_contents($manifestFile), true);
        if (empty($meta['slug'])) {
            self::rrmdir($tmp);
            return ['ok' => false, 'error' => 'theme.json missing required "slug" field.'];
        }

        $slug   = preg_replace('/[^a-z0-9\-_]/', '', $meta['slug']);
        $dest   = WC_ROOT . '/themes/' . $slug;
        $srcDir = dirname($manifestFile);

        if (is_dir($dest)) self::rrmdir($dest);
        self::rcopy($srcDir, $dest);
        self::rrmdir($tmp);

        return ['ok' => true, 'slug' => $slug, 'meta' => $meta];
    }

    public static function installFromRepo(string $zipUrl): array {
        $tmp  = tempnam(sys_get_temp_dir(), 'wc_') . '.zip';
        $data = AddonManager::httpGet($zipUrl);
        if ($data === null) {
            return ['ok' => false, 'error' => 'Failed to download from repository.'];
        }
        file_put_contents($tmp, $data);
        $result = self::install($tmp);
        unlink($tmp);
        return $result;
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
}

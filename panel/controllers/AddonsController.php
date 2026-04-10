<?php

class AddonsController {
    public static function index(array $p): void {
        Auth::require();
        $addons  = AddonManager::getAll();
        $flash   = $_GET['msg'] ?? null;
        $error   = $_GET['err'] ?? null;
        require WC_ROOT . '/panel/views/addons.php';
    }

    public static function activate(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        AddonManager::activate($_POST['slug'] ?? '');
        WordCore::redirect('/wc-admin/addons?msg=activated');
    }

    public static function deactivate(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        AddonManager::deactivate($_POST['slug'] ?? '');
        WordCore::redirect('/wc-admin/addons?msg=deactivated');
    }

    public static function delete(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        AddonManager::delete($_POST['slug'] ?? '');
        WordCore::redirect('/wc-admin/addons?msg=deleted');
    }

    public static function upload(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        if (empty($_FILES['zip']) || $_FILES['zip']['error'] !== UPLOAD_ERR_OK) {
            WordCore::redirect('/wc-admin/addons?err=upload_failed');
        }
        $result = AddonManager::install($_FILES['zip']['tmp_name']);
        if ($result['ok']) {
            WordCore::redirect('/wc-admin/addons?msg=installed');
        } else {
            WordCore::redirect('/wc-admin/addons?err=' . urlencode($result['error']));
        }
    }

    public static function browse(array $p): void {
        Auth::require();
        $repos    = Storage::get('core/repos', []);
        $packages = [];
        foreach ($repos as $repo) {
            if (empty($repo['url'])) continue;
            if (!in_array($repo['type'] ?? 'both', ['addons', 'both'])) continue;
            $repoBase = rtrim($repo['url'], '/');
            $body     = AddonManager::httpGet($repoBase . '/index.json');
            if ($body) {
                $data = json_decode($body, true) ?? [];
                foreach (($data['addons'] ?? []) as $pkg) {
                    $pkg['_repo'] = $repo['name'];
                    if (!empty($pkg['download']) && !str_starts_with($pkg['download'], 'http')) {
                        $pkg['download'] = $repoBase . '/' . ltrim($pkg['download'], '/');
                    }
                    $packages[] = $pkg;
                }
            }
        }
        require WC_ROOT . '/panel/views/addons_browse.php';
    }

    public static function installRemote(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $url = $_POST['url'] ?? '';
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            WordCore::redirect('/wc-admin/addons/browse?err=invalid_url');
        }
        $result = AddonManager::installFromRepo($url);
        if ($result['ok']) {
            WordCore::redirect('/wc-admin/addons?msg=installed');
        } else {
            WordCore::redirect('/wc-admin/addons/browse?err=' . urlencode($result['error']));
        }
    }
}

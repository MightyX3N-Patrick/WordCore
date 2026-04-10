<?php

class SettingsController {
    public static function index(array $p): void {
        Auth::require();
        $settings = Storage::get('core/settings', []);
        $repos    = Storage::get('core/repos', []);
        $flash    = $_GET['msg'] ?? null;
        $error    = $_GET['err'] ?? null;
        $section  = $_GET['section'] ?? 'general';
        require WC_ROOT . '/panel/views/settings.php';
    }

    public static function saveGeneral(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $settings = Storage::get('core/settings', []);
        $settings['site_name'] = trim($_POST['site_name'] ?? 'WordCore Site');
        Storage::set('core/settings', $settings);
        WordCore::redirect('/wc-admin/settings?msg=saved');
    }

    public static function addRepo(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $name = trim($_POST['name'] ?? '');
        $url  = trim($_POST['url']  ?? '');
        $type = $_POST['type'] ?? 'both';

        if (!$name || !$url) {
            WordCore::redirect('/wc-admin/settings?section=repos&err=missing_fields');
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            WordCore::redirect('/wc-admin/settings?section=repos&err=invalid_url');
        }

        $repos   = Storage::get('core/repos', []);
        $repos[] = [
            'id'    => uniqid(),
            'name'  => $name,
            'url'   => rtrim($url, '/'),
            'type'  => in_array($type, ['both', 'addons', 'themes']) ? $type : 'both',
            'added' => date('c'),
        ];
        Storage::set('core/repos', $repos);
        WordCore::redirect('/wc-admin/settings?section=repos&msg=repo_added');
    }

    public static function deleteRepo(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $id    = $_POST['id'] ?? '';
        $repos = Storage::get('core/repos', []);
        $repos = array_values(array_filter($repos, fn($r) => $r['id'] !== $id));
        Storage::set('core/repos', $repos);
        WordCore::redirect('/wc-admin/settings?section=repos&msg=repo_deleted');
    }

    public static function testRepo(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $url = trim($_POST['url'] ?? '');
        header('Content-Type: application/json');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo json_encode(['ok' => false, 'error' => 'Invalid URL.']);
            exit;
        }

        $indexUrl = rtrim($url, '/') . '/index.json';
        $body     = AddonManager::httpGet($indexUrl);

        if ($body === null) {
            echo json_encode(['ok' => false, 'error' => 'Could not reach ' . $indexUrl]);
            exit;
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            echo json_encode(['ok' => false, 'error' => 'index.json is not valid JSON.']);
            exit;
        }

        $ac = count($data['addons'] ?? []);
        $tc = count($data['themes'] ?? []);
        echo json_encode(['ok' => true, 'message' => "Connected. Found {$ac} addon(s) and {$tc} theme(s)."]);
        exit;
    }
}

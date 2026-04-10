<?php

class DashboardController {
    public static function index(array $p): void {
        Auth::require();
        $addons   = AddonManager::getAll();
        $themes   = ThemeManager::getAll();
        $users    = Auth::getUsers();
        $settings = Storage::get('core/settings', []);
        $active   = array_filter($addons, fn($a) => $a['active']);
        require WC_ROOT . '/panel/views/dashboard.php';
    }
}

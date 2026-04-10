<?php
require_once WC_ROOT . '/core/Storage.php';
require_once WC_ROOT . '/core/JsonDriver.php';
require_once WC_ROOT . '/core/Hooks.php';
require_once WC_ROOT . '/core/AddonManager.php';
require_once WC_ROOT . '/core/ThemeManager.php';
require_once WC_ROOT . '/core/Auth.php';
require_once WC_ROOT . '/core/Router.php';
require_once WC_ROOT . '/core/WordCore.php';

// Register built-in WordCore capabilities for the role editor
Hooks::filter('wc_capabilities', function (array $caps): array {
    $caps['WordCore']['manage_settings'] = 'Manage Settings';
    $caps['WordCore']['manage_users']    = 'Manage Users';
    $caps['WordCore']['manage_addons']   = 'Manage Addons';
    $caps['WordCore']['manage_themes']   = 'Manage Themes';
    return $caps;
});

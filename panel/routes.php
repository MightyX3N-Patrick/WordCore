<?php

require_once WC_ROOT . '/panel/controllers/LoginController.php';
require_once WC_ROOT . '/panel/controllers/DashboardController.php';
require_once WC_ROOT . '/panel/controllers/AddonsController.php';
require_once WC_ROOT . '/panel/controllers/ThemesController.php';
require_once WC_ROOT . '/panel/controllers/SettingsController.php';
require_once WC_ROOT . '/panel/controllers/UsersController.php';

Router::get('/',                  [LoginController::class,     'redirectToAdmin']);
Router::get('/wc-admin',             [DashboardController::class, 'index']);
Router::get('/wc-admin/login',       [LoginController::class,     'showLogin']);
Router::post('/wc-admin/login',      [LoginController::class,     'doLogin']);
Router::get('/wc-admin/logout',      [LoginController::class,     'doLogout']);

Router::get('/wc-admin/addons',             [AddonsController::class, 'index']);
Router::post('/wc-admin/addons/activate',   [AddonsController::class, 'activate']);
Router::post('/wc-admin/addons/deactivate', [AddonsController::class, 'deactivate']);
Router::post('/wc-admin/addons/delete',     [AddonsController::class, 'delete']);
Router::post('/wc-admin/addons/upload',     [AddonsController::class, 'upload']);
Router::get('/wc-admin/addons/browse',      [AddonsController::class, 'browse']);
Router::post('/wc-admin/addons/install-remote', [AddonsController::class, 'installRemote']);

Router::get('/wc-admin/themes',             [ThemesController::class, 'index']);
Router::post('/wc-admin/themes/activate',   [ThemesController::class, 'activate']);
Router::post('/wc-admin/themes/delete',     [ThemesController::class, 'delete']);
Router::post('/wc-admin/themes/upload',     [ThemesController::class, 'upload']);
Router::get('/wc-admin/themes/browse',      [ThemesController::class, 'browse']);
Router::post('/wc-admin/themes/install-remote', [ThemesController::class, 'installRemote']);

Router::get('/wc-admin/settings',                 [SettingsController::class, 'index']);
Router::post('/wc-admin/settings/general',        [SettingsController::class, 'saveGeneral']);
Router::post('/wc-admin/settings/repos/add',      [SettingsController::class, 'addRepo']);
Router::post('/wc-admin/settings/repos/delete',   [SettingsController::class, 'deleteRepo']);
Router::post('/wc-admin/settings/repos/test',     [SettingsController::class, 'testRepo']);

Router::get('/wc-admin/users',             [UsersController::class, 'index']);
Router::post('/wc-admin/users/create',     [UsersController::class, 'create']);
Router::post('/wc-admin/users/delete',     [UsersController::class, 'delete']);
Router::post('/wc-admin/users/password',   [UsersController::class, 'changePassword']);

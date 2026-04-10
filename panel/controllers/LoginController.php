<?php

class LoginController {
    public static function redirectToAdmin(array $p): void {
        WordCore::redirect('/wc-admin');
    }

    public static function showLogin(array $p): void {
        if (Auth::check()) { WordCore::redirect('/wc-admin'); }
        $error = $_GET['error'] ?? null;
        require WC_ROOT . '/panel/views/login.php';
    }

    public static function doLogin(array $p): void {
        Auth::verifyCsrf();
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($username, $password)) {
            WordCore::redirect('/wc-admin');
        } else {
            WordCore::redirect('/wc-admin/login?error=1');
        }
    }

    public static function doLogout(array $p): void {
        Auth::logout();
        WordCore::redirect('/wc-admin/login');
    }
}

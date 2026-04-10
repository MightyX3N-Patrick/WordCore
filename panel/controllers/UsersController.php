<?php

class UsersController {
    public static function index(array $p): void {
        Auth::require();
        $users = Auth::getUsers();
        $flash = $_GET['msg'] ?? null;
        $error = $_GET['err'] ?? null;
        require WC_ROOT . '/panel/views/users.php';
    }

    public static function create(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'admin';

        if (!$username || !$password) {
            WordCore::redirect('/wc-admin/users?err=missing_fields');
        }
        if (!Auth::createUser($username, $password, $role)) {
            WordCore::redirect('/wc-admin/users?err=username_taken');
        }
        WordCore::redirect('/wc-admin/users?msg=created');
    }

    public static function delete(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $username = $_POST['username'] ?? '';
        if ($username === (Auth::user()['username'] ?? '')) {
            WordCore::redirect('/wc-admin/users?err=cannot_delete_self');
        }
        Auth::deleteUser($username);
        WordCore::redirect('/wc-admin/users?msg=deleted');
    }

    public static function changePassword(array $p): void {
        Auth::require(); Auth::verifyCsrf();
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$username || strlen($password) < 6) {
            WordCore::redirect('/wc-admin/users?err=invalid_password');
        }
        Auth::changePassword($username, $password);
        WordCore::redirect('/wc-admin/users?msg=password_changed');
    }
}

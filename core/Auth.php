<?php

class Auth {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool {
        self::start();
        return !empty($_SESSION['wc_user']);
    }

    public static function user(): ?array {
        self::start();
        return $_SESSION['wc_user'] ?? null;
    }

    public static function attempt(string $username, string $password): bool {
        $users = Storage::get('core/users', []);
        foreach ($users as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                self::start();
                $_SESSION['wc_user'] = [
                    'username' => $user['username'],
                    'role'     => $user['role'],
                ];
                return true;
            }
        }
        return false;
    }

    public static function logout(): void {
        self::start();
        session_destroy();
    }

    public static function require(): void {
        if (!self::check()) {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
            header('Location: ' . $base . '/wc-admin/login');
            exit;
        }
    }

    public static function createUser(string $username, string $password, string $role = 'admin'): bool {
        $users = Storage::get('core/users', []);
        foreach ($users as $u) {
            if ($u['username'] === $username) return false;
        }
        $users[] = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role'     => $role,
            'created'  => date('c'),
        ];
        return Storage::set('core/users', $users);
    }

    public static function getUsers(): array {
        return Storage::get('core/users', []);
    }

    public static function deleteUser(string $username): bool {
        $users = Storage::get('core/users', []);
        $users = array_values(array_filter($users, fn($u) => $u['username'] !== $username));
        return Storage::set('core/users', $users);
    }

    public static function csrfToken(): string {
        self::start();
        if (empty($_SESSION['wc_csrf'])) {
            $_SESSION['wc_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['wc_csrf'];
    }

    public static function csrfField(): void {
        echo '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::csrfToken()) . '">';
    }

    public static function verifyCsrf(): void {
        self::start();
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['wc_csrf'] ?? '', $token)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }
    }

    public static function changePassword(string $username, string $newPassword): bool {
        $users = Storage::get('core/users', []);
        foreach ($users as &$u) {
            if ($u['username'] === $username) {
                $u['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
                return Storage::set('core/users', $users);
            }
        }
        return false;
    }
}

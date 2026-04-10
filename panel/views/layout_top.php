<?php
$currentUser = Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
function wc_url(string $path): string {
    global $base;
    return $base . $path;
}
function wc_active(string $prefix): string {
    global $currentPath, $base;
    $prefixFull = $base . $prefix;
    return str_starts_with($currentPath, $prefixFull) ? ' nav-active' : '';
}
function wc_active_exact(string $path): string {
    global $currentPath, $base;
    return $currentPath === $base . $path ? ' nav-active' : '';
}
function wc_flash(?string $msg, ?string $err): void {
    $messages = [
        'saved'            => 'Settings saved.',
        'activated'        => 'Activated successfully.',
        'deactivated'      => 'Deactivated.',
        'deleted'          => 'Deleted.',
        'installed'        => 'Installed successfully.',
        'created'          => 'User created.',
        'password_changed' => 'Password updated.',
        'repo_added'       => 'Repository added.',
        'repo_deleted'     => 'Repository removed.',
    ];
    $errors = [
        'upload_failed'        => 'Upload failed. Please try again.',
        'cannot_delete_active' => 'Cannot delete the active theme.',
        'cannot_delete_self'   => 'You cannot delete your own account.',
        'missing_fields'       => 'Please fill in all required fields.',
        'invalid_url'          => 'Please enter a valid URL.',
        'invalid_password'     => 'Password must be at least 6 characters.',
        'username_taken'       => 'That username is already taken.',
    ];
    if ($msg && isset($messages[$msg])) {
        echo '<div class="flash flash-ok">' . htmlspecialchars($messages[$msg]) . '</div>';
    }
    if ($err) {
        $text = $errors[$err] ?? htmlspecialchars(urldecode($err));
        echo '<div class="flash flash-err">' . $text . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — WordCore</title>
<link rel="stylesheet" href="<?= wc_url('/public/assets/css/admin.css') ?>">
</head>
<body data-base="<?= htmlspecialchars(WordCore::base()) ?>">
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">W</div>
      <span class="brand-name">WordCore</span>
      <span class="brand-ver">v<?= WC_VERSION ?></span>
    </div>
    <nav class="sidebar-nav">
      <a href="<?= wc_url('/wc-admin') ?>" class="nav-item<?= wc_active_exact('/wc-admin') ?>">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="1" y="1" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.2"/><rect x="8.5" y="1" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.2"/><rect x="1" y="8.5" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.2"/><rect x="8.5" y="8.5" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
        Dashboard
      </a>
      <a href="<?= wc_url('/wc-admin/addons') ?>" class="nav-item<?= wc_active('/wc-admin/addons') ?>">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M7.5 1L13 4V11L7.5 14L2 11V4L7.5 1Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/><path d="M7.5 1V14M2 4L13 11M13 4L2 11" stroke="currentColor" stroke-width="1.2"/></svg>
        Addons
      </a>
      <a href="<?= wc_url('/wc-admin/themes') ?>" class="nav-item<?= wc_active('/wc-admin/themes') ?>">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="1" y="1" width="13" height="13" rx="2" stroke="currentColor" stroke-width="1.2"/><path d="M1 5H14" stroke="currentColor" stroke-width="1.2"/><circle cx="3.5" cy="3" r="0.8" fill="currentColor"/><circle cx="6" cy="3" r="0.8" fill="currentColor"/></svg>
        Themes
      </a>
      <a href="<?= wc_url('/wc-admin/settings') ?>" class="nav-item<?= wc_active('/wc-admin/settings') ?>">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="2" stroke="currentColor" stroke-width="1.2"/><path d="M7.5 1v1.5M7.5 12.5V14M14 7.5h-1.5M2.5 7.5H1M11.7 3.3l-1.1 1.1M4.4 10.6l-1.1 1.1M11.7 11.7l-1.1-1.1M4.4 4.4L3.3 3.3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        Settings
      </a>
      <a href="<?= wc_url('/wc-admin/users') ?>" class="nav-item<?= wc_active('/wc-admin/users') ?>">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="5" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M1.5 13.5C1.5 11 4.2 9 7.5 9s6 2 6 4.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        Users
      </a>
      <?php foreach (AddonManager::getNavItems() as $item): ?>
      <a href="<?= htmlspecialchars(wc_url($item['url'])) ?>" class="nav-item<?= wc_active($item['url']) ?>">
        <?php if (!empty($item['icon'])): ?>
          <?= $item['icon'] ?>
        <?php else: ?>
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.2"/><path d="M7.5 4.5v3l2 2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        <?php endif; ?>
        <?= htmlspecialchars($item['label']) ?>
      </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <span class="sidebar-user"><?= htmlspecialchars($currentUser['username'] ?? '') ?></span>
      <a href="<?= wc_url('/wc-admin/logout') ?>" class="logout-link">Log out</a>
    </div>
  </aside>
  <main class="main">
    <div class="main-inner">

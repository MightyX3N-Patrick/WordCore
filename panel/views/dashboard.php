<?php $pageTitle = 'Dashboard'; require __DIR__ . '/layout_top.php'; ?>

<div class="page-header">
  <h1 class="page-title">Dashboard</h1>
</div>

<?php if (($_GET['err'] ?? '') === 'unauthorized'): ?>
  <div class="flash flash-err">You do not have permission to access that page.</div>
<?php endif; ?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">Addons</div>
    <div class="stat-value"><?= count($addons) ?></div>
    <div class="stat-sub ok"><?= count($active) ?> active</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Themes</div>
    <div class="stat-value"><?= count($themes) ?></div>
    <div class="stat-sub"><?= count(array_filter($themes, fn($t) => $t['active'])) ?> active</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Users</div>
    <div class="stat-value"><?= count($users) ?></div>
    <div class="stat-sub"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?> admin(s)</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">PHP</div>
    <div class="stat-value"><?= PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION ?></div>
    <div class="stat-sub ok">healthy</div>
  </div>
</div>

<div class="dash-grid">
  <div class="card">
    <div class="card-header">Active addons</div>
    <div class="item-list">
      <?php if (empty($active)): ?>
        <div class="empty-state"><div class="empty-desc">No addons active.</div></div>
      <?php else: ?>
        <?php foreach ($active as $slug => $a): ?>
        <div class="item-row">
          <div class="item-icon"><?= strtoupper(substr($slug, 0, 1)) ?></div>
          <div class="item-info">
            <div class="item-name">
              <?= htmlspecialchars($a['name'] ?? $slug) ?>
              <span class="item-ver"><?= htmlspecialchars($a['version'] ?? '') ?></span>
            </div>
            <div class="item-desc"><?= htmlspecialchars($a['description'] ?? '') ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">System info</div>
    <div class="card-body">
      <table class="data-table">
        <tr><td style="color:var(--text3)">WordCore</td><td><?= WC_VERSION ?></td></tr>
        <tr><td style="color:var(--text3)">PHP</td><td><?= PHP_VERSION ?></td></tr>
        <tr><td style="color:var(--text3)">OS</td><td><?= PHP_OS ?></td></tr>
        <tr><td style="color:var(--text3)">Site name</td><td><?= htmlspecialchars($settings['site_name'] ?? '—') ?></td></tr>
        <tr><td style="color:var(--text3)">Storage</td><td>JsonDriver (flat file)</td></tr>
        <tr><td style="color:var(--text3)">Installed</td><td><?= $settings['installed_at'] ? date('Y-m-d', strtotime($settings['installed_at'])) : '—' ?></td></tr>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>

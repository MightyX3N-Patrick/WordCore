<?php $pageTitle = 'Browse themes'; require __DIR__ . '/layout_top.php'; ?>

<div class="page-header">
  <h1 class="page-title">Browse themes</h1>
  <div class="page-actions">
    <div class="tab-bar">
      <a href="/wc-admin/themes" class="tab-btn">Installed</a>
      <a href="/wc-admin/themes/browse" class="tab-btn active">Browse repos</a>
    </div>
  </div>
</div>

<?php wc_flash(null, $_GET['err'] ?? null); ?>

<?php if (empty(Storage::get('core/repos', []))): ?>
  <div class="card">
    <div class="empty-state">
      <div class="empty-title">No repositories configured</div>
      <div class="empty-desc">Add a repository in <a href="/wc-admin/settings?section=repos" style="color:var(--info-text)">Settings → Repositories</a> to browse remote themes.</div>
    </div>
  </div>
<?php elseif (empty($packages)): ?>
  <div class="card">
    <div class="empty-state">
      <div class="empty-title">No themes found in repositories</div>
      <div class="empty-desc">Your repositories returned no theme packages.</div>
    </div>
  </div>
<?php else: ?>
  <div class="browse-grid">
    <?php foreach ($packages as $pkg): ?>
    <div class="browse-card">
      <div style="height:80px;background:<?= htmlspecialchars($pkg['preview_color'] ?? '#e8e8e4') ?>;"></div>
      <div class="browse-card-body">
        <div class="browse-card-name"><?= htmlspecialchars($pkg['name'] ?? $pkg['slug'] ?? 'Unknown') ?></div>
        <div class="browse-card-desc"><?= htmlspecialchars($pkg['description'] ?? '') ?></div>
        <div class="browse-card-meta">v<?= htmlspecialchars($pkg['version'] ?? '?') ?> · <?= htmlspecialchars($pkg['_repo'] ?? '') ?></div>
        <form method="POST" action="/wc-admin/themes/install-remote">
          <?php Auth::csrfField(); ?>
          <input type="hidden" name="url" value="<?= htmlspecialchars($pkg['download'] ?? '') ?>">
          <button class="btn btn-primary btn-sm" type="submit" <?= empty($pkg['download']) ? 'disabled' : '' ?>>Install</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/layout_bottom.php'; ?>

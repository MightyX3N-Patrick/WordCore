<?php $pageTitle = 'Addons'; require __DIR__ . '/layout_top.php'; ?>

<div class="page-header">
  <h1 class="page-title">Addons</h1>
  <div class="page-actions">
    <div class="tab-bar">
      <a href="/wc-admin/addons" class="tab-btn active">Installed</a>
      <a href="/wc-admin/addons/browse" class="tab-btn">Browse repos</a>
    </div>
    <label class="btn btn-primary" style="cursor:pointer;">
      Upload .zip
      <form method="POST" action="/wc-admin/addons/upload" enctype="multipart/form-data" style="display:none;">
        <?php Auth::csrfField(); ?>
        <input type="file" name="zip" accept=".zip" onchange="this.form.submit()">
      </form>
    </label>
  </div>
</div>

<?php wc_flash($flash, $error); ?>

<div class="card">
  <?php if (empty($addons)): ?>
    <div class="empty-state">
      <div class="empty-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1L14 4.5V11.5L8 15L2 11.5V4.5L8 1Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
      </div>
      <div class="empty-title">No addons installed</div>
      <div class="empty-desc">Upload a .zip or browse your repositories to install one.</div>
    </div>
  <?php else: ?>
    <div class="item-list">
      <?php foreach ($addons as $slug => $addon): ?>
      <div class="item-row <?= $addon['active'] ? '' : 'inactive' ?>">
        <div class="item-icon"><?= htmlspecialchars(strtoupper(substr($slug, 0, 1))) ?></div>
        <div class="item-info">
          <div class="item-name">
            <?= htmlspecialchars($addon['name'] ?? $slug) ?>
            <span class="badge <?= $addon['active'] ? 'badge-active' : 'badge-inactive' ?>">
              <?= $addon['active'] ? 'active' : 'inactive' ?>
            </span>
            <span class="item-ver"><?= htmlspecialchars($addon['version'] ?? '') ?></span>
          </div>
          <div class="item-desc"><?= htmlspecialchars($addon['description'] ?? '') ?></div>
        </div>
        <div class="item-actions">
          <?php if ($addon['active']): ?>
            <form method="POST" action="/wc-admin/addons/deactivate">
              <?php Auth::csrfField(); ?>
              <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
              <button class="btn btn-sm" type="submit">Deactivate</button>
            </form>
          <?php else: ?>
            <form method="POST" action="/wc-admin/addons/activate">
              <?php Auth::csrfField(); ?>
              <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
              <button class="btn btn-sm" type="submit">Activate</button>
            </form>
            <form method="POST" action="/wc-admin/addons/delete">
              <?php Auth::csrfField(); ?>
              <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
              <button class="btn btn-sm btn-danger" type="submit"
                data-confirm="Delete addon '<?= htmlspecialchars($addon['name'] ?? $slug) ?>'? This cannot be undone.">
                Delete
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>

<?php $pageTitle = 'Themes'; require __DIR__ . '/layout_top.php'; ?>

<div class="page-header">
  <h1 class="page-title">Themes</h1>
  <div class="page-actions">
    <div class="tab-bar">
      <a href="/wc-admin/themes" class="tab-btn active">Installed</a>
      <a href="/wc-admin/themes/browse" class="tab-btn">Browse repos</a>
    </div>
    <label class="btn btn-primary" style="cursor:pointer;">
      Upload .zip
      <form method="POST" action="/wc-admin/themes/upload" enctype="multipart/form-data" style="display:none;">
        <?php Auth::csrfField(); ?>
        <input type="file" name="zip" accept=".zip" onchange="this.form.submit()">
      </form>
    </label>
  </div>
</div>

<?php wc_flash($flash, $error); ?>

<?php if (empty($themes)): ?>
  <div class="card">
    <div class="empty-state">
      <div class="empty-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="currentColor" stroke-width="1.2"/><path d="M1 5.5H15" stroke="currentColor" stroke-width="1.2"/></svg>
      </div>
      <div class="empty-title">No themes installed</div>
      <div class="empty-desc">Upload a .zip or browse your repositories.</div>
    </div>
  </div>
<?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;">
    <?php foreach ($themes as $slug => $theme): ?>
    <div class="card" style="<?= $theme['active'] ? 'border-color:rgba(24,95,165,0.4);border-width:1.5px;' : '' ?>overflow:visible;">
      <div style="height:110px;background:<?= htmlspecialchars($theme['preview_color'] ?? '#e8e8e4') ?>;border-radius:11px 11px 0 0;display:flex;align-items:flex-end;padding:10px;">
        <?php if ($theme['active']): ?>
          <span class="badge badge-info">active</span>
        <?php else: ?>
          <span class="badge badge-inactive">inactive</span>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:4px;">
          <span style="font-size:13px;font-weight:600;"><?= htmlspecialchars($theme['name'] ?? $slug) ?></span>
          <span class="item-ver"><?= htmlspecialchars($theme['version'] ?? '') ?></span>
        </div>
        <div style="font-size:12px;color:var(--text2);margin-bottom:12px;"><?= htmlspecialchars($theme['description'] ?? '') ?></div>
        <div style="display:flex;gap:6px;">
          <?php if (!$theme['active']): ?>
            <form method="POST" action="/wc-admin/themes/activate">
              <?php Auth::csrfField(); ?>
              <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
              <button class="btn btn-sm btn-primary" type="submit">Activate</button>
            </form>
            <form method="POST" action="/wc-admin/themes/delete">
              <?php Auth::csrfField(); ?>
              <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
              <button class="btn btn-sm btn-danger" type="submit"
                data-confirm="Delete theme '<?= htmlspecialchars($theme['name'] ?? $slug) ?>'?">
                Delete
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <div style="border:1px dashed var(--border2);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;min-height:180px;">
      <label style="text-align:center;color:var(--text3);cursor:pointer;">
        <div style="font-size:22px;margin-bottom:6px;">+</div>
        <div style="font-size:12px;">Upload theme</div>
        <form method="POST" action="/wc-admin/themes/upload" enctype="multipart/form-data" style="display:none;">
          <?php Auth::csrfField(); ?>
          <input type="file" name="zip" accept=".zip" onchange="this.form.submit()">
        </form>
      </label>
    </div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/layout_bottom.php'; ?>

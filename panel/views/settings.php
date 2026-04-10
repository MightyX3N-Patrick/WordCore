<?php $pageTitle = 'Settings'; require __DIR__ . '/layout_top.php'; ?>

<div class="page-header">
  <h1 class="page-title">Settings</h1>
</div>

<?php wc_flash($flash, $error); ?>

<div class="settings-layout">
  <nav class="settings-nav">
    <a href="/wc-admin/settings?section=general"     class="<?= $section === 'general'     ? 'active' : '' ?>">General</a>
    <a href="/wc-admin/settings?section=repos"       class="<?= $section === 'repos'       ? 'active' : '' ?>">Repositories</a>
    <a href="/wc-admin/settings?section=permissions" class="<?= $section === 'permissions' ? 'active' : '' ?>">Permissions</a>
  </nav>

  <div>

    <?php if ($section === 'general'): ?>

      <div class="card">
        <div class="card-header">General</div>
        <div class="card-body">
          <form method="POST" action="/wc-admin/settings/general">
            <?php Auth::csrfField(); ?>
            <div class="form-group">
              <label class="form-label" for="site_name">Site name</label>
              <input class="form-input" type="text" id="site_name" name="site_name"
                value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
            </div>
            <div class="form-actions">
              <div></div>
              <button class="btn btn-primary" type="submit">Save changes</button>
            </div>
          </form>
        </div>
      </div>

    <?php elseif ($section === 'repos'): ?>

      <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
          <span>Configured repositories</span>
          <span style="font-size:11px;color:var(--text3)"><?= count($repos) ?> repo<?= count($repos) !== 1 ? 's' : '' ?></span>
        </div>
        <?php if (empty($repos)): ?>
          <div class="empty-state">
            <div class="empty-icon">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="3" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.2"/><path d="M1 6h14" stroke="currentColor" stroke-width="1.2"/><circle cx="4" cy="4.5" r="0.8" fill="currentColor"/><circle cx="6.5" cy="4.5" r="0.8" fill="currentColor"/></svg>
            </div>
            <div class="empty-title">No repositories added</div>
            <div class="empty-desc">Add a repository below to start browsing remote addons and themes.</div>
          </div>
        <?php else: ?>
          <div class="item-list">
            <?php foreach ($repos as $repo): ?>
            <div class="repo-row">
              <div class="repo-info">
                <div class="repo-name"><?= htmlspecialchars($repo['name']) ?></div>
                <div class="repo-url"><?= htmlspecialchars($repo['url']) ?></div>
                <div style="margin-top:5px;">
                  <span class="badge badge-info"><?= htmlspecialchars($repo['type'] ?? 'both') ?></span>
                </div>
              </div>
              <div style="display:flex;gap:6px;flex-shrink:0;">
                <form method="POST" action="/wc-admin/settings/repos/delete">
                  <?php Auth::csrfField(); ?>
                  <input type="hidden" name="id" value="<?= htmlspecialchars($repo['id']) ?>">
                  <button class="btn btn-sm btn-danger" type="submit"
                    data-confirm="Remove repository '<?= htmlspecialchars($repo['name']) ?>'?">
                    Remove
                  </button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="card">
        <div class="card-header">Add repository</div>
        <div class="card-body">
          <form method="POST" action="/wc-admin/settings/repos/add">
            <?php Auth::csrfField(); ?>
            <div class="form-group">
              <label class="form-label" for="repo-name">Name</label>
              <input class="form-input" type="text" id="repo-name" name="name" placeholder="My Addon Repo" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="repo-url">URL</label>
              <input class="form-input mono" type="url" id="repo-url" name="url"
                placeholder="https://example.com/wordcore-repo/" required>
              <div class="form-hint">
                WordCore fetches <code style="font-size:11px;background:var(--bg3);padding:1px 4px;border-radius:3px;font-family:monospace">index.json</code>
                from this URL to list packages. Any publicly accessible folder works.
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="repo-type">Type</label>
              <select class="form-select" id="repo-type" name="type">
                <option value="both">Addons &amp; themes</option>
                <option value="addons">Addons only</option>
                <option value="themes">Themes only</option>
              </select>
            </div>
            <div id="test-result" class="test-result"></div>
            <div class="form-actions">
              <button type="button" id="btn-test-repo" class="btn">Test connection</button>
              <button class="btn btn-primary" type="submit">Add repository</button>
            </div>
          </form>
        </div>
      </div>

      <div class="info-box" style="margin-top:14px;">
        <strong>How repositories work</strong><br>
        Host a folder on any web server or cPanel file manager. Place your addon/theme <code>.zip</code> files inside it
        alongside an <code>index.json</code> manifest. WordCore fetches that manifest when browsing,
        then downloads the selected <code>.zip</code> on install. No special server software needed.
        <br><br>
        Example <code>index.json</code>:<br>
        <pre style="margin-top:6px;font-size:11px;line-height:1.5;">{"addons":[{"slug":"my-addon","name":"My Addon","version":"1.0","description":"Does a thing.","download":"https://example.com/repo/my-addon.zip"}],"themes":[]}</pre>
      </div>

    <?php elseif ($section === 'permissions'): ?>

      <div class="card">
        <div class="card-header">Permissions</div>
        <div class="card-body">
          <p style="font-size:13px;color:var(--text2);">
            Role-based permissions can be configured via the Users screen. Advanced permission addons can extend this section.
          </p>
          <?php Hooks::fire('settings_permissions_ui'); ?>
        </div>
      </div>

    <?php endif; ?>

  </div>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>

<?php $pageTitle = 'Users'; require __DIR__ . '/layout_top.php';
$me = Auth::user()['username'] ?? '';
?>

<div class="page-header">
  <h1 class="page-title">Users</h1>
</div>

<?php wc_flash($flash, $error); ?>

<div class="card" style="margin-bottom:16px;">
  <div class="card-header">All users</div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Username</th>
        <th>Role</th>
        <th>Created</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): $uid = htmlspecialchars($user['username']); ?>
      <tr>
        <td style="font-weight:500;">
          <?= $uid ?>
          <?php if ($user['username'] === $me): ?>
            <span class="badge badge-info" style="margin-left:6px;">you</span>
          <?php endif; ?>
        </td>
        <td><span class="badge badge-<?= $user['role'] === 'admin' ? 'warn' : 'inactive' ?>"><?= htmlspecialchars($user['role']) ?></span></td>
        <td style="color:var(--text3);font-size:12px;"><?= $user['created'] ? date('Y-m-d', strtotime($user['created'])) : '—' ?></td>
        <td>
          <div style="display:flex;gap:6px;justify-content:flex-end;">
            <button class="btn btn-sm" onclick="togglePwForm('<?= $uid ?>', this)">Change password</button>
            <?php if ($user['username'] !== $me): ?>
              <form method="POST" action="/wc-admin/users/delete" style="margin:0;">
                <?php Auth::csrfField(); ?>
                <input type="hidden" name="username" value="<?= $uid ?>">
                <button class="btn btn-sm btn-danger" type="submit"
                  data-confirm="Delete user '<?= $uid ?>'?">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <tr id="pwrow-<?= $uid ?>" style="display:none;">
        <td colspan="4" style="padding:0 12px 14px;">
          <form method="POST" action="/wc-admin/users/password"
            style="display:flex;gap:8px;align-items:center;">
            <?php Auth::csrfField(); ?>
            <input type="hidden" name="username" value="<?= $uid ?>">
            <input class="form-input" type="password" name="password"
              placeholder="New password (min 6 chars)" style="flex:1;max-width:320px;"
              required minlength="6" autocomplete="new-password">
            <button class="btn btn-sm btn-primary" type="submit">Save</button>
            <button type="button" class="btn btn-sm" onclick="togglePwForm('<?= $uid ?>', null)">Cancel</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-header">Create user</div>
  <div class="card-body">
    <form method="POST" action="/wc-admin/users/create">
      <?php Auth::csrfField(); ?>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Username</label>
          <input class="form-input" type="text" name="username" required autocomplete="off">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input class="form-input" type="password" name="password" required minlength="6" autocomplete="new-password">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select class="form-select" name="role">
          <option value="admin">Admin</option>
          <option value="editor">Editor</option>
        </select>
      </div>
      <div class="form-actions">
        <div></div>
        <button class="btn btn-primary" type="submit">Create user</button>
      </div>
    </form>
  </div>
</div>

<script>
function togglePwForm(uid, btn) {
  var row = document.getElementById('pwrow-' + uid);
  var open = row.style.display === 'none';
  row.style.display = open ? 'table-row' : 'none';
  if (btn) btn.textContent = open ? 'Cancel' : 'Change password';
  else {
    var btns = document.querySelectorAll('button[onclick*="togglePwForm(\'' + uid + '\'"]');
    if (btns.length) btns[0].textContent = 'Change password';
  }
}
</script>

<?php require __DIR__ . '/layout_bottom.php'; ?>

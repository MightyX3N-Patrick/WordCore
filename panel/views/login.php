<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in — WordCore</title>
<link rel="stylesheet" href="/public/assets/css/admin.css">
</head>
<body data-base="<?= htmlspecialchars(WordCore::base()) ?>">
<div class="login-wrap">
  <div class="login-box">
    <div class="login-brand">
      <div class="login-brand-icon">W</div>
      <span class="login-brand-name">WordCore</span>
    </div>

    <?php if ($error): ?>
      <div class="flash flash-err" style="margin-bottom:20px;">Invalid username or password.</div>
    <?php endif; ?>

    <form method="POST" action="/wc-admin/login">
      <?php Auth::csrfField(); ?>
      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input class="form-input" type="text" id="username" name="username" autofocus autocomplete="username" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-input" type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;" type="submit">Log in</button>
    </form>
  </div>
</div>
</body>
</html>

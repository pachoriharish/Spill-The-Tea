<?php
// Included at the top of every visible page. Expects session_start() + db.php already loaded.
$user      = $_SESSION['username'] ?? null;
$authError = $_SESSION['auth_error'] ?? null;
$authTab   = $_SESSION['auth_tab'] ?? 'login';
$flash     = $_SESSION['flash'] ?? null;
unset($_SESSION['auth_error'], $_SESSION['auth_tab'], $_SESSION['flash']);
$pageTitle = $pageTitle ?? 'Spill The Tea';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> · Spill The Tea</title>
  <script>
    // Apply saved theme (or OS preference) before paint to avoid a flash
    (function () {
      var t = null;
      try { t = localStorage.getItem('theme'); } catch (e) {}
      if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', t);
    })();
  </script>
  <link rel="stylesheet" href="style.css">
  <script src="script.js" defer></script>
</head>
<body <?= $authError ? 'data-open-auth="' . e($authTab) . '"' : '' ?>>
<header class="site-header">
  <a class="logo" href="index.php">Spill The <span>Tea</span> 🍵</a>
  <nav class="nav">
    <button type="button" id="theme-toggle" class="btn btn-ghost" aria-label="Toggle light/dark theme">🌓</button>
    <?php if ($user): ?>
      <a href="index.php">Home</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <button type="button" class="btn" data-open-auth-btn>Login / Create Account</button>
    <?php endif; ?>
  </nav>
</header>

<?php if (!$user): ?>
<dialog id="auth-dialog" class="modal">
  <button type="button" class="modal-close" data-close aria-label="Close">✕</button>
  <div class="tabs">
    <button type="button" class="tab" data-tab="login">Login</button>
    <button type="button" class="tab" data-tab="signup">Create Account</button>
  </div>
  <p class="hint" id="auth-hint" hidden>Log in or create an account to like and dislike memes.</p>
  <?php if ($authError): ?><p class="error"><?= e($authError) ?></p><?php endif; ?>

  <form method="POST" action="login.php" class="auth-panel" data-panel="login">
    <label>Username <input type="text" name="username" required autocomplete="username"></label>
    <label>Password
      <span class="pass-wrap">
        <input type="password" name="password" required autocomplete="current-password">
        <button type="button" class="toggle-pass" aria-label="Show or hide password">👁</button>
      </span>
    </label>
    <button type="submit" class="btn">Login</button>
  </form>

  <form method="POST" action="signup.php" class="auth-panel" data-panel="signup" hidden>
    <label>Display name <input type="text" name="display_name" maxlength="100" required></label>
    <label>Email (optional) <input type="email" name="email" maxlength="150"></label>
    <label>Username <input type="text" name="username" pattern="[A-Za-z0-9_]{3,30}" title="3–30 letters, numbers or underscores" required autocomplete="username"></label>
    <label>Password
      <span class="pass-wrap">
        <input type="password" name="password" minlength="6" required autocomplete="new-password">
        <button type="button" class="toggle-pass" aria-label="Show or hide password">👁</button>
      </span>
    </label>
    <button type="submit" class="btn">Create account</button>
  </form>
</dialog>
<?php endif; ?>

<main class="container">
<?php if ($flash): ?><p class="flash <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></p><?php endif; ?>

<?php
require_once 'config.php';

if (isLoggedIn()) redirect('admin/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (($user['status'] ?? 'active') === 'pending') {
                $error = 'Akun Anda masih menunggu persetujuan admin.';
            } elseif (($user['status'] ?? 'active') === 'suspended') {
                $error = 'Akun Anda telah ditangguhkan. Hubungi admin.';
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['avatar']    = $user['avatar_path'] ?? null;
                redirect('admin/dashboard.php');
            }
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-page">
<div class="auth-container">
  <div class="auth-brand">
    <a href="index.php" class="logo"><?= APP_NAME ?></a>
    <p>Platform Blog Dinamis</p>
  </div>

  <div class="auth-card">
    <h2>Masuk ke Dashboard</h2>
    <p class="auth-sub">Kelola konten blog Anda</p>

    <?php if ($error): ?>
      <div class="alert error"><?= escape($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username / Email</label>
        <input type="text" name="username" value="<?= escape($_POST['username'] ?? '') ?>" 
               required autofocus placeholder="Masukkan username">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Masukkan password">
      </div>
      <button type="submit" class="btn-primary full">Masuk →</button>
    </form>

    <div class="auth-hint">
      <p><strong>Demo:</strong> admin / password</p>
    </div>
    <div style="text-align:center;margin-top:1.25rem;font-size:0.9rem;color:var(--ink-muted)">
      Belum punya akun?
      <a href="register.php" style="color:var(--accent);font-weight:600">Daftar sebagai Author</a>
    </div>
  </div>

  <div class="auth-back">
    <a href="index.php">← Kembali ke Blog</a>
  </div>
</div>
</body>
</html>

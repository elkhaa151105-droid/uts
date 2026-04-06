<?php
require_once 'config.php';

if (isLoggedIn()) redirect('admin/dashboard.php');

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $bio       = trim($_POST['bio'] ?? '');

    // Validasi
    if (!$full_name)  $errors[] = 'Nama lengkap wajib diisi.';
    if (!$username)   $errors[] = 'Username wajib diisi.';
    elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username))
        $errors[] = 'Username hanya boleh huruf, angka, dan underscore (3-30 karakter).';
    if (!$email)      $errors[] = 'Email wajib diisi.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Format email tidak valid.';
    if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
    if ($password !== $confirm) $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        // Cek duplikasi
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $errors[] = 'Username atau email sudah digunakan.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, full_name, role, status, bio)
                VALUES (?, ?, ?, ?, 'author', 'pending', ?)
            ");
            $stmt->execute([$username, $email, $hashed, $full_name, $bio]);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun Author — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-page">
<div class="auth-container" style="max-width:520px">
  <div class="auth-brand">
    <a href="index.php" class="logo"><?= APP_NAME ?></a>
    <p>Daftar sebagai Author</p>
  </div>

  <?php if ($success): ?>
    <div class="auth-card" style="text-align:center">
      <div style="font-size:3.5rem;margin-bottom:1rem">⏳</div>
      <h2 style="font-family:var(--font-serif);margin-bottom:.75rem">Pendaftaran Terkirim!</h2>
      <p style="color:var(--ink-soft);margin-bottom:1.5rem">
        Akun Anda sedang <strong>menunggu persetujuan admin</strong>.<br>
        Anda akan bisa login setelah admin menyetujui akun Anda.
      </p>
      <a href="login.php" class="btn-primary" style="display:inline-block">Kembali ke Login</a>
    </div>
  <?php else: ?>
    <div class="auth-card">
      <h2>Buat Akun Baru</h2>
      <p class="auth-sub">Isi form di bawah. Akun akan aktif setelah disetujui admin.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <ul><?php foreach ($errors as $e): ?><li><?= escape($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div class="form-group">
          <label>Nama Lengkap *</label>
          <input type="text" name="full_name" value="<?= escape($_POST['full_name'] ?? '') ?>"
                 required placeholder="Nama lengkap Anda">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Username *</label>
            <input type="text" name="username" value="<?= escape($_POST['username'] ?? '') ?>"
                   required placeholder="contoh: johndoe">
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?= escape($_POST['email'] ?? '') ?>"
                   required placeholder="email@anda.com">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Password *</label>
            <input type="password" name="password" required placeholder="Min. 8 karakter">
          </div>
          <div class="form-group">
            <label>Konfirmasi Password *</label>
            <input type="password" name="confirm_password" required placeholder="Ulangi password">
          </div>
        </div>
        <div class="form-group">
          <label>Bio Singkat</label>
          <textarea name="bio" rows="3" placeholder="Ceritakan sedikit tentang diri Anda..."><?= escape($_POST['bio'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn-primary full">Daftar Sekarang</button>
      </form>

      <div class="auth-hint" style="margin-top:1rem">
        <p>Sudah punya akun? <a href="login.php" style="color:var(--accent);font-weight:600">Masuk di sini</a></p>
      </div>
    </div>
  <?php endif; ?>

  <div class="auth-back"><a href="index.php">← Kembali ke Blog</a></div>
</div>
</body>
</html>

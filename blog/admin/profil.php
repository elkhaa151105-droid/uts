<?php
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');

// Ambil data user terkini dari DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$errors   = [];
$tab      = $_GET['tab'] ?? 'profil';

// ---- SIMPAN PROFIL ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $bio       = trim($_POST['bio'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    if (!$full_name) $errors[] = 'Nama lengkap wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';

    // Cek email unik (selain milik sendiri)
    if (empty($errors)) {
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $chk->execute([$email, $user['id']]);
        if ($chk->fetch()) $errors[] = 'Email sudah digunakan akun lain.';
    }

    // Upload avatar
    $avatar_path = $user['avatar_path'];
    if (!empty($_FILES['avatar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format avatar tidak valid (jpg, png, webp, gif).';
        } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran avatar maksimal 2MB.';
        } else {
            $avatarDir = UPLOAD_PATH . 'avatars/';
            if (!is_dir($avatarDir)) mkdir($avatarDir, 0755, true);
            // Hapus avatar lama
            if ($avatar_path && file_exists(UPLOAD_PATH . $avatar_path)) {
                @unlink(UPLOAD_PATH . $avatar_path);
            }
            $filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarDir . $filename)) {
                $avatar_path = 'avatars/' . $filename;
            } else {
                $errors[] = 'Gagal upload avatar.';
            }
        }
    }

    if (empty($errors)) {
        $pdo->prepare("UPDATE users SET full_name=?, email=?, bio=?, avatar_path=? WHERE id=?")
            ->execute([$full_name, $email, $bio, $avatar_path, $user['id']]);
        $_SESSION['full_name'] = $full_name;
        $_SESSION['avatar']    = $avatar_path;
        flashMessage('success', 'Profil berhasil diperbarui.');
        redirect('profil.php?tab=profil');
    }
    $tab = 'profil';
}

// ---- GANTI PASSWORD ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_password'])) {
    $old_pass  = $_POST['old_password'] ?? '';
    $new_pass  = $_POST['new_password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if (!password_verify($old_pass, $user['password']))
        $errors[] = 'Password lama tidak sesuai.';
    if (strlen($new_pass) < 8)
        $errors[] = 'Password baru minimal 8 karakter.';
    if ($new_pass !== $confirm)
        $errors[] = 'Konfirmasi password baru tidak cocok.';

    if (empty($errors)) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hashed, $user['id']]);
        flashMessage('success', 'Password berhasil diubah.');
        redirect('profil.php?tab=password');
    }
    $tab = 'password';
}

// Reload user setelah update
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Statistik artikel user
$artCount   = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id=?"); $artCount->execute([$user['id']]);
$artTotal   = $artCount->fetchColumn();
$artPublished = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id=? AND status='published'"); $artPublished->execute([$user['id']]); $artPub = $artPublished->fetchColumn();
$totalViews = $pdo->prepare("SELECT COALESCE(SUM(views),0) FROM articles WHERE author_id=?"); $totalViews->execute([$user['id']]); $views = $totalViews->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profil — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
<style>
.profile-hero{background:linear-gradient(135deg,#1a1a2e,#0f3460);border-radius:var(--radius-lg);padding:2rem;display:flex;align-items:center;gap:1.75rem;margin-bottom:2rem;color:white}
.avatar-wrap{position:relative;flex-shrink:0}
.avatar-lg{width:90px;height:90px;border-radius:50%;border:3px solid rgba(255,255,255,.25);object-fit:cover;display:flex;align-items:center;justify-content:center;font-size:2.25rem;font-weight:700;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:white;overflow:hidden}
.avatar-lg img{width:100%;height:100%;object-fit:cover}
.profile-hero h2{font-family:var(--font-serif);font-size:1.5rem;margin-bottom:.2rem}
.profile-hero p{color:rgba(255,255,255,.65);font-size:.875rem}
.profile-stats{display:flex;gap:1.5rem;margin-top:.75rem}
.pstat{text-align:center}
.pstat strong{display:block;font-size:1.3rem;font-weight:700}
.pstat span{font-size:.75rem;color:rgba(255,255,255,.5)}
.profile-tabs{display:flex;gap:.25rem;border-bottom:2px solid var(--border);margin-bottom:1.5rem}
.profile-tabs a{padding:.6rem 1.1rem;font-size:.9rem;font-weight:600;color:var(--ink-muted);border-bottom:2px solid transparent;margin-bottom:-2px;transition:color .2s,border-color .2s}
.profile-tabs a:hover{color:var(--ink)}
.profile-tabs a.active{color:var(--accent);border-bottom-color:var(--accent)}
.avatar-preview-wrap{display:flex;align-items:center;gap:1.25rem;margin-bottom:1rem}
.avatar-preview{width:72px;height:72px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,var(--accent),var(--accent-dark));display:flex;align-items:center;justify-content:center;font-size:1.75rem;font-weight:700;color:white;flex-shrink:0;border:2px solid var(--border)}
.avatar-preview img{width:100%;height:100%;object-fit:cover}
</style>
</head>
<body class="admin-body">
<?php include 'partials/admin-nav.php'; ?>
<div class="admin-layout">
  <?php include 'partials/admin-sidebar.php'; ?>
  <main class="admin-main">

    <!-- Profile Hero -->
    <div class="profile-hero">
      <div class="avatar-wrap">
        <div class="avatar-lg">
          <?php if (!empty($user['avatar_path']) && file_exists(UPLOAD_PATH . $user['avatar_path'])): ?>
            <img src="<?= escape(UPLOAD_URL . $user['avatar_path']) ?>" alt="Avatar">
          <?php else: ?>
            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
          <?php endif; ?>
        </div>
      </div>
      <div>
        <h2><?= escape($user['full_name']) ?></h2>
        <p>@<?= escape($user['username']) ?> · <span class="role-badge <?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span></p>
        <div class="profile-stats">
          <div class="pstat"><strong><?= $artTotal ?></strong><span>Artikel</span></div>
          <div class="pstat"><strong><?= $artPub ?></strong><span>Publik</span></div>
          <div class="pstat"><strong><?= number_format($views) ?></strong><span>Tampilan</span></div>
        </div>
      </div>
    </div>

    <?php $flash = getFlash(); if ($flash): ?>
      <div class="alert <?= $flash['type'] ?>"><?= escape($flash['message']) ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="profile-tabs">
      <a href="?tab=profil" class="<?= $tab==='profil'?'active':'' ?>">👤 Edit Profil</a>
      <a href="?tab=password" class="<?= $tab==='password'?'active':'' ?>">🔒 Ganti Password</a>
    </div>

    <?php if ($tab === 'profil'): ?>
    <!-- FORM EDIT PROFIL -->
    <div class="form-card">
      <h3 style="font-family:var(--font-serif);font-size:1.2rem;margin-bottom:1.5rem">Informasi Profil</h3>

      <?php if (!empty($errors)): ?>
        <div class="alert error"><ul><?php foreach ($errors as $e): ?><li><?= escape($e) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <!-- Avatar Upload -->
        <div class="form-group">
          <label>Foto Profil (Avatar)</label>
          <div class="avatar-preview-wrap">
            <div class="avatar-preview" id="avatarPreview">
              <?php if (!empty($user['avatar_path']) && file_exists(UPLOAD_PATH . $user['avatar_path'])): ?>
                <img src="<?= escape(UPLOAD_URL . $user['avatar_path']) ?>" alt="Avatar" id="avatarImg">
              <?php else: ?>
                <span id="avatarInitial"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
              <?php endif; ?>
            </div>
            <div>
              <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="previewAvatar(this)" style="display:none">
              <button type="button" onclick="document.getElementById('avatarInput').click()" class="btn-outline sm">📷 Pilih Foto</button>
              <p class="hint">JPG, PNG, WEBP, GIF · Maks. 2MB</p>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Nama Lengkap *</label>
            <input type="text" name="full_name" value="<?= escape($user['full_name']) ?>" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" value="<?= escape($user['username']) ?>" disabled style="opacity:.6;cursor:not-allowed">
            <p class="hint">Username tidak bisa diubah.</p>
          </div>
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" value="<?= escape($user['email']) ?>" required>
        </div>
        <div class="form-group">
          <label>Bio</label>
          <textarea name="bio" rows="4" placeholder="Ceritakan tentang diri Anda..."><?= escape($user['bio'] ?? '') ?></textarea>
        </div>
        <button type="submit" name="save_profile" class="btn-primary">💾 Simpan Profil</button>
      </form>
    </div>

    <?php else: ?>
    <!-- FORM GANTI PASSWORD -->
    <div class="form-card">
      <h3 style="font-family:var(--font-serif);font-size:1.2rem;margin-bottom:1.5rem">Ganti Password</h3>

      <?php if (!empty($errors)): ?>
        <div class="alert error"><ul><?php foreach ($errors as $e): ?><li><?= escape($e) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <form method="POST" style="max-width:440px">
        <div class="form-group">
          <label>Password Lama *</label>
          <input type="password" name="old_password" required placeholder="Masukkan password saat ini">
        </div>
        <div class="form-group">
          <label>Password Baru *</label>
          <input type="password" name="new_password" required placeholder="Min. 8 karakter" id="newPass" oninput="checkStrength(this.value)">
          <div id="strengthBar" style="height:4px;border-radius:2px;margin-top:6px;background:var(--border);transition:.3s">
            <div id="strengthFill" style="height:100%;border-radius:2px;width:0%;transition:.3s"></div>
          </div>
          <p class="hint" id="strengthLabel">Minimal 8 karakter</p>
        </div>
        <div class="form-group">
          <label>Konfirmasi Password Baru *</label>
          <input type="password" name="confirm_password" required placeholder="Ulangi password baru">
        </div>
        <button type="submit" name="save_password" class="btn-primary">🔒 Ubah Password</button>
      </form>
    </div>
    <?php endif; ?>

  </main>
</div>

<script>
function previewAvatar(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const wrap = document.getElementById('avatarPreview');
    wrap.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%;height:100%;object-fit:cover">`;
  };
  reader.readAsDataURL(input.files[0]);
}
function checkStrength(val) {
  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const colors = ['','#ef4444','#f97316','#eab308','#22c55e'];
  const labels = ['','Lemah','Cukup','Kuat','Sangat Kuat'];
  document.getElementById('strengthFill').style.width = (score*25)+'%';
  document.getElementById('strengthFill').style.background = colors[score]||'';
  document.getElementById('strengthLabel').textContent = labels[score]||'';
}
</script>
</body>
</html>

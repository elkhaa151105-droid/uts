<?php
$current = basename($_SERVER['PHP_SELF']);
function isActive($pages) {
    $current = basename($_SERVER['PHP_SELF']);
    return in_array($current, (array)$pages) ? 'active' : '';
}
?>
<aside class="admin-sidebar">
  <nav class="admin-sidenav">
    <a href="dashboard.php" class="<?= isActive('dashboard.php') ?>">
      <span>📊</span> Dashboard
    </a>

    <div class="nav-group">
      <span class="nav-label">Konten</span>
      <a href="artikel-baru.php" class="<?= isActive('artikel-baru.php') ?>">
        <span>✍</span> Artikel Baru
      </a>
      <a href="artikel-list.php" class="<?= isActive(['artikel-list.php','artikel-edit.php']) ?>">
        <span>📝</span> Semua Artikel
      </a>
      <a href="komentar.php" class="<?= isActive('komentar.php') ?>">
        <span>💬</span> Komentar
        <?php
        $pending = $pdo->query("SELECT COUNT(*) FROM comments WHERE status='pending'")->fetchColumn();
        if ($pending > 0): ?>
          <span class="badge-count"><?= $pending ?></span>
        <?php endif; ?>
      </a>
    </div>

    <?php if (isAdmin()): ?>
    <div class="nav-group">
      <span class="nav-label">Taksonomi</span>
      <a href="kategori.php" class="<?= isActive('kategori.php') ?>">
        <span>🗂</span> Kategori & Tag
      </a>
    </div>

    <div class="nav-group">
      <span class="nav-label">Manajemen User</span>
      <a href="users.php" class="<?= isActive('users.php') ?>">
        <span>👥</span> Kelola Users
        <?php
        try {
            $pendingUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetchColumn();
            if ($pendingUsers > 0): ?>
              <span class="badge-count"><?= $pendingUsers ?></span>
            <?php endif;
        } catch (PDOException $e) { /* kolom belum ada */ } ?>
      </a>
    </div>
    <?php endif; ?>

    <div class="nav-group">
      <span class="nav-label">Akun Saya</span>
      <a href="profil.php" class="<?= isActive('profil.php') ?>">
        <span>👤</span> Edit Profil
      </a>
    </div>

    <div class="nav-group">
      <span class="nav-label">Lainnya</span>
      <a href="../index.php" target="_blank">
        <span>🌐</span> Lihat Blog
      </a>
    </div>
  </nav>
</aside>

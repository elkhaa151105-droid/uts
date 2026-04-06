<?php
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');

// Cek apakah kolom status sudah ada di tabel users
$hasStatusCol = false;
try {
    $pdo->query("SELECT status FROM users LIMIT 1");
    $hasStatusCol = true;
} catch (PDOException $e) {
    // Kolom belum ada, jalankan migration_update.sql
}

// Statistik
$pendingUsers = 0;
if ($hasStatusCol) {
    $pendingUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetchColumn();
}

$stats = [
    'articles' => $pdo->query("SELECT COUNT(*) FROM articles WHERE author_id = " . (isAdmin() ? "author_id" : $_SESSION['user_id']))->fetchColumn(),
    'published' => $pdo->query("SELECT COUNT(*) FROM articles WHERE status='published'" . (!isAdmin() ? " AND author_id=".$_SESSION['user_id'] : ""))->fetchColumn(),
    'comments'  => $pdo->query("SELECT COUNT(*) FROM comments WHERE status='pending'")->fetchColumn(),
    'views'     => $pdo->query("SELECT COALESCE(SUM(views),0) FROM articles WHERE status='published'" . (!isAdmin() ? " AND author_id=".$_SESSION['user_id'] : ""))->fetchColumn(),
];

// Artikel terbaru
$recentArticles = $pdo->query("
    SELECT a.*, c.name as category_name, u.full_name as author_name 
    FROM articles a JOIN categories c ON a.category_id=c.id JOIN users u ON a.author_id=u.id
    " . (!isAdmin() ? "WHERE a.author_id=".$_SESSION['user_id'] : "") . "
    ORDER BY a.created_at DESC LIMIT 8
")->fetchAll();

// Komentar pending
$pendingComments = $pdo->query("
    SELECT cm.*, a.title as article_title, a.slug as article_slug
    FROM comments cm JOIN articles a ON cm.article_id=a.id
    WHERE cm.status='pending' ORDER BY cm.created_at DESC LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
<?php include 'partials/admin-nav.php'; ?>

<div class="admin-layout">
  <?php include 'partials/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-header">
      <div>
        <h1>Dashboard</h1>
        <p>Selamat datang, <strong><?= escape($_SESSION['full_name']) ?></strong> 👋</p>
      </div>
      <a href="artikel-baru.php" class="btn-primary">+ Artikel Baru</a>
    </div>

    <?php $flash = getFlash(); if ($flash): ?>
      <div class="alert <?= $flash['type'] ?>"><?= escape($flash['message']) ?></div>
    <?php endif; ?>

    <?php if (isAdmin() && $pendingUsers > 0): ?>
    <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #f59e0b;border-radius:var(--radius-lg);padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.5rem">
      <p style="margin:0;font-size:.9rem;color:#78350f;font-weight:600">
        ⏳ <strong><?= $pendingUsers ?> pendaftar baru</strong> menunggu persetujuan Anda.
      </p>
      <a href="users.php?status=pending" class="btn-primary" style="font-size:.82rem;padding:.4rem .9rem;white-space:nowrap">Tinjau →</a>
    </div>
    <?php endif; ?>

    <!-- Statistik -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-info">
          <h3><?= number_format($stats['articles']) ?></h3>
          <p>Total Artikel</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
          <h3><?= number_format($stats['published']) ?></h3>
          <p>Dipublikasikan</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-info">
          <h3><?= number_format($stats['comments']) ?></h3>
          <p>Komentar Pending</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👁</div>
        <div class="stat-info">
          <h3><?= number_format($stats['views']) ?></h3>
          <p>Total Tampilan</p>
        </div>
      </div>
    </div>

    <div class="dash-grid">
      <!-- Artikel Terbaru -->
      <div class="dash-panel">
        <div class="panel-head">
          <h3>Artikel Terbaru</h3>
          <a href="artikel-list.php">Lihat Semua →</a>
        </div>
        <table class="admin-table">
          <thead>
            <tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Tampilan</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($recentArticles as $a): ?>
            <tr>
              <td><a href="../artikel.php?slug=<?= $a['slug'] ?>" target="_blank"><?= escape(substr($a['title'],0,45)) ?>...</a></td>
              <td><?= escape($a['category_name']) ?></td>
              <td><span class="badge <?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
              <td><?= number_format($a['views']) ?></td>
              <td class="actions">
                <a href="artikel-edit.php?id=<?= $a['id'] ?>" class="btn-sm">Edit</a>
                <a href="artikel-hapus.php?id=<?= $a['id'] ?>" class="btn-sm danger" onclick="return confirm('Hapus artikel ini?')">Hapus</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Komentar Pending -->
      <div class="dash-panel">
        <div class="panel-head">
          <h3>Komentar Menunggu</h3>
          <a href="komentar.php">Kelola →</a>
        </div>
        <?php if (empty($pendingComments)): ?>
          <div class="empty-state sm"><span>✅</span><p>Tidak ada komentar pending.</p></div>
        <?php else: ?>
          <?php foreach ($pendingComments as $c): ?>
            <div class="comment-item">
              <div class="ci-head">
                <strong><?= escape($c['name']) ?></strong>
                <span><?= timeAgo($c['created_at']) ?></span>
              </div>
              <p class="ci-text"><?= escape(substr($c['content'], 0, 100)) ?>...</p>
              <p class="ci-article">Pada: <em><?= escape($c['article_title']) ?></em></p>
              <div class="ci-actions">
                <a href="komentar-aksi.php?id=<?= $c['id'] ?>&action=approve" class="btn-sm success">Setujui</a>
                <a href="komentar-aksi.php?id=<?= $c['id'] ?>&action=spam" class="btn-sm danger">Spam</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>

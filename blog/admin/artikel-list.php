<?php
// artikel-list.php
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');

$where = isAdmin() ? "" : "WHERE a.author_id = " . $_SESSION['user_id'];
$articles = $pdo->query("
    SELECT a.*, c.name as category_name, u.full_name as author_name
    FROM articles a JOIN categories c ON a.category_id=c.id JOIN users u ON a.author_id=u.id
    $where ORDER BY a.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Artikel — <?= APP_NAME ?></title>
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
      <h1>Kelola Artikel</h1>
      <a href="artikel-baru.php" class="btn-primary">+ Artikel Baru</a>
    </div>
    <?php $flash = getFlash(); if ($flash): ?>
      <div class="alert <?= $flash['type'] ?>"><?= escape($flash['message']) ?></div>
    <?php endif; ?>
    <div class="form-card">
      <table class="admin-table">
        <thead>
          <tr><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Status</th><th>Tampilan</th><th>Tanggal</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($articles as $a): ?>
          <tr>
            <td><a href="../artikel.php?slug=<?= $a['slug'] ?>" target="_blank"><?= escape(substr($a['title'],0,50)) ?>...</a></td>
            <td><?= escape($a['author_name']) ?></td>
            <td><?= escape($a['category_name']) ?></td>
            <td><span class="badge <?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            <td><?= number_format($a['views']) ?></td>
            <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
            <td class="actions">
              <a href="artikel-baru.php?id=<?= $a['id'] ?>" class="btn-sm">Edit</a>
              <a href="artikel-hapus.php?id=<?= $a['id'] ?>" class="btn-sm danger" onclick="return confirm('Yakin hapus artikel ini?')">Hapus</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($articles)): ?>
            <tr><td colspan="7" class="empty-state sm">Belum ada artikel.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>

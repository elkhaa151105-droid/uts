<?php
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');

$filter = $_GET['status'] ?? 'pending';
$comments = $pdo->prepare("
    SELECT cm.*, a.title as article_title, a.slug as article_slug
    FROM comments cm JOIN articles a ON cm.article_id=a.id
    WHERE cm.status = ?
    ORDER BY cm.created_at DESC
");
$comments->execute([$filter]);
$comments = $comments->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Komentar — <?= APP_NAME ?></title>
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
      <h1>Kelola Komentar</h1>
    </div>
    <?php $flash = getFlash(); if ($flash): ?>
      <div class="alert <?= $flash['type'] ?>"><?= escape($flash['message']) ?></div>
    <?php endif; ?>
    <div class="tab-filter">
      <a href="?status=pending" class="<?= $filter==='pending'?'active':'' ?>">Pending</a>
      <a href="?status=approved" class="<?= $filter==='approved'?'active':'' ?>">Disetujui</a>
      <a href="?status=spam" class="<?= $filter==='spam'?'active':'' ?>">Spam</a>
    </div>
    <div class="form-card">
      <table class="admin-table">
        <thead>
          <tr><th>Nama</th><th>Komentar</th><th>Artikel</th><th>Waktu</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($comments as $c): ?>
          <tr>
            <td>
              <strong><?= escape($c['name']) ?></strong><br>
              <small><?= escape($c['email']) ?></small>
            </td>
            <td><?= escape(substr($c['content'],0,100)) ?>...</td>
            <td><a href="../artikel.php?slug=<?= $c['article_slug'] ?>" target="_blank"><?= escape(substr($c['article_title'],0,40)) ?>...</a></td>
            <td><?= timeAgo($c['created_at']) ?></td>
            <td class="actions">
              <?php if ($c['status'] !== 'approved'): ?>
                <a href="komentar-aksi.php?id=<?= $c['id'] ?>&action=approve" class="btn-sm success">Setujui</a>
              <?php endif; ?>
              <?php if ($c['status'] !== 'spam'): ?>
                <a href="komentar-aksi.php?id=<?= $c['id'] ?>&action=spam" class="btn-sm">Spam</a>
              <?php endif; ?>
              <a href="komentar-aksi.php?id=<?= $c['id'] ?>&action=delete" class="btn-sm danger" onclick="return confirm('Hapus komentar ini?')">Hapus</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($comments)): ?>
            <tr><td colspan="5"><div class="empty-state sm"><span>✅</span><p>Tidak ada komentar.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>

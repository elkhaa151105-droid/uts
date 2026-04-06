<?php
require_once '../config.php';
if (!isLoggedIn() || !isAdmin()) {
    flashMessage('error', 'Akses hanya untuk admin.');
    redirect('dashboard.php');
}

// Handle aksi
if (isset($_GET['action']) && isset($_GET['id'])) {
    $uid    = (int)$_GET['id'];
    $action = $_GET['action'];

    // Jangan izinkan admin hapus/suspend dirinya sendiri
    if ($uid === (int)$_SESSION['user_id'] && in_array($action, ['suspend','delete'])) {
        flashMessage('error', 'Anda tidak bisa melakukan aksi ini pada akun Anda sendiri.');
        redirect('users.php');
    }

    switch ($action) {
        case 'approve':
            $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$uid]);
            flashMessage('success', 'Akun berhasil disetujui. Author sekarang bisa login.');
            break;
        case 'suspend':
            $pdo->prepare("UPDATE users SET status='suspended' WHERE id=?")->execute([$uid]);
            flashMessage('success', 'Akun berhasil ditangguhkan.');
            break;
        case 'activate':
            $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$uid]);
            flashMessage('success', 'Akun berhasil diaktifkan kembali.');
            break;
        case 'make_admin':
            $pdo->prepare("UPDATE users SET role='admin' WHERE id=?")->execute([$uid]);
            flashMessage('success', 'User berhasil dijadikan admin.');
            break;
        case 'make_author':
            $pdo->prepare("UPDATE users SET role='author' WHERE id=?")->execute([$uid]);
            flashMessage('success', 'User berhasil dijadikan author.');
            break;
        case 'delete':
            // Cek apakah punya artikel
            $artCheck = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id=?");
            $artCheck->execute([$uid]);
            if ($artCheck->fetchColumn() > 0) {
                flashMessage('error', 'Tidak bisa hapus user yang masih memiliki artikel.');
            } else {
                // Hapus avatar jika ada
                $udata = $pdo->prepare("SELECT avatar_path FROM users WHERE id=?");
                $udata->execute([$uid]);
                $urow = $udata->fetch();
                if ($urow && $urow['avatar_path'] && file_exists(UPLOAD_PATH . $urow['avatar_path'])) {
                    @unlink(UPLOAD_PATH . $urow['avatar_path']);
                }
                $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$uid]);
                flashMessage('success', 'User berhasil dihapus.');
            }
            break;
    }
    redirect('users.php');
}

$filter = $_GET['status'] ?? 'all';
$where  = $filter !== 'all' ? "WHERE status = '$filter'" : '';
$users  = $pdo->query("
    SELECT u.*, 
           (SELECT COUNT(*) FROM articles WHERE author_id=u.id) as article_count,
           (SELECT COALESCE(SUM(views),0) FROM articles WHERE author_id=u.id) as total_views
    FROM users u $where
    ORDER BY FIELD(u.status,'pending','active','suspended'), u.created_at DESC
")->fetchAll();

$counts = $pdo->query("
    SELECT status, COUNT(*) as n FROM users GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);
$total = array_sum($counts);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Users — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
<style>
.user-status-pending  { background:#fef9c3;color:#854d0e;padding:.2rem .6rem;border-radius:20px;font-size:.75rem;font-weight:700; }
.user-status-active   { background:#dcfce7;color:#15803d;padding:.2rem .6rem;border-radius:20px;font-size:.75rem;font-weight:700; }
.user-status-suspended{ background:#fee2e2;color:#b91c1c;padding:.2rem .6rem;border-radius:20px;font-size:.75rem;font-weight:700; }
.user-avatar-sm {
  width:36px;height:36px;border-radius:50%;
  background:linear-gradient(135deg,var(--accent),var(--accent-dark));
  color:white;display:inline-flex;align-items:center;justify-content:center;
  font-weight:700;font-size:.9rem;flex-shrink:0;overflow:hidden;
}
.user-avatar-sm img { width:100%;height:100%;object-fit:cover; }
.user-cell { display:flex;align-items:center;gap:.65rem; }
.user-cell strong { display:block;font-size:.9rem; }
.user-cell small { color:var(--ink-muted);font-size:.78rem; }
.pending-banner {
  background:linear-gradient(135deg,#fef3c7,#fde68a);
  border:1px solid #f59e0b;border-radius:var(--radius-lg);
  padding:1rem 1.25rem;display:flex;align-items:center;
  justify-content:space-between;gap:1rem;margin-bottom:1.5rem;
}
.pending-banner p { font-size:.9rem;color:#78350f;font-weight:600;margin:0; }
</style>
</head>
<body class="admin-body">
<?php include 'partials/admin-nav.php'; ?>
<div class="admin-layout">
  <?php include 'partials/admin-sidebar.php'; ?>
  <main class="admin-main">

    <div class="admin-header">
      <div>
        <h1>Kelola Users</h1>
        <p>Total <?= $total ?> user terdaftar</p>
      </div>
    </div>

    <?php $flash = getFlash(); if ($flash): ?>
      <div class="alert <?= $flash['type'] ?>"><?= escape($flash['message']) ?></div>
    <?php endif; ?>

    <!-- Banner pending -->
    <?php $pendingCount = $counts['pending'] ?? 0; if ($pendingCount > 0): ?>
      <div class="pending-banner">
        <p>⏳ Ada <strong><?= $pendingCount ?> pendaftar baru</strong> yang menunggu persetujuan Anda.</p>
        <a href="?status=pending" class="btn-primary" style="font-size:.85rem;padding:.45rem 1rem;white-space:nowrap">Tinjau Sekarang</a>
      </div>
    <?php endif; ?>

    <!-- Tab filter -->
    <div class="tab-filter">
      <a href="?status=all"      class="<?= $filter==='all'?'active':'' ?>">Semua (<?= $total ?>)</a>
      <a href="?status=active"   class="<?= $filter==='active'?'active':'' ?>">Aktif (<?= $counts['active']??0 ?>)</a>
      <a href="?status=pending"  class="<?= $filter==='pending'?'active':'' ?>">Pending (<?= $counts['pending']??0 ?>)</a>
      <a href="?status=suspended"class="<?= $filter==='suspended'?'active':'' ?>">Ditangguhkan (<?= $counts['suspended']??0 ?>)</a>
    </div>

    <div class="form-card" style="padding:0;overflow:hidden">
      <table class="admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Role</th>
            <th>Status</th>
            <th>Artikel</th>
            <th>Tampilan</th>
            <th>Bergabung</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td>
              <div class="user-cell">
                <div class="user-avatar-sm">
                  <?php if (!empty($u['avatar_path']) && file_exists(UPLOAD_PATH . $u['avatar_path'])): ?>
                    <img src="<?= escape(UPLOAD_URL . $u['avatar_path']) ?>" alt="">
                  <?php else: ?>
                    <?= strtoupper(substr($u['full_name'], 0, 1)) ?>
                  <?php endif; ?>
                </div>
                <div>
                  <strong><?= escape($u['full_name']) ?></strong>
                  <small>@<?= escape($u['username']) ?> · <?= escape($u['email']) ?></small>
                </div>
              </div>
            </td>
            <td><span class="role-badge <?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
            <td><span class="user-status-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
            <td><?= $u['article_count'] ?></td>
            <td><?= number_format($u['total_views']) ?></td>
            <td style="white-space:nowrap"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            <td>
              <div class="actions" style="flex-wrap:wrap;gap:.3rem">
                <?php if ($u['id'] == $_SESSION['user_id']): ?>
                  <a href="profil.php" class="btn-sm">Edit Profil</a>
                <?php else: ?>
                  <?php if ($u['status'] === 'pending'): ?>
                    <a href="?action=approve&id=<?= $u['id'] ?>" class="btn-sm success">✅ Setujui</a>
                    <a href="?action=delete&id=<?= $u['id'] ?>" class="btn-sm danger"
                       onclick="return confirm('Tolak dan hapus pendaftar ini?')">Tolak</a>
                  <?php elseif ($u['status'] === 'active'): ?>
                    <a href="?action=suspend&id=<?= $u['id'] ?>" class="btn-sm"
                       onclick="return confirm('Tangguhkan akun ini?')">⏸ Tangguhkan</a>
                    <?php if ($u['role'] === 'author'): ?>
                      <a href="?action=make_admin&id=<?= $u['id'] ?>" class="btn-sm"
                         onclick="return confirm('Jadikan admin?')">⬆ Jadikan Admin</a>
                    <?php else: ?>
                      <a href="?action=make_author&id=<?= $u['id'] ?>" class="btn-sm"
                         onclick="return confirm('Turunkan ke author?')">⬇ Jadikan Author</a>
                    <?php endif; ?>
                  <?php elseif ($u['status'] === 'suspended'): ?>
                    <a href="?action=activate&id=<?= $u['id'] ?>" class="btn-sm success">▶ Aktifkan</a>
                  <?php endif; ?>
                  <?php if ($u['status'] !== 'pending'): ?>
                    <a href="?action=delete&id=<?= $u['id'] ?>" class="btn-sm danger"
                       onclick="return confirm('Hapus user <?= escape($u['full_name']) ?>? Pastikan tidak ada artikel.')">Hapus</a>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($users)): ?>
            <tr><td colspan="7"><div class="empty-state sm"><span>👤</span><p>Tidak ada user ditemukan.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>
</body>
</html>

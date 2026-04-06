<?php
/**
 * MIGRATION RUNNER
 * Jalankan file ini SATU KALI melalui browser:
 * http://localhost/blog/migrate.php
 * Setelah selesai, HAPUS file ini dari server.
 */
require_once 'config.php';

$results = [];

$migrations = [
    'Tambah kolom status ke tabel users' => "ALTER TABLE users ADD COLUMN status ENUM('pending','active','suspended') NOT NULL DEFAULT 'active' AFTER role",
    'Tambah kolom avatar_path ke tabel users' => "ALTER TABLE users ADD COLUMN avatar_path VARCHAR(255) DEFAULT NULL AFTER bio",
    'Set semua user lama menjadi active' => "UPDATE users SET status = 'active' WHERE status IS NULL OR status = ''",
];

foreach ($migrations as $label => $sql) {
    try {
        $pdo->exec($sql);
        $results[] = ['ok' => true, 'label' => $label, 'msg' => 'Berhasil'];
    } catch (PDOException $e) {
        // 1060 = column already exists — bukan error fatal
        $isDuplicate = str_contains($e->getMessage(), 'Duplicate column') || $e->getCode() == '42S21';
        $results[] = [
            'ok'    => $isDuplicate,
            'label' => $label,
            'msg'   => $isDuplicate ? 'Sudah ada (dilewati)' : 'ERROR: ' . $e->getMessage()
        ];
    }
}

$allOk = !array_filter($results, fn($r) => !$r['ok']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Migration — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Source Sans 3', sans-serif; background: #f4f6f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
  .box { background: white; border-radius: 12px; padding: 2.5rem; max-width: 520px; width: 100%; box-shadow: 0 4px 24px rgba(0,0,0,.1); }
  h1 { font-size: 1.5rem; margin-bottom: .5rem; }
  p.sub { color: #6b7280; font-size: .9rem; margin-bottom: 1.75rem; }
  .item { display: flex; align-items: flex-start; gap: .75rem; padding: .75rem 0; border-bottom: 1px solid #f3f4f6; }
  .item:last-child { border-bottom: none; }
  .icon { font-size: 1.25rem; flex-shrink: 0; }
  .label { font-weight: 600; font-size: .9rem; }
  .msg { font-size: .8rem; color: #6b7280; margin-top: .15rem; }
  .msg.err { color: #dc2626; }
  .footer { margin-top: 1.75rem; text-align: center; }
  .btn { display: inline-block; padding: .65rem 1.5rem; border-radius: 6px; font-weight: 600; font-size: .9rem; text-decoration: none; background: #c0392b; color: white; }
  .btn.sec { background: #f3f4f6; color: #374151; margin-left: .5rem; }
  .warning { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: .85rem 1rem; font-size: .85rem; color: #78350f; margin-top: 1.25rem; }
</style>
</head>
<body>
<div class="box">
  <h1>🔧 Database Migration</h1>
  <p class="sub"><?= APP_NAME ?> — Update struktur tabel users</p>

  <div class="items">
    <?php foreach ($results as $r): ?>
      <div class="item">
        <span class="icon"><?= $r['ok'] ? '✅' : '❌' ?></span>
        <div>
          <div class="label"><?= escape($r['label']) ?></div>
          <div class="msg <?= $r['ok'] ? '' : 'err' ?>"><?= escape($r['msg']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="footer">
    <?php if ($allOk): ?>
      <p style="color:#15803d;font-weight:600;margin-bottom:1rem">✅ Migration selesai! Semua berhasil.</p>
      <a href="admin/dashboard.php" class="btn">Buka Dashboard →</a>
      <a href="login.php" class="btn sec">Login</a>
      <div class="warning">
        ⚠️ <strong>Penting:</strong> Hapus file <code>migrate.php</code> dari server setelah ini untuk keamanan.
      </div>
    <?php else: ?>
      <p style="color:#dc2626;font-weight:600;margin-bottom:1rem">❌ Ada error. Cek pesan di atas.</p>
      <a href="migrate.php" class="btn">Coba Lagi</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

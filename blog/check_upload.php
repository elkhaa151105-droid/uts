<?php
/**
 * UPLOAD DIAGNOSTIK
 * Buka: http://localhost/blog/check_upload.php
 * Hapus file ini setelah selesai diagnosa.
 */
require_once 'config.php';

$checks = [];

// 1. Cek UPLOAD_PATH
$uploadPath = UPLOAD_PATH;
$checks[] = [
    'label' => 'Path folder uploads',
    'value' => $uploadPath,
    'ok'    => true,
];

// 2. Cek folder uploads ada atau tidak
$folderExists = is_dir($uploadPath);
if (!$folderExists) {
    // Coba buat otomatis
    $created = mkdir($uploadPath, 0755, true);
    $folderExists = $created;
    $checks[] = [
        'label' => 'Folder uploads/',
        'value' => $created ? 'Tidak ada → Berhasil dibuat otomatis ✅' : 'Tidak ada dan GAGAL dibuat ❌',
        'ok'    => $created,
    ];
} else {
    $checks[] = [
        'label' => 'Folder uploads/',
        'value' => 'Sudah ada',
        'ok'    => true,
    ];
}

// 3. Cek writable
$isWritable = is_writable($uploadPath);
$checks[] = [
    'label' => 'Folder uploads/ bisa ditulis (writable)',
    'value' => $isWritable ? 'Ya' : 'TIDAK — perlu ubah permission ke 755 atau 777',
    'ok'    => $isWritable,
];

// 4. Cek php.ini upload settings
$checks[] = [
    'label' => 'file_uploads (php.ini)',
    'value' => ini_get('file_uploads') ? 'On' : 'Off — UPLOAD DINONAKTIFKAN!',
    'ok'    => (bool)ini_get('file_uploads'),
];
$checks[] = [
    'label' => 'upload_max_filesize',
    'value' => ini_get('upload_max_filesize'),
    'ok'    => true,
];
$checks[] = [
    'label' => 'post_max_size',
    'value' => ini_get('post_max_size'),
    'ok'    => true,
];
$checks[] = [
    'label' => 'max_file_uploads',
    'value' => ini_get('max_file_uploads'),
    'ok'    => true,
];

// 5. Coba tulis file test
$testFile = $uploadPath . '_test_write_' . time() . '.txt';
$writeOk = file_put_contents($testFile, 'test') !== false;
if ($writeOk) @unlink($testFile);
$checks[] = [
    'label' => 'Test tulis file ke uploads/',
    'value' => $writeOk ? 'Berhasil' : 'GAGAL — periksa permission folder',
    'ok'    => $writeOk,
];

// 6. Cek UPLOAD_URL
$checks[] = [
    'label' => 'URL uploads',
    'value' => UPLOAD_URL,
    'ok'    => true,
];

// 7. Test upload via form
$uploadResult = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['testfile']['name'])) {
    $err = $_FILES['testfile']['error'];
    if ($err === UPLOAD_ERR_OK) {
        $fname = 'test_' . time() . '_' . basename($_FILES['testfile']['name']);
        $dest  = UPLOAD_PATH . $fname;
        if (move_uploaded_file($_FILES['testfile']['tmp_name'], $dest)) {
            $uploadResult = "<span style='color:#15803d'>✅ Upload BERHASIL! File tersimpan di: <code>$dest</code><br>URL: <a href='" . UPLOAD_URL . $fname . "' target='_blank'>" . UPLOAD_URL . $fname . "</a></span>";
        } else {
            $uploadResult = "<span style='color:#dc2626'>❌ move_uploaded_file() GAGAL. Folder tidak writable.</span>";
        }
    } else {
        $errMap = [1=>'Terlalu besar (php.ini)',2=>'Terlalu besar (form)',3=>'Upload tidak lengkap',4=>'Tidak ada file',6=>'No temp folder',7=>'Gagal tulis disk'];
        $uploadResult = "<span style='color:#dc2626'>❌ Error upload: " . ($errMap[$err] ?? "Kode $err") . "</span>";
    }
}

$allOk = empty(array_filter($checks, fn($c) => !$c['ok']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Diagnostik Upload — <?= APP_NAME ?></title>
<style>
  body { font-family: system-ui, sans-serif; background: #f4f6f8; padding: 2rem; }
  .box { background: white; border-radius: 12px; padding: 2rem; max-width: 680px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
  h1 { font-size: 1.4rem; margin-bottom: .25rem; }
  .sub { color: #6b7280; margin-bottom: 1.5rem; font-size: .9rem; }
  table { width: 100%; border-collapse: collapse; font-size: .9rem; }
  th { text-align: left; background: #f9fafb; padding: .6rem .75rem; font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
  td { padding: .65rem .75rem; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
  .ok   { color: #15803d; font-weight: 700; }
  .fail { color: #dc2626; font-weight: 700; }
  .summary { padding: 1rem; border-radius: 8px; margin: 1.25rem 0; font-weight: 600; font-size: .95rem; }
  .summary.ok   { background: #dcfce7; color: #15803d; }
  .summary.fail { background: #fee2e2; color: #dc2626; }
  .test-form { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; }
  .test-form h3 { font-size: 1rem; margin-bottom: .75rem; }
  .upload-result { margin-top: .75rem; padding: .75rem; background: #f9fafb; border-radius: 6px; font-size: .875rem; }
  input[type=file] { font-size: .875rem; }
  button { margin-top: .75rem; background: #1d4ed8; color: white; border: none; padding: .55rem 1.25rem; border-radius: 6px; font-size: .875rem; font-weight: 600; cursor: pointer; }
  button:hover { background: #1e40af; }
  .fix-box { background: #fef9c3; border: 1px solid #f59e0b; border-radius: 8px; padding: 1rem; margin-top: 1.25rem; font-size: .875rem; }
  .fix-box h4 { margin: 0 0 .5rem; font-size: .95rem; }
  code { background: #f3f4f6; padding: .1rem .4rem; border-radius: 3px; font-size: .85em; }
  .warn { color: #78350f; }
</style>
</head>
<body>
<div class="box">
  <h1>🔍 Diagnostik Upload Gambar</h1>
  <p class="sub"><?= APP_NAME ?> — Cek kondisi upload file di server Anda</p>

  <table>
    <thead><tr><th>Pemeriksaan</th><th>Nilai</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($checks as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['label']) ?></td>
        <td><?= htmlspecialchars($c['value']) ?></td>
        <td class="<?= $c['ok'] ? 'ok' : 'fail' ?>"><?= $c['ok'] ? '✅ OK' : '❌ Masalah' ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="summary <?= $allOk ? 'ok' : 'fail' ?>">
    <?= $allOk
      ? '✅ Semua OK! Upload seharusnya berfungsi normal.'
      : '❌ Ditemukan masalah. Lihat baris merah di atas.' ?>
  </div>

  <?php if (!$allOk): ?>
  <div class="fix-box">
    <h4>💡 Cara Perbaikan di Laragon/Windows:</h4>
    <ol style="margin:.5rem 0 0 1.25rem;line-height:1.8">
      <li>Buka File Explorer → navigasi ke <code>C:\laragon\www\blog\</code></li>
      <li>Buat folder baru bernama <code>uploads</code> (jika belum ada)</li>
      <li>Klik kanan folder <code>uploads</code> → Properties → Security</li>
      <li>Pastikan user <em>IIS_IUSRS</em> atau <em>Everyone</em> punya izin <strong>Full Control</strong> atau minimal <strong>Write</strong></li>
      <li>Atau coba jalankan Laragon sebagai Administrator</li>
    </ol>
  </div>
  <?php endif; ?>

  <!-- Test Upload Langsung -->
  <div class="test-form">
    <h3>🧪 Test Upload File Langsung</h3>
    <p style="font-size:.875rem;color:#6b7280;margin-bottom:.75rem">Coba upload gambar kecil untuk memastikan sistem upload berjalan.</p>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="testfile" accept="image/*" required>
      <br><button type="submit">Upload Test</button>
    </form>
    <?php if ($uploadResult): ?>
      <div class="upload-result"><?= $uploadResult ?></div>
    <?php endif; ?>
  </div>

  <p style="margin-top:1.5rem;font-size:.8rem;color:#9ca3af;text-align:center">
    ⚠️ Hapus file <code>check_upload.php</code> dari server setelah selesai diagnosa.
  </p>
</div>
</body>
</html>

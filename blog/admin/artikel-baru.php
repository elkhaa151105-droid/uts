<?php
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$article = null;
$articleTags = [];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    if (!$article || (!isAdmin() && $article['author_id'] != $_SESSION['user_id'])) {
        flashMessage('error', 'Akses ditolak atau artikel tidak ditemukan.');
        redirect('dashboard.php');
    }
    $tagStmt = $pdo->prepare("SELECT tag_id FROM article_tags WHERE article_id = ?");
    $tagStmt->execute([$id]);
    $articleTags = array_column($tagStmt->fetchAll(), 'tag_id');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags ORDER BY name")->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = (int)($_POST['category_id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';
    $selectedTags = $_POST['tags'] ?? [];

    if (!$title) $errors[] = 'Judul wajib diisi.';
    if (!$content) $errors[] = 'Konten wajib diisi.';
    if (!$category_id) $errors[] = 'Pilih kategori.';

    // Upload thumbnail
    $thumbnail = $article['thumbnail'] ?? null;
    if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format gambar tidak valid. Gunakan: JPG, PNG, WEBP, atau GIF.';
        } elseif ($_FILES['thumbnail']['size'] > $maxSize) {
            $errors[] = 'Ukuran gambar terlalu besar. Maksimal 5MB.';
        } else {
            // Buat folder uploads jika belum ada
            if (!is_dir(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0755, true);
            }
            $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $ext;
            $dest = UPLOAD_PATH . $filename;
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
                $thumbnail = $filename;
            } else {
                $errors[] = 'Gagal menyimpan gambar. Pastikan folder uploads/ dapat ditulis (writable).';
            }
        }
    } elseif (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Ada error upload dari PHP
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'File terlalu besar (melebihi upload_max_filesize di php.ini).',
            UPLOAD_ERR_FORM_SIZE  => 'File terlalu besar (melebihi MAX_FILE_SIZE form).',
            UPLOAD_ERR_PARTIAL    => 'File hanya terupload sebagian.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary PHP tidak ditemukan.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
        ];
        $errCode = $_FILES['thumbnail']['error'];
        $errors[] = $uploadErrors[$errCode] ?? "Error upload (kode: $errCode).";
    }

    if (empty($errors)) {
        $articleSlug = slug($title);
        // Pastikan slug unik
        $checkSlug = $pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
        $checkSlug->execute([$articleSlug, $id]);
        if ($checkSlug->fetch()) $articleSlug .= '-' . time();

        if ($isEdit) {
            $stmt = $pdo->prepare("UPDATE articles SET title=?, slug=?, excerpt=?, content=?, category_id=?, status=?, thumbnail=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$title, $articleSlug, $excerpt, $content, $category_id, $status, $thumbnail, $id]);
            // Update tags
            $pdo->prepare("DELETE FROM article_tags WHERE article_id=?")->execute([$id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO articles (title, slug, excerpt, content, category_id, author_id, status, thumbnail) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $articleSlug, $excerpt, $content, $category_id, $_SESSION['user_id'], $status, $thumbnail]);
            $id = $pdo->lastInsertId();
        }

        // Simpan tags
        foreach ($selectedTags as $tagId) {
            $pdo->prepare("INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (?,?)")->execute([$id, $tagId]);
        }

        flashMessage('success', 'Artikel berhasil ' . ($isEdit ? 'diperbarui' : 'dibuat') . '!');
        redirect('artikel-list.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $isEdit ? 'Edit' : 'Buat' ?> Artikel — <?= APP_NAME ?></title>
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
        <h1><?= $isEdit ? 'Edit Artikel' : 'Buat Artikel Baru' ?></h1>
        <p><a href="artikel-list.php">← Kembali ke daftar</a></p>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert error"><ul><?php foreach ($errors as $e): ?><li><?= escape($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="article-form">
      <div class="form-layout">
        <!-- Kolom Utama -->
        <div class="form-main">
          <div class="form-card">
            <div class="form-group">
              <label>Judul Artikel *</label>
              <input type="text" name="title" value="<?= escape($article['title'] ?? $_POST['title'] ?? '') ?>" 
                     required placeholder="Masukkan judul yang menarik...">
            </div>
            <div class="form-group">
              <label>Ringkasan / Excerpt</label>
              <textarea name="excerpt" rows="3" placeholder="Deskripsi singkat artikel (untuk SEO dan preview)"><?= escape($article['excerpt'] ?? $_POST['excerpt'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
              <label>Konten Artikel *</label>
              <div class="editor-toolbar">
                <button type="button" onclick="formatText('bold')"><b>B</b></button>
                <button type="button" onclick="formatText('italic')"><i>I</i></button>
                <button type="button" onclick="insertH2()">H2</button>
                <button type="button" onclick="insertH3()">H3</button>
                <button type="button" onclick="insertLink()">🔗</button>
                <button type="button" onclick="insertList()">☰</button>
                <button type="button" onclick="togglePreview()">👁 Preview</button>
              </div>
              <textarea name="content" id="editor" rows="20" placeholder="Tulis konten artikel di sini... (HTML didukung)"><?= escape($article['content'] ?? $_POST['content'] ?? '') ?></textarea>
              <div id="preview" class="article-content" style="display:none; padding:1rem; background:#f9f9f9; border:1px solid #ddd; border-radius:4px;"></div>
            </div>
          </div>
        </div>

        <!-- Kolom Samping -->
        <div class="form-side">
          <!-- Publikasi -->
          <div class="form-card">
            <h4>Publikasi</h4>
            <div class="form-group">
              <label>Status</label>
              <select name="status">
                <option value="draft" <?= ($article['status']??'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($article['status']??'') === 'published' ? 'selected' : '' ?>>Publikasikan</option>
                <option value="archived" <?= ($article['status']??'') === 'archived' ? 'selected' : '' ?>>Arsip</option>
              </select>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn-primary full">
                <?= $isEdit ? '💾 Simpan Perubahan' : '🚀 Buat Artikel' ?>
              </button>
              <a href="artikel-list.php" class="btn-outline full">Batal</a>
            </div>
          </div>

          <!-- Kategori -->
          <div class="form-card">
            <h4>Kategori *</h4>
            <div class="form-group">
              <select name="category_id" required>
                <option value="">— Pilih Kategori —</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= ($article['category_id']??0) == $cat['id'] ? 'selected' : '' ?>>
                    <?= escape($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Tags -->
          <div class="form-card">
            <h4>Tags</h4>
            <div class="tags-check">
              <?php foreach ($tags as $tag): ?>
                <label class="tag-check">
                  <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"
                         <?= in_array($tag['id'], $articleTags) ? 'checked' : '' ?>>
                  #<?= escape($tag['name']) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Thumbnail -->
          <div class="form-card">
            <h4>Gambar Thumbnail</h4>
            <?php if (!empty($article['thumbnail'])): ?>
              <div class="current-thumb">
                <img src="<?= escape(UPLOAD_URL . $article['thumbnail']) ?>" alt="Thumbnail saat ini">
                <p>Thumbnail saat ini</p>
              </div>
            <?php endif; ?>
            <input type="file" name="thumbnail" accept="image/*" class="file-input">
            <p class="hint">Format: JPG, PNG, WEBP. Maks 2MB.</p>
          </div>
        </div>
      </div>
    </form>
  </main>
</div>

<script>
function formatText(cmd) {
  document.execCommand(cmd, false, null);
  const editor = document.getElementById('editor');
  const start = editor.selectionStart, end = editor.selectionEnd;
  const text = editor.value.substring(start, end);
  const tag = cmd === 'bold' ? 'strong' : 'em';
  editor.value = editor.value.substring(0, start) + `<${tag}>${text}</${tag}>` + editor.value.substring(end);
}
function insertH2() {
  const editor = document.getElementById('editor');
  const start = editor.selectionStart;
  const text = editor.value.substring(start, editor.selectionEnd) || 'Judul Bagian';
  editor.value = editor.value.substring(0, start) + `<h2>${text}</h2>` + editor.value.substring(editor.selectionEnd);
}
function insertH3() {
  const editor = document.getElementById('editor');
  const start = editor.selectionStart;
  const text = editor.value.substring(start, editor.selectionEnd) || 'Sub Judul';
  editor.value = editor.value.substring(0, start) + `<h3>${text}</h3>` + editor.value.substring(editor.selectionEnd);
}
function insertLink() {
  const url = prompt('Masukkan URL:');
  if (url) {
    const editor = document.getElementById('editor');
    const start = editor.selectionStart;
    const text = editor.value.substring(start, editor.selectionEnd) || 'Teks Link';
    editor.value = editor.value.substring(0, start) + `<a href="${url}">${text}</a>` + editor.value.substring(editor.selectionEnd);
  }
}
function insertList() {
  const editor = document.getElementById('editor');
  const pos = editor.selectionStart;
  editor.value = editor.value.substring(0, pos) + '<ul>\n  <li>Item 1</li>\n  <li>Item 2</li>\n</ul>' + editor.value.substring(pos);
}
function togglePreview() {
  const editor = document.getElementById('editor');
  const preview = document.getElementById('preview');
  if (preview.style.display === 'none') {
    preview.innerHTML = editor.value;
    preview.style.display = 'block';
    editor.style.display = 'none';
  } else {
    preview.style.display = 'none';
    editor.style.display = 'block';
  }
}
</script>
</body>
</html>

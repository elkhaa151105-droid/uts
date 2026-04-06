<?php
require_once '../config.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php');

$errors = [];
// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_category') {
        $name = trim($_POST['cat_name'] ?? '');
        $desc = trim($_POST['cat_desc'] ?? '');
        if (!$name) { $errors[] = 'Nama kategori wajib diisi.'; }
        else {
            $catSlug = slug($name);
            try {
                $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?,?,?)")->execute([$name, $catSlug, $desc]);
                flashMessage('success', 'Kategori berhasil ditambahkan.');
                redirect('kategori.php');
            } catch (PDOException $e) {
                $errors[] = 'Slug sudah digunakan, coba nama lain.';
            }
        }
    } elseif ($action === 'delete_category') {
        $catId = (int)($_POST['cat_id'] ?? 0);
        $check = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id=?");
        $check->execute([$catId]);
        if ($check->fetchColumn() > 0) {
            $errors[] = 'Tidak bisa hapus kategori yang masih memiliki artikel.';
        } else {
            $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$catId]);
            flashMessage('success', 'Kategori dihapus.');
            redirect('kategori.php');
        }
    } elseif ($action === 'add_tag') {
        $name = trim($_POST['tag_name'] ?? '');
        if (!$name) { $errors[] = 'Nama tag wajib diisi.'; }
        else {
            $tagSlug = slug($name);
            try {
                $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?,?)")->execute([$name, $tagSlug]);
                flashMessage('success', 'Tag berhasil ditambahkan.');
                redirect('kategori.php');
            } catch (PDOException $e) {
                $errors[] = 'Tag sudah ada.';
            }
        }
    } elseif ($action === 'delete_tag') {
        $tagId = (int)($_POST['tag_id'] ?? 0);
        $pdo->prepare("DELETE FROM tags WHERE id=?")->execute([$tagId]);
        flashMessage('success', 'Tag dihapus.');
        redirect('kategori.php');
    }
}

$categories = $pdo->query("SELECT c.*, COUNT(a.id) as count FROM categories c LEFT JOIN articles a ON c.id=a.category_id GROUP BY c.id ORDER BY c.name")->fetchAll();
$tags = $pdo->query("SELECT t.*, COUNT(at.article_id) as count FROM tags t LEFT JOIN article_tags at ON t.id=at.tag_id GROUP BY t.id ORDER BY t.name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kategori & Tag — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
<?php include 'partials/admin-nav.php'; ?>
<div class="admin-layout">
  <?php include 'partials/admin-sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header"><h1>Kategori & Tag</h1></div>
    <?php $flash = getFlash(); if ($flash): ?>
      <div class="alert <?= $flash['type'] ?>"><?= escape($flash['message']) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert error"><?= implode('<br>', array_map('escape', $errors)) ?></div>
    <?php endif; ?>

    <div class="cat-tag-grid">
      <!-- Kategori -->
      <div class="form-card">
        <h3>Kategori</h3>
        <form method="POST" class="inline-form">
          <input type="hidden" name="action" value="add_category">
          <div class="form-group">
            <label>Nama Kategori *</label>
            <input type="text" name="cat_name" placeholder="Nama kategori baru..." required>
          </div>
          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="cat_desc" rows="2" placeholder="Deskripsi singkat..."></textarea>
          </div>
          <button type="submit" class="btn-primary">+ Tambah Kategori</button>
        </form>
        <hr style="margin:1.5rem 0">
        <table class="admin-table">
          <thead><tr><th>Nama</th><th>Slug</th><th>Artikel</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
              <td><?= escape($cat['name']) ?></td>
              <td><code><?= escape($cat['slug']) ?></code></td>
              <td><?= $cat['count'] ?></td>
              <td>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="delete_category">
                  <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                  <button type="submit" class="btn-sm danger" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Tag -->
      <div class="form-card">
        <h3>Tag</h3>
        <form method="POST" class="inline-form">
          <input type="hidden" name="action" value="add_tag">
          <div class="form-group">
            <label>Nama Tag *</label>
            <input type="text" name="tag_name" placeholder="Nama tag baru..." required>
          </div>
          <button type="submit" class="btn-primary">+ Tambah Tag</button>
        </form>
        <hr style="margin:1.5rem 0">
        <div class="tag-list">
          <?php foreach ($tags as $tag): ?>
            <div class="tag-item">
              <span class="tag-pill">#<?= escape($tag['name']) ?> <small>(<?= $tag['count'] ?>)</small></span>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="delete_tag">
                <input type="hidden" name="tag_id" value="<?= $tag['id'] ?>">
                <button type="submit" class="btn-sm danger" onclick="return confirm('Hapus tag ini?')" title="Hapus">✕</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>

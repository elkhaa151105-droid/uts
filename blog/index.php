<?php
require_once 'config.php';

// Ambil artikel published dengan pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;
$tag_filter = isset($_GET['tag']) ? $_GET['tag'] : null;
$search = isset($_GET['q']) ? trim($_GET['q']) : null;

$where = ["a.status = 'published'"];
$params = [];

if ($category_filter) {
    $where[] = "c.slug = ?";
    $params[] = $category_filter;
}
if ($tag_filter) {
    $where[] = "EXISTS (SELECT 1 FROM article_tags at JOIN tags t ON at.tag_id = t.id WHERE at.article_id = a.id AND t.slug = ?)";
    $params[] = $tag_filter;
}
if ($search) {
    $where[] = "(a.title LIKE ? OR a.excerpt LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereStr = implode(' AND ', $where);

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM articles a JOIN categories c ON a.category_id = c.id WHERE $whereStr");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug,
           u.full_name as author_name, u.username as author_username
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    JOIN users u ON a.author_id = u.id
    WHERE $whereStr
    ORDER BY a.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Ambil kategori untuk sidebar
$categories = $pdo->query("SELECT c.*, COUNT(a.id) as count FROM categories c LEFT JOIN articles a ON c.id = a.category_id AND a.status='published' GROUP BY c.id ORDER BY count DESC")->fetchAll();

// Artikel populer
$popular = $pdo->query("SELECT a.*, u.full_name as author_name FROM articles a JOIN users u ON a.author_id = u.id WHERE a.status='published' ORDER BY a.views DESC LIMIT 5")->fetchAll();

// Tag cloud
$tags = $pdo->query("SELECT t.*, COUNT(at.article_id) as count FROM tags t LEFT JOIN article_tags at ON t.id = at.tag_id GROUP BY t.id ORDER BY count DESC LIMIT 20")->fetchAll();

$pageTitle = $search ? "Pencarian: $search" : ($category_filter ? "Kategori" : "Beranda");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= APP_NAME ?> — <?= escape($pageTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'partials/header.php'; ?>

<main class="container">
  <div class="layout-grid">
    <!-- Artikel Utama -->
    <section class="main-content">
      <?php if ($search): ?>
        <div class="page-header">
          <h1>Hasil pencarian untuk "<em><?= escape($search) ?></em>"</h1>
          <p><?= $total ?> artikel ditemukan</p>
        </div>
      <?php elseif ($category_filter): ?>
        <?php $cat = array_filter($categories, fn($c) => $c['slug'] === $category_filter); $cat = reset($cat); ?>
        <div class="page-header">
          <span class="label">Kategori</span>
          <h1><?= escape($cat['name'] ?? $category_filter) ?></h1>
          <p><?= $total ?> artikel</p>
        </div>
      <?php elseif ($tag_filter): ?>
        <div class="page-header">
          <span class="label">Tag</span>
          <h1>#<?= escape($tag_filter) ?></h1>
          <p><?= $total ?> artikel</p>
        </div>
      <?php else: ?>
        <!-- Hero artikel terbaru -->
        <?php if (!empty($articles) && $page === 1): ?>
          <?php $hero = array_shift($articles); ?>
          <article class="hero-article">
            <?php if ($hero['thumbnail']): ?>
              <div class="hero-img">
                <img src="<?= escape(UPLOAD_URL . $hero['thumbnail']) ?>" alt="<?= escape($hero['title']) ?>">
              </div>
            <?php else: ?>
              <div class="hero-img hero-placeholder" style="background: linear-gradient(135deg, #1a1a2e, #16213e);">
                <span class="hero-placeholder-icon">✍</span>
              </div>
            <?php endif; ?>
            <div class="hero-content">
              <div class="meta">
                <a href="?category=<?= $hero['category_slug'] ?>" class="category-badge"><?= escape($hero['category_name']) ?></a>
                <span class="dot">·</span>
                <time><?= timeAgo($hero['created_at']) ?></time>
              </div>
              <h2><a href="artikel.php?slug=<?= $hero['slug'] ?>"><?= escape($hero['title']) ?></a></h2>
              <p class="excerpt"><?= escape($hero['excerpt']) ?></p>
              <div class="article-footer">
                <span class="author">Oleh <strong><?= escape($hero['author_name']) ?></strong></span>
                <a href="artikel.php?slug=<?= $hero['slug'] ?>" class="btn-read">Baca Selengkapnya →</a>
              </div>
            </div>
          </article>
        <?php endif; ?>
      <?php endif; ?>

      <!-- Grid Artikel -->
      <?php if (!empty($articles)): ?>
        <div class="articles-grid">
          <?php foreach ($articles as $a): ?>
            <article class="article-card">
              <a href="artikel.php?slug=<?= $a['slug'] ?>" class="card-img-link">
                <?php if ($a['thumbnail']): ?>
                  <img src="<?= escape(UPLOAD_URL . $a['thumbnail']) ?>" alt="<?= escape($a['title']) ?>" loading="lazy">
                <?php else: ?>
                  <div class="img-placeholder">📝</div>
                <?php endif; ?>
              </a>
              <div class="card-body">
                <div class="meta">
                  <a href="?category=<?= $a['category_slug'] ?>" class="category-badge sm"><?= escape($a['category_name']) ?></a>
                  <time><?= timeAgo($a['created_at']) ?></time>
                </div>
                <h3><a href="artikel.php?slug=<?= $a['slug'] ?>"><?= escape($a['title']) ?></a></h3>
                <p class="excerpt sm"><?= escape(substr($a['excerpt'] ?? '', 0, 120)) ?>...</p>
                <div class="card-foot">
                  <span class="author-sm"><?= escape($a['author_name']) ?></span>
                  <span class="views">👁 <?= number_format($a['views']) ?></span>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php elseif (empty($articles) && !isset($hero)): ?>
        <div class="empty-state">
          <span>📭</span>
          <p>Belum ada artikel yang dipublikasikan.</p>
        </div>
      <?php endif; ?>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
        <nav class="pagination">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?><?= $search ? '&q='.urlencode($search) : '' ?><?= $category_filter ? '&category='.$category_filter : '' ?>"
               class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>
    </section>

    <!-- Sidebar -->
    <aside class="sidebar">
      <?php include 'partials/sidebar.php'; ?>
    </aside>
  </div>
</main>

<?php include 'partials/footer.php'; ?>
</body>
</html>

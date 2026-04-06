<?php
// partials/sidebar.php
// Sidebar mengambil datanya sendiri dari DB
// sehingga tidak bergantung pada variabel dari halaman parent

global $pdo;

// Kategori
$sidebarCategories = $pdo->query("
    SELECT c.*, COUNT(a.id) as count
    FROM categories c
    LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
    GROUP BY c.id
    ORDER BY count DESC
")->fetchAll();

// Artikel populer
$sidebarPopular = $pdo->query("
    SELECT a.*, u.full_name as author_name
    FROM articles a
    JOIN users u ON a.author_id = u.id
    WHERE a.status = 'published'
    ORDER BY a.views DESC
    LIMIT 5
")->fetchAll();

// Tag cloud
$sidebarTags = $pdo->query("
    SELECT t.*, COUNT(at.article_id) as count
    FROM tags t
    LEFT JOIN article_tags at ON t.id = at.tag_id
    GROUP BY t.id
    ORDER BY count DESC
    LIMIT 20
")->fetchAll();
?>

<!-- Pencarian -->
<div class="widget">
  <h4 class="widget-title">Cari Artikel</h4>
  <form method="GET" action="index.php" class="search-form sidebar-search">
    <input type="search" name="q" placeholder="Kata kunci..."
           value="<?= escape($_GET['q'] ?? '') ?>">
    <button type="submit">Cari</button>
  </form>
</div>

<!-- Kategori -->
<?php if (!empty($sidebarCategories)): ?>
<div class="widget">
  <h4 class="widget-title">Kategori</h4>
  <ul class="widget-list">
    <?php foreach ($sidebarCategories as $cat): ?>
      <li>
        <a href="index.php?category=<?= escape($cat['slug']) ?>">
          <?= escape($cat['name']) ?>
          <span class="count"><?= (int)$cat['count'] ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<!-- Artikel Populer -->
<?php if (!empty($sidebarPopular)): ?>
<div class="widget">
  <h4 class="widget-title">Paling Populer</h4>
  <div class="popular-list">
    <?php foreach ($sidebarPopular as $i => $p): ?>
      <a href="artikel.php?slug=<?= escape($p['slug']) ?>" class="popular-item">
        <span class="popular-num">0<?= $i + 1 ?></span>
        <div>
          <span class="popular-title">
            <?= escape(substr($p['title'], 0, 55)) ?>...
          </span>
          <small>👁 <?= number_format($p['views']) ?> tampilan</small>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Tag Cloud -->
<?php if (!empty($sidebarTags)): ?>
<div class="widget">
  <h4 class="widget-title">Tag</h4>
  <div class="tag-cloud">
    <?php foreach ($sidebarTags as $tag): ?>
      <a href="index.php?tag=<?= escape($tag['slug']) ?>"
         class="tag-pill"
         style="font-size: <?= min(1.1, 0.75 + $tag['count'] * 0.05) ?>rem">
        #<?= escape($tag['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

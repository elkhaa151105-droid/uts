<?php // partials/header.php ?>
<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.php" class="logo"><?= APP_NAME ?></a>
      <nav class="main-nav">
        <a href="index.php">Beranda</a>
        <?php
        $navCats = $pdo->query("SELECT name, slug FROM categories ORDER BY name LIMIT 5")->fetchAll();
        foreach ($navCats as $c): ?>
          <a href="index.php?category=<?= $c['slug'] ?>"><?= escape($c['name']) ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="header-actions">
        <form method="GET" action="index.php" class="search-form">
          <input type="search" name="q" placeholder="Cari artikel..." value="<?= escape($_GET['q'] ?? '') ?>">
          <button type="submit">🔍</button>
        </form>
        <?php if (isLoggedIn()): ?>
          <a href="admin/dashboard.php" class="btn-outline sm">Dashboard</a>
        <?php else: ?>
          <a href="login.php" class="btn-outline sm">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

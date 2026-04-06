<?php // partials/footer.php ?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="index.php" class="logo"><?= APP_NAME ?></a>
        <p>Platform blog dinamis dengan fitur lengkap untuk berbagi pengetahuan dan inspirasi.</p>
      </div>
      <div>
        <h5>Kategori</h5>
        <ul>
          <?php
          $footerCats = $pdo->query("SELECT name, slug FROM categories LIMIT 5")->fetchAll();
          foreach ($footerCats as $c): ?>
            <li><a href="index.php?category=<?= $c['slug'] ?>"><?= escape($c['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h5>Tautan</h5>
        <ul>
          <li><a href="index.php">Beranda</a></li>
          <?php if (isLoggedIn()): ?>
            <li><a href="admin/dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Keluar</a></li>
          <?php else: ?>
            <li><a href="login.php">Login Admin</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Dibuat dengan PHP & MySQL.</p>
    </div>
  </div>
</footer>

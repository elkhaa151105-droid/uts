<?php // admin/partials/admin-nav.php ?>
<nav class="admin-nav">
  <a href="../index.php" class="admin-logo"><?= APP_NAME ?></a>
  <div class="admin-nav-right">
    <span class="admin-user">
      <span class="role-badge <?= $_SESSION['role'] ?>"><?= ucfirst($_SESSION['role']) ?></span>
      <?= escape($_SESSION['full_name']) ?>
    </span>
    <a href="../logout.php" class="btn-outline sm">Keluar</a>
  </div>
</nav>

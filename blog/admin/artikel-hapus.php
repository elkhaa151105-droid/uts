<?php
// artikel-hapus.php
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $a = $stmt->fetch();
    if ($a && (isAdmin() || $a['author_id'] == $_SESSION['user_id'])) {
        $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
        flashMessage('success', 'Artikel berhasil dihapus.');
    } else {
        flashMessage('error', 'Akses ditolak.');
    }
}
redirect('artikel-list.php');

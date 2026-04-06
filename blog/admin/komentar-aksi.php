<?php
// komentar-aksi.php
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id && in_array($action, ['approve', 'spam', 'delete'])) {
    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);
        flashMessage('success', 'Komentar dihapus.');
    } else {
        $status = $action === 'approve' ? 'approved' : 'spam';
        $pdo->prepare("UPDATE comments SET status = ? WHERE id = ?")->execute([$status, $id]);
        flashMessage('success', 'Komentar diperbarui.');
    }
}
redirect('komentar.php');

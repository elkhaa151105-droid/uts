<?php
// artikel-edit.php — redirect ke artikel-baru.php dengan id
require_once '../config.php';
if (!isLoggedIn()) redirect('../login.php');
$id = (int)($_GET['id'] ?? 0);
redirect("artikel-baru.php?id=$id");

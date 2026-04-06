<?php
// ============================================
// KONFIGURASI DATABASE & APLIKASI
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'blog_db');

define('APP_NAME', 'TemNews');

// -----------------------------------------------
// AUTO-DETECT URL — bekerja otomatis di:
//   localhost, IP lokal, ngrok, maupun hosting
// Tidak perlu diubah manual saat URL ngrok berubah
// -----------------------------------------------
$_proto   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
             || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
$_host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$_selfDir = str_replace('\\', '/', __DIR__);
$_base    = $_docRoot !== '' ? rtrim(substr($_selfDir, strlen($_docRoot)), '/') : '';
define('APP_URL', $_proto . '://' . $_host . $_base);
unset($_proto, $_host, $_docRoot, $_selfDir, $_base);
// -----------------------------------------------

define('UPLOAD_PATH', rtrim(__DIR__, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_URL', APP_URL . '/uploads/');

// Koneksi PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['error' => 'Koneksi database gagal: ' . $e->getMessage()]));
}

// Helper Functions
function slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return $text;
}

function escape($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return $diff . ' detik lalu';
    if ($diff < 3600) return floor($diff/60) . ' menit lalu';
    if ($diff < 86400) return floor($diff/3600) . ' jam lalu';
    if ($diff < 2592000) return floor($diff/86400) . ' hari lalu';
    return date('d M Y', $time);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function currentUser() {
    return $_SESSION ?? null;
}

function flashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

session_start();

<?php
/**
 * DNA Vacation - Configuration
 */

// ===== DATABASE =====
define('DB_HOST', 'localhost');
define('DB_NAME', 'dna_vacation');
define('DB_USER', 'root');        // Ganti dengan user MySQL kamu
define('DB_PASS', '');            // Ganti dengan password MySQL kamu

// ===== APP =====
define('SITE_NAME', 'DNA Vacation');
define('BASE_URL', 'http://localhost/dna-vacation'); // Ganti sesuai URL kamu
define('ADMIN_URL', BASE_URL . '/admin');

// ===== UPLOADS =====
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');

// ===== WHATSAPP =====
define('DEFAULT_WA', '6281234567890'); // Ganti dengan nomor WA bisnis

// ===== SESSION =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== TIMEZONE =====
date_default_timezone_set('Asia/Makassar');

// ===== DATABASE CONNECTION =====
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
    die("Koneksi database gagal. Pastikan database sudah dibuat dan konfigurasi sudah benar.");
}

// ===== HELPER FUNCTIONS =====

function getSetting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : '';
}

function getWhatsAppNumber() {
    $wa = getSetting('whatsapp_number');
    return $wa ?: DEFAULT_WA;
}

function waLink($message = '') {
    $wa = getWhatsAppNumber();
    $url = "https://wa.me/{$wa}";
    if ($message) {
        $url .= "?text=" . urlencode($message);
    }
    return $url;
}

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function generateBookingCode($prefix = 'TRB') {
    return $prefix . date('ymd') . strtoupper(substr(uniqid(), -4));
}

function uploadImage($file, $folder = 'tours') {
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) return false;
    if ($file['size'] > 5 * 1024 * 1024) return false; // Max 5MB

    $dir = UPLOAD_DIR . $folder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = time() . '_' . uniqid() . '.' . $ext;
    $path = $dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $folder . '/' . $filename;
    }
    return false;
}

/**
 * Asset URL with cache-busting version stamp so browsers pick up
 * CSS/JS changes immediately instead of serving a stale cached copy.
 */
function asset($path) {
    $url = BASE_URL . '/assets/' . $path;
    $file = __DIR__ . '/../assets/' . $path;
    if (is_file($file)) {
        $url .= '?v=' . filemtime($file);
    }
    return $url;
}

function uploadUrl($path) {
    if (!$path) return BASE_URL . '/assets/img/placeholder.jpg';
    return UPLOAD_URL . $path;
}

/**
 * Get logo URL — uses custom uploaded logo if set, else default.
 */
function logoUrl() {
    $custom = getSetting('custom_logo');
    if ($custom) return uploadUrl($custom);
    return asset('img/logo-dna.jpg');
}

/**
 * Get hero background URL — uses custom uploaded image if set, else default.
 */
function heroBgUrl() {
    $custom = getSetting('hero_bg_image');
    if ($custom) return uploadUrl($custom);
    return asset('img/nature.jpg');
}

/**
 * Read a boolean toggle setting with a default when key is absent.
 */
function settingBool($key, $default = true) {
    $val = getSetting($key);
    if ($val === '' || $val === null) return $default;
    return $val === '1' || $val === 1 || $val === 'on' || $val === true;
}

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function truncate($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function setFlash($type, $message) {
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

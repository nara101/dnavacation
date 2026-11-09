<?php
// ===== DATABASE =====
define('DB_HOST', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');       
define('DB_PASS', '');  

// ===== APP =====
define('SITE_NAME', 'DNA VACATION');

define('SITE_DOMAIN', '');

if (SITE_DOMAIN !== '') {
    define('BASE_URL', rtrim(SITE_DOMAIN, '/'));
} elseif (PHP_SAPI === 'cli' || empty($_SERVER['HTTP_HOST'])) {
    // Fallback untuk CLI / cron job
    define('BASE_URL', 'http://localhost/dna-vacation');
} else {
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);
    $host = preg_replace('/[^A-Za-z0-9\.\-:]/', '', $_SERVER['HTTP_HOST']);

    $projectDir = str_replace('\\', '/', dirname(__DIR__));
    $docRoot    = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
    $basePath   = '';
    if ($docRoot !== '' && strpos($projectDir, $docRoot) === 0) {
        $basePath = rtrim(substr($projectDir, strlen($docRoot)), '/');
    }

    define('BASE_URL', ($isHttps ? 'https' : 'http') . '://' . $host . $basePath);
}

define('ADMIN_URL', BASE_URL . '/admin');

// ===== UPLOADS =====
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');

// ===== WHATSAPP =====
define('DEFAULT_WA', '6282349627779'); 

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

function asset($path) {
    $url = BASE_URL . '/assets/' . $path;
    $file = __DIR__ . '/../assets/' . $path;
    if (is_file($file)) {
        $url .= '?v=' . filemtime($file);
    }
    return $url;
}

function uploadVideo($file, $folder = 'testimonials') {
    $allowed = ['mp4', 'webm', 'mov'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) return false;
    if ($file['size'] > 50 * 1024 * 1024) return false; // Max 50MB

    $dir = UPLOAD_DIR . $folder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = time() . '_' . uniqid() . '.' . $ext;
    $path = $dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $folder . '/' . $filename;
    }
    return false;
}

function uploadUrl($path) {
    if (!$path) return BASE_URL . '/assets/img/placeholder.jpg';
    // Pass-through for full URLs (external images / stock photos)
    if (preg_match('#^https?://#i', $path)) return $path;
    return UPLOAD_URL . $path;
}

function videoEmbedUrl($url) {
    $url = trim((string)$url);
    if ($url === '') return null;

    if (preg_match('~(?:youtube\.com/(?:watch\?(?:[^#]*&)?v=|shorts/|embed/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('~drive\.google\.com/(?:file/d/|open\?id=|uc\?(?:[^#]*&)?id=)([A-Za-z0-9_-]+)~i', $url, $m)) {
        return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
    }
    return null;
}

function isVideoUrl($video) {
    return (bool)preg_match('#^https?://#i', trim((string)$video));
}

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
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>404 - Halaman Tidak Ditemukan</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
        echo '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">';
        echo '<style>body{font-family:"Plus Jakarta Sans",sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{text-align:center;padding:3rem}.code{font-size:6rem;font-weight:800;color:#0B2545;line-height:1}.msg{font-size:1.1rem;color:#6c757d;margin:1rem 0 2rem}</style>';
        echo '</head><body><div class="box"><div class="code">404</div><div class="msg">Halaman ini tidak ada.</div>';
        echo '<a href="' . BASE_URL . '/" class="btn btn-dark px-4">Kembali ke Beranda</a></div></body></html>';
        exit;
    }
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getLoggedInUser() {
    if (!isUserLoggedIn()) return null;
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, username, email, phone, full_name, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function requireUserLogin($returnUrl = null) {
    if (!isUserLoggedIn()) {
        $return = $returnUrl ?: ($_SERVER['REQUEST_URI'] ?? '/');
        $_SESSION['login_redirect'] = $return;
        setFlash('warning', 'Silakan login terlebih dahulu untuk melanjutkan.');
        header('Location: ' . BASE_URL . '/user-login.php');
        exit;
    }
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// ===== SMTP EMAIL CONFIG =====
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 465);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM_NAME', 'DNA VACATION');

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function sendMail($to, $subject, $htmlBody) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $htmlBody));

        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

function sendVerificationEmail($email, $token) {
    $link = BASE_URL . '/verify-email.php?token=' . urlencode($token);
    $siteName = getSetting('site_name') ?: SITE_NAME;
    $subject = "Verifikasi Email Anda - {$siteName}";
    $body  = "<h2>Halo!</h2>";
    $body .= "<p>Terima kasih telah mendaftar di <strong>{$siteName}</strong>.</p>";
    $body .= "<p>Untuk mengaktifkan akun Anda, silakan klik tombol di bawah ini:</p>";
    $body .= "<p><a href=\"{$link}\" style=\"display:inline-block;padding:12px 24px;background:#0B2545;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold\">Verifikasi Email</a></p>";
    $body .= "<p>Atau copy-paste link berikut:<br><a href=\"{$link}\">{$link}</a></p>";
    $body .= "<p>Link ini berlaku selama 24 jam.</p>";
    $body .= "<p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>";
    $body .= "<p>Salam,<br>{$siteName}</p>";
    return sendMail($email, $subject, $body);
}

function sendPasswordResetEmail($email, $token) {
    $link = BASE_URL . '/reset-password.php?token=' . urlencode($token);
    $siteName = getSetting('site_name') ?: SITE_NAME;
    $subject = "Reset Password - {$siteName}";
    $body  = "<h2>Reset Password</h2>";
    $body .= "<p>Kami menerima permintaan untuk mereset password akun Anda di <strong>{$siteName}</strong>.</p>";
    $body .= "<p>Klik tombol di bawah ini untuk membuat password baru:</p>";
    $body .= "<p><a href=\"{$link}\" style=\"display:inline-block;padding:12px 24px;background:#0B2545;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold\">Reset Password</a></p>";
    $body .= "<p>Atau copy-paste link berikut:<br><a href=\"{$link}\">{$link}</a></p>";
    $body .= "<p>Link ini berlaku selama 1 jam.</p>";
    $body .= "<p>Jika Anda tidak meminta reset password, abaikan email ini.</p>";
    $body .= "<p>Salam,<br>{$siteName}</p>";
    return sendMail($email, $subject, $body);
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

<?php
$pageTitle = 'Verifikasi Email';
require_once 'includes/config.php';

$token = $_GET['token'] ?? '';
$verified = false;
$error = '';

if ($token) {
    $stmt = $pdo->prepare("SELECT id, email, is_verified, verification_expires FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'Link verifikasi tidak valid atau sudah digunakan.';
    } elseif ($user['is_verified']) {
        $error = 'Akun Anda sudah terverifikasi sebelumnya. Silakan login.';
    } elseif (strtotime($user['verification_expires']) < time()) {
        $error = 'Link verifikasi sudah kedaluwarsa. Silakan daftar ulang.';
    } else {
        $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, verification_expires = NULL WHERE id = ?")
            ->execute([$user['id']]);
        $verified = true;
    }
} else {
    $error = 'Token verifikasi tidak ditemukan.';
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Verifikasi Email</h1>
        <p>Aktivasi akun DNA Vacation Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="auth-card text-center">
                    <?php if ($verified): ?>
                    <div class="mb-3">
                        <i class="bi bi-check-circle" style="font-size:4rem;color:#198754"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Email Terverifikasi!</h4>
                    <p class="text-muted mb-4">Akun Anda telah berhasil diaktifkan. Silakan login untuk mulai menggunakan layanan DNA Vacation.</p>
                    <a href="<?= BASE_URL ?>/user-login.php" class="btn btn-primary-custom btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Login Sekarang
                    </a>
                    <?php else: ?>
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle" style="font-size:4rem;color:#dc3545"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Verifikasi Gagal</h4>
                    <p class="text-muted mb-4"><?= e($error) ?></p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary-custom">
                            <i class="bi bi-person-plus"></i> Daftar Ulang
                        </a>
                        <a href="<?= BASE_URL ?>/user-login.php" class="btn btn-outline-primary-custom">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<?php
$pageTitle = 'Lupa Password';
require_once 'includes/config.php';

if (isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/account.php');
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot'])) {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $error = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if (!$user['is_verified']) {
                $error = 'Akun belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu.';
            } else {
                $token = generateToken();
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?")
                    ->execute([$token, $expires, $user['id']]);

                $sent = sendPasswordResetEmail($email, $token);
                if (!$sent) {
                    $error = 'Gagal mengirim email. Silakan coba lagi nanti.';
                } else {
                    $success = true;
                }
            }
        } else {
            $success = true;
        }
    }
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Lupa Password</h1>
        <p>Reset password akun Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <?php if ($success): ?>
                <div class="auth-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-envelope-paper" style="font-size:4rem;color:var(--primary)"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Cek Email Anda</h4>
                    <p class="text-muted mb-3">
                        Jika email terdaftar di sistem kami, kami telah mengirim link untuk mereset password.
                        Link berlaku selama 1 jam.
                    </p>
                    <div class="alert alert-info small text-start">
                        <i class="bi bi-info-circle-fill"></i>
                        Cek folder <strong>Spam/Junk</strong> jika email tidak ditemukan di inbox.
                    </div>
                    <a href="<?= BASE_URL ?>/user-login.php" class="btn btn-primary-custom mt-3">
                        <i class="bi bi-box-arrow-in-right"></i> Kembali ke Login
                    </a>
                </div>
                <?php else: ?>
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-key" style="font-size:3rem;color:var(--primary)"></i>
                        <h4 class="fw-bold mb-1 mt-2">Lupa Password?</h4>
                        <p class="text-muted small mb-0">Masukkan email Anda untuk menerima link reset password</p>
                    </div>

                    <?php if ($error): ?>
                    <div class="alert alert-danger small">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="forgot" value="1">
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Email Akun Anda</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg mb-3">
                            <i class="bi bi-send"></i> Kirim Link Reset
                        </button>
                    </form>
                    <div class="text-center">
                        <a href="<?= BASE_URL ?>/user-login.php" class="small text-decoration-none" style="color:var(--primary)">
                            <i class="bi bi-arrow-left"></i> Kembali ke Login
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

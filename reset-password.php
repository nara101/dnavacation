<?php
$pageTitle = 'Reset Password';
require_once 'includes/config.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$error = '';
$success = false;
$validToken = false;

if ($token) {
    $stmt = $pdo->prepare("SELECT id, email, reset_expires FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'Link reset password tidak valid atau sudah digunakan.';
    } elseif (strtotime($user['reset_expires']) < time()) {
        $error = 'Link reset password sudah kedaluwarsa. Silakan request ulang.';
    } else {
        $validToken = true;
    }
} else {
    $error = 'Token reset tidak ditemukan.';
}

if ($validToken && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$password) {
        $error = 'Password baru wajib diisi.';
        $validToken = true;
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
        $validToken = true;
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
        $validToken = true;
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?")
            ->execute([$hashedPassword, $user['id']]);
        $success = true;
        $validToken = false;
    }
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Reset Password</h1>
        <p>Buat password baru untuk akun Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <?php if ($success): ?>
                <div class="auth-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-check-circle" style="font-size:4rem;color:#198754"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Password Berhasil Direset!</h4>
                    <p class="text-muted mb-4">Password Anda telah diperbarui. Silakan login dengan password baru Anda.</p>
                    <a href="<?= BASE_URL ?>/user-login.php" class="btn btn-primary-custom btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Login Sekarang
                    </a>
                </div>
                <?php elseif ($validToken): ?>
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock" style="font-size:3rem;color:var(--primary)"></i>
                        <h4 class="fw-bold mb-1 mt-2">Buat Password Baru</h4>
                        <p class="text-muted small mb-0">Masukkan password baru untuk akun <strong><?= e($user['email']) ?></strong></p>
                    </div>

                    <?php if ($error): ?>
                    <div class="alert alert-danger small">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="reset" value="1">
                        <input type="hidden" name="token" value="<?= e($token) ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="newPassword" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('newPassword', this)"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="confirm_password" id="confirmNewPassword" class="form-control" placeholder="Ketik ulang password baru" required minlength="6">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('confirmNewPassword', this)"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg">
                            <i class="bi bi-check-lg"></i> Simpan Password Baru
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="auth-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle" style="font-size:4rem;color:#dc3545"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Link Tidak Valid</h4>
                    <p class="text-muted mb-4"><?= e($error) ?></p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?= BASE_URL ?>/forgot-password.php" class="btn btn-primary-custom">
                            <i class="bi bi-arrow-repeat"></i> Request Ulang
                        </a>
                        <a href="<?= BASE_URL ?>/user-login.php" class="btn btn-outline-primary-custom">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function togglePass(id, btn) {
    const inp = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

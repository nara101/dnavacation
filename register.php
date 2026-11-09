<?php
$pageTitle = 'Daftar Akun';
require_once 'includes/config.php';

if (isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/account.php');
    exit;
}

$errors = [];
$success = false;
$registeredEmail = '';

// Resend verification email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_email'])) {
    $resendTo = trim($_POST['resend_email']);
    $stmt = $pdo->prepare("SELECT id, email, is_verified, verification_token FROM users WHERE email = ?");
    $stmt->execute([$resendTo]);
    $user = $stmt->fetch();
    if ($user && !$user['is_verified']) {
        $token = generateToken();
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $pdo->prepare("UPDATE users SET verification_token = ?, verification_expires = ? WHERE id = ?")
            ->execute([$token, $expires, $user['id']]);
        sendVerificationEmail($resendTo, $token);
        setFlash('success', 'Email verifikasi telah dikirim ulang. Cek inbox atau folder Spam Anda.');
    }
    $success = true;
    $registeredEmail = $resendTo;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$username) $errors[] = 'Username wajib diisi.';
    elseif (strlen($username) < 3) $errors[] = 'Username minimal 3 karakter.';
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) $errors[] = 'Username hanya boleh huruf, angka, dan underscore.';

    if (!$email) $errors[] = 'Email wajib diisi.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';

    if (!$phone) $errors[] = 'Nomor telepon wajib diisi.';

    if (!$password) $errors[] = 'Password wajib diisi.';
    elseif (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if ($password !== $confirmPassword) $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->execute([$email, $username]);
        if ($check->fetch()) {
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkEmail->execute([$email]);
            if ($checkEmail->fetch()) {
                $errors[] = 'Email sudah terdaftar. Silakan login atau gunakan email lain.';
            } else {
                $errors[] = 'Username sudah digunakan. Silakan pilih username lain.';
            }
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $token = generateToken();
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, verification_token, verification_expires) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$username, $email, $phone, $hashedPassword, $token, $expires]);

        sendVerificationEmail($email, $token);
        $registeredEmail = $email;
        $success = true;
    }
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Daftar Akun</h1>
        <p>Buat akun untuk memesan layanan DNA Vacation</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <?php if ($success): ?>
                <?php $flash = getFlash(); if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'info' ?> small">
                    <i class="bi bi-check-circle-fill"></i> <?= e($flash['message']) ?>
                </div>
                <?php endif; ?>
                <div class="auth-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-envelope-check" style="font-size:4rem;color:var(--primary)"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Cek Email Anda!</h4>
                    <p class="text-muted mb-3">
                        Kami telah mengirim link verifikasi ke <strong><?= e($registeredEmail) ?></strong>.
                        Silakan buka email dan klik link verifikasi untuk mengaktifkan akun Anda.
                    </p>
                    <div class="alert alert-info small text-start">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>Tips:</strong> Cek folder <strong>Spam/Junk</strong> jika email tidak ditemukan di inbox.
                        Link verifikasi berlaku selama 24 jam.
                    </div>
                    <div class="mt-4 d-flex gap-2 justify-content-center flex-wrap">
                        <a href="<?= BASE_URL ?>/user-login.php" class="btn btn-primary-custom">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="resend_email" value="<?= e($registeredEmail) ?>">
                            <button type="submit" class="btn btn-outline-primary-custom">
                                <i class="bi bi-arrow-repeat"></i> Kirim Ulang Email
                            </button>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <img src="<?= e(logoUrl()) ?>" alt="<?= e(getSetting('site_name') ?: SITE_NAME) ?>" style="width:60px;height:60px;border-radius:12px;object-fit:cover" class="mb-2">
                        <h4 class="fw-bold mb-1">Buat Akun Baru</h4>
                        <p class="text-muted small mb-0">Daftar untuk memesan tour, hotel, dan layanan lainnya</p>
                    </div>

                    <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3"><?php foreach ($errors as $err): ?><li class="small"><?= e($err) ?></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="register" value="1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="contoh: john_doe" required minlength="3" pattern="[a-zA-Z0-9_]+" value="<?= e($_POST['username'] ?? '') ?>">
                            </div>
                            <small class="text-muted">Huruf, angka, dan underscore. Minimal 3 karakter.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="tel" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required value="<?= e($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="regPassword" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('regPassword', this)"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="confirm_password" id="regConfirmPassword" class="form-control" placeholder="Ketik ulang password" required minlength="6">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('regConfirmPassword', this)"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg mb-3">
                            <i class="bi bi-person-plus"></i> Daftar Sekarang
                        </button>
                    </form>
                    <div class="text-center">
                        <p class="small text-muted mb-0">Sudah punya akun? <a href="<?= BASE_URL ?>/user-login.php" class="fw-semibold text-decoration-none" style="color:var(--primary)">Login di sini</a></p>
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

<?php
$pageTitle = 'Login';
require_once 'includes/config.php';

if (isUserLoggedIn()) {
    if (isLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/');
    } else {
        header('Location: ' . BASE_URL . '/account.php');
    }
    exit;
}

$error = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Email atau password salah.';
        } elseif (!$user['is_verified']) {
            $error = 'Akun Anda belum diverifikasi. Silakan cek email Anda untuk link verifikasi.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';

            if (($user['role'] ?? 'user') === 'admin') {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['full_name'] ?: $user['username'];
                $_SESSION['admin_role'] = 'admin';
                header('Location: ' . BASE_URL . '/admin/');
                exit;
            }

            $redirect = $_SESSION['login_redirect'] ?? (BASE_URL . '/');
            unset($_SESSION['login_redirect']);
            header('Location: ' . $redirect);
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Login Akun</h1>
        <p>Masuk ke akun DNA Vacation Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <img src="<?= e(logoUrl()) ?>" alt="<?= e(getSetting('site_name') ?: SITE_NAME) ?>" style="width:60px;height:60px;border-radius:12px;object-fit:cover" class="mb-2">
                        <h4 class="fw-bold mb-1">Selamat Datang!</h4>
                        <p class="text-muted small mb-0">Login untuk memesan layanan DNA Vacation</p>
                    </div>

                    <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] === 'warning' ? 'warning' : ($flash['type'] === 'success' ? 'success' : 'info') ?> small">
                        <i class="bi bi-<?= $flash['type'] === 'warning' ? 'exclamation-triangle-fill' : ($flash['type'] === 'success' ? 'check-circle-fill' : 'info-circle-fill') ?>"></i>
                        <?= e($flash['message']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                    <div class="alert alert-danger small">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="login" value="1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Masukkan password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('loginPassword', this)"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <a href="<?= BASE_URL ?>/forgot-password.php" class="small text-decoration-none" style="color:var(--primary)">Lupa Password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                    <div class="text-center">
                        <p class="small text-muted mb-0">Belum punya akun? <a href="<?= BASE_URL ?>/register.php" class="fw-semibold text-decoration-none" style="color:var(--primary)">Daftar di sini</a></p>
                    </div>
                </div>
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

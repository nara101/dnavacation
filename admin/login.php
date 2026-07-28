<?php
require_once __DIR__ . '/../includes/config.php';

if (isLoggedIn()) { header('Location: ' . ADMIN_URL . '/'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['full_name'];
        $_SESSION['admin_role'] = $user['role'];
        header('Location: ' . ADMIN_URL . '/');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - DNA Vacation</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('img/logo-dna.jpg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body class="admin-login-page">
    <div class="admin-login-card">
        <div class="text-center mb-4">
            <img src="<?= asset('img/logo-dna.jpg') ?>" alt="DNA Vacation" style="width:80px;height:80px;border-radius:16px;object-fit:cover;box-shadow:0 8px 20px rgba(11,37,69,.25)">
            <h4 class="fw-bold mb-1 mt-3"><span class="text-primary-custom">DNA</span> Vacation</h4>
            <p class="text-muted small mb-0" style="font-family:var(--font-script);color:var(--gold-dark)!important">Admin Panel</p>
        </div>
        <?php if ($error): ?>
        <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 btn-lg"><i class="bi bi-box-arrow-in-right"></i> Login</button>
        </form>
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Website</a>
        </div>
        <div class="mt-4 p-2 rounded-3 text-center small" style="background:var(--primary-lighter);color:var(--primary-dark)">
            <i class="bi bi-info-circle"></i> Default: <strong>admin</strong> / <strong>admin123</strong>
        </div>
    </div>
</body>
</html>

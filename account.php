<?php
$pageTitle = 'Akun Saya';
require_once 'includes/config.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/user-login.php');
    exit;
}

$user = getLoggedInUser();
if (!$user) {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
    header('Location: ' . BASE_URL . '/user-login.php');
    exit;
}

$flash = getFlash();
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$phone) {
        $error = 'Nomor telepon wajib diisi.';
    } else {
        $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?")
            ->execute([$fullName, $phone, $user['id']]);

        if ($newPassword) {
            $fullUser = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $fullUser->execute([$user['id']]);
            $userData = $fullUser->fetch();

            if (!password_verify($currentPassword, $userData['password'])) {
                $error = 'Password lama tidak sesuai.';
            } elseif (strlen($newPassword) < 6) {
                $error = 'Password baru minimal 6 karakter.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Konfirmasi password baru tidak cocok.';
            } else {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                    ->execute([$hashed, $user['id']]);
            }
        }

        if (!$error) {
            $success = true;
            $user = getLoggedInUser();
        }
    }
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Akun Saya</h1>
        <p>Kelola profil dan lihat riwayat pesanan Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="auth-card text-center">
                    <div class="user-avatar-large mb-3">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                    <h5 class="fw-bold mb-1"><?= e($user['full_name'] ?: $user['username']) ?></h5>
                    <p class="text-muted small mb-1">@<?= e($user['username']) ?></p>
                    <p class="text-muted small mb-3"><?= e($user['email']) ?></p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Terverifikasi</span>
                        <span class="badge bg-light text-muted border">Member sejak <?= date('M Y', strtotime($user['created_at'])) ?></span>
                    </div>
                    <hr>
                    <a href="<?= BASE_URL ?>/user-logout.php" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="auth-card">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-gear text-primary-custom"></i> Edit Profil</h5>

                    <?php if ($success): ?>
                    <div class="alert alert-success small"><i class="bi bi-check-circle-fill"></i> Profil berhasil diperbarui.</div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                    <div class="alert alert-danger small"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Username</label>
                                <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                                <small class="text-muted">Username tidak dapat diubah</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                                <small class="text-muted">Email tidak dapat diubah</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name'] ?? '') ?>" placeholder="Nama lengkap Anda">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock text-primary-custom"></i> Ubah Password <small class="text-muted fw-normal">(opsional)</small></h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Password Lama</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Password saat ini">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Password Baru</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Min 6 karakter">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Konfirmasi</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Ketik ulang">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom mt-4">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>

                <div class="auth-card mt-4 text-center">
                    <a href="<?= BASE_URL ?>/order-history.php" class="btn btn-outline-primary-custom">
                        <i class="bi bi-clock-history"></i> Lihat Riwayat Pemesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

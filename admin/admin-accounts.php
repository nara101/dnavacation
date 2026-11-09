<?php
$pageTitle = 'Kelola Akun Admin';
require_once __DIR__ . '/includes/header.php';

$errors = [];
$success = '';

// Delete admin
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if ($deleteId === (int)$_SESSION['user_id']) {
        setFlash('danger', 'Tidak bisa menghapus akun Anda sendiri.');
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin'");
        $check->execute([$deleteId]);
        if ($check->fetch()) {
            $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?")->execute([$deleteId]);
            setFlash('success', 'Akun admin berhasil dihapus. Akun tetap ada sebagai user biasa.');
        }
    }
    header('Location: admin-accounts.php');
    exit;
}

// Add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $fullName  = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (strlen($username) < 3) $errors[] = 'Username minimal 3 karakter.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $errors[] = 'Email atau username sudah digunakan.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, full_name, role, is_verified) VALUES (?, ?, ?, ?, ?, 'admin', 1)");
        $stmt->execute([$username, $email, $phone, password_hash($password, PASSWORD_DEFAULT), $fullName ?: null]);
        setFlash('success', 'Akun admin baru berhasil ditambahkan.');
        header('Location: admin-accounts.php');
        exit;
    }
}

$admins = $pdo->query("SELECT id, username, email, full_name, phone, created_at FROM users WHERE role = 'admin' ORDER BY created_at")->fetchAll();
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary-custom"></i> Kelola Akun Admin</h4>
            <p class="text-muted small mb-0">Tambah atau hapus akun admin. Admin login melalui halaman login utama website.</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 small">
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Admin list -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-shield-check"></i> Daftar Akun Admin (<?= count($admins) ?>)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Dibuat</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $a): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="user-avatar-sm" style="width:32px;height:32px;font-size:.75rem"><?= strtoupper(substr($a['full_name'] ?: $a['username'], 0, 1)) ?></span>
                                            <div>
                                                <div class="fw-semibold small"><?= e($a['full_name'] ?: $a['username']) ?></div>
                                                <?php if ($a['id'] == $_SESSION['user_id']): ?>
                                                    <span class="badge bg-primary" style="font-size:.65rem">Anda</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small text-muted"><?= e($a['email']) ?></td>
                                    <td><code class="small"><?= e($a['username']) ?></code></td>
                                    <td class="small text-muted"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                    <td>
                                        <?php if ($a['id'] != $_SESSION['user_id']): ?>
                                        <a href="admin-accounts.php?delete=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus hak admin dari akun ini? Akun tetap ada sebagai user biasa.')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add admin form -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-person-plus-fill"></i> Tambah Admin Baru
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="add_admin" value="1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control" value="<?= e($_POST['full_name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required value="<?= e($_POST['username'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                            <div class="form-text">Minimal 6 karakter</div>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="bi bi-plus-lg"></i> Tambah Admin
                        </button>
                    </form>
                    <div class="mt-3 p-2 rounded-3 small" style="background:var(--primary-lighter, #e8eef4);color:var(--primary-dark, #0B2545)">
                        <i class="bi bi-info-circle"></i> Akun admin yang ditambahkan bisa langsung login melalui halaman login utama website. Tidak perlu verifikasi email.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

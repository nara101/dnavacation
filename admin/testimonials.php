<?php
$pageTitle = 'Testimoni';
require_once __DIR__ . '/includes/header.php';

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM testimonials WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Testimoni dihapus.');
    header('Location: testimonials.php');
    exit;
}

// Approve
if (isset($_GET['approve'])) {
    $pdo->prepare("UPDATE testimonials SET is_active=1 WHERE id = ?")->execute([(int)$_GET['approve']]);
    setFlash('success', 'Testimoni disetujui & ditampilkan di website.');
    header('Location: testimonials.php');
    exit;
}

// Toggle
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE id = ?")->execute([(int)$_GET['toggle']]);
    setFlash('success', 'Status testimoni diperbarui.');
    header('Location: testimonials.php');
    exit;
}

// Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'customer_name' => trim($_POST['customer_name']),
        'customer_location' => trim($_POST['customer_location']),
        'tour_id' => !empty($_POST['tour_id']) ? (int)$_POST['tour_id'] : null,
        'tour_name' => trim($_POST['tour_name']),
        'content' => trim($_POST['content']),
        'rating' => max(1, min(5, (int)$_POST['rating'])),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if (!empty($_FILES['image']['name'])) {
        $img = uploadImage($_FILES['image'], 'testimonials');
        if ($img) $data['image'] = $img;
    }

    $editId = (int)($_POST['edit_id'] ?? 0);
    if ($editId) {
        $fields = [];
        $values = [];
        foreach ($data as $k => $v) {
            $fields[] = "`$k` = ?";
            $values[] = $v;
        }
        $values[] = $editId;
        $pdo->prepare("UPDATE testimonials SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
        setFlash('success', 'Testimoni diperbarui.');
    } else {
        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $pdo->prepare("INSERT INTO testimonials ($fields) VALUES ($placeholders)")->execute(array_values($data));
        setFlash('success', 'Testimoni ditambahkan.');
    }
    header('Location: testimonials.php');
    exit;
}

// Filter
$filter = $_GET['filter'] ?? '';
if ($filter === 'pending') {
    $testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active=0 ORDER BY created_at DESC")->fetchAll();
} elseif ($filter === 'active') {
    $testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY created_at DESC")->fetchAll();
} else {
    $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY is_active ASC, created_at DESC")->fetchAll();
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}

$activeTours = $pdo->query("SELECT id, title FROM tours ORDER BY title")->fetchAll();
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=0")->fetchColumn();
?>

<div class="p-3 p-md-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-chat-quote text-primary-custom"></i> Testimoni</h4>

    <?php if ($pendingCount > 0): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i> Ada <strong><?= $pendingCount ?></strong> testimoni baru yang menunggu persetujuan.
    </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold"><?= $editItem ? 'Edit' : 'Tambah' ?> Testimoni</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editItem): ?>
                    <input type="hidden" name="edit_id" value="<?= $editItem['id'] ?>">
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control" value="<?= e($editItem['customer_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kota Asal</label>
                        <input type="text" name="customer_location" class="form-control" value="<?= e($editItem['customer_location'] ?? '') ?>" placeholder="Jakarta">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Paket Tour</label>
                        <select name="tour_id" class="form-select" onchange="if(this.value) document.querySelector('[name=tour_name]').value = this.options[this.selectedIndex].text; else document.querySelector('[name=tour_name]').value = '';">
                            <option value="">-- Pilih atau isi manual --</option>
                            <?php foreach ($activeTours as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($editItem['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= e($t['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Label Trip (custom)</label>
                        <input type="text" name="tour_name" class="form-control" value="<?= e($editItem['tour_name'] ?? '') ?>" placeholder="Contoh: Rental Innova, Booking Hotel">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Rating</label>
                        <select name="rating" class="form-select">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>" <?= ($editItem['rating'] ?? 5) == $i ? 'selected' : '' ?>><?= str_repeat('⭐', $i) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Foto</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="tActive" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tActive">Tampil</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Isi Testimoni <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="3" placeholder="Isi testimoni..." required><?= e($editItem['content'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                        <?php if ($editItem): ?>
                            <a href="testimonials.php" class="btn btn-outline-secondary">Batal Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter tabs -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="testimonials.php" class="btn btn-sm <?= !$filter ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
        <a href="?filter=pending" class="btn btn-sm <?= $filter === 'pending' ? 'btn-primary' : 'btn-outline-secondary' ?>">Menunggu <?= $pendingCount ? "($pendingCount)" : '' ?></a>
        <a href="?filter=active" class="btn btn-sm <?= $filter === 'active' ? 'btn-primary' : 'btn-outline-secondary' ?>">Aktif Tampil</a>
    </div>

    <!-- List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Trip</th>
                            <th>Rating</th>
                            <th>Isi Testimoni</th>
                            <th>Status</th>
                            <th style="width:140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($testimonials)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada testimoni.</td></tr>
                        <?php else: ?>
                            <?php foreach ($testimonials as $t): ?>
                                <tr>
                                    <td>
                                        <strong class="small"><?= e($t['customer_name']) ?></strong>
                                        <br><small class="text-muted"><?= e($t['customer_location'] ?: '-') ?></small>
                                    </td>
                                    <td class="small"><?= e($t['tour_name'] ?: '-') ?></td>
                                    <td class="text-warning small"><?= str_repeat('★', $t['rating']) ?><?= str_repeat('☆', 5 - $t['rating']) ?></td>
                                    <td class="small" style="max-width:300px"><?= e(truncate($t['content'], 100)) ?></td>
                                    <td>
                                        <?php if ($t['is_active']): ?>
                                            <a href="?toggle=<?= $t['id'] ?>" class="badge bg-success text-decoration-none" style="font-size:.7rem">Tampil</a>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark" style="font-size:.7rem">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$t['is_active']): ?>
                                            <a href="?approve=<?= $t['id'] ?>" class="btn btn-sm btn-success" title="Setujui"><i class="bi bi-check-lg"></i></a>
                                        <?php else: ?>
                                            <a href="?toggle=<?= $t['id'] ?>" class="btn btn-sm btn-outline-warning" title="Sembunyikan"><i class="bi bi-eye-slash"></i></a>
                                        <?php endif; ?>
                                        <a href="?edit=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus testimoni?"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

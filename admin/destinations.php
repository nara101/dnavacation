<?php
$pageTitle = 'Destinasi';
require_once __DIR__ . '/includes/header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM destinations WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Destinasi dihapus.');
    header('Location: destinations.php');
    exit;
}

if (isset($_GET['toggle_pop'])) {
    $pdo->prepare("UPDATE destinations SET is_popular = NOT is_popular WHERE id = ?")->execute([(int)$_GET['toggle_pop']]);
    header('Location: destinations.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $data = [
        'name' => $name,
        'slug' => slugify($name),
        'description' => trim($_POST['description']),
        'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
    ];

    if (!empty($_FILES['image']['name'])) {
        $img = uploadImage($_FILES['image'], 'destinations');
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
        $pdo->prepare("UPDATE destinations SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
        setFlash('success', 'Destinasi diperbarui.');
    } else {
        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $pdo->prepare("INSERT INTO destinations ($fields) VALUES ($placeholders)")->execute(array_values($data));
        setFlash('success', 'Destinasi ditambahkan.');
    }
    header('Location: destinations.php');
    exit;
}

$destinations = $pdo->query("SELECT * FROM destinations ORDER BY is_popular DESC, name")->fetchAll();
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}
?>

<div class="p-3 p-md-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-geo-alt text-primary-custom"></i> Destinasi</h4>

    <!-- Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold"><?= $editItem ? 'Edit' : 'Tambah' ?> Destinasi</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editItem): ?><input type="hidden" name="edit_id" value="<?= $editItem['id'] ?>"><?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= e($editItem['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Deskripsi Singkat</label>
                        <input type="text" name="description" class="form-control" value="<?= e($editItem['description'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Foto</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_popular" class="form-check-input" id="isPop" <?= ($editItem['is_popular'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isPop">Populer</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                        <?php if ($editItem): ?><a href="destinations.php" class="btn btn-outline-secondary">Batal</a><?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:70px">Foto</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Populer</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($destinations)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada destinasi.</td></tr>
                        <?php else: ?>
                            <?php foreach ($destinations as $d): ?>
                                <tr>
                                    <td>
                                        <?php if ($d['image']): ?>
                                            <img src="<?= uploadUrl($d['image']) ?>" class="rounded" style="width:56px;height:44px;object-fit:cover" onerror="handleImgError(this)">
                                        <?php else: ?>
                                            <div class="placeholder-img rounded" style="width:56px;height:44px;font-size:1rem"><i class="bi bi-geo-alt"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong class="small"><?= e($d['name']) ?></strong></td>
                                    <td class="small text-muted"><?= e(truncate($d['description'] ?? '', 80)) ?></td>
                                    <td>
                                        <a href="?toggle_pop=<?= $d['id'] ?>" class="badge bg-<?= $d['is_popular'] ? 'success' : 'secondary' ?> text-decoration-none" style="font-size:.7rem">
                                            <?= $d['is_popular'] ? 'Populer' : 'Standard' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="?edit=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus destinasi?"><i class="bi bi-trash"></i></a>
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

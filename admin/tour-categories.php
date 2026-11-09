<?php
$pageTitle = 'Jenis Paket Wisata';
require_once __DIR__ . '/includes/header.php';

// Delete (FK ON DELETE SET NULL handles related tours)
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM tour_categories WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Jenis paket wisata dihapus. Paket yang terkait akan tanpa kategori.');
    header('Location: tour-categories.php');
    exit;
}

// Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = (int)($_POST['edit_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$name) {
        setFlash('danger', 'Nama jenis paket wajib diisi.');
    } else {
        $slug = slugify($name);
        try {
            if ($editId) {
                $pdo->prepare("UPDATE tour_categories SET name=?, slug=?, description=? WHERE id=?")
                    ->execute([$name, $slug, $description, $editId]);
                setFlash('success', 'Jenis paket diperbarui.');
            } else {
                $pdo->prepare("INSERT INTO tour_categories (name, slug, description) VALUES (?,?,?)")
                    ->execute([$name, $slug, $description]);
                setFlash('success', 'Jenis paket ditambahkan.');
            }
        } catch (PDOException $ex) {
            setFlash('danger', 'Gagal menyimpan: nama/slug sudah ada.');
        }
    }
    header('Location: tour-categories.php');
    exit;
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM tours WHERE category_id=c.id) as tour_count FROM tour_categories c ORDER BY c.name")->fetchAll();

$editItem = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM tour_categories WHERE id = ?");
    $s->execute([(int)$_GET['edit']]);
    $editItem = $s->fetch();
}
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-tags text-primary-custom"></i> Jenis Paket Wisata</h4>
            <p class="text-muted small mb-0">Kategori paket tour (Open Trip, Private Trip, Family, dll). Muncul di filter paket tour user.</p>
        </div>
        <span class="badge bg-primary" style="font-size:.85rem"><?= count($categories) ?> jenis</span>
    </div>

    <!-- Add/Edit form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-<?= $editItem ? 'pencil' : 'plus-lg' ?>"></i> <?= $editItem ? 'Edit' : 'Tambah' ?> Jenis Paket</div>
        <div class="card-body">
            <form method="POST">
                <?php if ($editItem): ?><input type="hidden" name="edit_id" value="<?= $editItem['id'] ?>"><?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= e($editItem['name'] ?? '') ?>" required placeholder="Contoh: Open Trip, Honeymoon">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Deskripsi (opsional)</label>
                        <input type="text" name="description" class="form-control" value="<?= e($editItem['description'] ?? '') ?>" placeholder="Deskripsi singkat kategori">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                        <?php if ($editItem): ?><a href="tour-categories.php" class="btn btn-outline-secondary">Batal Edit</a><?php endif; ?>
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
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th>Paket Terkait</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada jenis paket wisata.</td></tr>
                        <?php else: ?>
                            <?php foreach ($categories as $c): ?>
                                <tr>
                                    <td class="small fw-semibold"><?= e($c['name']) ?></td>
                                    <td class="small"><code><?= e($c['slug']) ?></code></td>
                                    <td class="small text-muted"><?= e(truncate($c['description'] ?? '', 80)) ?></td>
                                    <td class="small"><span class="badge bg-light text-primary-custom border"><?= $c['tour_count'] ?> paket</span></td>
                                    <td>
                                        <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus jenis paket ini? Paket yang terkait akan tanpa kategori."><i class="bi bi-trash"></i></a>
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

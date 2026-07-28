<?php
$pageTitle = 'Paket Tour';
require_once __DIR__ . '/includes/header.php';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM tours WHERE id = ?")->execute([$id]);
    setFlash('success', 'Paket tour berhasil dihapus.');
    header('Location: tours.php');
    exit;
}

// Toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE tours SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    setFlash('success', 'Status paket diperbarui.');
    header('Location: tours.php');
    exit;
}

// Toggle best seller
if (isset($_GET['best_seller'])) {
    $id = (int)$_GET['best_seller'];
    $pdo->prepare("UPDATE tours SET is_best_seller = NOT is_best_seller WHERE id = ?")->execute([$id]);
    setFlash('success', 'Status best seller diperbarui.');
    header('Location: tours.php');
    exit;
}

$tours = $pdo->query("SELECT t.*, d.name as dest_name, tc.name as cat_name FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id LEFT JOIN tour_categories tc ON t.category_id = tc.id ORDER BY t.created_at DESC")->fetchAll();
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-map text-primary-custom"></i> Paket Tour</h4>
        <a href="tour-form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Paket</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:70px">Foto</th>
                            <th>Nama Paket</th>
                            <th>Destinasi</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th style="width:150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tours)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada paket tour.</td></tr>
                        <?php else: ?>
                            <?php foreach ($tours as $t): ?>
                                <tr>
                                    <td>
                                        <?php if ($t['image']): ?>
                                            <img src="<?= uploadUrl($t['image']) ?>" class="rounded" style="width:56px;height:44px;object-fit:cover" onerror="handleImgError(this)">
                                        <?php else: ?>
                                            <div class="placeholder-img rounded" style="width:56px;height:44px;font-size:1rem"><i class="bi bi-image"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= e($t['title']) ?></strong>
                                        <div>
                                            <?php if ($t['is_best_seller']): ?><span class="badge bg-warning text-dark" style="font-size:.65rem">Best Seller</span><?php endif; ?>
                                            <?php if ($t['is_promo']): ?><span class="badge bg-danger" style="font-size:.65rem">Promo</span><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="small"><?= e($t['dest_name'] ?? '-') ?></td>
                                    <td class="small"><?= e($t['cat_name'] ?? '-') ?></td>
                                    <td class="fw-semibold small"><?= formatRupiah($t['price']) ?></td>
                                    <td class="small"><?= e($t['duration']) ?></td>
                                    <td class="small text-muted"><i class="bi bi-eye"></i> <?= $t['view_count'] ?></td>
                                    <td>
                                        <a href="?toggle=<?= $t['id'] ?>" class="badge bg-<?= $t['is_active'] ? 'success' : 'secondary' ?> text-decoration-none" style="font-size:.7rem">
                                            <?= $t['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="?best_seller=<?= $t['id'] ?>" class="btn btn-sm btn-outline-warning" title="<?= $t['is_best_seller'] ? 'Hapus dari' : 'Set jadi' ?> Best Seller"><i class="bi bi-star<?= $t['is_best_seller'] ? '-fill' : '' ?>"></i></a>
                                        <a href="tour-form.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus paket ini?" title="Hapus"><i class="bi bi-trash"></i></a>
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

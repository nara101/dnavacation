<?php
$pageTitle = 'Data Mobil';
require_once __DIR__ . '/includes/header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM cars WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Mobil berhasil dihapus.');
    header('Location: cars.php');
    exit;
}

if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE cars SET is_available = NOT is_available WHERE id = ?")->execute([(int)$_GET['toggle']]);
    setFlash('success', 'Status ketersediaan diperbarui.');
    header('Location: cars.php');
    exit;
}

if (isset($_GET['feature'])) {
    $pdo->prepare("UPDATE cars SET is_featured = NOT is_featured WHERE id = ?")->execute([(int)$_GET['feature']]);
    setFlash('success', 'Status featured diperbarui.');
    header('Location: cars.php');
    exit;
}

$cars = $pdo->query("SELECT * FROM cars ORDER BY is_featured DESC, created_at DESC")->fetchAll();
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-car-front text-primary-custom"></i> Data Mobil</h4>
        <a href="car-form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Mobil</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:70px">Foto</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Kapasitas</th>
                            <th>Harga + Sopir</th>
                            <th>Self Drive</th>
                            <th>Status</th>
                            <th style="width:140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cars)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data mobil.</td></tr>
                        <?php else: ?>
                            <?php foreach ($cars as $c): ?>
                                <tr>
                                    <td>
                                        <?php if ($c['image']): ?>
                                            <img src="<?= uploadUrl($c['image']) ?>" class="rounded" style="width:56px;height:44px;object-fit:cover" onerror="handleImgError(this)">
                                        <?php else: ?>
                                            <div class="placeholder-img rounded" style="width:56px;height:44px;font-size:1rem"><i class="bi bi-car-front"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="small"><?= e($c['name']) ?></strong>
                                        <?php if ($c['brand']): ?><br><small class="text-muted"><?= e($c['brand']) ?> · <?= $c['year'] ?></small><?php endif; ?>
                                        <?php if ($c['is_featured']): ?><span class="badge bg-warning text-dark ms-1" style="font-size:.65rem">Featured</span><?php endif; ?>
                                    </td>
                                    <td class="small"><?= strtoupper($c['type']) ?></td>
                                    <td class="small"><?= $c['capacity'] ?> org</td>
                                    <td class="small fw-semibold"><?= formatRupiah($c['price_with_driver']) ?></td>
                                    <td class="small"><?= $c['has_self_drive'] && $c['price_without_driver'] ? formatRupiah($c['price_without_driver']) : '-' ?></td>
                                    <td>
                                        <a href="?toggle=<?= $c['id'] ?>" class="badge bg-<?= $c['is_available'] ? 'success' : 'secondary' ?> text-decoration-none" style="font-size:.7rem">
                                            <?= $c['is_available'] ? 'Tersedia' : 'Off' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="?feature=<?= $c['id'] ?>" class="btn btn-sm btn-outline-warning" title="<?= $c['is_featured'] ? 'Hapus dari' : 'Set jadi' ?> Featured"><i class="bi bi-star<?= $c['is_featured'] ? '-fill' : '' ?>"></i></a>
                                        <a href="car-form.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus mobil ini?"><i class="bi bi-trash"></i></a>
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

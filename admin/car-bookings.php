<?php
$pageTitle = 'Booking Mobil';
require_once __DIR__ . '/includes/header.php';

if (isset($_POST['update_status'])) {
    $pdo->prepare("UPDATE car_bookings SET status = ? WHERE id = ?")->execute([$_POST['status'], (int)$_POST['booking_id']]);
    setFlash('success', 'Status booking diperbarui.');
    header('Location: car-bookings.php');
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM car_bookings WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Booking dihapus.');
    header('Location: car-bookings.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($statusFilter) { $where = 'WHERE cb.status = ?'; $params[] = $statusFilter; }

$stmt = $pdo->prepare("SELECT cb.*, c.name as car_name FROM car_bookings cb LEFT JOIN cars c ON cb.car_id = c.id $where ORDER BY cb.created_at DESC");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$statuses = ['baru'=>'Baru','diproses'=>'Diproses','menunggu_pembayaran'=>'Menunggu Bayar','terkonfirmasi'=>'Terkonfirmasi','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
?>

<div class="p-3 p-md-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-receipt text-primary-custom"></i> Booking Mobil</h4>

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="car-bookings.php" class="btn btn-sm <?= !$statusFilter ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
        <?php foreach ($statuses as $k => $v): ?>
            <a href="?status=<?= $k ?>" class="btn btn-sm <?= $statusFilter === $k ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $v ?></a>
        <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>WhatsApp</th>
                            <th>Mobil</th>
                            <th>Tanggal Sewa</th>
                            <th>Sopir</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data booking.</td></tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td><code><?= e($b['booking_code']) ?></code></td>
                                    <td class="small"><?= e($b['customer_name']) ?></td>
                                    <td><a href="https://wa.me/<?= e($b['customer_whatsapp']) ?>" target="_blank" class="text-success small"><i class="bi bi-whatsapp"></i> <?= e($b['customer_whatsapp']) ?></a></td>
                                    <td class="small"><?= e($b['car_name'] ?? '-') ?></td>
                                    <td class="small"><?= date('d/m/y', strtotime($b['start_date'])) ?> - <?= date('d/m/y', strtotime($b['end_date'])) ?></td>
                                    <td class="small"><?= $b['with_driver'] ? 'Ya' : 'Self' ?></td>
                                    <td class="small fw-semibold"><?= formatRupiah($b['total_price']) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-select-sm" style="width:auto;font-size:.75rem" onchange="this.form.submit()">
                                                <?php foreach ($statuses as $k => $v): ?>
                                                    <option value="<?= $k ?>" <?= $b['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/<?= e($b['customer_whatsapp']) ?>?text=<?= urlencode('Halo ' . $b['customer_name'] . ', terima kasih sudah booking mobil di DNA Vacation. Kode: ' . $b['booking_code']) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i></a>
                                        <a href="?delete=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus booking ini?"><i class="bi bi-trash"></i></a>
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

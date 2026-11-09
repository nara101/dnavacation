<?php
$pageTitle = 'Booking Tour';
require_once __DIR__ . '/includes/header.php';

// Update status
if (isset($_POST['update_status'])) {
    $pdo->prepare("UPDATE tour_bookings SET status = ? WHERE id = ?")->execute([$_POST['status'], (int)$_POST['booking_id']]);
    setFlash('success', 'Status booking diperbarui.');
    header('Location: bookings.php');
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM tour_bookings WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Booking dihapus.');
    header('Location: bookings.php');
    exit;
}

// Filter
$statusFilter = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($statusFilter) {
    $where = 'WHERE tb.status = ?';
    $params[] = $statusFilter;
}

$bookings = $pdo->prepare("SELECT tb.*, t.title as tour_name FROM tour_bookings tb LEFT JOIN tours t ON tb.tour_id = t.id $where ORDER BY tb.created_at DESC");
$bookings->execute($params);
$bookings = $bookings->fetchAll();

$statuses = [
    'baru' => 'Baru',
    'diproses' => 'Diproses',
    'menunggu_pembayaran' => 'Menunggu Bayar',
    'terkonfirmasi' => 'Terkonfirmasi',
    'selesai' => 'Selesai',
    'dibatalkan' => 'Dibatalkan'
];

$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewItem = null;
if ($viewId) {
    $s = $pdo->prepare("SELECT tb.*, t.title as tour_name FROM tour_bookings tb LEFT JOIN tours t ON tb.tour_id = t.id WHERE tb.id = ?");
    $s->execute([$viewId]);
    $viewItem = $s->fetch();
}
?>

<div class="p-3 p-md-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-journal-check text-primary-custom"></i> Booking Tour</h4>

    <!-- Filter -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="bookings.php" class="btn btn-sm <?= !$statusFilter ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
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
                            <th>Tour</th>
                            <th>Tanggal</th>
                            <th>Pax</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data booking.</td></tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td><code><?= e($b['booking_code']) ?></code></td>
                                    <td>
                                        <strong class="small"><?= e($b['customer_name']) ?></strong>
                                        <?php if ($b['customer_email']): ?><br><small class="text-muted"><?= e($b['customer_email']) ?></small><?php endif; ?>
                                    </td>
                                    <td><a href="https://wa.me/<?= e($b['customer_whatsapp']) ?>" target="_blank" class="text-success small"><i class="bi bi-whatsapp"></i> <?= e($b['customer_whatsapp']) ?></a></td>
                                    <td class="small"><?= e(truncate($b['tour_name'] ?? '-', 30)) ?></td>
                                    <td class="small"><?= $b['departure_date'] ? date('d M Y', strtotime($b['departure_date'])) : '-' ?></td>
                                    <td class="small"><?= $b['num_persons'] ?></td>
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
                                        <a href="?view=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                        <a href="https://wa.me/<?= e($b['customer_whatsapp']) ?>?text=<?= urlencode('Halo ' . $b['customer_name'] . ', terima kasih sudah booking di DNA Vacation. Booking Anda: ' . $b['booking_code']) ?>" target="_blank" class="btn btn-sm btn-success" title="Kirim WA"><i class="bi bi-whatsapp"></i></a>
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

<?php if ($viewItem): ?>
<div class="modal fade show d-block" id="viewModal" tabindex="-1" style="background:rgba(0,0,0,.5)">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-journal-check text-primary-custom"></i> Detail Booking Tour</h5>
                <a href="bookings.php" class="btn-close" aria-label="Close"></a>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong class="text-muted small text-uppercase">Data Pelanggan</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light">
                            <div><i class="bi bi-person"></i> <strong><?= e($viewItem['customer_name']) ?></strong></div>
                            <?php if ($viewItem['customer_email']): ?>
                            <div class="small mt-1"><i class="bi bi-envelope"></i> <a href="mailto:<?= e($viewItem['customer_email']) ?>"><?= e($viewItem['customer_email']) ?></a></div>
                            <?php endif; ?>
                            <div class="small mt-1"><i class="bi bi-whatsapp text-success"></i> <a href="https://wa.me/<?= e($viewItem['customer_whatsapp']) ?>" target="_blank"><?= e($viewItem['customer_whatsapp']) ?></a></div>
                            <div class="small mt-1 text-muted">Booking dibuat: <?= date('d M Y, H:i', strtotime($viewItem['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted small text-uppercase">Detail Booking</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light">
                            <div><i class="bi bi-hash"></i> Kode: <strong><?= e($viewItem['booking_code']) ?></strong></div>
                            <div class="small mt-1"><i class="bi bi-map"></i> Paket: <strong><?= e($viewItem['tour_name'] ?? '-') ?></strong></div>
                            <div class="small mt-1"><i class="bi bi-calendar"></i> Tanggal: <strong><?= $viewItem['departure_date'] ? date('d M Y', strtotime($viewItem['departure_date'])) : '-' ?></strong></div>
                            <div class="small mt-1"><i class="bi bi-people"></i> Peserta: <strong><?= $viewItem['num_persons'] ?> orang</strong></div>
                            <div class="small mt-1"><i class="bi bi-cash"></i> Total: <strong><?= formatRupiah($viewItem['total_price']) ?></strong></div>
                            <div class="small mt-1"><i class="bi bi-flag"></i> Status: <span class="badge bg-<?= $viewItem['status'] === 'terkonfirmasi' || $viewItem['status'] === 'selesai' ? 'success' : ($viewItem['status'] === 'dibatalkan' ? 'danger' : 'warning') ?>"><?= $statuses[$viewItem['status']] ?? ucfirst($viewItem['status']) ?></span></div>
                        </div>
                    </div>
                    <?php if ($viewItem['notes']): ?>
                    <div class="col-12">
                        <strong class="text-muted small text-uppercase">Catatan</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light small" style="white-space:pre-wrap"><?= e($viewItem['notes']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <a href="https://wa.me/<?= e($viewItem['customer_whatsapp']) ?>?text=<?= urlencode('Halo ' . $viewItem['customer_name'] . ', terima kasih sudah booking di DNA Vacation. Kode booking Anda: ' . $viewItem['booking_code'] . '. Tim kami akan segera memproses pesanan Anda.') ?>" target="_blank" class="btn btn-wa"><i class="bi bi-whatsapp"></i> Balas via WA</a>
                <?php if ($viewItem['customer_email']): ?>
                <a href="mailto:<?= e($viewItem['customer_email']) ?>?subject=<?= urlencode('Konfirmasi Booking ' . $viewItem['booking_code'] . ' - DNA Vacation') ?>&body=<?= urlencode("Halo " . $viewItem['customer_name'] . ",\n\nTerima kasih telah melakukan booking di DNA Vacation.\n\nDetail Booking:\n- Kode: " . $viewItem['booking_code'] . "\n- Paket: " . ($viewItem['tour_name'] ?? '-') . "\n- Tanggal: " . ($viewItem['departure_date'] ? date('d M Y', strtotime($viewItem['departure_date'])) : '-') . "\n- Peserta: " . $viewItem['num_persons'] . " orang\n- Total: " . formatRupiah($viewItem['total_price']) . "\n\nSalam,\nTim DNA Vacation") ?>" class="btn btn-outline-primary"><i class="bi bi-envelope"></i> Balas via Email</a>
                <?php endif; ?>
                <a href="bookings.php" class="btn btn-secondary">Tutup</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

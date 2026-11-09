<?php
$pageTitle = 'Custom Trip';
require_once __DIR__ . '/includes/header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM custom_trip_requests WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Request dihapus.');
    header('Location: custom-trips.php');
    exit;
}

if (isset($_POST['update_status'])) {
    $pdo->prepare("UPDATE custom_trip_requests SET status = ? WHERE id = ?")->execute([$_POST['status'], (int)$_POST['req_id']]);
    setFlash('success', 'Status diperbarui.');
    header('Location: custom-trips.php');
    exit;
}

// Filter
$statusFilter = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($statusFilter) { $where = 'WHERE status = ?'; $params[] = $statusFilter; }

$stmt = $pdo->prepare("SELECT * FROM custom_trip_requests $where ORDER BY created_at DESC");
$stmt->execute($params);
$requests = $stmt->fetchAll();

$statuses = ['baru'=>'Baru','diproses'=>'Diproses','penawaran_terkirim'=>'Penawaran Terkirim','terkonfirmasi'=>'Terkonfirmasi','selesai'=>'Selesai'];
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewItem = null;
if ($viewId) {
    $s = $pdo->prepare("SELECT * FROM custom_trip_requests WHERE id = ?");
    $s->execute([$viewId]);
    $viewItem = $s->fetch();
}
?>

<div class="p-3 p-md-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-compass text-primary-custom"></i> Custom Trip Request</h4>

    <!-- Filter -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="custom-trips.php" class="btn btn-sm <?= !$statusFilter ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
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
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Email/WA</th>
                            <th>Destinasi</th>
                            <th>Tanggal Trip</th>
                            <th>Peserta</th>
                            <th>Butuh Hotel</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada request custom trip.</td></tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td class="small text-muted"><?= date('d/m/y H:i', strtotime($r['created_at'])) ?></td>
                                    <td class="small">
                                        <strong><?= e($r['customer_name']) ?></strong>
                                    </td>
                                    <td class="small">
                                        <?php if ($r['customer_email']): ?>
                                        <a href="mailto:<?= e($r['customer_email']) ?>" class="text-decoration-none d-block"><i class="bi bi-envelope"></i> <?= e(truncate($r['customer_email'], 22)) ?></a>
                                        <?php endif; ?>
                                        <a href="https://wa.me/<?= e($r['customer_whatsapp']) ?>" target="_blank" class="text-success"><i class="bi bi-whatsapp"></i> <?= e($r['customer_whatsapp']) ?></a>
                                    </td>
                                    <td class="small"><?= e($r['destination'] ?: '-') ?><?php if ($r['destination_other']): ?> <em class="text-muted">(custom)</em><?php endif; ?></td>
                                    <td class="small">
                                        <?php if ($r['date_start'] && $r['date_end']): ?>
                                            <?= date('d/m', strtotime($r['date_start'])) ?> - <?= date('d/m/y', strtotime($r['date_end'])) ?>
                                        <?php else: ?>
                                            <?= e($r['preferred_dates'] ?: '-') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small"><?= $r['num_persons'] ?></td>
                                    <td class="small">
                                        <?php if ($r['needs_hotel']): ?>
                                            <span class="badge bg-info text-dark" style="font-size:.65rem">Ya</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary" style="font-size:.65rem">Tidak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small"><?= e($r['budget_range'] ?: '-') ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="req_id" value="<?= $r['id'] ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-select-sm" style="width:auto;font-size:.75rem" onchange="this.form.submit()">
                                                <?php foreach ($statuses as $k => $v): ?>
                                                    <option value="<?= $k ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="?view=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                        <a href="https://wa.me/<?= e($r['customer_whatsapp']) ?>" target="_blank" class="btn btn-sm btn-success" title="Chat WA"><i class="bi bi-whatsapp"></i></a>
                                        <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus?" title="Hapus"><i class="bi bi-trash"></i></a>
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

<?php if ($viewItem):
    $attractionsArr = json_decode($viewItem['attractions'] ?? '[]', true) ?: [];
?>
<!-- View Modal -->
<div class="modal fade show d-block" id="viewModal" tabindex="-1" style="background:rgba(0,0,0,.5)">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-compass text-primary-custom"></i> Detail Request Custom Trip</h5>
                <a href="custom-trips.php" class="btn-close" aria-label="Close"></a>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong class="text-muted small text-uppercase">Data Pribadi</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light">
                            <div><i class="bi bi-person"></i> <strong><?= e($viewItem['customer_name']) ?></strong></div>
                            <div class="small mt-1"><i class="bi bi-envelope"></i> <a href="mailto:<?= e($viewItem['customer_email']) ?>"><?= e($viewItem['customer_email'] ?: '-') ?></a></div>
                            <div class="small mt-1"><i class="bi bi-whatsapp text-success"></i> <a href="https://wa.me/<?= e($viewItem['customer_whatsapp']) ?>" target="_blank"><?= e($viewItem['customer_whatsapp']) ?></a></div>
                            <div class="small mt-1 text-muted">Dikirim: <?= date('d M Y, H:i', strtotime($viewItem['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted small text-uppercase">Detail Trip</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light">
                            <div><i class="bi bi-geo-alt"></i> Destinasi: <strong><?= e($viewItem['destination'] ?: '-') ?></strong></div>
                            <?php if ($viewItem['destination_other']): ?>
                                <div class="small mt-1 text-muted">(Custom: <?= e($viewItem['destination_other']) ?>)</div>
                            <?php endif; ?>
                            <div class="small mt-1"><i class="bi bi-calendar"></i> Tanggal:
                                <?php if ($viewItem['date_start'] && $viewItem['date_end']): ?>
                                    <strong><?= date('d M Y', strtotime($viewItem['date_start'])) ?> - <?= date('d M Y', strtotime($viewItem['date_end'])) ?></strong>
                                    (<?= max(1, round((strtotime($viewItem['date_end']) - strtotime($viewItem['date_start']))/86400) + 1) ?> hari)
                                <?php else: ?>
                                    <?= e($viewItem['preferred_dates'] ?: '-') ?>
                                <?php endif; ?>
                            </div>
                            <div class="small mt-1"><i class="bi bi-people"></i> Peserta: <strong><?= $viewItem['num_persons'] ?> orang</strong></div>
                            <div class="small mt-1"><i class="bi bi-tag"></i> Jenis: <?= e($viewItem['trip_type'] ?: '-') ?></div>
                            <div class="small mt-1"><i class="bi bi-cash"></i> Budget: <?= e($viewItem['budget_range'] ?: '-') ?></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <strong class="text-muted small text-uppercase">Akomodasi</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light">
                            <?php if ($viewItem['needs_hotel']): ?>
                                <div><span class="badge bg-info text-dark">Butuh Hotel</span></div>
                                <div class="mt-2"><i class="bi bi-building"></i> Pilihan Hotel: <strong><?= e($viewItem['hotel_choice'] ?: '-') ?></strong></div>
                                <?php if ($viewItem['hotel_choice'] === 'Lainnya' && $viewItem['hotel_other']): ?>
                                    <div class="small mt-1 text-muted">Nama hotel yang diinginkan: <strong><?= e($viewItem['hotel_other']) ?></strong></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div><span class="badge bg-secondary">Tour saja, tanpa hotel</span></div>
                                <?php if ($attractionsArr): ?>
                                    <div class="mt-2 small"><i class="bi bi-signpost-split"></i> Objek wisata yang diinginkan:</div>
                                    <div class="mt-1">
                                        <?php foreach ($attractionsArr as $a): ?>
                                            <span class="badge bg-light text-dark border me-1 mb-1" style="font-weight:500"><?= e($a) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($viewItem['attraction_other']): ?>
                                    <div class="small mt-2 text-muted">Objek wisata lainnya: <strong><?= e($viewItem['attraction_other']) ?></strong></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <strong class="text-muted small text-uppercase">Jenis Wisata</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light">
                            <?= e($viewItem['vacation_type'] ?: '-') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted small text-uppercase">Info Kedatangan</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light">
                            <div class="small"><i class="bi bi-ticket-perforated"></i> Tiket: <strong><?= e($viewItem['has_ticket'] ?: '-') ?></strong></div>
                            <div class="small mt-1"><i class="bi bi-clock"></i> Jam kedatangan: <strong><?= e($viewItem['arrival_time'] ?: '-') ?></strong></div>
                            <div class="small mt-1"><i class="bi bi-pin-map"></i> Lokasi penjemputan: <strong><?= e($viewItem['pickup_location'] ?: '-') ?></strong></div>
                        </div>
                    </div>

                    <?php if ($viewItem['special_requests']): ?>
                    <div class="col-12">
                        <strong class="text-muted small text-uppercase">Request Khusus</strong>
                        <div class="mt-2 p-3 rounded-3 bg-light small" style="white-space:pre-wrap"><?= e($viewItem['special_requests']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <a href="https://wa.me/<?= e($viewItem['customer_whatsapp']) ?>?text=<?= urlencode('Halo ' . $viewItem['customer_name'] . ', terima kasih atas request custom trip Anda ke ' . $viewItem['destination'] . '. Tim DNA Vacation akan segera membuatkan penawaran untuk Anda.') ?>" target="_blank" class="btn btn-wa"><i class="bi bi-whatsapp"></i> Balas via WA</a>
                <?php if ($viewItem['customer_email']): ?>
                <a href="mailto:<?= e($viewItem['customer_email']) ?>?subject=<?= urlencode('Re: Custom Trip Request Anda') ?>" class="btn btn-outline-primary"><i class="bi bi-envelope"></i> Balas via Email</a>
                <?php endif; ?>
                <a href="custom-trips.php" class="btn btn-secondary">Tutup</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

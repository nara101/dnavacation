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

$requests = $pdo->query("SELECT * FROM custom_trip_requests ORDER BY created_at DESC")->fetchAll();
$statuses = ['baru'=>'Baru','diproses'=>'Diproses','penawaran_terkirim'=>'Penawaran Terkirim','terkonfirmasi'=>'Terkonfirmasi','selesai'=>'Selesai'];
?>

<div class="p-3 p-md-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-compass text-primary-custom"></i> Custom Trip Request</h4>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>WhatsApp</th>
                            <th>Destinasi</th>
                            <th>Tipe</th>
                            <th>Peserta</th>
                            <th>Budget</th>
                            <th>Preferred Date</th>
                            <th>Status</th>
                            <th style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada request custom trip.</td></tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td class="small text-muted"><?= date('d/m/y', strtotime($r['created_at'])) ?></td>
                                    <td class="small"><?= e($r['customer_name']) ?></td>
                                    <td><a href="https://wa.me/<?= e($r['customer_whatsapp']) ?>" target="_blank" class="text-success small"><i class="bi bi-whatsapp"></i></a></td>
                                    <td class="small"><?= e($r['destination'] ?: '-') ?></td>
                                    <td class="small"><?= e($r['trip_type'] ?: '-') ?></td>
                                    <td class="small"><?= $r['num_persons'] ?></td>
                                    <td class="small"><?= e($r['budget_range'] ?: '-') ?></td>
                                    <td class="small"><?= e($r['preferred_dates'] ?: '-') ?></td>
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
                                        <a href="https://wa.me/<?= e($r['customer_whatsapp']) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i></a>
                                        <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus?"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php if ($r['special_requests']): ?>
                                <tr>
                                    <td colspan="10" class="small text-muted bg-light" style="padding-left:2rem"><i class="bi bi-chat-left-text"></i> Request khusus: <?= e($r['special_requests']) ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

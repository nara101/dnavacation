<?php
$pageTitle = 'Request Hotel';
require_once __DIR__ . '/includes/header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM hotel_requests WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Request dihapus.');
    header('Location: hotel-requests.php');
    exit;
}

if (isset($_POST['update_status'])) {
    $pdo->prepare("UPDATE hotel_requests SET status = ? WHERE id = ?")->execute([$_POST['status'], (int)$_POST['req_id']]);
    setFlash('success', 'Status diperbarui.');
    header('Location: hotel-requests.php');
    exit;
}

$requests = $pdo->query("SELECT * FROM hotel_requests ORDER BY created_at DESC")->fetchAll();
$statuses = ['baru'=>'Baru','diproses'=>'Diproses','terkonfirmasi'=>'Terkonfirmasi','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
?>

<div class="p-3 p-md-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-building text-primary-custom"></i> Request Hotel</h4>

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
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Tamu/Kamar</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada request.</td></tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td class="small text-muted"><?= date('d/m/y', strtotime($r['created_at'])) ?></td>
                                    <td class="small"><?= e($r['customer_name']) ?></td>
                                    <td><a href="https://wa.me/<?= e($r['customer_whatsapp']) ?>" target="_blank" class="text-success small"><i class="bi bi-whatsapp"></i> <?= e($r['customer_whatsapp']) ?></a></td>
                                    <td class="small"><?= e($r['destination'] ?: '-') ?></td>
                                    <td class="small"><?= $r['check_in'] ? date('d M Y', strtotime($r['check_in'])) : '-' ?></td>
                                    <td class="small"><?= $r['check_out'] ? date('d M Y', strtotime($r['check_out'])) : '-' ?></td>
                                    <td class="small"><?= $r['num_guests'] ?> tamu / <?= $r['num_rooms'] ?? 1 ?> kamar</td>
                                    <td class="small"><?= e($r['budget'] ?: '-') ?></td>
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
                                        <a href="https://wa.me/<?= e($r['customer_whatsapp']) ?>?text=<?= urlencode('Halo ' . $r['customer_name'] . ', terima kasih request hotel di DNA Vacation.') ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i></a>
                                        <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus?"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php if ($r['notes']): ?>
                                <tr>
                                    <td colspan="10" class="small text-muted bg-light" style="padding-left:2rem"><i class="bi bi-chat-left-text"></i> Catatan: <?= e($r['notes']) ?></td>
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

<?php
include "auth.php";
include "../db.php";
$adminPage = 'booking_rental';

if (isset($_POST['update_status'])) {
    $bid = (int)$_POST['id'];
    $st = $_POST['status'];
    $stmt = $conn->prepare("UPDATE rental_booking SET status=? WHERE id=?");
    $stmt->bind_param("si", $st, $bid);
    $stmt->execute();
    header("Location: booking-rental.php"); exit;
}
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM rental_booking WHERE id=$did");
    header("Location: booking-rental.php"); exit;
}

$filter = $_GET['status'] ?? '';
$where = '1=1';
if ($filter) $where .= " AND rb.status='" . $conn->real_escape_string($filter) . "'";
$result = $conn->query("SELECT rb.*, r.nama_mobil FROM rental_booking rb LEFT JOIN rental r ON r.id=rb.rental_id WHERE $where ORDER BY rb.id DESC");
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Booking Rental</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div class="content">
    <div class="header"><div><h1>Booking Rental Mobil</h1><p>Kelola pesanan sewa mobil</p></div></div>
    <div style="background:#fff; padding:18px; border-radius:12px; box-shadow:0 4px 18px rgba(0,0,0,0.06); margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px; align-items:center;">
            <label style="font-weight:600;color:#0a3d62;">Filter Status:</label>
            <select name="status" onchange="this.form.submit()" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;">
                <option value="">Semua</option>
                <?php foreach (['baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filter==$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="table-box">
        <table>
            <thead><tr><th>#</th><th>Tanggal Booking</th><th>Nama</th><th>WhatsApp</th><th>Mobil</th><th>Periode Sewa</th><th>Lokasi</th><th>Sopir</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if ($result && $result->num_rows): $no=1; while ($r = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><small><?= date('d M Y H:i', strtotime($r['created_at'])) ?></small></td>
                    <td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
                    <td><?= htmlspecialchars($r['whatsapp']) ?></td>
                    <td><?= htmlspecialchars($r['nama_mobil'] ?? '-') ?></td>
                    <td><small><?= $r['tanggal_mulai'] ?><br>s/d <?= $r['tanggal_selesai'] ?></small></td>
                    <td><small><?= htmlspecialchars($r['lokasi_penjemputan']) ?></small></td>
                    <td><?= $r['dengan_sopir']?'Ya':'Tidak' ?></td>
                    <td>
                        <form method="POST" style="display:flex;gap:4px;">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <select name="status" onchange="this.form.submit()" style="padding:5px;border-radius:6px;border:1px solid #ddd;font-size:0.8rem;">
                                <?php foreach (['baru','diproses','menunggu_pembayaran','terkonfirmasi','selesai','dibatalkan'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $r['status']==$s?'selected':'' ?>><?= str_replace('_',' ',$s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td class="actions">
                        <?php
                            $wa = preg_replace('/^0/', '62', preg_replace('/\D/', '', $r['whatsapp']));
                            $msg = "Halo {$r['nama']}, terkait booking rental mobil {$r['nama_mobil']}.";
                        ?>
                        <a href="https://wa.me/<?= $wa ?>?text=<?= urlencode($msg) ?>" target="_blank" class="btn-wa"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="?delete=<?= $r['id'] ?>" class="btn-delete" onclick="return confirm('Hapus booking?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="10" class="empty-row">Belum ada booking</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body></html>

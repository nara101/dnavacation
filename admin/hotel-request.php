<?php
include "auth.php";
include "../db.php";
$adminPage = 'hotel_request';

if (isset($_POST['update_status'])) {
    $bid = (int)$_POST['id'];
    $st = $_POST['status'];
    $stmt = $conn->prepare("UPDATE hotel_request SET status=? WHERE id=?");
    $stmt->bind_param("si", $st, $bid);
    $stmt->execute();
    header("Location: hotel-request.php"); exit;
}
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM hotel_request WHERE id=$did");
    header("Location: hotel-request.php"); exit;
}
$result = $conn->query("SELECT * FROM hotel_request ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Request Hotel</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div class="content">
    <div class="header"><div><h1>Request Booking Hotel</h1><p>Daftar permintaan booking hotel dari pelanggan</p></div></div>
    <div class="table-box">
        <table>
            <thead><tr><th>#</th><th>Tanggal</th><th>Nama</th><th>WhatsApp</th><th>Destinasi</th><th>Check-in</th><th>Check-out</th><th>Tamu</th><th>Budget</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if ($result && $result->num_rows): $no=1; while ($r = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><small><?= date('d M Y H:i', strtotime($r['created_at'])) ?></small></td>
                    <td>
                        <strong><?= htmlspecialchars($r['nama']) ?></strong>
                        <?php if ($r['catatan']): ?><br><small style="color:#999;"><i><?= htmlspecialchars($r['catatan']) ?></i></small><?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['whatsapp']) ?></td>
                    <td><?= htmlspecialchars($r['destinasi']) ?></td>
                    <td><?= $r['check_in'] ?></td>
                    <td><?= $r['check_out'] ?></td>
                    <td><?= $r['jumlah_tamu'] ?></td>
                    <td><?= htmlspecialchars($r['budget']) ?></td>
                    <td>
                        <form method="POST" style="display:flex;gap:4px;">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <select name="status" onchange="this.form.submit()" style="padding:5px;border-radius:6px;border:1px solid #ddd;font-size:0.8rem;">
                                <?php foreach (['baru','diproses','selesai','dibatalkan'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $r['status']==$s?'selected':'' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td class="actions">
                        <?php
                            $wa = preg_replace('/^0/', '62', preg_replace('/\D/', '', $r['whatsapp']));
                            $msg = "Halo {$r['nama']}, terkait request hotel di {$r['destinasi']}.";
                        ?>
                        <a href="https://wa.me/<?= $wa ?>?text=<?= urlencode($msg) ?>" target="_blank" class="btn-wa"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="?delete=<?= $r['id'] ?>" class="btn-delete" onclick="return confirm('Hapus request?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="11" class="empty-row">Belum ada request hotel</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body></html>

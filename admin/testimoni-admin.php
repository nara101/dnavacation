<?php
include "auth.php";
include "../db.php";
$adminPage = 'testimoni';

if (isset($_POST['save'])) {
    $nama = trim($_POST['nama']);
    $isi = trim($_POST['isi']);
    $rating = (int)$_POST['rating'];
    $aktif = isset($_POST['is_active']) ? 1 : 0;
    if (!empty($_POST['edit_id'])) {
        $eid = (int)$_POST['edit_id'];
        $stmt = $conn->prepare("UPDATE testimoni SET nama=?,isi=?,rating=?,is_active=? WHERE id=?");
        $stmt->bind_param("ssiii", $nama,$isi,$rating,$aktif,$eid);
    } else {
        $stmt = $conn->prepare("INSERT INTO testimoni (nama,isi,rating,is_active) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii", $nama,$isi,$rating,$aktif);
    }
    $stmt->execute();
    header("Location: testimoni-admin.php"); exit;
}
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM testimoni WHERE id=$did");
    header("Location: testimoni-admin.php"); exit;
}
$editData = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editData = $conn->query("SELECT * FROM testimoni WHERE id=$eid")->fetch_assoc();
}
$result = $conn->query("SELECT * FROM testimoni ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Kelola Testimoni</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div class="content">
    <div class="header"><div><h1>Testimoni Pelanggan</h1><p>Kelola testimoni yang tampil di homepage</p></div></div>

    <div class="form-box" style="max-width:none;margin-bottom:24px;">
        <h2><?= $editData ? 'Edit Testimoni' : 'Tambah Testimoni' ?></h2>
        <form method="POST">
            <?php if ($editData): ?><input type="hidden" name="edit_id" value="<?= $editData['id'] ?>"><?php endif; ?>
            <div class="form-row">
                <div class="form-group"><label>Nama *</label><input type="text" name="nama" value="<?= htmlspecialchars($editData['nama'] ?? '') ?>" required></div>
                <div class="form-group"><label>Rating</label>
                    <select name="rating">
                        <?php for ($i=5;$i>=1;$i--): ?>
                            <option value="<?= $i ?>" <?= ($editData['rating'] ?? 5)==$i?'selected':'' ?>><?= str_repeat('★', $i) ?> (<?= $i ?>)</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Isi Testimoni *</label><textarea name="isi" rows="4" required><?= htmlspecialchars($editData['isi'] ?? '') ?></textarea></div>
            <div class="form-group">
                <label><input type="checkbox" name="is_active" value="1" <?= (!isset($editData) || $editData['is_active'])?'checked':'' ?>> Tampilkan di homepage</label>
            </div>
            <div class="form-actions">
                <button name="save" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $editData ? 'Update' : 'Simpan' ?></button>
                <?php if ($editData): ?><a href="testimoni-admin.php" class="btn-primary" style="background:#777;">Batal</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-box">
        <table>
            <thead><tr><th>#</th><th>Nama</th><th>Isi</th><th>Rating</th><th>Aktif</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if ($result->num_rows): $no=1; while ($r = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
                    <td><small><?= htmlspecialchars($r['isi']) ?></small></td>
                    <td><?= str_repeat('★', (int)$r['rating']) ?></td>
                    <td><span class="status <?= $r['is_active']?'aktif':'nonaktif' ?>"><?= $r['is_active']?'Aktif':'Non-aktif' ?></span></td>
                    <td class="actions">
                        <a href="?edit=<?= $r['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                        <a href="?delete=<?= $r['id'] ?>" class="btn-delete" onclick="return confirm('Hapus?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" class="empty-row">Belum ada testimoni</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body></html>

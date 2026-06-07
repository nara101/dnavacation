<?php
include "auth.php";
include "../db.php";
$adminPage = 'rental';
if (!isset($_GET['id'])) {
    header("Location: rental-admin.php");
    exit;
}
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM rental WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) {
    echo "Mobil tidak ditemukan";
    exit;
}

$err = '';
if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama_mobil']);
    $tipe = trim($_POST['tipe']);
    $kap = (int)$_POST['kapasitas'];
    $trans = $_POST['transmisi'];
    $hph = (int)$_POST['harga_per_hari'];
    $hds = (int)$_POST['harga_dengan_sopir'];
    $area = trim($_POST['area_layanan']);
    $fasilitas = trim($_POST['fasilitas']);
    $syarat = trim($_POST['syarat_sewa']);
    $status = $_POST['status'];
    $gambar = $data['gambar'];
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'rental_' . time() . '_' . rand(100, 999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . "/../uploads/rentals/$gambar");
    }
    $stmt = $conn->prepare("UPDATE rental SET nama_mobil=?,tipe=?,kapasitas=?,transmisi=?,harga_per_hari=?,harga_dengan_sopir=?,area_layanan=?,fasilitas=?,syarat_sewa=?,gambar=?,status=? WHERE id=?");
    $stmt->bind_param("ssisiisssssi", $nama, $tipe, $kap, $trans, $hph, $hds, $area, $fasilitas, $syarat, $gambar, $status, $id);
    if ($stmt->execute()) {
        header("Location: rental-admin.php");
        exit;
    }
    $err = $conn->error;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Mobil</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="header">
            <div>
                <h1>Edit Mobil</h1>
            </div><a href="rental-admin.php" class="btn-primary" style="background:#666;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group"><label>Nama Mobil *</label><input type="text" name="nama_mobil" value="<?= htmlspecialchars($data['nama_mobil']) ?>" required></div>
                    <div class="form-group"><label>Tipe</label><input type="text" name="tipe" value="<?= htmlspecialchars($data['tipe']) ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Kapasitas *</label><input type="number" name="kapasitas" min="1" value="<?= $data['kapasitas'] ?>" required></div>
                    <div class="form-group"><label>Transmisi *</label>
                        <select name="transmisi">
                            <option value="manual" <?= $data['transmisi'] == 'manual' ? 'selected' : '' ?>>Manual</option>
                            <option value="automatic" <?= $data['transmisi'] == 'automatic' ? 'selected' : '' ?>>Automatic</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Harga / Hari (tanpa sopir) *</label><input type="number" name="harga_per_hari" value="<?= $data['harga_per_hari'] ?>" required></div>
                    <div class="form-group"><label>Harga / Hari (dengan sopir)</label><input type="number" name="harga_dengan_sopir" value="<?= $data['harga_dengan_sopir'] ?>"></div>
                </div>
                <div class="form-group"><label>Area Layanan</label><input type="text" name="area_layanan" value="<?= htmlspecialchars($data['area_layanan']) ?>"></div>
                <div class="form-group"><label>Fasilitas</label><textarea name="fasilitas" rows="4"><?= htmlspecialchars($data['fasilitas']) ?></textarea></div>
                <div class="form-group"><label>Syarat Sewa</label><textarea name="syarat_sewa" rows="3"><?= htmlspecialchars($data['syarat_sewa']) ?></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Status</label>
                        <select name="status">
                            <option value="tersedia" <?= $data['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                            <option value="tidak_tersedia" <?= $data['status'] == 'tidak_tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Foto Saat Ini</label>
                        <?php if ($data['gambar']): ?>
                            <div><img src="<?= fotoUrl($data['gambar'], 'rentals') ?>" style="height:70px;border-radius:6px;margin-bottom:8px;" onerror="this.src='../assets/yogya.jpg'"></div>
                        <?php endif; ?>
                        <input type="file" name="gambar" accept="image/*">
                    </div>
                </div>
                <div class="form-actions">
                    <button name="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                    <a href="rental-admin.php" class="btn-primary" style="background:#777;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
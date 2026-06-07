<?php
include "auth.php";
include "../db.php";
$adminPage = 'rental';
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
    $gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'rental_' . time() . '_' . rand(100, 999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . "/../uploads/rentals/$gambar");
    }
    $stmt = $conn->prepare("INSERT INTO rental (nama_mobil,tipe,kapasitas,transmisi,harga_per_hari,harga_dengan_sopir,area_layanan,fasilitas,syarat_sewa,gambar,status) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssisiisssss", $nama, $tipe, $kap, $trans, $hph, $hds, $area, $fasilitas, $syarat, $gambar, $status);
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
    <title>Tambah Mobil</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="header">
            <div>
                <h1>Tambah Mobil Rental</h1>
            </div><a href="rental-admin.php" class="btn-primary" style="background:#666;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group"><label>Nama Mobil *</label><input type="text" name="nama_mobil" required></div>
                    <div class="form-group"><label>Tipe (MPV/City Car/dll)</label><input type="text" name="tipe"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Kapasitas *</label><input type="number" name="kapasitas" min="1" required value="4"></div>
                    <div class="form-group"><label>Transmisi *</label>
                        <select name="transmisi">
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Harga / Hari (tanpa sopir) *</label><input type="number" name="harga_per_hari" min="0" required></div>
                    <div class="form-group"><label>Harga / Hari (dengan sopir)</label><input type="number" name="harga_dengan_sopir" min="0" value="0"></div>
                </div>
                <div class="form-group"><label>Area Layanan</label><input type="text" name="area_layanan" placeholder="Misal: Jakarta, Bogor, Depok"></div>
                <div class="form-group"><label>Fasilitas (1 baris = 1 item)</label><textarea name="fasilitas" rows="4"></textarea></div>
                <div class="form-group"><label>Syarat Sewa</label><textarea name="syarat_sewa" rows="3"></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Status</label>
                        <select name="status">
                            <option value="tersedia">Tersedia</option>
                            <option value="tidak_tersedia">Tidak Tersedia</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Foto Mobil</label><input type="file" name="gambar" accept="image/*"></div>
                </div>
                <div class="form-actions">
                    <button name="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    <a href="rental-admin.php" class="btn-primary" style="background:#777;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
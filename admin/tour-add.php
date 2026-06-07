<?php
include "auth.php";
include "../db.php";
$adminPage = 'tour';

$err = '';
if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $durasi = $_POST['durasi'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $judul = trim($_POST['judul']);
    $durasi = trim($_POST['durasi']);
    $lokasi = trim($_POST['lokasi']);
    $kategori = $_POST['kategori'];
    $harga = (int)$_POST['harga'];
    $deskripsi = trim($_POST['deskripsi']);
    $itinerary = trim($_POST['itinerary']);
    $finc = trim($_POST['fasilitas_termasuk']);
    $fexc = trim($_POST['fasilitas_tidak_termasuk']);
    $sk = trim($_POST['syarat_ketentuan']);
    $mp = trim($_POST['meeting_point']);
    $bs = isset($_POST['is_bestseller']) ? 1 : 0;
    $promo = isset($_POST['is_promo']) ? 1 : 0;
    $status = $_POST['status'];
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $judul));

    $gambar = $_FILES['gambar']['name'];
    move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/$gambar");

    $stmt = $conn->prepare(
        "INSERT INTO tour (judul,durasi,lokasi,harga,deskripsi,gambar)
         VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param("sssiss", $judul, $durasi, $lokasi, $harga, $deskripsi, $gambar);
    $stmt->execute();

    header("Location: tour-admin.php");
    exit;
    $gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'tour_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $target = __DIR__ . "/../uploads/tours/$gambar";
        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            $err = "Upload gambar gagal";
        }
    }
    if (!$err) {
        $stmt = $conn->prepare("INSERT INTO tour (judul,slug,durasi,lokasi,kategori,harga,deskripsi,itinerary,fasilitas_termasuk,fasilitas_tidak_termasuk,syarat_ketentuan,meeting_point,gambar,is_bestseller,is_promo,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssissssssssss", $judul, $slug, $durasi, $lokasi, $kategori, $harga, $deskripsi, $itinerary, $finc, $fexc, $sk, $mp, $gambar, $bs, $promo, $status);
        if ($stmt->execute()) {
            header("Location: tour-admin.php");
            exit;
        } else {
            $err = $conn->error;
        }
    }
}
?>

<h2>Tambah Tour</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="judul" placeholder="Judul" required><br><br>
    <input type="text" name="durasi" placeholder="Durasi" required><br><br>
    <input type="text" name="lokasi" placeholder="Lokasi" required><br><br>
    <input type="number" name="harga" placeholder="Harga" required><br><br>
    <textarea name="deskripsi" placeholder="Deskripsi" required></textarea><br><br>
    <input type="file" name="gambar" required><br><br>

    <button name="submit">Simpan</button>
</form>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Tour - DNA Vacation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="header">
            <div>
                <h1>Tambah Paket Tour</h1>
                <p>Isi semua data paket tour dengan lengkap</p>
            </div><a href="tour-admin.php" class="btn-primary" style="background:#666;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group"><label>Judul Paket *</label><input type="text" name="judul" required></div>
                    <div class="form-group"><label>Lokasi *</label><input type="text" name="lokasi" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Durasi *</label><input type="text" name="durasi" placeholder="Contoh: 3 Hari 2 Malam" required></div>
                    <div class="form-group"><label>Kategori *</label>
                        <select name="kategori" required>
                            <option value="open_trip">Open Trip</option>
                            <option value="private_trip">Private Trip</option>
                            <option value="family_trip">Family Trip</option>
                            <option value="honeymoon">Honeymoon</option>
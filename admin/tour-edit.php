<?php
include "auth.php";
include "../db.php";
$adminPage = 'tour';

if (!isset($_GET['id'])) {
    header("Location: tour-admin.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: tour-admin.php");
    exit;
}
$id = (int)$_GET['id'];
$data = $conn->query("SELECT * FROM tour WHERE id=$id")->fetch_assoc();

if (!$data) {
    echo "Tour tidak ditemukan";
    exit;
}
$stmt = $conn->prepare("SELECT * FROM tour WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) {
    echo "Tour tidak ditemukan";
    exit;
}

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
    $gambar = $data['gambar'];

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/$gambar");

        $conn->query("UPDATE tour SET gambar='$gambar' WHERE id=$id");
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'tour_' . time() . '_' . rand(100, 999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . "/../uploads/tours/$gambar");
    }

    $stmt = $conn->prepare(
        "UPDATE tour SET judul=?, durasi=?, lokasi=?, harga=?, deskripsi=? WHERE id=?"
    );
    $stmt->bind_param("sssisi", $judul, $durasi, $lokasi, $harga, $deskripsi, $id);
    $stmt->execute();

    header("Location: tour-admin.php");
    exit;
    $stmt = $conn->prepare("UPDATE tour SET judul=?,slug=?,durasi=?,lokasi=?,kategori=?,harga=?,deskripsi=?,itinerary=?,fasilitas_termasuk=?,fasilitas_tidak_termasuk=?,syarat_ketentuan=?,meeting_point=?,gambar=?,is_bestseller=?,is_promo=?,status=? WHERE id=?");
    $stmt->bind_param("sssssissssssssssi", $judul, $slug, $durasi, $lokasi, $kategori, $harga, $deskripsi, $itinerary, $finc, $fexc, $sk, $mp, $gambar, $bs, $promo, $status, $id);
    if ($stmt->execute()) {
        header("Location: tour-admin.php");
        exit;
    }
    $err = $conn->error;
}
?>

<h2>Edit Tour</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="judul" value="<?= $data['judul'] ?>" required><br><br>
    <input type="text" name="durasi" value="<?= $data['durasi'] ?>" required><br><br>
    <input type="text" name="lokasi" value="<?= $data['lokasi'] ?>" required><br><br>
    <input type="number" name="harga" value="<?= $data['harga'] ?>" required><br><br>
    <textarea name="deskripsi" required><?= $data['deskripsi'] ?></textarea><br><br>

    <img src="../uploads/<?= $data['gambar'] ?>" width="150"><br><br>
    <input type="file" name="gambar"><br><br>

    <button name="submit">Update</button>
</form>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Tour</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="header">
            <div>
                <h1>Edit Paket Tour</h1>
            </div><a href="tour-admin.php" class="btn-primary" style="background:#666;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">
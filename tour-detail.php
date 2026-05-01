<?php
include "db.php";

if (!isset($_GET['id'])) {
    header("Location: tour.php");
    exit;
}

$id = (int)$_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM tour WHERE id=$id");
$tour = mysqli_fetch_assoc($q);

if (!$tour) {
    echo "Tour tidak ditemukan";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title><?= htmlspecialchars($tour['judul']); ?></title>
</head>

<body>

    <img src="uploads/<?= $tour['gambar']; ?>" width="100%">

    <h1><?= $tour['judul']; ?></h1>
    <p>⏱ <?= $tour['durasi']; ?> | 📍 <?= $tour['lokasi']; ?></p>
    <p><strong>Rp <?= number_format($tour['harga']); ?></strong></p>

    <h3>Deskripsi</h3>
    <p><?= nl2br($tour['deskripsi']); ?></p>

</body>

</html>
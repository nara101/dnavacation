<?php
include "auth.php";
include "../db.php";

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

if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $durasi = $_POST['durasi'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/$gambar");

        $conn->query("UPDATE tour SET gambar='$gambar' WHERE id=$id");
    }

    $stmt = $conn->prepare(
        "UPDATE tour SET judul=?, durasi=?, lokasi=?, harga=?, deskripsi=? WHERE id=?"
    );
    $stmt->bind_param("sssisi", $judul, $durasi, $lokasi, $harga, $deskripsi, $id);
    $stmt->execute();

    header("Location: tour-admin.php");
    exit;
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
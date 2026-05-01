<?php
include "auth.php";
include "../db.php";

if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $durasi = $_POST['durasi'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

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
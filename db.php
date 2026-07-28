<?php
$host = "localhost";
$user = "root";
$pass = ""; // kosong kalau di XAMPP
$db   = "dna"; // ganti sesuai yang kamu buat tadi

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

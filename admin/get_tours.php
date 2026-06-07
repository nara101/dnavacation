<?php
$conn = new mysqli("localhost", "user", "password", "database");
$result = $conn->query("SELECT * FROM tours");
include __DIR__ . "/../db.php";
header('Content-Type: application/json');
$result = $conn->query("SELECT id, judul, durasi, lokasi, harga, gambar FROM tour WHERE status='aktif' ORDER BY id DESC");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
while ($row = $result->fetch_assoc()) $data[] = $row;
echo json_encode($data);

<?php
/**
 * SETUP DATABASE DNA VACATION
 * Jalankan file ini sekali: http://localhost/dnavacation/setup.php
 * Setelah selesai, hapus file ini untuk keamanan.
 */

$host = "localhost";
$user = "root";
$pass = "";
$dbName = "dna";

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Koneksi MySQL gagal: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbName);

// Clean reset: drop existing tables agar schema selalu segar
foreach (['tour_booking','rental_booking','hotel_request','testimoni','settings','blog','tour','rental','admin'] as $t) {
    $conn->query("DROP TABLE IF EXISTS `$t`");
}

$sqlFile = __DIR__ . "/database.sql";
if (!file_exists($sqlFile)) {
    die("File database.sql tidak ditemukan!");
}

$sql = file_get_contents($sqlFile);
// Jalankan multi-query
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) $result->free();
    } while ($conn->more_results() && $conn->next_result());
}

// Pastikan admin default punya password yang valid
$hash = password_hash("admin123", PASSWORD_DEFAULT);
$conn->query("DELETE FROM admin WHERE username='admin'");
$stmt = $conn->prepare("INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)");
$nama = "Administrator";
$user = "admin";
$stmt->bind_param("sss", $user, $hash, $nama);
$stmt->execute();

echo "<!DOCTYPE html><html><head><title>Setup DNA Vacation</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#0a3d62;color:#fff;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0}
.box{background:#fff;color:#333;padding:40px;border-radius:16px;max-width:540px;box-shadow:0 20px 50px rgba(0,0,0,.3)}
h1{color:#0a3d62}
.ok{color:#16a085;font-weight:600}
.warn{background:#fff3cd;border-left:4px solid #f39c12;padding:14px;border-radius:6px;margin:18px 0}
.btn{display:inline-block;background:#0a3d62;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;margin-top:14px;font-weight:600}
code{background:#f4f4f4;padding:3px 8px;border-radius:4px;font-family:monospace}
</style></head><body><div class='box'>
<h1>Setup Berhasil!</h1>
<p class='ok'>Database <code>$dbName</code> dan tabel-tabelnya berhasil dibuat.</p>
<p>Login admin default:</p>
<ul>
<li>Username: <code>admin</code></li>
<li>Password: <code>admin123</code></li>
</ul>
<div class='warn'><strong>Penting:</strong> Hapus file <code>setup.php</code> ini setelah setup selesai untuk keamanan.</div>
<a href='index.php' class='btn'>Lihat Website</a>
<a href='admin/login.php' class='btn' style='background:#16a085'>Login Admin</a>
</div></body></html>";

$conn->close();

<?php
$host = "localhost";
$user = "root";
$pass = ""; // kosong kalau di XAMPP
$db   = "dna"; // ganti sesuai yang kamu buat tadi
if (!isset($conn) || !($conn instanceof mysqli)) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "dna";

    $conn = new mysqli($host, $user, $pass, $db);
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error . "<br>Pastikan database 'dna' sudah dibuat. Jalankan <a href='setup.php'>setup.php</a> dulu.");
    }
    $conn->set_charset("utf8mb4");
}

if (!function_exists('getSetting')) {
    function getSetting($key, $default = '')
    {
        global $conn;
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            $r = $conn->query("SELECT setting_key, setting_value FROM settings");
            if ($r) {
                while ($row = $r->fetch_assoc()) {
                    $cache[$row['setting_key']] = $row['setting_value'];
                }
            }
        }
        return isset($cache[$key]) ? $cache[$key] : $default;
    }
}

if (!function_exists('rupiah')) {
    function rupiah($angka)
    {
        return 'Rp ' . number_format((int)$angka, 0, ',', '.');
    }
}

if (!function_exists('waLink')) {
    function waLink($pesan = '')
    {
        $nomor = getSetting('whatsapp_number', '6285280825858');
        return "https://wa.me/{$nomor}?text=" . urlencode($pesan);
    }
}

if (!function_exists('labelKategori')) {
    function labelKategori($k)
    {
        $map = [
            'private_trip' => 'Private Trip',
            'open_trip' => 'Open Trip',
            'family_trip' => 'Family Trip',
            'honeymoon' => 'Honeymoon',
            'corporate_trip' => 'Corporate Trip',
            'tips_travel' => 'Tips Travel',
            'rekomendasi_destinasi' => 'Rekomendasi Destinasi',
            'paket_tour' => 'Paket Tour',
            'hotel' => 'Hotel',
            'rental_mobil' => 'Rental Mobil',
            'promo' => 'Promo',
        ];
        return $map[$k] ?? ucfirst(str_replace('_', ' ', $k));
    }
}

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
    if (!function_exists('fotoUrl')) {
        function fotoUrl($filename, $folder = 'tours')
        {
            if (empty($filename)) return 'assets/yogya.jpg';
            $path = __DIR__ . "/uploads/$folder/$filename";
            if (file_exists($path)) return "uploads/$folder/$filename";
            $assetPath = __DIR__ . "/assets/$filename";
            if (file_exists($assetPath)) return "assets/$filename";
            return 'assets/yogya.jpg';
        }
    }
}

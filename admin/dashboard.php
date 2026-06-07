<?php
include "auth.php";
include "sidebar.php";
include "../db.php";
$adminPage = 'dashboard';

// statistics
$totalTour = (int)($conn->query("SELECT COUNT(*) c FROM tour")->fetch_assoc()['c'] ?? 0);
$totalRental = (int)($conn->query("SELECT COUNT(*) c FROM rental")->fetch_assoc()['c'] ?? 0);
$totalBlog = (int)($conn->query("SELECT COUNT(*) c FROM blog")->fetch_assoc()['c'] ?? 0);
$totalBookingTour = (int)($conn->query("SELECT COUNT(*) c FROM tour_booking")->fetch_assoc()['c'] ?? 0);
$totalBookingRental = (int)($conn->query("SELECT COUNT(*) c FROM rental_booking")->fetch_assoc()['c'] ?? 0);
$totalHotelReq = (int)($conn->query("SELECT COUNT(*) c FROM hotel_request")->fetch_assoc()['c'] ?? 0);
$newBooking = (int)($conn->query("SELECT COUNT(*) c FROM tour_booking WHERE status='baru'")->fetch_assoc()['c'] ?? 0);
$newRental = (int)($conn->query("SELECT COUNT(*) c FROM rental_booking WHERE status='baru'")->fetch_assoc()['c'] ?? 0);

$recentTour = $conn->query("SELECT tb.*, t.judul FROM tour_booking tb LEFT JOIN tour t ON t.id=tb.tour_id ORDER BY tb.id DESC LIMIT 5");
$recentRental = $conn->query("SELECT rb.*, r.nama_mobil FROM rental_booking rb LEFT JOIN rental r ON r.id=rb.rental_id ORDER BY rb.id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <title>Dashboard - DNA Vacation Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="content">

        <div class="header">
            <h1>Selamat datang, <?= htmlspecialchars($_SESSION['admin']); ?></h1>
            <p>Kelola data website DNAVacation melalui panel admin</p>
<?php include "sidebar.php"; ?>
<div class="content">
    <div class="header">
        <div>
            <h1>Selamat datang, <?= htmlspecialchars($_SESSION['admin']) ?>!</h1>
            <p>Kelola seluruh data website DNA Vacation di sini</p>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Tour</h3>
                <p>Kelola paket tour dan destinasi wisata</p>
                <a href="tour-admin.php">Kelola Tour</a>
        <div class="user-info">
            <div class="avatar"><?= strtoupper(substr($_SESSION['admin'], 0, 1)) ?></div>
            <div>
                <strong><?= htmlspecialchars($_SESSION['admin']) ?></strong><br>
                <small><?= date('d M Y') ?></small>
            </div>
        </div>
    </div>

            <div class="card">
                <h3>Rental</h3>
                <p>Kelola kendaraan dan harga rental</p>
                <a href="rental-admin.php">Kelola Rental</a>
            </div>
    <div class="stats-grid">
        <div class="stat-card"><div class="icon"><i class="fa-solid fa-suitcase-rolling"></i></div><div class="info"><small>Total Paket Tour</small><h3><?= $totalTour ?></h3></div></div>
        <div class="stat-card success"><div class="icon"><i class="fa-solid fa-car"></i></div><div class="info"><small>Total Rental</small><h3><?= $totalRental ?></h3></div></div>
        <div class="stat-card warning"><div class="icon"><i class="fa-solid fa-newspaper"></i></div><div class="info"><small>Artikel Blog</small><h3><?= $totalBlog ?></h3></div></div>
        <div class="stat-card danger"><div class="icon"><i class="fa-solid fa-bell"></i></div><div class="info"><small>Booking Baru</small><h3><?= $newBooking + $newRental ?></h3></div></div>
    </div>

            <div class="card">
                <h3>Blog</h3>
                <p>Kelola artikel dan berita website</p>
                <a href="blog-admin.php">Kelola Blog</a>
            </div>
        </div>
    <div class="stats-grid">
        <div class="stat-card"><div class="icon"><i class="fa-solid fa-clipboard-list"></i></div><div class="info"><small>Booking Tour</small><h3><?= $totalBookingTour ?></h3></div></div>
        <div class="stat-card success"><div class="icon"><i class="fa-solid fa-clipboard-check"></i></div><div class="info"><small>Booking Rental</small><h3><?= $totalBookingRental ?></h3></div></div>
        <div class="stat-card warning"><div class="icon"><i class="fa-solid fa-hotel"></i></div><div class="info"><small>Request Hotel</small><h3><?= $totalHotelReq ?></h3></div></div>
    </div>

    <div class="section-box">
        <h3><i class="fa-solid fa-clock-rotate-left me-2"></i>Booking Tour Terbaru</h3>
        <div class="table-box" style="box-shadow:none;">
            <table>
                <thead><tr><th>#</th><th>Nama</th><th>Paket</th><th>Tanggal</th><th>Peserta</th><th>Status</th></tr></thead>
                <tbody>
                <?php $no=1; if ($recentTour->num_rows): while ($r = $recentTour->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['nama']) ?></td>
                        <td><?= htmlspecialchars($r['judul']) ?></td>
                        <td><?= $r['tanggal_keberangkatan'] ?></td>
                        <td><?= $r['jumlah_peserta'] ?></td>
                        <td><span class="status <?= $r['status'] ?>"><?= str_replace('_',' ',$r['status']) ?></span></td>
                    </tr>
                <?php endwhile; else: ?>
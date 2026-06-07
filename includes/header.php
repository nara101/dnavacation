<?php
if (!isset($conn)) include __DIR__ . "/../db.php";
$currentPage = $currentPage ?? '';
$pageTitle = $pageTitle ?? 'DNA Vacation - Tour & Travel Terpercaya';
$pageDesc = $pageDesc ?? 'DNA Vacation menyediakan paket tour, rental mobil, dan booking hotel terpercaya di seluruh Indonesia.';
$waNumber = getSetting('whatsapp_number', '6285280825858');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= isset($basePath) ? $basePath : '' ?>style.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php"><img src="<?= isset($basePath) ? $basePath : '' ?>assets/logoo.png" alt="DNA Vacation"></a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php" class="<?= $currentPage=='home'?'active':'' ?>">Beranda</a></li>
            <li><a href="tour.php" class="<?= $currentPage=='tour'?'active':'' ?>">Tour</a></li>
            <li><a href="rental.php" class="<?= $currentPage=='rental'?'active':'' ?>">Rental</a></li>
            <li><a href="hotel.php" class="<?= $currentPage=='hotel'?'active':'' ?>">Hotel</a></li>
            <li><a href="blog.php" class="<?= $currentPage=='blog'?'active':'' ?>">Blog</a></li>
            <li><a href="about.php" class="<?= $currentPage=='about'?'active':'' ?>">Tentang</a></li>
            <li><a href="kontak.php" class="<?= $currentPage=='kontak'?'active':'' ?>">Kontak</a></li>
            <li><a href="<?= waLink('Halo DNA Vacation, saya ingin booking hotel.') ?>" class="cta" target="_blank">Booking Hotel</a></li>
        </ul>
        <div class="hamburger">
            <span></span><span></span><span></span>
        </div>
    </nav>
</header>

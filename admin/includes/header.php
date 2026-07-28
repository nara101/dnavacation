<?php
require_once __DIR__ . '/../../includes/config.php';
requireLogin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Compute unread badges
$newTourBookings = (int)$pdo->query("SELECT COUNT(*) FROM tour_bookings WHERE status='baru'")->fetchColumn();
$newCarBookings = (int)$pdo->query("SELECT COUNT(*) FROM car_bookings WHERE status='baru'")->fetchColumn();
$newHotelReqs = (int)$pdo->query("SELECT COUNT(*) FROM hotel_requests WHERE status='baru'")->fetchColumn();
$newCustomTrips = (int)$pdo->query("SELECT COUNT(*) FROM custom_trip_requests WHERE status='baru'")->fetchColumn();
$pendingTesti = (int)$pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=0")->fetchColumn();
$unreadMsgs = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - Admin ' : 'Admin ' ?><?= SITE_NAME ?></title>
    <link rel="icon" type="image/jpeg" href="<?= asset('img/logo-dna.jpg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">

<!-- Admin Sidebar -->
<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="<?= ADMIN_URL ?>/">
            <img src="<?= asset('img/logo-dna.jpg') ?>" alt="DNA">
            <div>
                <span>DNA Vacation</span>
                <span class="tag">Admin Panel</span>
            </div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= ADMIN_URL ?>/" class="sidebar-link <?= $currentPage === 'index' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
        <a href="<?= ADMIN_URL ?>/settings.php" class="sidebar-link sidebar-link-highlight <?= $currentPage === 'settings' ? 'active' : '' ?>">
            <i class="bi bi-palette2"></i><span>Pengaturan Tampilan</span>
        </a>

        <div class="sidebar-heading">Tour</div>
        <a href="<?= ADMIN_URL ?>/tours.php" class="sidebar-link <?= in_array($currentPage, ['tours','tour-form']) ? 'active' : '' ?>">
            <i class="bi bi-map"></i><span>Paket Tour</span>
        </a>
        <a href="<?= ADMIN_URL ?>/bookings.php" class="sidebar-link <?= $currentPage === 'bookings' ? 'active' : '' ?>">
            <i class="bi bi-journal-check"></i><span>Booking Tour</span>
            <?php if ($newTourBookings): ?><span class="badge bg-danger ms-auto"><?= $newTourBookings ?></span><?php endif; ?>
        </a>

        <div class="sidebar-heading">Rental Mobil</div>
        <a href="<?= ADMIN_URL ?>/cars.php" class="sidebar-link <?= in_array($currentPage, ['cars','car-form']) ? 'active' : '' ?>">
            <i class="bi bi-car-front"></i><span>Data Mobil</span>
        </a>
        <a href="<?= ADMIN_URL ?>/car-bookings.php" class="sidebar-link <?= $currentPage === 'car-bookings' ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i><span>Booking Mobil</span>
            <?php if ($newCarBookings): ?><span class="badge bg-danger ms-auto"><?= $newCarBookings ?></span><?php endif; ?>
        </a>

        <div class="sidebar-heading">Konten</div>
        <a href="<?= ADMIN_URL ?>/blog.php" class="sidebar-link <?= in_array($currentPage, ['blog','blog-form']) ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i><span>Blog</span>
        </a>
        <a href="<?= ADMIN_URL ?>/testimonials.php" class="sidebar-link <?= $currentPage === 'testimonials' ? 'active' : '' ?>">
            <i class="bi bi-chat-quote"></i><span>Testimoni</span>
            <?php if ($pendingTesti): ?><span class="badge bg-warning text-dark ms-auto"><?= $pendingTesti ?></span><?php endif; ?>
        </a>
        <a href="<?= ADMIN_URL ?>/destinations.php" class="sidebar-link <?= $currentPage === 'destinations' ? 'active' : '' ?>">
            <i class="bi bi-geo-alt"></i><span>Destinasi</span>
        </a>

        <div class="sidebar-heading">Request</div>
        <a href="<?= ADMIN_URL ?>/hotel-requests.php" class="sidebar-link <?= $currentPage === 'hotel-requests' ? 'active' : '' ?>">
            <i class="bi bi-building"></i><span>Request Hotel</span>
            <?php if ($newHotelReqs): ?><span class="badge bg-danger ms-auto"><?= $newHotelReqs ?></span><?php endif; ?>
        </a>
        <a href="<?= ADMIN_URL ?>/custom-trips.php" class="sidebar-link <?= $currentPage === 'custom-trips' ? 'active' : '' ?>">
            <i class="bi bi-compass"></i><span>Custom Trip</span>
            <?php if ($newCustomTrips): ?><span class="badge bg-danger ms-auto"><?= $newCustomTrips ?></span><?php endif; ?>
        </a>
        <a href="<?= ADMIN_URL ?>/messages.php" class="sidebar-link <?= $currentPage === 'messages' ? 'active' : '' ?>">
            <i class="bi bi-envelope"></i><span>Pesan Masuk</span>
            <?php if ($unreadMsgs): ?><span class="badge bg-danger ms-auto"><?= $unreadMsgs ?></span><?php endif; ?>
        </a>

        <div class="sidebar-heading">Sistem</div>
        <a href="<?= ADMIN_URL ?>/settings.php" class="sidebar-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
            <i class="bi bi-gear"></i><span>Pengaturan Tampilan</span>
        </a>
        <a href="<?= ADMIN_URL ?>/logout.php" class="sidebar-link" style="color:#fca5a5">
            <i class="bi bi-box-arrow-left"></i><span>Logout</span>
        </a>
    </nav>
</div>

<!-- Admin Content -->
<div class="admin-content" id="adminContent">
    <!-- Top Bar -->
    <div class="admin-topbar">
        <button class="btn btn-link sidebar-toggle p-0 border-0" id="sidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
            <a href="<?= ADMIN_URL ?>/settings.php" class="btn btn-sm btn-gold">
                <i class="bi bi-palette2"></i> <span class="d-none d-sm-inline">Pengaturan Tampilan</span>
            </a>
            <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye"></i> <span class="d-none d-sm-inline">Lihat Website</span>
            </a>
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-person-circle"></i> <?= e($_SESSION['admin_name'] ?? 'Admin') ?>
            </span>
        </div>
    </div>

    <?php
    $flash = getFlash();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mx-3 mt-3">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

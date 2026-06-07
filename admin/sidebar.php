<?php
$adminPage = $adminPage ?? '';
?>
<div class="sidebar">
    <h2>DNAVACATION</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="tour-admin.php">Kelola Tour</a>
    <a href="rental-admin.php">Kelola Rental</a>
    <a href="blog-admin.php">Kelola Blog</a>

    <hr>

    <a href="logout.php">Logout</a>
</div>
<div class="brand">
    <h2>DNA<span>VACATION</span></h2>
    <small>Admin Panel</small>
</div>
<nav class="sidebar-menu">
    <a href="dashboard.php" class="<?= $adminPage == 'dashboard' ? 'active' : '' ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <div class="menu-group">Master Data</div>
    <a href="tour-admin.php" class="<?= $adminPage == 'tour' ? 'active' : '' ?>"><i class="fa-solid fa-suitcase-rolling"></i> Paket Tour</a>
    <a href="rental-admin.php" class="<?= $adminPage == 'rental' ? 'active' : '' ?>"><i class="fa-solid fa-car"></i> Rental Mobil</a>
    <a href="blog-admin.php" class="<?= $adminPage == 'blog' ? 'active' : '' ?>"><i class="fa-solid fa-newspaper"></i> Blog</a>
    <a href="testimoni-admin.php" class="<?= $adminPage == 'testimoni' ? 'active' : '' ?>"><i class="fa-solid fa-comments"></i> Testimoni</a>
    <div class="menu-group">Transaksi</div>
    <a href="booking-tour.php" class="<?= $adminPage == 'booking_tour' ? 'active' : '' ?>"><i class="fa-solid fa-clipboard-list"></i> Booking Tour</a>
    <a href="booking-rental.php" class="<?= $adminPage == 'booking_rental' ? 'active' : '' ?>"><i class="fa-solid fa-clipboard-check"></i> Booking Rental</a>
    <a href="hotel-request.php" class="<?= $adminPage == 'hotel_request' ? 'active' : '' ?>"><i class="fa-solid fa-hotel"></i> Request Hotel</a>
    <div class="menu-group">Pengaturan</div>
    <a href="settings.php" class="<?= $adminPage == 'settings' ? 'active' : '' ?>"><i class="fa-solid fa-gear"></i> Setting Website</a>
    <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</nav>
</div>
<?php
include "auth.php";
include "sidebar.php";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="content">

        <div class="header">
            <h1>Selamat datang, <?= htmlspecialchars($_SESSION['admin']); ?></h1>
            <p>Kelola data website DNAVacation melalui panel admin</p>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Tour</h3>
                <p>Kelola paket tour dan destinasi wisata</p>
                <a href="tour-admin.php">Kelola Tour</a>
            </div>

            <div class="card">
                <h3>Rental</h3>
                <p>Kelola kendaraan dan harga rental</p>
                <a href="rental-admin.php">Kelola Rental</a>
            </div>

            <div class="card">
                <h3>Blog</h3>
                <p>Kelola artikel dan berita website</p>
                <a href="blog-admin.php">Kelola Blog</a>
            </div>
        </div>

    </div>

</body>

</html>
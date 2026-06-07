<?php
include "auth.php";
include "sidebar.php";
include "../db.php";
$adminPage = 'rental';
$result = $conn->query("SELECT * FROM rental ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Rental</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            display: flex
        }

        .sidebar {
            width: 240px;
            background: #1f3c4a;
            min-height: 100vh;
            padding: 20px;
            color: #fff
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px
        }

        .sidebar a:hover {
            background: #2c5364
        }

        .content {
            flex: 1;
            padding: 30px
        }

        .header {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px
        }

        .btn {
            background: #2c5364;
            color: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none
        }

        table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden
        }

        th,
        td {
            padding: 15px
        }

        th {
            background: #1f3c4a;
            color: #fff
        }
    </style>
    <title>Kelola Rental - DNA Vacation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="tour.php">Kelola Tour</a>
        <a href="rentl-admin.php">Kelola Rental</a>
        <a href="blog.php">Kelola Blog</a>
        <a href="logout.php">Logout</a>
        <?php include "sidebar.php"; ?>
        <div class="content">
            <div class="header">
                <div>
                    <h1>Kelola Rental Mobil</h1>
                    <p>Daftar mobil rental</p>
                </div>
                <a href="rental-add.php" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Mobil</a>
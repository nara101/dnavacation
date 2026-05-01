<?php
include "auth.php";
include "sidebar.php";
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
</head>

<body>

    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="tour.php">Kelola Tour</a>
        <a href="rentl-admin.php">Kelola Rental</a>
        <a href="blog.php">Kelola Blog</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Kelola Rental</h2>
            <a href="rental_tambah.php" class="btn">+ Tambah Rental</a>
        </div>

        <table>
            <tr>
                <th>No</th>
                <th>Nama Kendaraan</th>
                <th>Harga / Hari</th>
                <th>Aksi</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Toyota Avanza</td>
                <td>Rp 350.000</td>
                <td>
                    <a class="btn">Edit</a>
                    <a class="btn" style="background:#b30000">Hapus</a>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
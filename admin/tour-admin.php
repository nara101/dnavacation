<?php
include "auth.php";
include "../db.php";
include "sidebar.php";

$result = mysqli_query($conn, "SELECT * FROM tour ORDER BY id DESC");
$adminPage = 'tour';
$result = $conn->query("SELECT * FROM tour ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Tour</title>
    <title>Kelola Tour - DNA Vacation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="content">

        <div class="header">
            <h1>Kelola Tour</h1>
            <p>Tambah, edit, dan hapus paket tour</p>
        </div>

        <a href="tour-add.php" class="btn-primary">+ Tambah Tour</a>

        <div class="table-box">
            <table>
                <thead>
<?php include "sidebar.php"; ?>
<div class="content">
    <div class="header">
        <div><h1>Kelola Paket Tour</h1><p>Tambah, edit, atau hapus paket tour</p></div>
        <a href="tour-add.php" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Tour</a>
    </div>
    <div class="table-box">
        <table>
            <thead>
                <tr><th>#</th><th>Foto</th><th>Judul</th><th>Lokasi</th><th>Durasi</th><th>Harga</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows): $no=1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <th>No</th>
                        <th>Judul Tour</th>
                        <th>Lokasi</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                        <td><?= $no++ ?></td>
                        <td><img src="<?= fotoUrl($row['gambar']) ?>" class="thumb" onerror="this.src='../assets/yogya.jpg'"></td>
                        <td>
                            <strong><?= htmlspecialchars($row['judul']) ?></strong>
                            <?php if ($row['is_bestseller']): ?><br><span class="status terkonfirmasi" style="font-size:0.65rem;">Best Seller</span><?php endif; ?>
                            <?php if ($row['is_promo']): ?><span class="status dibatalkan" style="font-size:0.65rem;">Promo</span><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['lokasi']) ?></td>
                        <td><?= htmlspecialchars($row['durasi']) ?></td>
                        <td><?= rupiah($row['harga']) ?></td>
                        <td><?= labelKategori($row['kategori']) ?></td>
                        <td><span class="status <?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                        <td class="actions">
                            <a href="tour-edit.php?id=<?= $row['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="tour-delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Hapus tour ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                <td>Rp <?= number_format($row['harga']) ?></td>
                                <td>
                                    <a href="tour-edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="tour-delete.php?id=<?= $row['id'] ?>"
                                        class="btn-delete"
                                        onclick="return confirm('Yakin ingin menghapus tour ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">Belum ada data tour</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
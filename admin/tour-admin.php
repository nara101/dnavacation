<?php
include "auth.php";
include "../db.php";
include "sidebar.php";

$result = mysqli_query($conn, "SELECT * FROM tour ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Tour</title>
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
                    <tr>
                        <th>No</th>
                        <th>Judul Tour</th>
                        <th>Lokasi</th>
                        <th>Harga</th>
                        <th>Aksi</th>
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

    </div>

</body>

</html>
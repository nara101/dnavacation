<?php
include "auth.php";
include "../db.php";
$adminPage = 'blog';
$result = $conn->query("SELECT * FROM blog ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Blog</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="header">
            <div>
                <h1>Kelola Blog</h1>
                <p>Tambah, edit, atau hapus artikel</p>
            </div>
            <a href="blog-add.php" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Artikel</a>
        </div>
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows): $no = 1;
                        while ($r = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><img src="<?= fotoUrl($r['gambar'], 'blog') ?>" class="thumb" onerror="this.src='../assets/yogya.jpg'"></td>
                                <td><strong><?= htmlspecialchars($r['judul']) ?></strong></td>
                                <td><?= labelKategori($r['kategori']) ?></td>
                                <td><span class="status <?= $r['status'] ?>"><?= $r['status'] ?></span></td>
                                <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                                <td class="actions">
                                    <a href="../blog-detail.php?id=<?= $r['id'] ?>" target="_blank" class="btn-view"><i class="fa-solid fa-eye"></i></a>
                                    <a href="blog-edit.php?id=<?= $r['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="blog-delete.php?id=<?= $r['id'] ?>" class="btn-delete" onclick="return confirm('Hapus artikel?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="7" class="empty-row">Belum ada artikel</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
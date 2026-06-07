<?php
include "auth.php";
include "../db.php";
$adminPage = 'blog';
$err = '';
if (isset($_POST['submit'])) {
    $judul = trim($_POST['judul']);
    $kategori = $_POST['kategori'];
    $konten = $_POST['konten'];
    $meta_title = trim($_POST['meta_title']);
    $meta_desc = trim($_POST['meta_description']);
    $status = $_POST['status'];
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $judul));
    $gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'blog_' . time() . '_' . rand(100, 999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . "/../uploads/blog/$gambar");
    }
    $stmt = $conn->prepare("INSERT INTO blog (judul,slug,kategori,konten,gambar,meta_title,meta_description,status) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssss", $judul, $slug, $kategori, $konten, $gambar, $meta_title, $meta_desc, $status);
    if ($stmt->execute()) {
        header("Location: blog-admin.php");
        exit;
    }
    $err = $conn->error;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Artikel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="header">
            <div>
                <h1>Tambah Artikel Blog</h1>
            </div><a href="blog-admin.php" class="btn-primary" style="background:#666;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group"><label>Judul Artikel *</label><input type="text" name="judul" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Kategori *</label>
                        <select name="kategori">
                            <option value="tips_travel">Tips Travel</option>
                            <option value="rekomendasi_destinasi">Rekomendasi Destinasi</option>
                            <option value="paket_tour">Paket Tour</option>
                            <option value="hotel">Hotel</option>
                            <option value="rental_mobil">Rental Mobil</option>
                            <option value="promo">Promo</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Status</label>
                        <select name="status">
                            <option value="publish">Publish</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Konten Artikel *</label><textarea name="konten" rows="14" required></textarea></div>
                <div class="form-group"><label>Foto Cover</label><input type="file" name="gambar" accept="image/*"></div>
                <hr>
                <h4 style="color:#0a3d62;margin:18px 0 10px;">SEO Settings</h4>
                <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title" placeholder="Untuk hasil pencarian Google"></div>
                <div class="form-group"><label>Meta Description</label><textarea name="meta_description" rows="2"></textarea></div>
                <div class="form-actions">
                    <button name="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    <a href="blog-admin.php" class="btn-primary" style="background:#777;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
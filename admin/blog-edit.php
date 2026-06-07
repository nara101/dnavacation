<?php
include "auth.php";
include "../db.php";
$adminPage = 'blog';
if (!isset($_GET['id'])) {
    header("Location: blog-admin.php");
    exit;
}
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM blog WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) {
    echo "Artikel tidak ditemukan";
    exit;
}

$err = '';
if (isset($_POST['submit'])) {
    $judul = trim($_POST['judul']);
    $kategori = $_POST['kategori'];
    $konten = $_POST['konten'];
    $meta_title = trim($_POST['meta_title']);
    $meta_desc = trim($_POST['meta_description']);
    $status = $_POST['status'];
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $judul));
    $gambar = $data['gambar'];
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'blog_' . time() . '_' . rand(100, 999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . "/../uploads/blog/$gambar");
    }
    $stmt = $conn->prepare("UPDATE blog SET judul=?,slug=?,kategori=?,konten=?,gambar=?,meta_title=?,meta_description=?,status=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $judul, $slug, $kategori, $konten, $gambar, $meta_title, $meta_desc, $status, $id);
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
    <title>Edit Artikel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="header">
            <div>
                <h1>Edit Artikel</h1>
            </div><a href="blog-admin.php" class="btn-primary" style="background:#666;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group"><label>Judul *</label><input type="text" name="judul" value="<?= htmlspecialchars($data['judul']) ?>" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Kategori</label>
                        <select name="kategori">
                            <?php foreach (['tips_travel', 'rekomendasi_destinasi', 'paket_tour', 'hotel', 'rental_mobil', 'promo'] as $k): ?>
                                <option value="<?= $k ?>" <?= $data['kategori'] == $k ? 'selected' : '' ?>><?= labelKategori($k) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Status</label>
                        <select name="status">
                            <option value="publish" <?= $data['status'] == 'publish' ? 'selected' : '' ?>>Publish</option>
                            <option value="draft" <?= $data['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Konten *</label><textarea name="konten" rows="14" required><?= htmlspecialchars($data['konten']) ?></textarea></div>
                <div class="form-group">
                    <label>Foto Cover</label>
                    <?php if ($data['gambar']): ?><div><img src="<?= fotoUrl($data['gambar'], 'blog') ?>" style="height:100px;border-radius:8px;margin-bottom:10px;" onerror="this.src='../assets/yogya.jpg'"></div><?php endif; ?>
                    <input type="file" name="gambar" accept="image/*">
                </div>
                <h4 style="color:#0a3d62;margin:18px 0 10px;">SEO Settings</h4>
                <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title" value="<?= htmlspecialchars($data['meta_title']) ?>"></div>
                <div class="form-group"><label>Meta Description</label><textarea name="meta_description" rows="2"><?= htmlspecialchars($data['meta_description']) ?></textarea></div>
                <div class="form-actions">
                    <button name="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                    <a href="blog-admin.php" class="btn-primary" style="background:#777;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
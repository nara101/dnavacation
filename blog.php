<?php
include "db.php";
$currentPage = 'blog';
$pageTitle = 'Blog & Tips Travel - DNA Vacation';
$pageDesc = 'Tips travel, rekomendasi destinasi, panduan liburan dari DNA Vacation.';

$where = ["status='publish'"];
if (!empty($_GET['kategori'])) {
    $k = $conn->real_escape_string($_GET['kategori']);
    $where[] = "kategori='$k'";
}
if (!empty($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $where[] = "(judul LIKE '%$q%' OR konten LIKE '%$q%')";
}
$whereSQL = implode(' AND ', $where);
$result = $conn->query("SELECT * FROM blog WHERE $whereSQL ORDER BY id DESC");

include "includes/header.php";
?>
<section class="page-hero">
    <h1>Blog & Artikel</h1>
    <p class="breadcrumb-trail"><a href="index.php">Home</a> / Blog</p>
</section>

<section class="tour-list-section">
    <div class="container">
        <form method="GET" class="filter-bar">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Cari Artikel</label>
                    <input type="text" name="q" class="form-control" placeholder="Kata kunci..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach (['tips_travel','rekomendasi_destinasi','paket_tour','hotel','rental_mobil','promo'] as $k): ?>
                            <option value="<?= $k ?>" <?= ($_GET['kategori']??'')==$k?'selected':'' ?>><?= labelKategori($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn w-100" style="background:#0a3d62;color:#fff;border:none;padding:10px 0;border-radius:8px;">Filter</button>
                </div>
            </div>
        </form>

        <?php if ($result && $result->num_rows): ?>
            <div class="blog-grid">
                <?php while ($b = $result->fetch_assoc()): ?>
                    <a href="blog-detail.php?id=<?= $b['id'] ?>" class="blog-card">
                        <img src="<?= fotoUrl($b['gambar'], 'blog') ?>" alt="<?= htmlspecialchars($b['judul']) ?>">
                        <div class="blog-content">
                            <p class="meta">
                                <i class="fa-regular fa-folder"></i> <?= labelKategori($b['kategori']) ?>
                                &nbsp;<i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($b['created_at'])) ?>
                            </p>
                            <h5><?= htmlspecialchars($b['judul']) ?></h5>
                            <p><?= htmlspecialchars(substr(strip_tags($b['konten']), 0, 110)) ?>...</p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <h4>Belum ada artikel</h4>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include "includes/footer.php"; ?>

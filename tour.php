<?php
include "db.php";
$currentPage = 'tour';
$pageTitle = 'Paket Tour - DNA Vacation';
$pageDesc = 'Pilih paket tour terbaik dari DNA Vacation: Open Trip, Private Trip, Family, Honeymoon, Corporate.';

// Filter
$where = ["status='aktif'"];
if (!empty($_GET['lokasi'])) {
    $loc = $conn->real_escape_string($_GET['lokasi']);
    $where[] = "lokasi LIKE '%$loc%'";
}
if (!empty($_GET['kategori'])) {
    $kat = $conn->real_escape_string($_GET['kategori']);
    $where[] = "kategori='$kat'";
}
if (!empty($_GET['durasi'])) {
    $d = (int)$_GET['durasi'];
    if ($d >= 4) {
        $where[] = "durasi REGEXP '[4-9] Hari|1[0-9] Hari'";
    } else {
        $where[] = "durasi LIKE '$d Hari%'";
    }
}
if (!empty($_GET['harga'])) {
    switch ($_GET['harga']) {
        case 'lt1': $where[] = "harga < 1000000"; break;
        case '1to3': $where[] = "harga BETWEEN 1000000 AND 3000000"; break;
        case '3to5': $where[] = "harga BETWEEN 3000000 AND 5000000"; break;
        case 'gt5': $where[] = "harga > 5000000"; break;
    }
}
if (!empty($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $where[] = "(judul LIKE '%$q%' OR lokasi LIKE '%$q%' OR deskripsi LIKE '%$q%')";
}

$sort = $_GET['sort'] ?? 'newest';
$order = match($sort) {
    'cheap' => 'harga ASC',
    'expensive' => 'harga DESC',
    'popular' => 'is_bestseller DESC, id DESC',
    default => 'id DESC',
};

$whereSQL = implode(' AND ', $where);
$result = $conn->query("SELECT * FROM tour WHERE $whereSQL ORDER BY $order");

include "includes/header.php";
?>
<section class="page-hero">
    <h1>Paket Tour</h1>
    <p class="breadcrumb-trail"><a href="index.php">Home</a> / Paket Tour</p>
</section>

<section class="tour-list-section">
    <div class="container">
        <form method="GET" class="filter-bar">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" name="q" class="form-control" placeholder="Cari paket..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Lokasi</label>
                    <select name="lokasi" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach (['Bali','Yogyakarta','Malang','Bromo','Surabaya','Karimunjawa','Lombok'] as $loc): ?>
                            <option value="<?= $loc ?>" <?= ($_GET['lokasi'] ?? '')==$loc?'selected':'' ?>><?= $loc ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua</option>
                        <option value="open_trip" <?= ($_GET['kategori']??'')=='open_trip'?'selected':'' ?>>Open Trip</option>
                        <option value="private_trip" <?= ($_GET['kategori']??'')=='private_trip'?'selected':'' ?>>Private Trip</option>
                        <option value="family_trip" <?= ($_GET['kategori']??'')=='family_trip'?'selected':'' ?>>Family Trip</option>
                        <option value="honeymoon" <?= ($_GET['kategori']??'')=='honeymoon'?'selected':'' ?>>Honeymoon</option>
                        <option value="corporate_trip" <?= ($_GET['kategori']??'')=='corporate_trip'?'selected':'' ?>>Corporate Trip</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Harga</label>
                    <select name="harga" class="form-select">
                        <option value="">Semua</option>
                        <option value="lt1" <?= ($_GET['harga']??'')=='lt1'?'selected':'' ?>>&lt; 1 Juta</option>
                        <option value="1to3" <?= ($_GET['harga']??'')=='1to3'?'selected':'' ?>>1 - 3 Juta</option>
                        <option value="3to5" <?= ($_GET['harga']??'')=='3to5'?'selected':'' ?>>3 - 5 Juta</option>
                        <option value="gt5" <?= ($_GET['harga']??'')=='gt5'?'selected':'' ?>>&gt; 5 Juta</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Terbaru</option>
                        <option value="cheap" <?= $sort=='cheap'?'selected':'' ?>>Termurah</option>
                        <option value="expensive" <?= $sort=='expensive'?'selected':'' ?>>Termahal</option>
                        <option value="popular" <?= $sort=='popular'?'selected':'' ?>>Populer</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100" style="background:#0a3d62;border:none;padding:10px 0;border-radius:8px;"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="tour-grid">
                <?php while ($t = $result->fetch_assoc()): ?>
                    <a href="tour-detail.php?id=<?= $t['id'] ?>" class="paket-card">
                        <?php if ($t['is_bestseller']): ?><span class="badge-card">Best Seller</span><?php endif; ?>
                        <?php if (!$t['is_bestseller'] && $t['is_promo']): ?><span class="badge-card promo">Promo</span><?php endif; ?>
                        <img src="<?= fotoUrl($t['gambar']) ?>" alt="<?= htmlspecialchars($t['judul']) ?>">
                        <div class="body">
                            <span class="badge text-bg-secondary mb-2"><?= labelKategori($t['kategori']) ?></span>
                            <h3><?= htmlspecialchars($t['judul']) ?></h3>
                            <div class="meta">
                                <i class="fa-regular fa-clock"></i> <?= htmlspecialchars($t['durasi']) ?>
                                &nbsp;|&nbsp;
                                <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($t['lokasi']) ?>
                            </div>
                            <div class="price"><small>Mulai dari</small><br><?= rupiah($t['harga']) ?> /pax</div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-suitcase fa-3x text-muted mb-3"></i>
                <h4>Tidak ada paket sesuai filter</h4>
                <p class="text-muted">Coba ubah filter pencarian Anda</p>
                <a href="tour.php" class="btnpaket">Reset Filter</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>

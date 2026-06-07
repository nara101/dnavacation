<?php
include "db.php";
$currentPage = 'rental';
$pageTitle = 'Rental Mobil - DNA Vacation';
$pageDesc = 'Sewa mobil dengan atau tanpa sopir di DNA Vacation, harga terjangkau dan armada terawat.';

$where = ["status='tersedia'"];
if (!empty($_GET['kapasitas'])) {
    $k = (int)$_GET['kapasitas'];
    $where[] = "kapasitas >= $k";
}
if (!empty($_GET['transmisi'])) {
    $t = $conn->real_escape_string($_GET['transmisi']);
    $where[] = "transmisi='$t'";
}
if (!empty($_GET['harga'])) {
    switch ($_GET['harga']) {
        case 'lt400': $where[] = "harga_per_hari < 400000"; break;
        case '400to700': $where[] = "harga_per_hari BETWEEN 400000 AND 700000"; break;
        case '700to1500': $where[] = "harga_per_hari BETWEEN 700000 AND 1500000"; break;
        case 'gt1500': $where[] = "harga_per_hari > 1500000"; break;
    }
}
if (!empty($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $where[] = "(nama_mobil LIKE '%$q%' OR tipe LIKE '%$q%')";
}

$whereSQL = implode(' AND ', $where);
$result = $conn->query("SELECT * FROM rental WHERE $whereSQL ORDER BY harga_per_hari ASC");

include "includes/header.php";
?>
<section class="page-hero">
    <h1>Rental Mobil</h1>
    <p class="breadcrumb-trail"><a href="index.php">Home</a> / Rental</p>
</section>

<section class="tour-list-section">
    <div class="container">
        <form method="GET" class="filter-bar">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Cari Mobil</label>
                    <input type="text" name="q" class="form-control" placeholder="Misal: Avanza..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Kapasitas Min</label>
                    <select name="kapasitas" class="form-select">
                        <option value="">Semua</option>
                        <option value="4" <?= ($_GET['kapasitas']??'')=='4'?'selected':'' ?>>4 Orang</option>
                        <option value="6" <?= ($_GET['kapasitas']??'')=='6'?'selected':'' ?>>6 Orang</option>
                        <option value="7" <?= ($_GET['kapasitas']??'')=='7'?'selected':'' ?>>7 Orang</option>
                        <option value="14" <?= ($_GET['kapasitas']??'')=='14'?'selected':'' ?>>14+ Orang</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Transmisi</label>
                    <select name="transmisi" class="form-select">
                        <option value="">Semua</option>
                        <option value="manual" <?= ($_GET['transmisi']??'')=='manual'?'selected':'' ?>>Manual</option>
                        <option value="automatic" <?= ($_GET['transmisi']??'')=='automatic'?'selected':'' ?>>Automatic</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Harga/Hari</label>
                    <select name="harga" class="form-select">
                        <option value="">Semua</option>
                        <option value="lt400" <?= ($_GET['harga']??'')=='lt400'?'selected':'' ?>>&lt; Rp 400rb</option>
                        <option value="400to700" <?= ($_GET['harga']??'')=='400to700'?'selected':'' ?>>Rp 400rb - 700rb</option>
                        <option value="700to1500" <?= ($_GET['harga']??'')=='700to1500'?'selected':'' ?>>Rp 700rb - 1.5jt</option>
                        <option value="gt1500" <?= ($_GET['harga']??'')=='gt1500'?'selected':'' ?>>&gt; Rp 1.5jt</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn w-100" style="background:#0a3d62;color:#fff;border:none;padding:10px 0;border-radius:8px;"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="tour-grid">
                <?php while ($r = $result->fetch_assoc()): ?>
                    <a href="rental-detail.php?id=<?= $r['id'] ?>" class="paket-card">
                        <img src="<?= fotoUrl($r['gambar'], 'rentals') ?>" alt="<?= htmlspecialchars($r['nama_mobil']) ?>">
                        <div class="body">
                            <span class="badge text-bg-secondary mb-2"><?= htmlspecialchars($r['tipe']) ?></span>
                            <h3><?= htmlspecialchars($r['nama_mobil']) ?></h3>
                            <div class="meta">
                                <i class="fa-solid fa-users"></i> <?= $r['kapasitas'] ?> Orang
                                &nbsp;|&nbsp;
                                <i class="fa-solid fa-gear"></i> <?= ucfirst($r['transmisi']) ?>
                            </div>
                            <div class="price"><small>Mulai dari</small><br><?= rupiah($r['harga_per_hari']) ?> /hari</div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-car fa-3x text-muted mb-3"></i>
                <h4>Tidak ada mobil sesuai filter</h4>
                <a href="rental.php" class="btnpaket">Reset Filter</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>

<?php
$pageTitle = 'Sewa Mobil';
require_once 'includes/header.php';

$where = ['c.is_available = 1'];
$params = [];

if (!empty($_GET['type'])) { $where[] = 'c.type = ?'; $params[] = $_GET['type']; }
if (!empty($_GET['capacity'])) { $where[] = 'c.capacity >= ?'; $params[] = (int)$_GET['capacity']; }
if (!empty($_GET['transmission'])) { $where[] = 'c.transmission = ?'; $params[] = $_GET['transmission']; }
if (!empty($_GET['max_price'])) { $where[] = 'c.price_with_driver <= ?'; $params[] = (int)$_GET['max_price']; }
if (!empty($_GET['search'])) {
    $where[] = '(c.name LIKE ? OR c.brand LIKE ?)';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}

$sort = 'ORDER BY c.is_featured DESC, c.price_with_driver ASC';
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc': $sort = 'ORDER BY c.price_with_driver ASC'; break;
        case 'price_desc': $sort = 'ORDER BY c.price_with_driver DESC'; break;
        case 'capacity': $sort = 'ORDER BY c.capacity DESC'; break;
    }
}

$whereSQL = implode(' AND ', $where);
$stmt = $pdo->prepare("SELECT * FROM cars c WHERE {$whereSQL} {$sort}");
$stmt->execute($params);
$cars = $stmt->fetchAll();

// Get pickup date from query (if user came from search)
$pickupDate = $_GET['pickup_date'] ?? '';
$returnDate = $_GET['return_date'] ?? '';
?>

<div class="page-header">
    <div class="container">
        <h1>Rent <span class="script">Mobil</span></h1>
        <p>Pilihan mobil terbaik dengan/tanpa sopir untuk perjalanan Anda di Sulawesi & Indonesia</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Filter Sidebar -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-funnel-fill text-primary-custom"></i> Filter Mobil</h6>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Cari Nama/Brand</label>
                            <input type="text" name="search" class="form-control" placeholder="Avanza, Innova..." value="<?= e($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Jenis Mobil</label>
                            <select name="type" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="mpv" <?= ($_GET['type']??'')==='mpv'?'selected':'' ?>>MPV</option>
                                <option value="suv" <?= ($_GET['type']??'')==='suv'?'selected':'' ?>>SUV</option>
                                <option value="sedan" <?= ($_GET['type']??'')==='sedan'?'selected':'' ?>>Sedan</option>
                                <option value="city" <?= ($_GET['type']??'')==='city'?'selected':'' ?>>City Car</option>
                                <option value="minibus" <?= ($_GET['type']??'')==='minibus'?'selected':'' ?>>Minibus</option>
                                <option value="bus" <?= ($_GET['type']??'')==='bus'?'selected':'' ?>>Bus</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kapasitas Min.</label>
                            <select name="capacity" class="form-select">
                                <option value="">Semua</option>
                                <option value="4" <?= ($_GET['capacity']??'')==='4'?'selected':'' ?>>4+ orang</option>
                                <option value="7" <?= ($_GET['capacity']??'')==='7'?'selected':'' ?>>7+ orang</option>
                                <option value="12" <?= ($_GET['capacity']??'')==='12'?'selected':'' ?>>12+ orang</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Transmisi</label>
                            <select name="transmission" class="form-select">
                                <option value="">Semua</option>
                                <option value="automatic" <?= ($_GET['transmission']??'')==='automatic'?'selected':'' ?>>Automatic</option>
                                <option value="manual" <?= ($_GET['transmission']??'')==='manual'?'selected':'' ?>>Manual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Max Harga/Hari</label>
                            <select name="max_price" class="form-select">
                                <option value="">Semua</option>
                                <option value="500000" <?= ($_GET['max_price']??'')==='500000'?'selected':'' ?>>&lt; Rp 500rb</option>
                                <option value="800000" <?= ($_GET['max_price']??'')==='800000'?'selected':'' ?>>&lt; Rp 800rb</option>
                                <option value="1500000" <?= ($_GET['max_price']??'')==='1500000'?'selected':'' ?>>&lt; Rp 1,5jt</option>
                                <option value="3000000" <?= ($_GET['max_price']??'')==='3000000'?'selected':'' ?>>&lt; Rp 3jt</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="">Rekomendasi</option>
                                <option value="price_asc" <?= ($_GET['sort']??'')==='price_asc'?'selected':'' ?>>Harga Terendah</option>
                                <option value="price_desc" <?= ($_GET['sort']??'')==='price_desc'?'selected':'' ?>>Harga Tertinggi</option>
                                <option value="capacity" <?= ($_GET['sort']??'')==='capacity'?'selected':'' ?>>Kapasitas Terbanyak</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-funnel"></i> Terapkan Filter</button>
                        <?php if (array_filter($_GET)): ?>
                        <a href="<?= BASE_URL ?>/cars.php" class="btn btn-outline-secondary w-100 mt-2 btn-sm">Reset Filter</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Cars Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted small mb-0">Menampilkan <strong><?= count($cars) ?></strong> mobil tersedia</p>
                </div>

                <?php if (empty($cars)): ?>
                    <div class="text-center py-5 bg-white rounded-3">
                        <i class="bi bi-car-front text-muted" style="font-size:3rem"></i>
                        <p class="mt-3 text-muted">Tidak ada mobil yang sesuai dengan filter Anda.</p>
                        <a href="<?= BASE_URL ?>/cars.php" class="btn btn-primary-custom">Reset Filter</a>
                    </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($cars as $car):
                        $facilities = json_decode($car['facilities'], true) ?: [];
                    ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card car-card position-relative">
                            <?php if ($car['is_featured']): ?><span class="badge-bestseller"><i class="bi bi-star-fill me-1"></i>Featured</span><?php endif; ?>
                            <div class="image-wrapper">
                            <?php if ($car['image']): ?>
                                <img src="<?= uploadUrl($car['image']) ?>" class="card-img-top" alt="<?= e($car['name']) ?>" onerror="handleImgError(this)">
                            <?php else: ?>
                                <div class="card-img-top placeholder-img" style="height:210px"><i class="bi bi-car-front"></i></div>
                            <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if ($car['brand']): ?>
                                    <small class="text-muted text-uppercase" style="letter-spacing:.5px;font-weight:600;font-size:.7rem"><?= e($car['brand']) ?> · <?= $car['year'] ?></small>
                                <?php endif; ?>
                                <h5 class="card-title mt-1"><?= e($car['name']) ?></h5>
                                <div class="card-meta">
                                    <span><i class="bi bi-people me-1"></i><?= $car['capacity'] ?> orang</span>
                                    <span><i class="bi bi-briefcase me-1"></i><?= $car['luggage'] ?? 2 ?> koper</span>
                                    <span><i class="bi bi-gear me-1"></i><?= ucfirst($car['transmission']) ?></span>
                                </div>
                                <?php if ($facilities): ?>
                                <div class="mb-2">
                                    <?php foreach (array_slice($facilities, 0, 3) as $f): ?>
                                    <span class="badge bg-light text-dark border me-1 mb-1" style="font-size:.7rem;font-weight:500"><?= e($f) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($facilities) > 3): ?>
                                    <span class="badge bg-light text-muted border" style="font-size:.7rem">+<?= count($facilities) - 3 ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div class="border-top pt-3 mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Mulai dari</small>
                                        <div class="price"><?= formatRupiah($car['price_with_driver']) ?><small class="d-block text-end">/hari + sopir</small></div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?= BASE_URL ?>/car-detail.php?slug=<?= e($car['slug']) ?>" class="btn btn-outline-primary-custom btn-sm flex-fill">Detail</a>
                                        <a href="<?= BASE_URL ?>/car-detail.php?slug=<?= e($car['slug']) ?>#booking" class="btn btn-primary-custom btn-sm flex-fill">Booking</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

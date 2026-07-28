<?php
$pageTitle = 'Paket Tour';
require_once 'includes/header.php';

// Filters
$where = ['t.is_active = 1'];
$params = [];

if (!empty($_GET['destination'])) {
    $where[] = 't.destination_id = ?';
    $params[] = (int)$_GET['destination'];
}
if (!empty($_GET['category'])) {
    $where[] = 't.category_id = ?';
    $params[] = (int)$_GET['category'];
}
if (!empty($_GET['search'])) {
    $where[] = '(t.title LIKE ? OR t.description LIKE ?)';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}
if (!empty($_GET['max_price'])) {
    $where[] = 't.price <= ?';
    $params[] = (int)$_GET['max_price'];
}
if (!empty($_GET['duration'])) {
    $where[] = 't.duration_days = ?';
    $params[] = (int)$_GET['duration'];
}

$sort = 'ORDER BY t.is_best_seller DESC, t.sort_order ASC, t.created_at DESC';
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc': $sort = 'ORDER BY t.price ASC'; break;
        case 'price_desc': $sort = 'ORDER BY t.price DESC'; break;
        case 'newest': $sort = 'ORDER BY t.created_at DESC'; break;
        case 'popular': $sort = 'ORDER BY t.view_count DESC'; break;
    }
}

$whereSQL = implode(' AND ', $where);

// Pagination
$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM tours t WHERE {$whereSQL}");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT t.*, d.name as destination_name, c.name as category_name FROM tours t LEFT JOIN destinations d ON t.destination_id=d.id LEFT JOIN tour_categories c ON t.category_id=c.id WHERE {$whereSQL} {$sort} LIMIT {$perPage} OFFSET {$offset}");
$stmt->execute($params);
$tours = $stmt->fetchAll();

$allDestinations = $pdo->query("SELECT * FROM destinations ORDER BY name")->fetchAll();
$allCategories = $pdo->query("SELECT * FROM tour_categories ORDER BY name")->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>Paket <span class="script">Tour</span></h1>
        <p>Temukan paket tour terbaik untuk liburan impian Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Filter Sidebar -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-funnel-fill text-primary-custom"></i> Filter Tour</h6>
                    <form id="filterForm" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Cari Paket</label>
                            <input type="text" name="search" class="form-control" placeholder="Toraja, Wakatobi..." value="<?= e($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Destinasi</label>
                            <select name="destination" class="form-select">
                                <option value="">Semua Destinasi</option>
                                <?php foreach ($allDestinations as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= ($_GET['destination'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Jenis Trip</label>
                            <select name="category" class="form-select">
                                <option value="">Semua Jenis</option>
                                <?php foreach ($allCategories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($_GET['category'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Durasi</label>
                            <select name="duration" class="form-select">
                                <option value="">Semua Durasi</option>
                                <option value="2" <?= ($_GET['duration'] ?? '') == '2' ? 'selected' : '' ?>>2 Hari</option>
                                <option value="3" <?= ($_GET['duration'] ?? '') == '3' ? 'selected' : '' ?>>3 Hari</option>
                                <option value="4" <?= ($_GET['duration'] ?? '') == '4' ? 'selected' : '' ?>>4 Hari</option>
                                <option value="5" <?= ($_GET['duration'] ?? '') == '5' ? 'selected' : '' ?>>5 Hari</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Max Harga</label>
                            <select name="max_price" class="form-select">
                                <option value="">Semua Harga</option>
                                <option value="2000000" <?= ($_GET['max_price'] ?? '') == '2000000' ? 'selected' : '' ?>>&lt; Rp 2jt</option>
                                <option value="4000000" <?= ($_GET['max_price'] ?? '') == '4000000' ? 'selected' : '' ?>>&lt; Rp 4jt</option>
                                <option value="6000000" <?= ($_GET['max_price'] ?? '') == '6000000' ? 'selected' : '' ?>>&lt; Rp 6jt</option>
                                <option value="10000000" <?= ($_GET['max_price'] ?? '') == '10000000' ? 'selected' : '' ?>>&lt; Rp 10jt</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="">Rekomendasi</option>
                                <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Harga Termurah</option>
                                <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                                <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                <option value="popular" <?= ($_GET['sort'] ?? '') == 'popular' ? 'selected' : '' ?>>Terpopuler</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-funnel"></i> Terapkan</button>
                        <?php if (array_filter($_GET)): ?>
                        <a href="<?= BASE_URL ?>/tours.php" class="btn btn-outline-secondary w-100 mt-2 btn-sm">Reset Filter</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Tour Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted small mb-0">Menampilkan <strong><?= count($tours) ?></strong> dari <strong><?= $total ?></strong> paket</p>
                </div>
                <?php if (empty($tours)): ?>
                    <div class="text-center py-5 bg-white rounded-3">
                        <i class="bi bi-search text-muted" style="font-size:3rem"></i>
                        <p class="mt-3 text-muted">Tidak ada paket tour yang sesuai dengan filter Anda.</p>
                        <a href="<?= BASE_URL ?>/tours.php" class="btn btn-primary-custom">Lihat Semua Paket</a>
                    </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($tours as $tour): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card tour-card position-relative">
                            <?php if ($tour['is_best_seller']): ?><span class="badge-bestseller"><i class="bi bi-star-fill me-1"></i>Best Seller</span><?php endif; ?>
                            <?php if ($tour['is_promo']): ?><span class="badge-promo"><i class="bi bi-fire me-1"></i>Promo</span><?php endif; ?>
                            <div class="image-wrapper">
                            <?php if ($tour['image']): ?>
                                <img src="<?= uploadUrl($tour['image']) ?>" class="card-img-top" alt="<?= e($tour['title']) ?>" onerror="handleImgError(this)">
                            <?php else: ?>
                                <div class="card-img-top placeholder-img" style="height:210px"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="card-meta">
                                    <span><i class="bi bi-geo-alt me-1"></i><?= e($tour['destination_name'] ?? '-') ?></span>
                                    <span><i class="bi bi-clock me-1"></i><?= e($tour['duration']) ?></span>
                                </div>
                                <h5 class="card-title"><?= e($tour['title']) ?></h5>
                                <p class="small text-muted flex-grow-1"><?= truncate(strip_tags($tour['description']), 80) ?></p>
                                <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-end">
                                    <div class="price">
                                        <?php if ($tour['is_promo'] && $tour['promo_price']): ?>
                                            <small class="text-decoration-line-through text-muted d-block"><?= formatRupiah($tour['price']) ?></small>
                                            <?= formatRupiah($tour['promo_price']) ?>
                                        <?php else: ?>
                                            <?= formatRupiah($tour['price']) ?>
                                        <?php endif; ?>
                                        <small>/orang</small>
                                    </div>
                                    <a href="<?= BASE_URL ?>/tour-detail.php?slug=<?= e($tour['slug']) ?>" class="btn btn-primary-custom btn-sm">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"><i class="bi bi-chevron-left"></i></a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"><i class="bi bi-chevron-right"></i></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

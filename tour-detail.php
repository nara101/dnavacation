<?php
require_once 'includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . '/tours.php'); exit; }

$stmt = $pdo->prepare("SELECT t.*, d.name as destination_name, c.name as category_name FROM tours t LEFT JOIN destinations d ON t.destination_id=d.id LEFT JOIN tour_categories c ON t.category_id=c.id WHERE t.slug=? AND t.is_active=1");
$stmt->execute([$slug]);
$tour = $stmt->fetch();
if (!$tour) { header('Location: ' . BASE_URL . '/tours.php'); exit; }

// Increment view count
$pdo->prepare("UPDATE tours SET view_count = view_count + 1 WHERE id = ?")->execute([$tour['id']]);

$pageTitle = $tour['title'];
$pageDescription = truncate(strip_tags($tour['description']), 160);

// Handle booking form submission
$bookingSuccess = false;
$bookingError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_tour'])) {
    $name = trim($_POST['customer_name'] ?? '');
    $wa = trim($_POST['customer_whatsapp'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $date = $_POST['departure_date'] ?? '';
    $persons = max(1, (int)($_POST['num_persons'] ?? 1));
    $notes = trim($_POST['notes'] ?? '');

    if ($name && $wa) {
        $code = generateBookingCode('TRB');
        $totalPrice = ($tour['is_promo'] && $tour['promo_price'] ? $tour['promo_price'] : $tour['price']) * $persons;

        $ins = $pdo->prepare("INSERT INTO tour_bookings (booking_code, tour_id, customer_name, customer_whatsapp, customer_email, departure_date, num_persons, total_price, notes) VALUES (?,?,?,?,?,?,?,?,?)");
        $ins->execute([$code, $tour['id'], $name, $wa, $email, $date ?: null, $persons, $totalPrice, $notes]);
        $bookingSuccess = true;

        // Build WhatsApp message
        $waMsg = "Halo DNA Vacation, saya ingin booking paket tour:\n";
        $waMsg .= "Kode Booking: {$code}\n";
        $waMsg .= "Nama Paket: {$tour['title']}\n";
        $waMsg .= "Tanggal Keberangkatan: " . ($date ?: 'Belum ditentukan') . "\n";
        $waMsg .= "Jumlah Peserta: {$persons}\n";
        $waMsg .= "Nama: {$name}\n";
        $waMsg .= "Catatan: " . ($notes ?: '-');
    } else {
        $bookingError = 'Nama dan nomor WhatsApp wajib diisi.';
    }
}

// Related tours
$related = $pdo->prepare("SELECT t.*, d.name as destination_name FROM tours t LEFT JOIN destinations d ON t.destination_id=d.id WHERE t.is_active=1 AND t.id != ? AND (t.destination_id = ? OR t.category_id = ?) ORDER BY RAND() LIMIT 3");
$related->execute([$tour['id'], $tour['destination_id'], $tour['category_id']]);
$relatedTours = $related->fetchAll();

$itinerary = json_decode($tour['itinerary'], true) ?: [];
$gallery = json_decode($tour['gallery'], true) ?: [];
$includes = json_decode($tour['includes'], true) ?: [];
$excludes = json_decode($tour['excludes'], true) ?: [];

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><?= e($tour['title']) ?></h1>
        <div class="breadcrumb-custom">
            <a href="<?= BASE_URL ?>/">Home</a> <span>/</span>
            <a href="<?= BASE_URL ?>/tours.php">Paket Tour</a> <span>/</span>
            <span class="active"><?= e($tour['title']) ?></span>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($bookingSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Booking berhasil!</strong> Kode booking Anda: <strong><?= e($code) ?></strong>. Tim kami akan segera menghubungi Anda.
            <a href="<?= waLink($waMsg) ?>" target="_blank" class="btn btn-wa btn-sm ms-2">Konfirmasi via WhatsApp</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($bookingError): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= e($bookingError) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Badges row -->
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <?php if ($tour['is_best_seller']): ?><span class="badge" style="background:var(--secondary);padding:.4rem .8rem;border-radius:100px"><i class="bi bi-star-fill"></i> Best Seller</span><?php endif; ?>
                    <?php if ($tour['is_promo']): ?><span class="badge" style="background:var(--accent);padding:.4rem .8rem;border-radius:100px"><i class="bi bi-fire"></i> Promo</span><?php endif; ?>
                    <span class="badge bg-light text-primary-custom border" style="padding:.4rem .8rem;border-radius:100px"><i class="bi bi-eye"></i> <?= $tour['view_count'] ?> views</span>
                </div>

                <!-- Main Image -->
                <?php if ($tour['image']): ?>
                    <img src="<?= uploadUrl($tour['image']) ?>" class="detail-main-img mb-3" alt="<?= e($tour['title']) ?>" onerror="handleImgError(this)">
                <?php else: ?>
                    <div class="detail-main-img placeholder-img mb-3 rounded-3" style="height:400px"><i class="bi bi-image" style="font-size:4rem"></i></div>
                <?php endif; ?>

                <!-- Gallery -->
                <?php if ($gallery): ?>
                <div class="row g-2 mb-4">
                    <?php foreach (array_slice($gallery, 0, 4) as $img): ?>
                    <div class="col-3">
                        <img src="<?= uploadUrl($img) ?>" class="w-100 rounded-2 detail-gallery" style="height:100px;object-fit:cover" alt="Gallery" onerror="handleImgError(this)">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-geo-alt text-primary-custom me-1"></i><?= e($tour['destination_name'] ?? '-') ?></span>
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-clock text-primary-custom me-1"></i><?= e($tour['duration']) ?></span>
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-tag text-primary-custom me-1"></i><?= e($tour['category_name'] ?? '-') ?></span>
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-people text-primary-custom me-1"></i><?= $tour['min_persons'] ?>-<?= $tour['max_persons'] ?> orang</span>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h5 class="fw-bold">Deskripsi</h5>
                    <div class="text-muted"><?= nl2br(e($tour['description'])) ?></div>
                </div>

                <!-- Itinerary -->
                <?php if ($itinerary): ?>
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Itinerary</h5>
                    <?php foreach ($itinerary as $day): ?>
                    <div class="itinerary-day">
                        <h6>Hari <?= $day['day'] ?? '' ?> - <?= e($day['title'] ?? '') ?></h6>
                        <?php if (isset($day['activities'])): ?>
                        <ul class="small text-muted">
                            <?php foreach ($day['activities'] as $act): ?>
                            <li><?= e($act) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Facilities -->
                <div class="row g-4 mb-4">
                    <?php if ($includes): ?>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Fasilitas Termasuk</h5>
                        <ul class="facility-list">
                            <?php foreach ($includes as $item): ?>
                            <li class="included"><i class="bi bi-check-circle-fill"></i><?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if ($excludes): ?>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Tidak Termasuk</h5>
                        <ul class="facility-list">
                            <?php foreach ($excludes as $item): ?>
                            <li class="excluded"><i class="bi bi-x-circle-fill"></i><?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Meeting Point -->
                <?php if ($tour['meeting_point']): ?>
                <div class="mb-4">
                    <h5 class="fw-bold">Meeting Point</h5>
                    <p class="text-muted"><i class="bi bi-pin-map me-1"></i><?= e($tour['meeting_point']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Terms -->
                <?php if ($tour['terms']): ?>
                <div class="mb-4">
                    <h5 class="fw-bold">Syarat & Ketentuan</h5>
                    <p class="text-muted small"><?= nl2br(e($tour['terms'])) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Booking Sidebar -->
            <div class="col-lg-4">
                <div class="booking-sidebar">
                    <div class="mb-3">
                        <span class="text-muted small">Mulai dari</span>
                        <div class="price-display">
                            <?php if ($tour['is_promo'] && $tour['promo_price']): ?>
                                <small class="text-decoration-line-through text-muted d-block" style="font-size:.85rem"><?= formatRupiah($tour['price']) ?></small>
                                <?= formatRupiah($tour['promo_price']) ?>
                            <?php else: ?>
                                <?= formatRupiah($tour['price']) ?>
                            <?php endif; ?>
                        </div>
                        <span class="text-muted small">/<?= e($tour['price_label'] ?: 'per orang') ?></span>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3">Form Booking</h6>
                    <form method="POST" action="">
                        <input type="hidden" name="book_tour" value="1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap *</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nomor WhatsApp *</label>
                            <input type="text" name="customer_whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email (opsional)</label>
                            <input type="email" name="customer_email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tanggal Keberangkatan</label>
                            <input type="date" name="departure_date" class="form-control" min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Jumlah Peserta</label>
                            <input type="number" name="num_persons" class="form-control" value="1" min="1" max="<?= $tour['max_persons'] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 mb-2">
                            <i class="bi bi-calendar-check me-1"></i>Booking Sekarang
                        </button>
                    </form>
                    <a href="<?= waLink("Halo DNA Vacation, saya tertarik dengan paket: {$tour['title']}. Bisa info lebih lanjut?") ?>"
                       target="_blank" class="btn btn-wa w-100">
                        <i class="bi bi-whatsapp me-1"></i>Tanya via WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Tours -->
        <?php if ($relatedTours): ?>
        <div class="mt-5">
            <h4 class="fw-bold mb-4">Paket Tour Serupa</h4>
            <div class="row g-4">
                <?php foreach ($relatedTours as $rt): ?>
                <div class="col-md-4">
                    <div class="card tour-card position-relative">
                        <?php if ($rt['image']): ?>
                            <img src="<?= uploadUrl($rt['image']) ?>" class="card-img-top" alt="<?= e($rt['title']) ?>" onerror="handleImgError(this)">
                        <?php else: ?>
                            <div class="card-img-top placeholder-img" style="height:200px"><i class="bi bi-image"></i></div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="card-meta">
                                <span><i class="bi bi-geo-alt me-1"></i><?= e($rt['destination_name'] ?? '-') ?></span>
                                <span><i class="bi bi-clock me-1"></i><?= e($rt['duration']) ?></span>
                            </div>
                            <h6 class="card-title"><?= e($rt['title']) ?></h6>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="price"><?= formatRupiah($rt['price']) ?> <small>/orang</small></span>
                                <a href="<?= BASE_URL ?>/tour-detail.php?slug=<?= e($rt['slug']) ?>" class="btn btn-primary-custom btn-sm">Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

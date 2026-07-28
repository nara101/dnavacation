<?php
require_once 'includes/config.php';
$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . '/cars.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM cars WHERE slug=? AND is_available=1");
$stmt->execute([$slug]);
$car = $stmt->fetch();
if (!$car) { header('Location: ' . BASE_URL . '/cars.php'); exit; }

$pageTitle = $car['name'];
$pageDescription = truncate(strip_tags($car['description']), 160);
$facilities = json_decode($car['facilities'], true) ?: [];

$bookingSuccess = false;
$bookingError = '';
$code = '';
$waMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_car'])) {
    $name = trim($_POST['customer_name'] ?? '');
    $wa = trim($_POST['customer_whatsapp'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $pickupTime = $_POST['pickup_time'] ?? '08:00';
    $pickup = trim($_POST['pickup_location'] ?? '');
    $returnLoc = trim($_POST['return_location'] ?? '');
    $withDriver = (int)($_POST['with_driver'] ?? 1);
    $notes = trim($_POST['notes'] ?? '');

    if ($name && $wa && $startDate && $endDate) {
        if (strtotime($endDate) < strtotime($startDate)) {
            $bookingError = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
        } else {
            $code = generateBookingCode('CRB');
            $days = max(1, ceil((strtotime($endDate) - strtotime($startDate)) / 86400) + 1);
            $pricePerDay = $withDriver ? $car['price_with_driver'] : $car['price_without_driver'];
            $totalPrice = $pricePerDay * $days;

            $ins = $pdo->prepare("INSERT INTO car_bookings (booking_code, car_id, customer_name, customer_whatsapp, start_date, end_date, pickup_time, pickup_location, return_location, with_driver, total_price, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $ins->execute([$code, $car['id'], $name, $wa, $startDate, $endDate, $pickupTime, $pickup, $returnLoc, $withDriver, $totalPrice, $notes]);
            $bookingSuccess = true;

            $waMsg = "Halo DNA Vacation, saya ingin sewa mobil:\n";
            $waMsg .= "Kode Booking: {$code}\n";
            $waMsg .= "Mobil: {$car['name']}\n";
            $waMsg .= "Tanggal Sewa: {$startDate} s/d {$endDate} ({$days} hari)\n";
            $waMsg .= "Jam Penjemputan: {$pickupTime}\n";
            $waMsg .= "Lokasi Penjemputan: " . ($pickup ?: '-') . "\n";
            $waMsg .= "Lokasi Pengembalian: " . ($returnLoc ?: 'Sama dengan penjemputan') . "\n";
            $waMsg .= "Dengan Sopir: " . ($withDriver ? 'Ya' : 'Tidak') . "\n";
            $waMsg .= "Total Estimasi: Rp " . number_format($totalPrice, 0, ',', '.') . "\n";
            $waMsg .= "Nama: {$name}\n";
            $waMsg .= "Catatan: " . ($notes ?: '-');
        }
    } else {
        $bookingError = 'Lengkapi semua data yang wajib diisi (Nama, WA, tanggal mulai & selesai).';
    }
}

// Related cars
$related = $pdo->prepare("SELECT * FROM cars WHERE is_available=1 AND id != ? AND type = ? LIMIT 3");
$related->execute([$car['id'], $car['type']]);
$relatedCars = $related->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><?= e($car['name']) ?></h1>
        <div class="breadcrumb-custom">
            <a href="<?= BASE_URL ?>/">Home</a> <span>/</span>
            <a href="<?= BASE_URL ?>/cars.php">Rent Mobil</a> <span>/</span>
            <span class="active"><?= e($car['name']) ?></span>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($bookingSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong><i class="bi bi-check-circle-fill"></i> Booking berhasil!</strong> Kode Anda: <strong><?= e($code) ?></strong>.
            Tim kami akan konfirmasi via WhatsApp. Silakan lanjut chat untuk mempercepat proses.
            <a href="<?= waLink($waMsg) ?>" target="_blank" class="btn btn-wa btn-sm ms-2 mt-2 mt-md-0"><i class="bi bi-whatsapp"></i> Konfirmasi via WA</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($bookingError): ?>
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($bookingError) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <?php if ($car['image']): ?>
                    <img src="<?= uploadUrl($car['image']) ?>" class="detail-main-img mb-4" alt="<?= e($car['name']) ?>" onerror="handleImgError(this)">
                <?php else: ?>
                    <div class="detail-main-img placeholder-img mb-4 rounded-3"><i class="bi bi-car-front" style="font-size:5rem"></i></div>
                <?php endif; ?>

                <!-- Quick Facts -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border text-center h-100">
                            <i class="bi bi-people text-primary-custom d-block mb-1" style="font-size:1.5rem"></i>
                            <div class="fw-bold small"><?= $car['capacity'] ?> Penumpang</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border text-center h-100">
                            <i class="bi bi-briefcase text-primary-custom d-block mb-1" style="font-size:1.5rem"></i>
                            <div class="fw-bold small"><?= $car['luggage'] ?? 2 ?> Koper</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border text-center h-100">
                            <i class="bi bi-gear text-primary-custom d-block mb-1" style="font-size:1.5rem"></i>
                            <div class="fw-bold small"><?= ucfirst($car['transmission']) ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border text-center h-100">
                            <i class="bi bi-fuel-pump text-primary-custom d-block mb-1" style="font-size:1.5rem"></i>
                            <div class="fw-bold small"><?= ucfirst($car['fuel_type'] ?? 'bensin') ?></div>
                        </div>
                    </div>
                </div>

                <?php if ($car['description']): ?>
                <div class="mb-4">
                    <h5 class="fw-bold mb-2">Deskripsi</h5>
                    <p class="text-muted"><?= nl2br(e($car['description'])) ?></p>
                </div>
                <?php endif; ?>

                <?php if ($facilities): ?>
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Fasilitas Mobil</h5>
                    <div class="row g-2">
                        <?php foreach ($facilities as $f): ?>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-center">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span class="small"><?= e($f) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Pilihan Paket & Harga</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:linear-gradient(135deg,var(--primary-lighter),#fff);border:1.5px solid var(--primary-light)">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-person-badge text-primary-custom fs-4"></i>
                                    <h6 class="fw-bold mb-0">Dengan Sopir</h6>
                                </div>
                                <p class="fw-bold mb-1 text-primary-custom" style="font-size:1.4rem"><?= formatRupiah($car['price_with_driver']) ?></p>
                                <small class="text-muted d-block mb-2">per hari · 12 jam</small>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="bi bi-check2 text-success"></i> Sopir profesional</li>
                                    <li><i class="bi bi-check2 text-success"></i> BBM di dalam kota</li>
                                    <li><i class="bi bi-check2 text-success"></i> Tanpa deposit</li>
                                </ul>
                            </div>
                        </div>
                        <?php if ($car['has_self_drive'] && $car['price_without_driver']): ?>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 h-100" style="background:#f8fafc;border:1.5px solid var(--gray-200)">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-steering-wheel text-secondary-custom fs-4" style="color:var(--secondary)!important"></i>
                                    <h6 class="fw-bold mb-0">Tanpa Sopir (Self Drive)</h6>
                                </div>
                                <p class="fw-bold mb-1" style="color:var(--secondary);font-size:1.4rem"><?= formatRupiah($car['price_without_driver']) ?></p>
                                <small class="text-muted d-block mb-2">per hari · 24 jam</small>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="bi bi-check2 text-success"></i> Free 24 jam pemakaian</li>
                                    <li><i class="bi bi-check2 text-success"></i> SIM A wajib</li>
                                    <li><i class="bi bi-info-circle text-warning"></i> Deposit jaminan</li>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($car['service_area']): ?>
                <div class="mb-4">
                    <h5 class="fw-bold mb-2">Area Layanan</h5>
                    <p class="text-muted"><i class="bi bi-geo-alt-fill text-primary-custom"></i> <?= e($car['service_area']) ?></p>
                </div>
                <?php endif; ?>

                <?php if ($car['terms']): ?>
                <div class="mb-4">
                    <h5 class="fw-bold mb-2">Syarat Sewa</h5>
                    <div class="text-muted small p-3 bg-light rounded-3"><?= nl2br(e($car['terms'])) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4" id="booking">
                <div class="booking-sidebar">
                    <h6 class="fw-bold mb-3"><i class="bi bi-calendar-check text-primary-custom"></i> Booking Mobil</h6>
                    <form method="POST">
                        <input type="hidden" name="book_car" value="1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <label class="form-label small fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="startDate" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-semibold">Jam Ambil</label>
                                <input type="time" name="pickup_time" class="form-control" value="08:00">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="endDate" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Pilihan Paket <span class="text-danger">*</span></label>
                            <select name="with_driver" id="withDriver" class="form-select" onchange="calcPrice()">
                                <option value="1" data-price="<?= $car['price_with_driver'] ?>">Dengan Sopir - <?= formatRupiah($car['price_with_driver']) ?>/hari</option>
                                <?php if ($car['has_self_drive'] && $car['price_without_driver']): ?>
                                <option value="0" data-price="<?= $car['price_without_driver'] ?>">Tanpa Sopir - <?= formatRupiah($car['price_without_driver']) ?>/hari</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Lokasi Penjemputan</label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="Bandara, hotel, alamat...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Lokasi Pengembalian</label>
                            <input type="text" name="return_location" class="form-control" placeholder="Kosongkan jika sama dengan penjemputan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Rute yang direncanakan, keperluan khusus, dll"></textarea>
                        </div>

                        <!-- Price Summary -->
                        <div id="priceSummary" class="p-3 rounded-3 mb-3" style="background:var(--primary-lighter)">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Harga per hari</span>
                                <span class="fw-semibold" id="pricePerDay"><?= formatRupiah($car['price_with_driver']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Durasi</span>
                                <span class="fw-semibold" id="duration">- hari</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total Estimasi</span>
                                <span class="fw-bold text-primary-custom" id="totalPrice">-</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 mb-2">
                            <i class="bi bi-calendar-check"></i> Booking Sekarang
                        </button>
                    </form>
                    <a href="<?= waLink("Halo DNA Vacation, saya ingin sewa {$car['name']}. Bisa info lebih lanjut?") ?>" target="_blank" class="btn btn-wa w-100">
                        <i class="bi bi-whatsapp"></i> Tanya via WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Cars -->
        <?php if ($relatedCars): ?>
        <div class="mt-5">
            <h4 class="fw-bold mb-4">Mobil Serupa</h4>
            <div class="row g-4">
                <?php foreach ($relatedCars as $rc): ?>
                <div class="col-md-4">
                    <div class="card car-card">
                        <div class="image-wrapper">
                        <?php if ($rc['image']): ?>
                            <img src="<?= uploadUrl($rc['image']) ?>" class="card-img-top" alt="<?= e($rc['name']) ?>" onerror="handleImgError(this)">
                        <?php else: ?>
                            <div class="card-img-top placeholder-img" style="height:200px"><i class="bi bi-car-front"></i></div>
                        <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title"><?= e($rc['name']) ?></h6>
                            <div class="card-meta">
                                <span><i class="bi bi-people me-1"></i><?= $rc['capacity'] ?> orang</span>
                                <span><i class="bi bi-gear me-1"></i><?= ucfirst($rc['transmission']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="price"><?= formatRupiah($rc['price_with_driver']) ?> <small>/hari</small></span>
                                <a href="<?= BASE_URL ?>/car-detail.php?slug=<?= e($rc['slug']) ?>" class="btn btn-primary-custom btn-sm">Detail</a>
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

<script>
function formatRp(n) {
    return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function calcPrice() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    const driverSel = document.getElementById('withDriver');
    const perDay = parseInt(driverSel.selectedOptions[0].dataset.price || 0);
    document.getElementById('pricePerDay').textContent = formatRp(perDay);
    if (start && end) {
        const days = Math.max(1, Math.ceil((new Date(end) - new Date(start)) / 86400000) + 1);
        document.getElementById('duration').textContent = days + ' hari';
        document.getElementById('totalPrice').textContent = formatRp(perDay * days);
    } else {
        document.getElementById('duration').textContent = '- hari';
        document.getElementById('totalPrice').textContent = '-';
    }
}
document.getElementById('startDate')?.addEventListener('change', function() {
    document.getElementById('endDate').min = this.value;
    calcPrice();
});
document.getElementById('endDate')?.addEventListener('change', calcPrice);
</script>

<?php require_once 'includes/footer.php'; ?>

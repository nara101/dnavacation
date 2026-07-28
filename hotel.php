<?php
$pageTitle = 'Booking Hotel';
require_once 'includes/header.php';

$success = false;
$waMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_hotel'])) {
    $name = trim($_POST['customer_name'] ?? '');
    $wa = trim($_POST['customer_whatsapp'] ?? '');
    $dest = trim($_POST['destination'] ?? '');
    $checkin = $_POST['check_in'] ?? '';
    $checkout = $_POST['check_out'] ?? '';
    $guests = max(1, (int)($_POST['num_guests'] ?? 1));
    $rooms = max(1, (int)($_POST['num_rooms'] ?? 1));
    $budget = trim($_POST['budget'] ?? '');
    $type = trim($_POST['hotel_type'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($name && $wa) {
        $ins = $pdo->prepare("INSERT INTO hotel_requests (customer_name, customer_whatsapp, destination, check_in, check_out, num_guests, num_rooms, budget, hotel_type, notes) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $ins->execute([$name, $wa, $dest, $checkin ?: null, $checkout ?: null, $guests, $rooms, $budget, $type, $notes]);
        $success = true;

        $waMsg = "Halo DNA Vacation, saya ingin dibantu booking hotel:\n";
        $waMsg .= "Destinasi: " . ($dest ?: '-') . "\n";
        $waMsg .= "Check-in: " . ($checkin ?: '-') . "\n";
        $waMsg .= "Check-out: " . ($checkout ?: '-') . "\n";
        $waMsg .= "Jumlah Tamu: {$guests}\n";
        $waMsg .= "Jumlah Kamar: {$rooms}\n";
        $waMsg .= "Budget: " . ($budget ?: '-') . "\n";
        $waMsg .= "Tipe Hotel: " . ($type ?: '-') . "\n";
        $waMsg .= "Nama: {$name}\n";
        $waMsg .= "Catatan: " . ($notes ?: '-');
    }
}
?>

<div class="page-header">
    <div class="container">
        <h1>Booking <span class="script">Hotel</span></h1>
        <p>Bingung cari hotel? Ceritakan kebutuhan Anda, kami carikan yang terbaik!</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong><i class="bi bi-check-circle-fill"></i> Request terkirim!</strong> Tim kami akan segera menghubungi Anda dengan rekomendasi hotel terbaik.
            <a href="<?= waLink($waMsg) ?>" target="_blank" class="btn btn-wa btn-sm ms-2 mt-2 mt-md-0"><i class="bi bi-whatsapp"></i> Lanjut Chat WA</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- CTA Banner -->
        <div class="rounded-3 p-4 p-md-5 mb-5 text-white text-center" style="background:linear-gradient(135deg,var(--primary-darker),var(--primary))">
            <i class="bi bi-building-fill-check" style="font-size:3rem"></i>
            <h3 class="fw-bold mt-2">Booking Hotel? Kami Bantu Lewat WhatsApp!</h3>
            <p class="mb-3 opacity-90 mx-auto" style="max-width:600px">Karena setiap traveler punya preferensi unik, kami menyediakan layanan booking hotel personal via WhatsApp. Cepat, transparan, dan tanpa hidden cost.</p>
            <a href="<?= waLink('Halo DNA Vacation, saya ingin dibantu booking hotel.') ?>" target="_blank" class="btn btn-lg" style="background:#fff;color:var(--primary-darker);font-weight:700">
                <i class="bi bi-whatsapp"></i> Chat Admin Sekarang
            </a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-3">Kenapa Booking Hotel via DNA Vacation?</h3>
                <div class="mb-3">
                    <div class="d-flex gap-3 mb-3">
                        <div class="feature-icon flex-shrink-0"><i class="bi bi-search"></i></div>
                        <div>
                            <h6 class="fw-bold">Kami Carikan Hotel Terbaik</h6>
                            <p class="text-muted small mb-0">Tim kami mencarikan hotel sesuai budget, lokasi, dan preferensi khusus Anda.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="feature-icon flex-shrink-0"><i class="bi bi-cash-coin"></i></div>
                        <div>
                            <h6 class="fw-bold">Harga Kompetitif</h6>
                            <p class="text-muted small mb-0">Kerjasama langsung dengan banyak hotel untuk harga terbaik.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="feature-icon flex-shrink-0"><i class="bi bi-headset"></i></div>
                        <div>
                            <h6 class="fw-bold">Konsultasi Gratis</h6>
                            <p class="text-muted small mb-0">Bingung pilih hotel? Konsultasi dulu via WhatsApp — 100% gratis!</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="feature-icon flex-shrink-0"><i class="bi bi-heart"></i></div>
                        <div>
                            <h6 class="fw-bold">Personal Recommendation</h6>
                            <p class="text-muted small mb-0">Rekomendasi berdasarkan pengalaman langsung tim kami di destinasi.</p>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mt-4 mb-3">Jenis Akomodasi yang Kami Bantu</h5>
                <div class="row g-2">
                    <?php foreach (['Budget Hotel', 'Family Hotel', 'Villa', 'Resort', 'Homestay', 'Hotel Bintang 4-5'] as $ht): ?>
                    <div class="col-6">
                        <div class="p-2 border rounded-2 text-center small"><i class="bi bi-building text-primary-custom me-1"></i><?= $ht ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="booking-sidebar" style="position:static">
                    <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-text text-primary-custom"></i> Form Request Hotel</h5>
                    <form method="POST">
                        <input type="hidden" name="request_hotel" value="1">
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kota/Destinasi</label>
                            <input type="text" name="destination" class="form-control" placeholder="Contoh: Bali, Toraja, Labuan Bajo">
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-semibold">Check-in</label>
                                <input type="date" name="check_in" class="form-control" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-semibold">Check-out</label>
                                <input type="date" name="check_out" class="form-control" min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-semibold">Jumlah Tamu</label>
                                <input type="number" name="num_guests" class="form-control" value="2" min="1">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-semibold">Jumlah Kamar</label>
                                <input type="number" name="num_rooms" class="form-control" value="1" min="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tipe Hotel</label>
                            <select name="hotel_type" class="form-select">
                                <option value="">Pilih tipe</option>
                                <option>Budget Hotel</option>
                                <option>Family Hotel</option>
                                <option>Villa</option>
                                <option>Resort</option>
                                <option>Homestay</option>
                                <option>Hotel Bintang 4-5</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Budget per Malam</label>
                            <select name="budget" class="form-select">
                                <option value="">Pilih budget</option>
                                <option>&lt; Rp 300.000</option>
                                <option>Rp 300.000 - Rp 500.000</option>
                                <option>Rp 500.000 - Rp 1.000.000</option>
                                <option>Rp 1.000.000 - Rp 2.000.000</option>
                                <option>&gt; Rp 2.000.000</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Preferensi khusus (dekat pantai, kolam renang, sarapan, dll)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 mb-2"><i class="bi bi-send"></i> Kirim Request</button>
                        <a href="<?= waLink('Halo DNA Vacation, saya ingin dibantu booking hotel.') ?>" target="_blank" class="btn btn-wa w-100">
                            <i class="bi bi-whatsapp"></i> Chat Langsung via WA
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

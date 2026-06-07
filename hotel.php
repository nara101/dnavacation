<?php
include "db.php";
$currentPage = 'hotel';
$pageTitle = 'Bantuan Booking Hotel - DNA Vacation';
$pageDesc = 'Bantuan booking hotel terbaik di seluruh Indonesia via WhatsApp DNA Vacation.';

$success = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request'])) {
    $nama = trim($_POST['nama']);
    $wa = trim($_POST['whatsapp']);
    $destinasi = trim($_POST['destinasi']);
    $cin = $_POST['check_in'];
    $cout = $_POST['check_out'];
    $tamu = (int)$_POST['tamu'];
    $budget = trim($_POST['budget'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($nama && $wa && $destinasi && $cin && $cout) {
        $stmt = $conn->prepare("INSERT INTO hotel_request (nama,whatsapp,destinasi,check_in,check_out,jumlah_tamu,budget,catatan) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssiss", $nama, $wa, $destinasi, $cin, $cout, $tamu, $budget, $catatan);
        if ($stmt->execute()) {
            $success = true;
            $waMsg = "Halo DNA Vacation, saya ingin dibantu booking hotel:\n"
                . "Destinasi: $destinasi\n"
                . "Check-in: $cin\nCheck-out: $cout\n"
                . "Jumlah Tamu: $tamu\n"
                . "Budget: " . ($budget ?: '-') . "\n"
                . "Nama: $nama\n"
                . "Catatan: " . ($catatan ?: '-');
            $waRedirect = waLink($waMsg);
        }
    } else {
        $error = "Lengkapi data wajib";
    }
}
include "includes/header.php";
?>
<section class="page-hero">
    <h1>Bantuan Booking Hotel</h1>
    <p class="breadcrumb-trail"><a href="index.php">Home</a> / Hotel</p>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 style="color:#0a3d62; font-weight:800;">Cari Hotel? Biar Kami yang Bantu!</h2>
                <p class="lead text-muted">
                    Mulai dari budget hotel, family hotel, villa, hingga resort eksklusif.
                    Tim DNA Vacation siap membantu Anda menemukan hotel terbaik sesuai kebutuhan.
                </p>
                <div class="row g-3 mt-3">
                    <?php foreach ([
                        ['Budget Hotel','fa-hotel'],
                        ['Family Hotel','fa-people-roof'],
                        ['Villa Eksklusif','fa-umbrella-beach'],
                        ['Resort Premium','fa-spa'],
                        ['Hotel Dekat Wisata','fa-map-location-dot'],
                        ['Apartemen Harian','fa-building'],
                    ] as $item): ?>
                        <div class="col-6">
                            <div style="background:#f8f9fb; padding:18px; border-radius:12px; display:flex; align-items:center; gap:14px;">
                                <i class="fa-solid <?= $item[1] ?>" style="color:#f39c12; font-size:1.5rem;"></i>
                                <span style="color:#0a3d62; font-weight:600;"><?= $item[0] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="booking-form mt-4 mt-lg-0">
                    <h3><i class="fa-solid fa-hotel me-2"></i>Request Booking Hotel</h3>
                    <?php if ($success): ?>
                        <div class="alert-success-dna"><i class="fa-solid fa-circle-check me-2"></i>Permintaan dikirim! Akan diarahkan ke WhatsApp...</div>
                        <script>setTimeout(() => location.href = <?= json_encode($waRedirect) ?>, 1500);</script>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label>Nama *</label><input type="text" name="nama" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label>WhatsApp *</label><input type="text" name="whatsapp" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label>Kota / Destinasi *</label><input type="text" name="destinasi" class="form-control" placeholder="Misal: Bali, Yogyakarta" required></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label>Check-in *</label><input type="date" name="check_in" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                            <div class="col-md-6 mb-3"><label>Check-out *</label><input type="date" name="check_out" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label>Jumlah Tamu</label><input type="number" name="tamu" value="2" min="1" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label>Budget /malam</label><input type="text" name="budget" placeholder="Misal: Rp 500rb" class="form-control"></div>
                        </div>
                        <div class="mb-3"><label>Catatan</label><textarea name="catatan" rows="2" class="form-control" placeholder="Spesifikasi hotel yang diinginkan..."></textarea></div>
                        <button name="request" class="btn-book"><i class="fa-solid fa-paper-plane me-2"></i>Kirim Permintaan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="background:#f8f9fb; padding:80px 20px;">
    <div class="section-title">
        <h2>Kenapa Booking Hotel Lewat DNA Vacation?</h2>
    </div>
    <div class="container">
        <div class="row g-4">
            <?php foreach ([
                ['fa-tag','Harga Negotiable','Kami bantu cari harga terbaik untuk Anda'],
                ['fa-headset','Personal Assistant','Dilayani langsung tim profesional'],
                ['fa-shield','Aman & Terpercaya','Hotel verified dan rekomended'],
            ] as $f): ?>
                <div class="col-md-4">
                    <div class="category-card">
                        <i class="fa-solid <?= $f[0] ?>"></i>
                        <h4><?= $f[1] ?></h4>
                        <p><?= $f[2] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>

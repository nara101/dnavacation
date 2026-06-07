<?php
include "db.php";
$currentPage = 'tour';

if (!isset($_GET['id'])) {
    header("Location: tour.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM tour WHERE id=? AND status='aktif'");
$stmt->bind_param("i", $id);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();

if (!$tour) {
    http_response_code(404);
    $pageTitle = 'Tour tidak ditemukan';
    include "includes/header.php";
    echo "<section class='page-hero'><h1>Tour Tidak Ditemukan</h1></section>";
    echo "<div class='container py-5 text-center'><a href='tour.php' class='btnpaket'>Kembali ke daftar tour</a></div>";
    include "includes/footer.php";
    exit;
}

$pageTitle = $tour['judul'] . ' - DNA Vacation';
$pageDesc = substr(strip_tags($tour['deskripsi']), 0, 160);

// Proses booking form
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $nama = trim($_POST['nama']);
    $wa = trim($_POST['whatsapp']);
    $email = trim($_POST['email'] ?? '');
    $tgl = $_POST['tanggal'];
    $peserta = (int)$_POST['peserta'];
    $catatan = trim($_POST['catatan'] ?? '');

    if ($nama && $wa && $tgl && $peserta > 0) {
        $stmt = $conn->prepare("INSERT INTO tour_booking (tour_id,nama,whatsapp,email,tanggal_keberangkatan,jumlah_peserta,catatan) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("issssis", $id, $nama, $wa, $email, $tgl, $peserta, $catatan);
        if ($stmt->execute()) {
            $success = true;
            $waMsg = "Halo DNA Vacation, saya ingin booking paket tour:\n"
                . "Nama Paket: {$tour['judul']}\n"
                . "Tanggal Keberangkatan: $tgl\n"
                . "Jumlah Peserta: $peserta\n"
                . "Nama: $nama\n"
                . "Catatan: " . ($catatan ?: '-');
            $waRedirect = waLink($waMsg);
        }
    } else {
        $error = 'Lengkapi data yang wajib diisi';
    }
}

// Rekomendasi paket serupa
$rec = $conn->query("SELECT * FROM tour WHERE id!=$id AND status='aktif' AND (lokasi LIKE '%{$tour['lokasi']}%' OR kategori='{$tour['kategori']}') ORDER BY RAND() LIMIT 3");

include "includes/header.php";
?>
<div class="tour-detail-hero">
    <img src="<?= fotoUrl($tour['gambar']) ?>" alt="<?= htmlspecialchars($tour['judul']) ?>">
</div>

<div class="container">
    <div class="tour-detail-info">
        <div class="row">
            <div class="col-lg-8">
                <span class="badge text-bg-warning mb-2"><?= labelKategori($tour['kategori']) ?></span>
                <?php if ($tour['is_bestseller']): ?><span class="badge text-bg-danger ms-1 mb-2">Best Seller</span><?php endif; ?>
                <h1><?= htmlspecialchars($tour['judul']) ?></h1>
                <div class="meta-row">
                    <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($tour['lokasi']) ?></span>
                    <span><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($tour['durasi']) ?></span>
                    <?php if ($tour['meeting_point']): ?>
                        <span><i class="fa-solid fa-map-pin"></i> Meeting: <?= htmlspecialchars($tour['meeting_point']) ?></span>
                    <?php endif; ?>
                </div>

                <h3 class="section-title-detail">Deskripsi Paket</h3>
                <div><?= nl2br(htmlspecialchars($tour['deskripsi'])) ?></div>

                <?php if ($tour['itinerary']): ?>
                <h3 class="section-title-detail">Itinerary Perjalanan</h3>
                <div><?= nl2br(htmlspecialchars($tour['itinerary'])) ?></div>
                <?php endif; ?>

                <div class="row mt-3">
                    <?php if ($tour['fasilitas_termasuk']): ?>
                    <div class="col-md-6">
                        <h3 class="section-title-detail" style="color:#16a085;"><i class="fa-solid fa-circle-check me-2"></i>Termasuk</h3>
                        <ul style="padding-left:18px;">
                            <?php foreach (explode("\n", $tour['fasilitas_termasuk']) as $f): if (trim($f)): ?>
                                <li><?= htmlspecialchars(trim($f)) ?></li>
                            <?php endif; endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if ($tour['fasilitas_tidak_termasuk']): ?>
                    <div class="col-md-6">
                        <h3 class="section-title-detail" style="color:#e74c3c;"><i class="fa-solid fa-circle-xmark me-2"></i>Tidak Termasuk</h3>
                        <ul style="padding-left:18px;">
                            <?php foreach (explode("\n", $tour['fasilitas_tidak_termasuk']) as $f): if (trim($f)): ?>
                                <li><?= htmlspecialchars(trim($f)) ?></li>
                            <?php endif; endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($tour['syarat_ketentuan']): ?>
                <h3 class="section-title-detail">Syarat & Ketentuan</h3>
                <div><?= nl2br(htmlspecialchars($tour['syarat_ketentuan'])) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="tour-price-box">
                    <small>Mulai dari</small>
                    <div class="price"><?= rupiah($tour['harga']) ?></div>
                    <small>/ peserta</small>
                </div>

                <div class="booking-form" id="bookingForm">
                    <h3>Form Booking</h3>
                    <?php if ($success): ?>
                        <div class="alert-success-dna">
                            <i class="fa-solid fa-circle-check me-2"></i>Booking berhasil dikirim! Anda akan diarahkan ke WhatsApp...
                        </div>
                        <script>setTimeout(() => location.href = <?= json_encode($waRedirect) ?>, 1500);</script>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nama Lengkap *</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>No. WhatsApp *</label>
                            <input type="text" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="mb-3">
                            <label>Email (opsional)</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Keberangkatan *</label>
                            <input type="date" name="tanggal" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Jumlah Peserta *</label>
                            <input type="number" name="peserta" class="form-control" min="1" value="2" required>
                        </div>
                        <div class="mb-3">
                            <label>Catatan</label>
                            <textarea name="catatan" rows="2" class="form-control" placeholder="Permintaan khusus..."></textarea>
                        </div>
                        <button name="book" class="btn-book"><i class="fa-solid fa-paper-plane me-2"></i>Kirim Booking</button>
                    </form>
                    <a href="<?= waLink("Halo DNA Vacation, saya tertarik dengan paket: {$tour['judul']}") ?>" class="btn-wa" target="_blank">
                        <i class="fa-brands fa-whatsapp me-2"></i>Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <?php if ($rec && $rec->num_rows): ?>
        <hr class="my-5">
        <h3 class="section-title-detail">Paket Serupa</h3>
        <div class="row g-4">
            <?php while ($r = $rec->fetch_assoc()): ?>
                <div class="col-md-4">
                    <a href="tour-detail.php?id=<?= $r['id'] ?>" class="paket-card">
                        <img src="<?= fotoUrl($r['gambar']) ?>" alt="">
                        <div class="body">
                            <h3><?= htmlspecialchars($r['judul']) ?></h3>
                            <div class="meta"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($r['lokasi']) ?></div>
                            <div class="price"><?= rupiah($r['harga']) ?></div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="height:60px;"></div>

<?php include "includes/footer.php"; ?>

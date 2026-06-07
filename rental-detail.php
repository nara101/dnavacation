<?php
include "db.php";
$currentPage = 'rental';

if (!isset($_GET['id'])) {
    header("Location: rental.php");
    exit;
}
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM rental WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mobil = $stmt->get_result()->fetch_assoc();

if (!$mobil) {
    header("Location: rental.php");
    exit;
}

$pageTitle = $mobil['nama_mobil'] . ' - Rental DNA Vacation';

$success = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $nama = trim($_POST['nama']);
    $wa = trim($_POST['whatsapp']);
    $email = trim($_POST['email'] ?? '');
    $mulai = $_POST['mulai'];
    $selesai = $_POST['selesai'];
    $lokasi = trim($_POST['lokasi']);
    $sopir = isset($_POST['sopir']) ? 1 : 0;
    $catatan = trim($_POST['catatan'] ?? '');

    if ($nama && $wa && $mulai && $selesai && $lokasi) {
        $stmt = $conn->prepare("INSERT INTO rental_booking (rental_id,nama,whatsapp,email,tanggal_mulai,tanggal_selesai,lokasi_penjemputan,dengan_sopir,catatan) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("issssssis", $id, $nama, $wa, $email, $mulai, $selesai, $lokasi, $sopir, $catatan);
        if ($stmt->execute()) {
            $success = true;
            $durasi = (strtotime($selesai) - strtotime($mulai)) / 86400 + 1;
            $waMsg = "Halo DNA Vacation, saya ingin sewa mobil:\n"
                . "Mobil: {$mobil['nama_mobil']}\n"
                . "Tanggal Sewa: $mulai s/d $selesai ($durasi hari)\n"
                . "Lokasi Penjemputan: $lokasi\n"
                . ($sopir ? "Dengan Sopir" : "Tanpa Sopir") . "\n"
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
<div class="tour-detail-hero">
    <img src="<?= fotoUrl($mobil['gambar'], 'rentals') ?>" alt="<?= htmlspecialchars($mobil['nama_mobil']) ?>">
</div>

<div class="container">
    <div class="tour-detail-info">
        <div class="row">
            <div class="col-lg-8">
                <span class="badge text-bg-warning mb-2"><?= htmlspecialchars($mobil['tipe']) ?></span>
                <h1><?= htmlspecialchars($mobil['nama_mobil']) ?></h1>
                <div class="meta-row">
                    <span><i class="fa-solid fa-users"></i> Kapasitas <?= $mobil['kapasitas'] ?> orang</span>
                    <span><i class="fa-solid fa-gear"></i> <?= ucfirst($mobil['transmisi']) ?></span>
                    <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($mobil['area_layanan']) ?></span>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div style="background:#f8f9fb;padding:18px;border-radius:10px;">
                            <small class="text-muted">Tanpa Sopir</small>
                            <h4 style="color:#0a3d62;font-weight:700;"><?= rupiah($mobil['harga_per_hari']) ?>/hari</h4>
                        </div>
                    </div>
                    <?php if ($mobil['harga_dengan_sopir']): ?>
                    <div class="col-md-6 mb-3">
                        <div style="background:#f8f9fb;padding:18px;border-radius:10px;">
                            <small class="text-muted">Dengan Sopir</small>
                            <h4 style="color:#0a3d62;font-weight:700;"><?= rupiah($mobil['harga_dengan_sopir']) ?>/hari</h4>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($mobil['fasilitas']): ?>
                <h3 class="section-title-detail">Fasilitas</h3>
                <ul style="padding-left:18px;">
                    <?php foreach (explode("\n", $mobil['fasilitas']) as $f): if (trim($f)): ?>
                        <li><?= htmlspecialchars(trim($f)) ?></li>
                    <?php endif; endforeach; ?>
                </ul>
                <?php endif; ?>

                <?php if ($mobil['syarat_sewa']): ?>
                <h3 class="section-title-detail">Syarat Sewa</h3>
                <div><?= nl2br(htmlspecialchars($mobil['syarat_sewa'])) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="booking-form">
                    <h3>Form Sewa Mobil</h3>
                    <?php if ($success): ?>
                        <div class="alert-success-dna"><i class="fa-solid fa-circle-check me-2"></i>Booking dikirim! Redirect ke WhatsApp...</div>
                        <script>setTimeout(() => location.href = <?= json_encode($waRedirect) ?>, 1500);</script>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3"><label>Nama Lengkap *</label><input type="text" name="nama" class="form-control" required></div>
                        <div class="mb-3"><label>No. WhatsApp *</label><input type="text" name="whatsapp" class="form-control" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
                        <div class="mb-3"><label>Tanggal Mulai *</label><input type="date" name="mulai" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                        <div class="mb-3"><label>Tanggal Selesai *</label><input type="date" name="selesai" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                        <div class="mb-3"><label>Lokasi Penjemputan *</label><input type="text" name="lokasi" class="form-control" required></div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="sopir" id="sopir" class="form-check-input">
                            <label for="sopir" class="form-check-label">Dengan Sopir</label>
                        </div>
                        <div class="mb-3"><label>Catatan</label><textarea name="catatan" rows="2" class="form-control"></textarea></div>
                        <button name="book" class="btn-book"><i class="fa-solid fa-paper-plane me-2"></i>Kirim Booking</button>
                    </form>
                    <a href="<?= waLink("Halo DNA Vacation, saya tertarik sewa mobil: {$mobil['nama_mobil']}") ?>" class="btn-wa" target="_blank">
                        <i class="fa-brands fa-whatsapp me-2"></i>Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="height:60px;"></div>

<?php include "includes/footer.php"; ?>

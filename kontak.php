<?php
include "db.php";
$currentPage = 'kontak';
$pageTitle = 'Kontak - DNA Vacation';
$pageDesc = 'Hubungi DNA Vacation via WhatsApp, Instagram, atau email untuk konsultasi liburan.';

$waNumber = getSetting('whatsapp_number', '6285280825858');
$ig = getSetting('instagram_url');
$email = getSetting('email');
$alamat = getSetting('alamat');
$telepon = getSetting('telepon');

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim'])) {
    $nama = trim($_POST['nama']);
    $wa = trim($_POST['whatsapp']);
    $pesan = trim($_POST['pesan']);
    if ($nama && $wa && $pesan) {
        $waMsg = "Halo DNA Vacation, ada pesan baru:\n"
            . "Nama: $nama\n"
            . "WhatsApp: $wa\n"
            . "Pesan: $pesan";
        $waRedirect = waLink($waMsg);
        $sent = true;
    }
}
include "includes/header.php";
?>
<section class="page-hero">
    <h1>Kontak Kami</h1>
    <p class="breadcrumb-trail"><a href="index.php">Home</a> / Kontak</p>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <h2 style="color:#0a3d62; font-weight:800;">Mari Berkenalan!</h2>
                <p class="text-muted">Tim DNA Vacation siap membantu Anda. Hubungi kami melalui channel berikut:</p>

                <?php foreach ([
                    ['fa-brands fa-whatsapp','WhatsApp','+'.$waNumber, waLink('Halo DNA Vacation')],
                    ['fa-brands fa-instagram','Instagram','@dnavacation', $ig],
                    ['fa-solid fa-envelope','Email', $email, 'mailto:'.$email],
                    ['fa-solid fa-phone','Telepon', $telepon, 'tel:'.preg_replace('/\s+/','',$telepon)],
                    ['fa-solid fa-location-dot','Alamat', $alamat, '#'],
                ] as $c): ?>
                    <a href="<?= $c[3] ?>" target="_blank" style="display:flex; align-items:center; gap:16px; background:#fff; padding:18px; border-radius:12px; margin-bottom:14px; text-decoration:none; color:inherit; box-shadow:0 2px 12px rgba(0,0,0,0.06); transition:0.3s;" onmouseover="this.style.transform='translateX(6px)'" onmouseout="this.style.transform='translateX(0)'">
                        <div style="width:50px; height:50px; background:#0a3d62; color:#fff; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;"><i class="<?= $c[0] ?>"></i></div>
                        <div><small class="text-muted"><?= $c[1] ?></small><div style="color:#0a3d62; font-weight:600;"><?= htmlspecialchars($c[2]) ?></div></div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-7">
                <div class="booking-form">
                    <h3><i class="fa-solid fa-paper-plane me-2"></i>Kirim Pesan</h3>
                    <?php if ($sent): ?>
                        <div class="alert-success-dna">Pesan siap dikirim via WhatsApp! Mengalihkan...</div>
                        <script>setTimeout(() => location.href = <?= json_encode($waRedirect) ?>, 1200);</script>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3"><label>Nama *</label><input type="text" name="nama" class="form-control" required></div>
                        <div class="mb-3"><label>No. WhatsApp *</label><input type="text" name="whatsapp" class="form-control" required></div>
                        <div class="mb-3"><label>Pesan *</label><textarea name="pesan" rows="5" class="form-control" required></textarea></div>
                        <button name="kirim" class="btn-book"><i class="fa-brands fa-whatsapp me-2"></i>Kirim via WhatsApp</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>

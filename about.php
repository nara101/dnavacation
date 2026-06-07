<?php
include "db.php";
$currentPage = 'about';
$pageTitle = 'Tentang Kami - DNA Vacation';
$pageDesc = 'Profil DNA Vacation, travel agent terpercaya untuk paket tour, rental mobil, dan booking hotel.';
include "includes/header.php";
?>
<section class="page-hero">
    <h1>Tentang DNA Vacation</h1>
    <p class="breadcrumb-trail"><a href="index.php">Home</a> / Tentang Kami</p>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="assets/Wonderful Bali.jpg" style="border-radius:18px;box-shadow:0 12px 30px rgba(0,0,0,0.15);">
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <h2 style="color:#0a3d62; font-weight:800;">Siapa Kami?</h2>
                <p class="text-muted" style="font-size:1.05rem; line-height:1.8;">
                    <?= nl2br(htmlspecialchars(getSetting('about_text', 'DNA Vacation adalah travel agent terpercaya yang menyediakan paket tour, rental mobil, dan layanan booking hotel di seluruh Indonesia. Kami berkomitmen memberikan pengalaman liburan terbaik untuk setiap pelanggan.'))) ?>
                </p>
                <a href="<?= waLink('Halo DNA Vacation, saya tertarik konsultasi.') ?>" class="btn-primary-dna mt-2" target="_blank"><i class="fa-brands fa-whatsapp me-2"></i>Hubungi Kami</a>
            </div>
        </div>
    </div>
</section>

<section style="background:#f8f9fb; padding:80px 20px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div style="background:#fff; padding:36px; border-radius:14px; height:100%;">
                    <i class="fa-solid fa-bullseye" style="color:#f39c12; font-size:2.5rem; margin-bottom:14px;"></i>
                    <h3 style="color:#0a3d62; font-weight:700;">Visi Kami</h3>
                    <p class="text-muted">Menjadi travel agent terdepan dan terpercaya di Indonesia yang memberikan pengalaman liburan tak terlupakan bagi setiap pelanggan.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background:#fff; padding:36px; border-radius:14px; height:100%;">
                    <i class="fa-solid fa-rocket" style="color:#f39c12; font-size:2.5rem; margin-bottom:14px;"></i>
                    <h3 style="color:#0a3d62; font-weight:700;">Misi Kami</h3>
                    <ul class="text-muted" style="padding-left:20px;">
                        <li>Menyediakan layanan paket tour berkualitas dan terjangkau</li>
                        <li>Melayani rental kendaraan dengan armada terawat</li>
                        <li>Membantu pelanggan mendapatkan hotel terbaik</li>
                        <li>Memberikan pelayanan profesional 24/7</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="padding: 80px 20px;">
    <div class="section-title"><h2>Keunggulan DNA Vacation</h2></div>
    <div class="container">
        <div class="row g-4">
            <?php foreach ([
                ['fa-medal','Berpengalaman','Telah melayani ratusan pelanggan dengan kepuasan terbaik'],
                ['fa-users-gear','Tim Profesional','Guide & sopir bersertifikat dan berpengalaman'],
                ['fa-handshake','Harga Transparan','Tidak ada biaya tersembunyi'],
                ['fa-circle-check','Kualitas Terjamin','Hotel, mobil, dan destinasi pilihan terbaik'],
            ] as $f): ?>
                <div class="col-md-3">
                    <div class="category-card"><i class="fa-solid <?= $f[0] ?>"></i><h4><?= $f[1] ?></h4><p><?= $f[2] ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>

<?php
$pageTitle = 'Tentang Kami';
require_once 'includes/header.php';

$vision = getSetting('vision') ?: 'Menjadi travel agent terdepan di Indonesia Timur yang menghadirkan pengalaman liburan tak terlupakan dengan pelayanan berkualitas tinggi.';
$mission = getSetting('mission') ?: 'Menyediakan paket wisata berkualitas dengan harga kompetitif, memberikan pelayanan personal 24/7 via WhatsApp, membangun kepercayaan melalui transparansi, dan mempromosikan pariwisata Indonesia Timur ke dunia.';
$about = getSetting('about_text') ?: 'DNA Vacation adalah travel agent terpercaya untuk liburan impian Anda.';

$statsTours = $pdo->query("SELECT COUNT(*) FROM tours WHERE is_active=1")->fetchColumn();
$statsDest = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
$statsTesti = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn();
$statsBookings = $pdo->query("SELECT COUNT(*) FROM tour_bookings")->fetchColumn() + 500; // baseline
?>

<div class="page-header">
    <div class="container">
        <h1>Tentang <span class="script">DNA</span> Vacation</h1>
        <p>Mengenal lebih dekat travel partner Anda untuk menjelajahi Indonesia</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center mb-5">
            <div class="col-lg-6 order-lg-1 order-2">
                <span class="badge bg-light text-primary-custom border mb-2" style="padding:.5rem 1rem;font-weight:600"><i class="bi bi-airplane-engines"></i> Travel Agent Terpercaya</span>
                <h2 class="fw-bold mb-3"><span class="text-primary-custom">DNA</span> Vacation</h2>
                <p class="text-muted"><?= nl2br(e($about)) ?></p>
                <p class="text-muted">Kami berkomitmen memberikan pengalaman liburan terbaik dengan harga terjangkau dan pelayanan profesional. Dengan tim berpengalaman dan koneksi luas di berbagai destinasi wisata, kami siap mewujudkan liburan impian Anda.</p>
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>/tours.php" class="btn btn-primary-custom me-2"><i class="bi bi-compass"></i> Lihat Paket Tour</a>
                    <a href="<?= waLink('Halo DNA Vacation, saya ingin konsultasi liburan.') ?>" target="_blank" class="btn btn-wa"><i class="bi bi-whatsapp"></i> Chat Kami</a>
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1">
                <div class="position-relative">
                    <div class="rounded-3 overflow-hidden" style="height:360px;background:linear-gradient(135deg,var(--primary),var(--primary-dark))">
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white">
                            <div class="text-center">
                                <i class="bi bi-globe-asia-australia" style="font-size:5rem;opacity:.4"></i>
                                <h3 class="fw-bold mt-3">Indonesia Timur</h3>
                                <p class="opacity-75">Toraja · Wakatobi · Bunaken · Labuan Bajo · Raja Ampat</p>
                            </div>
                        </div>
                    </div>
                    <div class="position-absolute" style="bottom:-30px;right:-20px;background:var(--secondary);color:#fff;padding:1.25rem 1.5rem;border-radius:16px;box-shadow:var(--shadow-lg)">
                        <div class="fw-bold" style="font-size:2rem;line-height:1">5+</div>
                        <div class="small">Tahun Pengalaman</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="p-4 rounded-3 h-100" style="background:linear-gradient(135deg,var(--primary-lighter),#fff);border:1px solid var(--primary-light)">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="feature-icon" style="width:44px;height:44px;font-size:1.25rem"><i class="bi bi-bullseye"></i></div>
                        <h4 class="fw-bold mb-0">Visi</h4>
                    </div>
                    <p class="text-muted mb-0"><?= nl2br(e($vision)) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-3 h-100" style="background:linear-gradient(135deg,var(--secondary-light),#fff);border:1px solid var(--secondary-light)">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="feature-icon" style="background:linear-gradient(135deg,var(--secondary-light),#fff);color:var(--secondary);width:44px;height:44px;font-size:1.25rem"><i class="bi bi-flag"></i></div>
                        <h4 class="fw-bold mb-0">Misi</h4>
                    </div>
                    <p class="text-muted mb-0"><?= nl2br(e($mission)) ?></p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-4 text-center py-4 rounded-3" style="background:var(--gray-50)">
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num"><?= $statsBookings ?>+</div>
                    <div class="stat-label">Pelanggan Puas</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num"><?= $statsTours ?>+</div>
                    <div class="stat-label">Paket Tour</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num"><?= $statsDest ?>+</div>
                    <div class="stat-label">Destinasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num"><?= $statsTesti ?>+</div>
                    <div class="stat-label">Testimoni 5★</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-5 bg-gray-50">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Nilai-Nilai Kami</h2>
            <p class="section-subtitle">Prinsip yang menjadi fondasi setiap layanan DNA Vacation</p>
        </div>
        <div class="row g-4">
            <?php
            $values = [
                ['bi-hand-thumbs-up', 'Integritas', 'Kami transparan dalam harga & jujur dalam pelayanan. Tidak ada hidden cost, tidak ada janji palsu.'],
                ['bi-star', 'Kualitas', 'Setiap detail perjalanan kami kurasi dengan cermat — dari akomodasi, transport, hingga guide lokal.'],
                ['bi-heart', 'Personal Touch', 'Setiap pelanggan unik. Kami dengarkan kebutuhan Anda dan susun trip yang sesuai.'],
                ['bi-lightning', 'Responsif', 'Respons WhatsApp cepat, 24/7. Kami selalu siap menjawab pertanyaan & handle emergency.'],
                ['bi-shield-check', 'Terpercaya', 'Ratusan trip sukses. Testimoni jujur dari pelanggan asli menjadi bukti kualitas kami.'],
                ['bi-globe', 'Cinta Indonesia', 'Kami passionate dalam mempromosikan keindahan Nusantara, terutama Indonesia Timur.'],
            ];
            foreach ($values as $v): ?>
            <div class="col-md-6 col-lg-4">
                <div class="p-4 rounded-3 bg-white h-100 feature-item">
                    <div class="feature-icon"><i class="bi <?= $v[0] ?>"></i></div>
                    <h6 class="fw-bold"><?= $v[1] ?></h6>
                    <p class="text-muted small mb-0"><?= $v[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-section text-center">
    <div class="container">
        <h3 class="fw-bold mb-3">Ingin Liburan Bersama Kami?</h3>
        <p class="mb-4 opacity-85 mx-auto" style="max-width:600px">Konsultasi gratis via WhatsApp — kami siap membantu wujudkan liburan impian Anda!</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="<?= waLink('Halo DNA Vacation, saya ingin konsultasi tentang liburan.') ?>" target="_blank" class="btn btn-wa btn-lg px-4">
                <i class="bi bi-whatsapp"></i> Chat WhatsApp
            </a>
            <a href="<?= BASE_URL ?>/tours.php" class="btn btn-lg px-4" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)">Lihat Paket Tour</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

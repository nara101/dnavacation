<?php
include "db.php";
$currentPage = 'home';
$pageTitle = getSetting('site_name', 'DNA Vacation') . ' - Tour, Rental & Booking Hotel Terpercaya';
$pageDesc = getSetting('hero_subtitle', 'Paket tour, sewa mobil, dan booking hotel terpercaya');

// Ambil paket tour populer (bestseller dulu)
$tours = $conn->query("SELECT * FROM tour WHERE status='aktif' ORDER BY is_bestseller DESC, id DESC LIMIT 6");
$blogs = $conn->query("SELECT * FROM blog WHERE status='publish' ORDER BY id DESC LIMIT 3");
$testi = $conn->query("SELECT * FROM testimoni WHERE is_active=1 ORDER BY id DESC LIMIT 4");

include "includes/header.php";
?>
<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <h1><?= htmlspecialchars(getSetting('hero_title', 'Nikmati Liburanmu bersama DNA Vacation!')) ?></h1>
        <p class="lead">Paket Tour, Rental Mobil, dan Booking Hotel terpercaya untuk liburan tak terlupakan</p>

        <form class="search-bar" action="tour.php" method="GET">
            <select name="lokasi">
                <option value="">Destinasi Wisata</option>
                <option value="Bali">Bali</option>
                <option value="Yogyakarta">Yogyakarta</option>
                <option value="Malang">Malang</option>
                <option value="Bromo">Bromo</option>
                <option value="Surabaya">Surabaya</option>
                <option value="Karimunjawa">Karimunjawa</option>
            </select>
            <select name="durasi">
                <option value="">Durasi</option>
                <option value="1">1 Hari</option>
                <option value="2">2 Hari</option>
                <option value="3">3 Hari</option>
                <option value="4">4 Hari+</option>
            </select>
            <select name="harga">
                <option value="">Range Harga</option>
                <option value="lt1">&lt; 1 Juta</option>
                <option value="1to3">1 - 3 Juta</option>
                <option value="3to5">3 - 5 Juta</option>
                <option value="gt5">&gt; 5 Juta</option>
            </select>
            <button class="find-btn" type="submit"><i class="fa-solid fa-magnifying-glass me-2"></i>Cari Paket</button>
        </form>

        <div class="hero-cta">
            <a href="tour.php" class="btn-primary-dna"><i class="fa-solid fa-suitcase-rolling me-2"></i>Lihat Paket Tour</a>
            <a href="<?= waLink('Halo DNA Vacation, saya ingin konsultasi liburan.') ?>" class="btn-outline-dna" target="_blank"><i class="fa-brands fa-whatsapp me-2"></i>Konsultasi WhatsApp</a>
        </div>
    </div>
</section>

<!-- KATEGORI LAYANAN -->
<section class="category-section">
    <div class="section-title">
        <h2>Layanan Kami</h2>
        <p>Solusi liburan lengkap dari DNA Vacation</p>
    </div>
    <div class="category-grid">
        <a href="tour.php" class="category-card">
            <i class="fa-solid fa-suitcase-rolling"></i>
            <h4>Paket Tour</h4>
            <p>Open trip, private trip, family, honeymoon, & corporate trip</p>
        </a>
        <a href="rental.php" class="category-card">
            <i class="fa-solid fa-car"></i>
            <h4>Rental Mobil</h4>
            <p>Sewa mobil dengan/tanpa sopir, berbagai jenis mobil</p>
        </a>
        <a href="hotel.php" class="category-card">
            <i class="fa-solid fa-hotel"></i>
            <h4>Booking Hotel</h4>
            <p>Bantuan booking hotel terbaik via WhatsApp</p>
        </a>
        <a href="<?= waLink('Halo DNA Vacation, saya mau buat custom trip / private trip.') ?>" target="_blank" class="category-card">
            <i class="fa-solid fa-route"></i>
            <h4>Custom Trip</h4>
            <p>Buat itinerary perjalanan sesuai keinginanmu</p>
        </a>
    </div>
</section>

<!-- PAKET TOUR POPULER -->
<section class="paket-section">
    <h2 class="paket-title">PAKET TOUR TERBAIK</h2>
    <p class="paket-subtitle">Temukan Pengalaman Liburan Terbaik Bersama Kami</p>
    <div class="paket-grid">
        <?php while ($t = $tours->fetch_assoc()): ?>
            <a href="tour-detail.php?id=<?= $t['id'] ?>" class="paket-card">
                <?php if ($t['is_bestseller']): ?><span class="badge-card">Best Seller</span><?php endif; ?>
                <?php if (!$t['is_bestseller'] && $t['is_promo']): ?><span class="badge-card promo">Promo</span><?php endif; ?>
                <img src="<?= fotoUrl($t['gambar']) ?>" alt="<?= htmlspecialchars($t['judul']) ?>">
                <div class="body">
                    <h3><?= htmlspecialchars($t['judul']) ?></h3>
                    <div class="meta">
                        <i class="fa-regular fa-clock"></i> <?= htmlspecialchars($t['durasi']) ?>
                        &nbsp;|&nbsp;
                        <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($t['lokasi']) ?>
                    </div>
                    <div class="price"><small>Mulai dari</small><br><?= rupiah($t['harga']) ?> /pax</div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
    <div class="btnpaket-container">
        <a href="tour.php" class="btnpaket"><i class="fa-solid fa-arrow-right me-2"></i>Lihat Semua Paket</a>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="why-choose-us text-center">
    <div class="container">
        <h2 class="fw-bold">Kenapa Memilih DNA Vacation?</h2>
        <p class="lead">Kami hadir untuk memastikan setiap langkah perjalanan Anda menjadi pengalaman luar biasa.</p>
        <div class="row justify-content-center mt-4">
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <div class="icon-circle"><i class="fa-solid fa-sack-dollar"></i></div>
                    <h5>Harga Terjangkau</h5>
                    <p>Paket lengkap dengan harga ramah di kantong, tanpa biaya tersembunyi.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <div class="icon-circle"><i class="fa-solid fa-globe"></i></div>
                    <h5>Beragam Destinasi</h5>
                    <p>Pilihan destinasi dari Sabang sampai Merauke, sesuai selera Anda.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <div class="icon-circle"><i class="fa-solid fa-thumbs-up"></i></div>
                    <h5>Pelayanan Berkualitas</h5>
                    <p>Tim profesional, ramah, dan siap mendampingi perjalanan Anda.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <div class="icon-circle"><i class="fa-solid fa-shield-halved"></i></div>
                    <h5>Aman & Terpercaya</h5>
                    <p>Ratusan pelanggan puas telah mempercayakan liburannya pada kami.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <div class="icon-circle"><i class="fa-solid fa-headset"></i></div>
                    <h5>Customer Service 24/7</h5>
                    <p>Selalu siap melayani konsultasi Anda kapan saja via WhatsApp.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <div class="icon-circle"><i class="fa-solid fa-route"></i></div>
                    <h5>Custom Itinerary</h5>
                    <p>Buat itinerary sesuai keinginan, kami yang siapkan semuanya.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CARA BOOKING -->
<section style="padding: 80px 20px; background:#fff;">
    <div class="section-title">
        <h2>Cara Booking</h2>
        <p>3 langkah mudah liburan bersama DNA Vacation</p>
    </div>
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div style="background:#f8f9fb;padding:40px 24px;border-radius:18px;height:100%;">
                    <div style="width:70px;height:70px;background:#0a3d62;color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;margin-bottom:18px;">1</div>
                    <h5 style="color:#0a3d62;font-weight:700;">Pilih Paket / Mobil</h5>
                    <p style="color:#666;">Telusuri paket tour atau rental mobil yang tersedia.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:#f8f9fb;padding:40px 24px;border-radius:18px;height:100%;">
                    <div style="width:70px;height:70px;background:#0a3d62;color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;margin-bottom:18px;">2</div>
                    <h5 style="color:#0a3d62;font-weight:700;">Isi Form / Chat WA</h5>
                    <p style="color:#666;">Submit form booking atau langsung chat WhatsApp kami.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:#f8f9fb;padding:40px 24px;border-radius:18px;height:100%;">
                    <div style="width:70px;height:70px;background:#0a3d62;color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;margin-bottom:18px;">3</div>
                    <h5 style="color:#0a3d62;font-weight:700;">Konfirmasi & Berangkat</h5>
                    <p style="color:#666;">Tim kami konfirmasi detail dan siapkan perjalanan Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIAL -->
<section class="testimonial-section">
    <div class="testimonial-header">
        <h2>Kata Mereka Tentang Kami</h2>
        <div class="stars">★★★★★</div>
        <p>Rating 4.9 dari ratusan pelanggan</p>
    </div>
    <div class="testimonial-grid">
        <?php while ($t = $testi->fetch_assoc()): ?>
            <div class="testimonial-card">
                <div class="quote"><i class="fa-solid fa-quote-left"></i></div>
                <p><?= htmlspecialchars($t['isi']) ?></p>
                <div class="author">
                    <div class="avatar"><?= strtoupper(substr($t['nama'], 0, 1)) ?></div>
                    <div>
                        <h4><?= htmlspecialchars($t['nama']) ?></h4>
                        <div class="rating"><?= str_repeat('★', (int)$t['rating']) ?></div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- BLOG -->
<section class="blog-section">
    <div class="section-title">
        <h2>Blog & Artikel</h2>
        <p>Berbagi cerita & tips seputar wisata Indonesia</p>
    </div>
    <div class="blog-grid">
        <?php while ($b = $blogs->fetch_assoc()): ?>
            <a href="blog-detail.php?id=<?= $b['id'] ?>" class="blog-card">
                <img src="<?= fotoUrl($b['gambar'], 'blog') ?>" alt="<?= htmlspecialchars($b['judul']) ?>">
                <div class="blog-content">
                    <p class="meta">
                        <i class="fa-regular fa-folder"></i> <?= labelKategori($b['kategori']) ?>
                        &nbsp;
                        <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($b['created_at'])) ?>
                    </p>
                    <h5><?= htmlspecialchars($b['judul']) ?></h5>
                    <p><?= htmlspecialchars(substr(strip_tags($b['konten']), 0, 100)) ?>...</p>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
    <div class="btnpaket-container">
        <a href="blog.php" class="btnpaket"><i class="fa-solid fa-arrow-right me-2"></i>Semua Artikel</a>
    </div>
</section>

<!-- CTA Akhir -->
<section style="background: linear-gradient(135deg,#0a3d62,#1e6091); color:#fff; padding:80px 20px; text-align:center;">
    <div class="container">
        <h2 style="font-weight:800; font-size:2.4rem; margin-bottom:14px;">Siap Berlibur?</h2>
        <p style="opacity:0.92; font-size:1.1rem; margin-bottom:30px;">Chat langsung WhatsApp kami untuk konsultasi GRATIS.</p>
        <a href="<?= waLink('Halo DNA Vacation, saya ingin konsultasi liburan.') ?>" target="_blank" class="btn-primary-dna" style="background:#25d366;">
            <i class="fa-brands fa-whatsapp me-2"></i>Chat WhatsApp Sekarang
        </a>
    </div>
</section>

<?php include "includes/footer.php"; ?>

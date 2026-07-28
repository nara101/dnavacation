<?php
$pageTitle = '';
require_once 'includes/header.php';

$popularTours = $pdo->query("SELECT t.*, d.name as destination_name FROM tours t LEFT JOIN destinations d ON t.destination_id=d.id WHERE t.is_active=1 ORDER BY t.is_best_seller DESC, t.view_count DESC LIMIT 6")->fetchAll();
$destinations = $pdo->query("SELECT d.*, (SELECT MIN(price) FROM tours WHERE destination_id=d.id AND is_active=1) as min_price FROM destinations d WHERE d.is_popular=1 ORDER BY d.name LIMIT 4")->fetchAll();
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY id DESC LIMIT 4")->fetchAll();
$latestPosts = $pdo->query("SELECT p.*, c.name as category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id WHERE p.is_published=1 ORDER BY p.published_at DESC LIMIT 3")->fetchAll();
$featuredCars = $pdo->query("SELECT * FROM cars WHERE is_available=1 AND is_featured=1 ORDER BY id DESC LIMIT 3")->fetchAll();
$allDestinations = $pdo->query("SELECT * FROM destinations ORDER BY name")->fetchAll();

// Section toggles (default all visible)
$showTrust = settingBool('show_trust_strip', true);
$showDestinations = settingBool('show_destinations', true);
$showFeaturedCars = settingBool('show_featured_cars', true);
$showTestimonialsHome = settingBool('show_testimonials_home', true);
$showBlogHome = settingBool('show_blog_home', true);
$showCta = settingBool('show_cta', true);

// Section titles from settings
$secDestTitle = getSetting('section_destinations_title') ?: 'Popular Destinations';
$secDestSub = getSetting('section_destinations_subtitle') ?: 'Destinasi favorit yang paling dicari traveler';
$secToursTitle = getSetting('section_tours_title') ?: 'Best Tour Packages';
$secToursSub = getSetting('section_tours_subtitle') ?: 'Paket tour paling populer pilihan traveler';
$secCarsTitle = getSetting('section_cars_title') ?: 'Featured Rental Cars';
$secCarsSub = getSetting('section_cars_subtitle') ?: 'Mobil nyaman dengan/tanpa sopir';
$secTestiTitle = getSetting('section_testimonials_title') ?: 'What Our Travelers Say';
$secTestiSub = getSetting('section_testimonials_subtitle') ?: 'Cerita nyata dari pelanggan yang sudah berlibur bersama kami';
$secBlogTitle = getSetting('section_blog_title') ?: 'Travel Stories & Tips';
$secBlogSub = getSetting('section_blog_subtitle') ?: 'Inspirasi dan tips untuk perjalanan terbaik Anda';

// Helper: split "Best Tour Packages" so middle word becomes script accent
$splitTitle = function($str) {
    $words = explode(' ', trim($str));
    $n = count($words);
    if ($n < 2) return ['', $str, ''];
    if ($n === 2) return [$words[0], $words[1], ''];
    $mid = (int)floor($n / 2);
    return [
        implode(' ', array_slice($words, 0, $mid)),
        $words[$mid],
        implode(' ', array_slice($words, $mid + 1))
    ];
};

// Hero content
$heroBadge = getSetting('hero_badge') ?: 'EXPLORE. DREAM. DISCOVER.';
$heroBefore = getSetting('hero_headline_before') ?: 'Discover Amazing';
$heroScript = getSetting('hero_headline_script') ?: 'Places';
$heroAfter = getSetting('hero_headline_after') ?: 'with Us';
$heroSubheadline = getSetting('hero_subheadline') ?: 'Temukan paket tour, rental mobil, dan hotel terbaik untuk perjalanan Anda ke destinasi eksotis Indonesia Timur.';
$heroCtaPrimary = getSetting('hero_cta_primary') ?: 'Explore Now';
$heroCtaSecondary = getSetting('hero_cta_secondary') ?: 'Konsultasi Gratis';

// CTA
$ctaHeadline = getSetting('cta_headline') ?: 'Your Next Adventure Awaits!';
$ctaSubheadline = getSetting('cta_subheadline') ?: 'Hubungi kami sekarang dan biarkan tim DNA Vacation mewujudkan liburan impian Anda!';

$badgeKeys = ['bestseller', 'popular', 'trending', 'bestseller'];
$badgeLabels = ['Bestseller', 'Popular', 'Trending', 'Bestseller'];
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-xl-8">
                <div class="hero-badge">
                    <i class="bi bi-airplane-fill"></i>
                    <span><?= e($heroBadge) ?></span>
                </div>
                <h1>
                    <?= e($heroBefore) ?><br>
                    <span class="script"><?= e($heroScript) ?></span> <?= e($heroAfter) ?>
                </h1>
                <p class="hero-lead mb-4"><?= e($heroSubheadline) ?></p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= BASE_URL ?>/tours.php" class="btn btn-gold btn-lg px-4">
                        <i class="bi bi-compass"></i> <?= e($heroCtaPrimary) ?>
                    </a>
                    <a href="<?= waLink('Halo DNA Vacation, saya ingin konsultasi tentang paket liburan.') ?>" target="_blank" class="btn btn-wa btn-lg px-4">
                        <i class="bi bi-whatsapp"></i> <?= e($heroCtaSecondary) ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Hero Search Box -->
        <div class="hero-search">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-tour"><i class="bi bi-compass"></i> Paket Tour</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-car"><i class="bi bi-car-front"></i> Rent Mobil</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-hotel"><i class="bi bi-building"></i> Hotel</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-tour">
                    <form action="<?= BASE_URL ?>/tours.php" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1"><i class="bi bi-geo-alt text-primary-custom"></i> Destinasi</label>
                            <select name="destination" class="form-select">
                                <option value="">Semua Destinasi</option>
                                <?php foreach ($allDestinations as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1"><i class="bi bi-calendar-week text-primary-custom"></i> Durasi</label>
                            <select name="duration" class="form-select">
                                <option value="">Semua</option>
                                <option value="2">2 Hari</option>
                                <option value="3">3 Hari</option>
                                <option value="4">4 Hari</option>
                                <option value="5">5 Hari</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1"><i class="bi bi-search text-primary-custom"></i> Kata Kunci</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari paket...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-search-primary"><i class="bi bi-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="tab-car">
                    <form action="<?= BASE_URL ?>/cars.php" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1"><i class="bi bi-tag text-primary-custom"></i> Tipe Mobil</label>
                            <select name="type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="mpv">MPV</option>
                                <option value="suv">SUV</option>
                                <option value="city">City Car</option>
                                <option value="minibus">Minibus</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1"><i class="bi bi-people text-primary-custom"></i> Kapasitas</label>
                            <select name="capacity" class="form-select">
                                <option value="">Semua</option>
                                <option value="4">4+ Orang</option>
                                <option value="7">7+ Orang</option>
                                <option value="12">12+ Orang</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1"><i class="bi bi-gear text-primary-custom"></i> Transmisi</label>
                            <select name="transmission" class="form-select">
                                <option value="">Semua</option>
                                <option value="automatic">Automatic</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-search-primary"><i class="bi bi-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="tab-hotel">
                    <div class="text-center py-3">
                        <p class="mb-3 text-muted">Butuh bantuan cari hotel? Tim kami siap bantu Anda!</p>
                        <a href="<?= BASE_URL ?>/hotel.php" class="btn btn-primary-custom me-2">
                            <i class="bi bi-building"></i> Isi Request Hotel
                        </a>
                        <a href="<?= waLink('Halo DNA Vacation, saya ingin dibantu booking hotel.') ?>" target="_blank" class="btn btn-wa">
                            <i class="bi bi-whatsapp"></i> Chat Langsung
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Strip -->
<?php if ($showTrust): ?>
<section class="trust-strip">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="trust-item">
                    <div class="icon"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <strong>Best Price Guarantee</strong>
                        <span>Harga terbaik & transparan</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="trust-item">
                    <div class="icon"><i class="bi bi-headset"></i></div>
                    <div>
                        <strong>24/7 Customer Support</strong>
                        <span>Chat kapan saja via WhatsApp</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="trust-item">
                    <div class="icon"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <strong>Secure Booking</strong>
                        <span>Data & transaksi aman</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="trust-item">
                    <div class="icon"><i class="bi bi-award"></i></div>
                    <div>
                        <strong>Handpicked Experiences</strong>
                        <span>Kurasi lokal expert</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Destinations -->
<?php if ($showDestinations && $destinations): [$destA, $destB, $destC] = $splitTitle($secDestTitle); ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Best Choice</span>
                <h2 class="section-title mb-1"><?= e($destA) ?> <?php if ($destB): ?><span class="script-accent"><?= e($destB) ?></span><?php endif; ?> <?= e($destC) ?></h2>
                <p class="section-subtitle mb-0"><?= e($secDestSub) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/tours.php" class="btn btn-outline-primary-custom">View All Destinations <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            <?php foreach ($destinations as $idx => $dest):
                $bk = $badgeKeys[$idx % 4];
                $bl = $badgeLabels[$idx % 4];
                $minPrice = $dest['min_price'] ? number_format($dest['min_price']/1000, 0) . 'K' : null;
            ?>
            <div class="col-6 col-md-6 col-lg-3">
                <a href="<?= BASE_URL ?>/tours.php?destination=<?= $dest['id'] ?>" class="text-decoration-none">
                    <div class="destination-card">
                        <span class="dest-badge <?= $bk ?>"><?= $bl ?></span>
                        <?php if ($dest['image']): ?>
                            <img src="<?= uploadUrl($dest['image']) ?>" alt="<?= e($dest['name']) ?>" onerror="handleImgError(this)">
                        <?php else: ?>
                            <img src="<?= e(heroBgUrl()) ?>" alt="<?= e($dest['name']) ?>" onerror="handleImgError(this)">
                        <?php endif; ?>
                        <div class="overlay"></div>
                        <div class="dest-info">
                            <div>
                                <div class="dest-name"><?= e($dest['name']) ?></div>
                                <div class="dest-loc"><i class="bi bi-geo-alt-fill"></i> Indonesia</div>
                            </div>
                            <?php if ($minPrice): ?>
                            <div class="dest-price">
                                <small>Mulai</small>
                                <strong>Rp <?= $minPrice ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Why Choose -->
<section class="py-5 bg-gray-50">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="section-eyebrow">Why Us?</span>
                <h2 class="section-title mb-3">Why Choose <span class="script-accent"><?= e(getSetting('site_name') ?: 'DNA Vacation') ?></span>?</h2>
                <p class="text-muted"><?= e(getSetting('about_text') ?: 'Kami adalah travel agent yang mengutamakan pengalaman personal, harga transparan, dan pelayanan responsif.') ?></p>
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>/about.php" class="btn btn-primary-custom"><i class="bi bi-info-circle"></i> Tentang Kami</a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    <?php
                    $features = [
                        ['bi-map', 'Wide Range of Choices', '8+ paket tour ke destinasi eksotis Indonesia Timur.'],
                        ['bi-hand-thumbs-up', 'Trusted by Travelers', '500+ pelanggan puas dengan pengalaman kelas satu.'],
                        ['bi-calendar-check', 'Flexible & Easy Booking', 'Booking cepat, konfirmasi via WhatsApp.'],
                        ['bi-tag', 'Unbeatable Deals', 'Harga kompetitif tanpa hidden cost.'],
                    ];
                    foreach ($features as $f): ?>
                    <div class="col-md-6 feature-item">
                        <div class="p-3 rounded-3 bg-white h-100" style="border:1px solid var(--gray-200)">
                            <div class="d-flex gap-3">
                                <div class="feature-icon flex-shrink-0" style="width:48px;height:48px;font-size:1.25rem;margin-bottom:0"><i class="bi <?= $f[0] ?>"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1"><?= $f[1] ?></h6>
                                    <p class="text-muted small mb-0"><?= $f[2] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Best Tours -->
<?php [$tourA, $tourB, $tourC] = $splitTitle($secToursTitle); ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Recommended</span>
                <h2 class="section-title mb-1"><?= e($tourA) ?> <?php if ($tourB): ?><span class="script-accent"><?= e($tourB) ?></span><?php endif; ?> <?= e($tourC) ?></h2>
                <p class="section-subtitle mb-0"><?= e($secToursSub) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/tours.php" class="btn btn-outline-primary-custom">Lihat Semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($popularTours as $tour): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card tour-card position-relative">
                    <?php if ($tour['is_best_seller']): ?><span class="badge-bestseller"><i class="bi bi-star-fill me-1"></i>Best Seller</span><?php endif; ?>
                    <?php if ($tour['is_promo']): ?><span class="badge-promo"><i class="bi bi-fire me-1"></i>Promo</span><?php endif; ?>
                    <div class="image-wrapper">
                    <?php if ($tour['image']): ?>
                        <img src="<?= uploadUrl($tour['image']) ?>" class="card-img-top" alt="<?= e($tour['title']) ?>" onerror="handleImgError(this)">
                    <?php else: ?>
                        <div class="card-img-top placeholder-img" style="height:220px"><i class="bi bi-image"></i></div>
                    <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="card-meta">
                            <span><i class="bi bi-geo-alt me-1"></i><?= e($tour['destination_name'] ?? '-') ?></span>
                            <span><i class="bi bi-clock me-1"></i><?= e($tour['duration']) ?></span>
                        </div>
                        <h5 class="card-title"><?= e($tour['title']) ?></h5>
                        <p class="small text-muted flex-grow-1"><?= truncate(strip_tags($tour['description']), 90) ?></p>
                        <div class="d-flex justify-content-between align-items-end mt-2 border-top pt-3">
                            <div class="price">
                                <?php if ($tour['is_promo'] && $tour['promo_price']): ?>
                                    <small class="text-decoration-line-through text-muted d-block"><?= formatRupiah($tour['price']) ?></small>
                                    <?= formatRupiah($tour['promo_price']) ?>
                                <?php else: ?>
                                    <?= formatRupiah($tour['price']) ?>
                                <?php endif; ?>
                                <small>/orang</small>
                            </div>
                            <a href="<?= BASE_URL ?>/tour-detail.php?slug=<?= e($tour['slug']) ?>" class="btn btn-primary-custom btn-sm">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Services -->
<section class="py-5 bg-gray-50">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Our Services</span>
            <h2 class="section-title">All Your Travel <span class="script-accent">Needs</span></h2>
            <p class="section-subtitle">Semua kebutuhan liburan Anda dalam satu tempat</p>
        </div>
        <div class="row g-3">
            <?php
            $categories = [
                ['bi-compass', 'Paket Tour', 'Open trip, private trip, family, honeymoon & corporate.', 'tours.php'],
                ['bi-car-front', 'Rent Mobil', 'Sewa mobil dengan/tanpa sopir untuk perjalanan nyaman.', 'cars.php'],
                ['bi-building', 'Booking Hotel', 'Kami bantu carikan hotel terbaik sesuai budget Anda.', 'hotel.php'],
                ['bi-pencil-square', 'Custom Trip', 'Buat paket liburan sesuai keinginan Anda sendiri.', 'custom-trip.php'],
            ];
            foreach ($categories as $c): ?>
            <div class="col-6 col-lg-3">
                <a href="<?= BASE_URL ?>/<?= $c[3] ?>" class="category-card">
                    <i class="bi <?= $c[0] ?>"></i>
                    <h6 class="fw-bold mb-1"><?= $c[1] ?></h6>
                    <p class="small text-muted mb-0"><?= $c[2] ?></p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Cars -->
<?php if ($showFeaturedCars && $featuredCars): [$carA, $carB, $carC] = $splitTitle($secCarsTitle); ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Vehicles</span>
                <h2 class="section-title mb-1"><?= e($carA) ?> <?php if ($carB): ?><span class="script-accent"><?= e($carB) ?></span><?php endif; ?> <?= e($carC) ?></h2>
                <p class="section-subtitle mb-0"><?= e($secCarsSub) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/cars.php" class="btn btn-outline-primary-custom">Lihat Semua Mobil <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredCars as $car): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card car-card">
                    <div class="image-wrapper">
                    <?php if ($car['image']): ?>
                        <img src="<?= uploadUrl($car['image']) ?>" class="card-img-top" alt="<?= e($car['name']) ?>" onerror="handleImgError(this)">
                    <?php else: ?>
                        <div class="card-img-top placeholder-img" style="height:220px"><i class="bi bi-car-front"></i></div>
                    <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <small class="text-muted text-uppercase" style="letter-spacing:.5px;font-weight:600;font-size:.7rem"><?= e($car['brand']) ?> · <?= $car['year'] ?></small>
                        <h5 class="card-title mt-1"><?= e($car['name']) ?></h5>
                        <div class="card-meta">
                            <span><i class="bi bi-people me-1"></i><?= $car['capacity'] ?> orang</span>
                            <span><i class="bi bi-gear me-1"></i><?= ucfirst($car['transmission']) ?></span>
                            <span><i class="bi bi-tag me-1"></i><?= strtoupper($car['type']) ?></span>
                        </div>
                        <div class="border-top pt-3 d-flex justify-content-between align-items-end">
                            <div class="price"><?= formatRupiah($car['price_with_driver']) ?><small class="d-block">/hari + sopir</small></div>
                            <a href="<?= BASE_URL ?>/car-detail.php?slug=<?= e($car['slug']) ?>" class="btn btn-primary-custom btn-sm">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Steps -->
<section class="py-5 bg-gray-50">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">How It Works</span>
            <h2 class="section-title">Booking dalam <span class="script-accent">4 Langkah</span></h2>
            <p class="section-subtitle">Wujudkan liburan impian Anda dengan mudah</p>
        </div>
        <div class="row g-3">
            <?php
            $steps = [
                ['Pilih Paket', 'Jelajahi paket tour atau layanan yang Anda inginkan.'],
                ['Isi Form Booking', 'Lengkapi data diri dan detail perjalanan Anda.'],
                ['Konfirmasi & Bayar', 'Tim kami menghubungi via WhatsApp untuk konfirmasi.'],
                ['Nikmati Liburan', 'Semuanya sudah kami siapkan — tinggal berangkat!'],
            ];
            foreach ($steps as $i => $s): ?>
            <div class="col-6 col-lg-3">
                <div class="step-item">
                    <div class="step-number"><?= $i + 1 ?></div>
                    <h6 class="fw-bold"><?= $s[0] ?></h6>
                    <p class="small text-muted mb-0"><?= $s[1] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<?php if ($showTestimonialsHome && $testimonials): [$tA, $tB, $tC] = $splitTitle($secTestiTitle); ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Reviews</span>
                <h2 class="section-title mb-1"><?= e($tA) ?> <?php if ($tB): ?><span class="script-accent"><?= e($tB) ?></span><?php endif; ?> <?= e($tC) ?></h2>
                <p class="section-subtitle mb-0"><?= e($secTestiSub) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/testimonials.php" class="btn btn-outline-primary-custom">Semua Testimoni <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($testimonials as $t): ?>
            <div class="col-md-6 col-lg-3">
                <div class="testimonial-card">
                    <div class="stars">
                        <?php for ($i = 0; $i < $t['rating']; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                        <?php for ($i = $t['rating']; $i < 5; $i++): ?><i class="bi bi-star text-muted"></i><?php endfor; ?>
                    </div>
                    <p class="small mb-3">"<?= e(truncate($t['content'], 140)) ?>"</p>
                    <div class="d-flex align-items-center gap-2 mt-auto">
                        <div class="customer-avatar"><?= strtoupper(substr($t['customer_name'], 0, 1)) ?></div>
                        <div>
                            <div class="customer-name"><?= e($t['customer_name']) ?></div>
                            <div class="customer-location"><?= e($t['customer_location']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog -->
<?php if ($showBlogHome && $latestPosts): [$bA, $bB, $bC] = $splitTitle($secBlogTitle); ?>
<section class="py-5 bg-gray-50">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Blog & Tips</span>
                <h2 class="section-title mb-1"><?= e($bA) ?> <?php if ($bB): ?><span class="script-accent"><?= e($bB) ?></span><?php endif; ?> <?= e($bC) ?></h2>
                <p class="section-subtitle mb-0"><?= e($secBlogSub) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/blog.php" class="btn btn-outline-primary-custom">Semua Artikel <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($latestPosts as $post): ?>
            <div class="col-md-4">
                <div class="card blog-card">
                    <div class="image-wrapper">
                    <?php if ($post['image']): ?>
                        <img src="<?= uploadUrl($post['image']) ?>" class="card-img-top" alt="<?= e($post['title']) ?>" onerror="handleImgError(this)">
                    <?php else: ?>
                        <div class="card-img-top placeholder-img" style="height:200px"><i class="bi bi-journal-text"></i></div>
                    <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <span class="blog-category"><?= e($post['category_name'] ?? 'Artikel') ?></span>
                        <h6 class="card-title mt-1"><?= e($post['title']) ?></h6>
                        <p class="small text-muted"><?= truncate(strip_tags($post['excerpt'] ?: $post['content']), 100) ?></p>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <small class="text-muted"><i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($post['published_at'] ?: $post['created_at'])) ?></small>
                            <a href="<?= BASE_URL ?>/blog-detail.php?slug=<?= e($post['slug']) ?>" class="text-primary-custom small fw-semibold text-decoration-none">Baca <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<?php if ($showCta): [$cA, $cB, $cC] = $splitTitle($ctaHeadline); ?>
<section class="cta-section text-center">
    <div class="container">
        <span class="section-eyebrow" style="color:var(--gold-light)">Let's Go</span>
        <h2 class="fw-bold mb-3"><?= e($cA) ?> <?php if ($cB): ?><span class="script-accent"><?= e($cB) ?></span><?php endif; ?> <?= e($cC) ?></h2>
        <p class="mb-4 opacity-85 mx-auto" style="max-width:620px"><?= e($ctaSubheadline) ?></p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="<?= waLink('Halo DNA Vacation, saya tertarik untuk booking paket tour.') ?>" target="_blank" class="btn btn-gold btn-lg px-4">
                <i class="bi bi-whatsapp"></i> Chat WhatsApp
            </a>
            <a href="<?= BASE_URL ?>/tours.php" class="btn btn-lg px-4" style="background:rgba(255,255,255,.12);color:#fff;border:1.5px solid rgba(255,255,255,.3);border-radius:100px;padding:.7rem 1.6rem;font-weight:600">Lihat Paket Tour</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

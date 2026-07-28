<?php
$pageTitle = 'Pengaturan';
require_once __DIR__ . '/includes/header.php';

// Helper: handle single-image upload for site branding assets
$uploadSiteImage = function($fileKey) {
    if (empty($_FILES[$fileKey]['name'])) return null;
    return uploadImage($_FILES[$fileKey], 'site');
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Text-based settings
    $textFields = [
        // Identitas
        'site_name', 'site_tagline', 'brand_tagline', 'footer_tagline',
        // Kontak
        'whatsapp_number', 'instagram_url', 'facebook_url', 'tiktok_url',
        'email', 'phone', 'address', 'operational_hours',
        // Hero
        'hero_badge', 'hero_headline_before', 'hero_headline_script', 'hero_headline_after',
        'hero_subheadline', 'hero_cta_primary', 'hero_cta_secondary',
        // Sections
        'section_destinations_title', 'section_destinations_subtitle',
        'section_tours_title', 'section_tours_subtitle',
        'section_cars_title', 'section_cars_subtitle',
        'section_testimonials_title', 'section_testimonials_subtitle',
        'section_blog_title', 'section_blog_subtitle',
        // CTA
        'cta_headline', 'cta_subheadline',
        // Tentang
        'about_text', 'vision', 'mission',
        // SEO
        'meta_title', 'meta_description', 'footer_text', 'copyright_text',
        // Feature toggles
        'show_trust_strip', 'show_destinations', 'show_featured_cars',
        'show_testimonials_home', 'show_blog_home', 'show_cta',
    ];
    foreach ($textFields as $key) {
        $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        $check = $pdo->prepare("SELECT id FROM site_settings WHERE setting_key = ?");
        $check->execute([$key]);
        if ($check->fetch()) {
            $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?")->execute([$value, $key]);
        } else {
            $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)")->execute([$key, $value]);
        }
    }

    // File uploads: custom logo and hero background
    $fileFields = ['custom_logo', 'hero_bg_image', 'page_header_bg_image'];
    foreach ($fileFields as $key) {
        // Reset?
        if (!empty($_POST["reset_$key"])) {
            $pdo->prepare("DELETE FROM site_settings WHERE setting_key = ?")->execute([$key]);
            continue;
        }
        $path = $uploadSiteImage($key);
        if ($path) {
            $check = $pdo->prepare("SELECT id FROM site_settings WHERE setting_key = ?");
            $check->execute([$key]);
            if ($check->fetch()) {
                $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?")->execute([$path, $key]);
            } else {
                $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)")->execute([$key, $path]);
            }
        }
    }

    setFlash('success', 'Pengaturan tampilan berhasil disimpan.');
    header('Location: settings.php' . (isset($_POST['tab']) ? '?tab=' . urlencode($_POST['tab']) : ''));
    exit;
}

// Load settings
$settingsRows = $pdo->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll();
$s = [];
foreach ($settingsRows as $row) {
    $s[$row['setting_key']] = $row['setting_value'];
}

// Helper for defaults
$val = function($key, $default = '') use ($s) {
    return $s[$key] ?? $default;
};
$checked = function($key, $default = true) use ($s) {
    if (!isset($s[$key])) return $default;
    return $s[$key] === '1' || $s[$key] === 1 || $s[$key] === 'on';
};

$currentTab = $_GET['tab'] ?? 'identitas';
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-palette2 text-primary-custom"></i> Pengaturan Tampilan Website</h4>
            <p class="text-muted small mb-0">Kontrol semua konten & tampilan yang dilihat pengunjung. Semua perubahan langsung tampil di website.</p>
        </div>
        <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i> Preview Website</a>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="tab" value="<?= e($currentTab) ?>">

        <div class="row g-3">
            <!-- Tab Navigation -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm" style="position:sticky;top:80px">
                    <div class="list-group list-group-flush" role="tablist">
                        <?php
                        $tabs = [
                            'identitas' => ['bi-badge-3d', 'Identitas & Logo'],
                            'header' => ['bi-menu-app', 'Header / Navbar'],
                            'hero' => ['bi-image', 'Landing Page Hero'],
                            'sections' => ['bi-layout-text-window', 'Judul Section'],
                            'cta' => ['bi-megaphone', 'CTA Banner'],
                            'footer' => ['bi-window-dock', 'Footer'],
                            'kontak' => ['bi-telephone', 'Kontak & Social'],
                            'tentang' => ['bi-info-circle', 'Tentang Kami'],
                            'toggles' => ['bi-toggles', 'Tampil / Sembunyi'],
                            'seo' => ['bi-search', 'SEO'],
                        ];
                        foreach ($tabs as $key => [$icon, $label]): ?>
                            <a href="#tab-<?= $key ?>" class="list-group-item list-group-item-action <?= $currentTab === $key ? 'active' : '' ?>"
                               data-bs-toggle="tab" role="tab"
                               onclick="document.querySelector('[name=tab]').value='<?= $key ?>'">
                                <i class="bi <?= $icon ?> me-2"></i><?= $label ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="tab-content">

                    <!-- IDENTITAS & LOGO -->
                    <div class="tab-pane fade <?= $currentTab === 'identitas' ? 'show active' : '' ?>" id="tab-identitas">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-badge-3d"></i> Identitas Website & Logo</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Nama Website</label>
                                        <input type="text" name="site_name" class="form-control" value="<?= e($val('site_name', 'DNA Vacation')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Tagline Utama</label>
                                        <input type="text" name="site_tagline" class="form-control" value="<?= e($val('site_tagline')) ?>" placeholder="It's More Than Fun">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Logo Custom (opsional)</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php $logoSrc = $s['custom_logo'] ?? null; ?>
                                            <img src="<?= $logoSrc ? uploadUrl($logoSrc) : asset('img/logo-dna.jpg') ?>" alt="Logo" style="width:80px;height:80px;object-fit:cover;border-radius:12px;border:2px solid var(--gray-200)">
                                            <div class="flex-grow-1">
                                                <input type="file" name="custom_logo" class="form-control" accept="image/*">
                                                <small class="text-muted">Kosongkan untuk pakai logo default. Rekomendasi: 500x500px, format PNG/JPG.</small>
                                                <?php if ($logoSrc): ?>
                                                    <div class="form-check mt-2">
                                                        <input type="checkbox" name="reset_custom_logo" value="1" class="form-check-input" id="resetLogo">
                                                        <label class="form-check-label small text-danger" for="resetLogo">Kembalikan ke logo default</label>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HEADER / NAVBAR -->
                    <div class="tab-pane fade <?= $currentTab === 'header' ? 'show active' : '' ?>" id="tab-header">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-menu-app"></i> Header / Navbar</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Nama Brand di Navbar</label>
                                        <input type="text" name="site_name" class="form-control" value="<?= e($val('site_name', 'DNA Vacation')) ?>">
                                        <small class="text-muted">Sama dengan Nama Website di tab Identitas</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Sub-brand / Tagline di Navbar (script)</label>
                                        <input type="text" name="brand_tagline" class="form-control" value="<?= e($val('brand_tagline', "It's More Than Fun")) ?>" placeholder="It's More Than Fun">
                                        <small class="text-muted">Ditampilkan di bawah nama brand dengan font script emas</small>
                                    </div>
                                </div>
                                <div class="mt-4 p-3 rounded-3" style="background:var(--primary-lighter)">
                                    <p class="small mb-2 fw-semibold"><i class="bi bi-eye"></i> Preview:</p>
                                    <div class="d-flex align-items-center gap-2 bg-white p-3 rounded" style="border:1px solid var(--gray-200)">
                                        <img src="<?= isset($s['custom_logo']) ? uploadUrl($s['custom_logo']) : asset('img/logo-dna.jpg') ?>" style="width:46px;height:46px;border-radius:8px;object-fit:cover">
                                        <div>
                                            <div class="fw-bold" style="color:var(--primary);font-size:1.15rem"><?= e($val('site_name', 'DNA Vacation')) ?></div>
                                            <div style="font-family:'Pacifico',cursive;color:var(--gold-dark);font-size:.8rem"><?= e($val('brand_tagline', "It's More Than Fun")) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LANDING PAGE HERO -->
                    <div class="tab-pane fade <?= $currentTab === 'hero' ? 'show active' : '' ?>" id="tab-hero">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-image"></i> Landing Page Hero</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Background Image Hero</label>
                                        <?php $heroSrc = $s['hero_bg_image'] ?? null; ?>
                                        <div class="mb-2">
                                            <img src="<?= $heroSrc ? uploadUrl($heroSrc) : asset('img/nature.jpg') ?>" alt="Hero" style="width:100%;max-width:400px;height:150px;object-fit:cover;border-radius:12px;border:2px solid var(--gray-200)">
                                        </div>
                                        <input type="file" name="hero_bg_image" class="form-control" accept="image/*">
                                        <small class="text-muted">Rekomendasi: 1920x1080px landscape. Kosongkan untuk pakai default (nature.jpg).</small>
                                        <?php if ($heroSrc): ?>
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="reset_hero_bg_image" value="1" class="form-check-input" id="resetHero">
                                                <label class="form-check-label small text-danger" for="resetHero">Kembalikan ke background default</label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Badge Hero (di atas judul)</label>
                                        <input type="text" name="hero_badge" class="form-control" value="<?= e($val('hero_badge', 'EXPLORE. DREAM. DISCOVER.')) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Judul (bagian awal)</label>
                                        <input type="text" name="hero_headline_before" class="form-control" value="<?= e($val('hero_headline_before', 'Discover Amazing')) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Kata Script (gold, italic)</label>
                                        <input type="text" name="hero_headline_script" class="form-control" value="<?= e($val('hero_headline_script', 'Places')) ?>" style="font-family:'Pacifico',cursive;color:var(--gold-dark)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Judul (bagian akhir)</label>
                                        <input type="text" name="hero_headline_after" class="form-control" value="<?= e($val('hero_headline_after', 'with Us')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Subheadline</label>
                                        <textarea name="hero_subheadline" class="form-control" rows="2"><?= e($val('hero_subheadline', 'Temukan paket tour, rental mobil, dan hotel terbaik untuk perjalanan Anda ke destinasi eksotis Indonesia Timur.')) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Teks Tombol Utama (Gold)</label>
                                        <input type="text" name="hero_cta_primary" class="form-control" value="<?= e($val('hero_cta_primary', 'Explore Now')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Teks Tombol WhatsApp</label>
                                        <input type="text" name="hero_cta_secondary" class="form-control" value="<?= e($val('hero_cta_secondary', 'Konsultasi Gratis')) ?>">
                                    </div>
                                </div>
                                <div class="mt-4 p-3 rounded-3" style="background:var(--primary-darker);color:#fff;background-image:linear-gradient(rgba(6,24,56,.6),rgba(6,24,56,.75)),url('<?= $heroSrc ? uploadUrl($heroSrc) : asset('img/nature.jpg') ?>');background-size:cover;background-position:center;min-height:200px">
                                    <div class="p-2 small mb-2" style="background:rgba(255,255,255,.15);display:inline-block;border-radius:100px;padding:.3rem .8rem;font-size:.7rem"><?= e($val('hero_badge', 'EXPLORE.')) ?></div>
                                    <h4 class="fw-bold mb-2" style="line-height:1.2">
                                        <?= e($val('hero_headline_before', 'Discover Amazing')) ?>
                                        <span style="font-family:'Pacifico',cursive;color:var(--gold-light);font-weight:400"><?= e($val('hero_headline_script', 'Places')) ?></span>
                                        <?= e($val('hero_headline_after', 'with Us')) ?>
                                    </h4>
                                    <p class="small opacity-75 mb-0"><?= e(truncate($val('hero_subheadline'), 100)) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION HEADINGS -->
                    <div class="tab-pane fade <?= $currentTab === 'sections' ? 'show active' : '' ?>" id="tab-sections">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-layout-text-window"></i> Judul Section di Homepage</div>
                            <div class="card-body">
                                <p class="text-muted small">Kustomisasi judul setiap section di homepage. Kosongkan untuk pakai default.</p>
                                <?php
                                $sectionFields = [
                                    ['destinations', 'Popular Destinations', 'Destinasi favorit yang paling dicari traveler'],
                                    ['tours', 'Best Tour Packages', 'Paket tour paling populer pilihan traveler'],
                                    ['cars', 'Featured Rental Cars', 'Mobil nyaman dengan/tanpa sopir'],
                                    ['testimonials', 'What Our Travelers Say', 'Cerita nyata dari pelanggan'],
                                    ['blog', 'Travel Stories & Tips', 'Inspirasi & tips untuk perjalanan terbaik'],
                                ];
                                foreach ($sectionFields as [$key, $defTitle, $defSub]): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <label class="form-label fw-semibold small text-uppercase text-muted"><?= $defTitle ?></label>
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <input type="text" name="section_<?= $key ?>_title" class="form-control form-control-sm" value="<?= e($val("section_{$key}_title", $defTitle)) ?>" placeholder="<?= $defTitle ?>">
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="section_<?= $key ?>_subtitle" class="form-control form-control-sm" value="<?= e($val("section_{$key}_subtitle", $defSub)) ?>" placeholder="<?= $defSub ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- CTA BANNER -->
                    <div class="tab-pane fade <?= $currentTab === 'cta' ? 'show active' : '' ?>" id="tab-cta">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-megaphone"></i> Banner CTA (bawah homepage)</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Judul CTA</label>
                                        <input type="text" name="cta_headline" class="form-control" value="<?= e($val('cta_headline', 'Your Next Adventure Awaits!')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Subheadline CTA</label>
                                        <textarea name="cta_subheadline" class="form-control" rows="2"><?= e($val('cta_subheadline', 'Hubungi kami sekarang dan biarkan tim DNA Vacation mewujudkan liburan impian Anda!')) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="tab-pane fade <?= $currentTab === 'footer' ? 'show active' : '' ?>" id="tab-footer">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-window-dock"></i> Footer</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Tagline Footer (script)</label>
                                        <input type="text" name="footer_tagline" class="form-control" value="<?= e($val('footer_tagline', "It's More Than Fun")) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Copyright Line</label>
                                        <input type="text" name="copyright_text" class="form-control" value="<?= e($val('copyright_text', 'Made with ♥ for Indonesian travelers')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Deskripsi Singkat (di kolom pertama footer)</label>
                                        <textarea name="about_text" class="form-control" rows="4"><?= e($val('about_text')) ?></textarea>
                                        <small class="text-muted">Sama dengan "Tentang" — muncul di footer & halaman About</small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Text Footer Tambahan</label>
                                        <input type="text" name="footer_text" class="form-control" value="<?= e($val('footer_text')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KONTAK -->
                    <div class="tab-pane fade <?= $currentTab === 'kontak' ? 'show active' : '' ?>" id="tab-kontak">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-telephone"></i> Kontak & Social Media</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Nomor WhatsApp (link)</label>
                                        <input type="text" name="whatsapp_number" class="form-control" value="<?= e($val('whatsapp_number')) ?>" placeholder="6281234567890">
                                        <small class="text-muted">Format: 62xxx (tanpa + atau spasi) — untuk link WhatsApp</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Nomor Telepon (display)</label>
                                        <input type="text" name="phone" class="form-control" value="<?= e($val('phone')) ?>" placeholder="+62 812-3456-7890">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?= e($val('email')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Jam Operasional</label>
                                        <input type="text" name="operational_hours" class="form-control" value="<?= e($val('operational_hours', 'Setiap hari, 08:00 - 22:00 WITA')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Alamat</label>
                                        <input type="text" name="address" class="form-control" value="<?= e($val('address')) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small"><i class="bi bi-instagram"></i> Instagram URL</label>
                                        <input type="url" name="instagram_url" class="form-control" value="<?= e($val('instagram_url')) ?>" placeholder="https://instagram.com/dnavacation">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small"><i class="bi bi-facebook"></i> Facebook URL</label>
                                        <input type="url" name="facebook_url" class="form-control" value="<?= e($val('facebook_url')) ?>" placeholder="https://facebook.com/dnavacation">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small"><i class="bi bi-tiktok"></i> TikTok URL</label>
                                        <input type="url" name="tiktok_url" class="form-control" value="<?= e($val('tiktok_url')) ?>" placeholder="https://tiktok.com/@dnavacation">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TENTANG -->
                    <div class="tab-pane fade <?= $currentTab === 'tentang' ? 'show active' : '' ?>" id="tab-tentang">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-info-circle"></i> Tentang Kami</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Tentang DNA Vacation</label>
                                        <textarea name="about_text" class="form-control" rows="4"><?= e($val('about_text')) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Visi</label>
                                        <textarea name="vision" class="form-control" rows="3"><?= e($val('vision')) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Misi</label>
                                        <textarea name="mission" class="form-control" rows="3"><?= e($val('mission')) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOGGLES -->
                    <div class="tab-pane fade <?= $currentTab === 'toggles' ? 'show active' : '' ?>" id="tab-toggles">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-toggles"></i> Tampil / Sembunyi Section Homepage</div>
                            <div class="card-body">
                                <p class="text-muted small">Aktifkan/nonaktifkan section di homepage tanpa perlu edit kode.</p>
                                <?php
                                $toggleFields = [
                                    ['show_trust_strip', 'Trust Strip', 'Bar dengan 4 badge (Best Price, 24/7 Support, dll)'],
                                    ['show_destinations', 'Popular Destinations', 'Grid destinasi favorit'],
                                    ['show_featured_cars', 'Featured Cars', 'Section mobil pilihan di homepage'],
                                    ['show_testimonials_home', 'Testimoni di Homepage', 'Section testimoni di homepage'],
                                    ['show_blog_home', 'Blog di Homepage', 'Section artikel blog di homepage'],
                                    ['show_cta', 'CTA Banner', 'Banner "Your Next Adventure" di paling bawah'],
                                ];
                                foreach ($toggleFields as [$key, $label, $desc]): ?>
                                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2">
                                    <div>
                                        <strong class="small"><?= $label ?></strong>
                                        <div class="text-muted small"><?= $desc ?></div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="<?= $key ?>" value="0">
                                        <input type="checkbox" name="<?= $key ?>" value="1" class="form-check-input" id="tg_<?= $key ?>" style="width:3rem;height:1.5rem" <?= $checked($key, true) ? 'checked' : '' ?>>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="tab-pane fade <?= $currentTab === 'seo' ? 'show active' : '' ?>" id="tab-seo">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold"><i class="bi bi-search"></i> SEO Meta Tags</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Meta Title (default)</label>
                                        <input type="text" name="meta_title" class="form-control" value="<?= e($val('meta_title')) ?>">
                                        <small class="text-muted">Judul yang muncul di tab browser & hasil Google</small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Meta Description (default)</label>
                                        <textarea name="meta_description" class="form-control" rows="3"><?= e($val('meta_description')) ?></textarea>
                                        <small class="text-muted">Deskripsi yang muncul di bawah judul di hasil Google (max 160 karakter)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Save Button (always visible) -->
                <div class="d-flex justify-content-end mt-3 pb-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5"><i class="bi bi-check-lg"></i> Simpan Semua Pengaturan</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

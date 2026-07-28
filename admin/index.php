<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

// ---- Core counts ----
$totalTours       = (int)$pdo->query("SELECT COUNT(*) FROM tours")->fetchColumn();
$activeTours      = (int)$pdo->query("SELECT COUNT(*) FROM tours WHERE is_active=1")->fetchColumn();
$totalCars        = (int)$pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$availableCars    = (int)$pdo->query("SELECT COUNT(*) FROM cars WHERE is_available=1")->fetchColumn();
$totalBlog        = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$publishedBlog    = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_published=1")->fetchColumn();
$totalTesti       = (int)$pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
$activeTesti      = (int)$pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn();

// ---- Incoming requests (needs attention) ----
$totalTourBookings = (int)$pdo->query("SELECT COUNT(*) FROM tour_bookings")->fetchColumn();
$totalCarBookings  = (int)$pdo->query("SELECT COUNT(*) FROM car_bookings")->fetchColumn();
$totalHotelReq     = (int)$pdo->query("SELECT COUNT(*) FROM hotel_requests")->fetchColumn();
$totalCustom       = (int)$pdo->query("SELECT COUNT(*) FROM custom_trip_requests")->fetchColumn();

// ---- Revenue ----
$revenueThisMonth = (int)$pdo->query("SELECT COALESCE(SUM(total_price),0) FROM tour_bookings WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) AND status IN ('terkonfirmasi','selesai')")->fetchColumn();
$revenueCarMonth  = (int)$pdo->query("SELECT COALESCE(SUM(total_price),0) FROM car_bookings WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) AND status IN ('terkonfirmasi','selesai')")->fetchColumn();
$revenueTotal     = $revenueThisMonth + $revenueCarMonth;

// ---- Recent activity ----
$recentTourBookings   = $pdo->query("SELECT tb.*, t.title as tour_name FROM tour_bookings tb LEFT JOIN tours t ON tb.tour_id = t.id ORDER BY tb.created_at DESC LIMIT 5")->fetchAll();
$recentCarBookings    = $pdo->query("SELECT cb.*, c.name as car_name FROM car_bookings cb LEFT JOIN cars c ON cb.car_id = c.id ORDER BY cb.created_at DESC LIMIT 5")->fetchAll();
$pendingTestimonials  = $pdo->query("SELECT * FROM testimonials WHERE is_active=0 ORDER BY created_at DESC LIMIT 5")->fetchAll();
$topTours             = $pdo->query("SELECT title, view_count, price FROM tours WHERE is_active=1 ORDER BY view_count DESC LIMIT 5")->fetchAll();

// Total items needing attention
$needsAttention = $newTourBookings + $newCarBookings + $newHotelReqs + $newCustomTrips + $pendingTesti + $unreadMsgs;
?>

<div class="p-3 p-md-4">

    <!-- Page heading -->
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-speedometer2 text-primary-custom"></i> Dashboard</h4>
            <p class="text-muted small mb-0">Selamat datang kembali, <strong><?= e($_SESSION['admin_name']) ?></strong>! Berikut ringkasan aktivitas website Anda.</p>
        </div>
        <div class="text-md-end">
            <div class="text-muted small"><?= date('l, d F Y') ?></div>
            <?php if ($needsAttention > 0): ?>
                <span class="badge bg-danger mt-1"><i class="bi bi-bell-fill"></i> <?= $needsAttention ?> item butuh perhatian</span>
            <?php else: ?>
                <span class="badge bg-success mt-1"><i class="bi bi-check-circle-fill"></i> Semua sudah ditangani</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="settings.php" class="quick-action quick-action-gold">
                <span class="qa-icon" style="background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#fff"><i class="bi bi-palette2"></i></span>
                <span class="qa-text">
                    <span class="qa-title">Pengaturan Tampilan</span>
                    <span class="qa-desc">Logo, hero, footer</span>
                </span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="tour-form.php" class="quick-action">
                <span class="qa-icon" style="background:var(--primary-light);color:var(--primary)"><i class="bi bi-plus-lg"></i></span>
                <span class="qa-text">
                    <span class="qa-title">Tambah Paket Tour</span>
                    <span class="qa-desc">Buat paket wisata baru</span>
                </span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="blog-form.php" class="quick-action">
                <span class="qa-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-pencil-square"></i></span>
                <span class="qa-text">
                    <span class="qa-title">Tulis Artikel</span>
                    <span class="qa-desc">Posting blog baru</span>
                </span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="testimonials.php<?= $pendingTesti ? '?filter=pending' : '' ?>" class="quick-action">
                <span class="qa-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-chat-quote"></i></span>
                <span class="qa-text">
                    <span class="qa-title">Moderasi Testimoni</span>
                    <span class="qa-desc"><?= $pendingTesti ? $pendingTesti . ' menunggu approval' : 'Tidak ada pending' ?></span>
                </span>
            </a>
        </div>
    </div>

    <!-- Revenue + Attention row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="revenue-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small opacity-75 mb-1">Estimasi Revenue Bulan Ini</div>
                        <h3 class="fw-bold mb-2"><?= formatRupiah($revenueTotal) ?></h3>
                        <div class="d-flex gap-3 small opacity-85">
                            <span><i class="bi bi-compass"></i> Tour: <?= formatRupiah($revenueThisMonth) ?></span>
                            <span><i class="bi bi-car-front"></i> Mobil: <?= formatRupiah($revenueCarMonth) ?></span>
                        </div>
                    </div>
                    <i class="bi bi-graph-up-arrow" style="font-size:2.5rem;opacity:.25"></i>
                </div>
                <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,.15)">
                    <small class="opacity-75">Dihitung dari booking berstatus <em>Terkonfirmasi</em> &amp; <em>Selesai</em> pada <?= date('F Y') ?>.</small>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-inbox text-primary-custom"></i> Perlu Ditindaklanjuti</span>
                    <?php if ($needsAttention > 0): ?><span class="badge bg-danger"><?= $needsAttention ?></span><?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php
                        $attentionItems = [
                            ['bookings.php',        'bi-journal-check', 'Booking Tour',   $newTourBookings,  '#dbeafe', '#1d4ed8'],
                            ['car-bookings.php',    'bi-receipt',       'Booking Mobil',  $newCarBookings,   '#fce7f3', '#be185d'],
                            ['hotel-requests.php',  'bi-building',      'Request Hotel',  $newHotelReqs,     '#ede9fe', '#6d28d9'],
                            ['custom-trips.php',    'bi-compass',       'Custom Trip',    $newCustomTrips,   '#ffedd5', '#c2410c'],
                            ['testimonials.php',    'bi-chat-quote',    'Testimoni',      $pendingTesti,     '#fef3c7', '#b45309'],
                            ['messages.php',        'bi-envelope',      'Pesan Masuk',    $unreadMsgs,       '#d1fae5', '#047857'],
                        ];
                        foreach ($attentionItems as [$url, $icon, $label, $count, $bg, $fg]): ?>
                        <div class="col-6 col-md-4">
                            <a href="<?= $url ?>" class="attention-tile <?= $count > 0 ? 'has-items' : '' ?>">
                                <span class="at-icon" style="background:<?= $bg ?>;color:<?= $fg ?>"><i class="bi <?= $icon ?>"></i></span>
                                <span class="at-count"><?= $count ?></span>
                                <span class="at-label"><?= $label ?></span>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content stats -->
    <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:.75rem;letter-spacing:1px">Ringkasan Konten</h6>
    <div class="row g-3 mb-4">
        <?php
        $contentStats = [
            ['tours.php',        'bi-map',                'Paket Tour',  $totalTours, "$activeTours aktif",       'var(--primary-light)', 'var(--primary)'],
            ['cars.php',         'bi-car-front',          'Mobil',       $totalCars,  "$availableCars tersedia",  '#dbeafe', '#1d4ed8'],
            ['blog.php',         'bi-file-earmark-text',  'Artikel Blog',$totalBlog,  "$publishedBlog published", '#d1fae5', '#047857'],
            ['testimonials.php', 'bi-chat-quote',         'Testimoni',   $totalTesti, "$activeTesti tampil",      '#fef3c7', '#b45309'],
        ];
        foreach ($contentStats as [$url, $icon, $label, $value, $sub, $bg, $fg]): ?>
        <div class="col-6 col-lg-3">
            <a href="<?= $url ?>" class="stat-card stat-card-link">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="stat-icon" style="background:<?= $bg ?>;color:<?= $fg ?>"><i class="bi <?= $icon ?>"></i></span>
                    <i class="bi bi-arrow-up-right text-muted" style="font-size:.85rem"></i>
                </div>
                <div class="stat-value"><?= $value ?></div>
                <div class="stat-label"><?= $label ?></div>
                <div class="stat-sub"><?= $sub ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Booking totals -->
    <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:.75rem;letter-spacing:1px">Total Transaksi</h6>
    <div class="row g-3 mb-4">
        <?php
        $bookingStats = [
            ['bookings.php',       'bi-journal-check', 'Booking Tour',  $totalTourBookings, '#fef3c7', '#b45309'],
            ['car-bookings.php',   'bi-receipt',       'Booking Mobil', $totalCarBookings,  '#fce7f3', '#be185d'],
            ['hotel-requests.php', 'bi-building',      'Request Hotel', $totalHotelReq,     '#ede9fe', '#6d28d9'],
            ['custom-trips.php',   'bi-compass',       'Custom Trip',   $totalCustom,       '#ffedd5', '#c2410c'],
        ];
        foreach ($bookingStats as [$url, $icon, $label, $value, $bg, $fg]): ?>
        <div class="col-6 col-lg-3">
            <a href="<?= $url ?>" class="stat-card stat-card-link">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="stat-icon" style="background:<?= $bg ?>;color:<?= $fg ?>"><i class="bi <?= $icon ?>"></i></span>
                    <i class="bi bi-arrow-up-right text-muted" style="font-size:.85rem"></i>
                </div>
                <div class="stat-value"><?= $value ?></div>
                <div class="stat-label"><?= $label ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Recent bookings -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-journal-check text-primary-custom"></i> Booking Tour Terbaru</span>
                    <a href="bookings.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentTourBookings)): ?>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Belum ada booking tour masuk.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 small align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Pelanggan</th>
                                        <th>Paket</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentTourBookings as $b): ?>
                                        <tr>
                                            <td><code class="small"><?= e($b['booking_code']) ?></code></td>
                                            <td><?= e($b['customer_name']) ?></td>
                                            <td class="text-muted"><?= e(truncate($b['tour_name'] ?? '-', 22)) ?></td>
                                            <td><span class="status-badge status-<?= e($b['status']) ?>"><?= e(str_replace('_', ' ', $b['status'])) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-receipt text-primary-custom"></i> Booking Mobil Terbaru</span>
                    <a href="car-bookings.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentCarBookings)): ?>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Belum ada booking mobil masuk.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 small align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Pelanggan</th>
                                        <th>Mobil</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCarBookings as $b): ?>
                                        <tr>
                                            <td><code class="small"><?= e($b['booking_code']) ?></code></td>
                                            <td><?= e($b['customer_name']) ?></td>
                                            <td class="text-muted"><?= e(truncate($b['car_name'] ?? '-', 18)) ?></td>
                                            <td><span class="status-badge status-<?= e($b['status']) ?>"><?= e(str_replace('_', ' ', $b['status'])) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom row: top tours + pending testimonials -->
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-fire text-danger"></i> Paket Tour Terpopuler
                </div>
                <div class="card-body">
                    <?php if (empty($topTours)): ?>
                        <div class="empty-state"><i class="bi bi-map"></i><p>Belum ada paket tour.</p></div>
                    <?php else: ?>
                        <?php foreach ($topTours as $i => $t): ?>
                        <div class="d-flex align-items-center gap-3 <?= $i < count($topTours) - 1 ? 'mb-3 pb-3 border-bottom' : '' ?>">
                            <span class="rank-badge"><?= $i + 1 ?></span>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold small text-truncate"><?= e($t['title']) ?></div>
                                <div class="text-muted" style="font-size:.75rem"><?= formatRupiah($t['price']) ?></div>
                            </div>
                            <span class="badge bg-light text-muted flex-shrink-0"><i class="bi bi-eye"></i> <?= $t['view_count'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-chat-quote text-warning"></i> Testimoni Menunggu Persetujuan</span>
                    <a href="testimonials.php" class="btn btn-sm btn-outline-primary">Kelola</a>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingTestimonials)): ?>
                        <div class="empty-state">
                            <i class="bi bi-check2-circle text-success"></i>
                            <p>Semua testimoni sudah dimoderasi.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingTestimonials as $t): ?>
                        <div class="pending-testi">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <strong class="small"><?= e($t['customer_name']) ?></strong>
                                        <span class="text-muted" style="font-size:.75rem"><?= e($t['customer_location'] ?: '-') ?></span>
                                        <span class="text-warning" style="font-size:.75rem"><?= str_repeat('★', $t['rating']) ?></span>
                                    </div>
                                    <p class="mb-0 text-muted" style="font-size:.82rem"><?= e(truncate($t['content'], 130)) ?></p>
                                </div>
                                <div class="d-flex gap-1 flex-shrink-0">
                                    <a href="testimonials.php?approve=<?= $t['id'] ?>" class="btn btn-success btn-sm" title="Setujui"><i class="bi bi-check-lg"></i></a>
                                    <a href="testimonials.php?delete=<?= $t['id'] ?>" class="btn btn-outline-danger btn-sm" data-confirm="Hapus testimoni ini?" title="Hapus"><i class="bi bi-trash"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

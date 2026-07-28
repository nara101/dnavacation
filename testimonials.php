<?php
$pageTitle = 'Testimoni Pelanggan';
require_once 'includes/header.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    $name = trim($_POST['customer_name'] ?? '');
    $location = trim($_POST['customer_location'] ?? '');
    $tourName = trim($_POST['tour_name'] ?? '');
    $tourId = (int)($_POST['tour_id'] ?? 0) ?: null;
    $content = trim($_POST['content'] ?? '');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $img = uploadImage($_FILES['image'], 'testimonials');
        if ($img) $image = $img;
    }

    if ($name && $content) {
        $stmt = $pdo->prepare("INSERT INTO testimonials (customer_name, customer_location, content, rating, tour_id, tour_name, image, is_active) VALUES (?,?,?,?,?,?,?,0)");
        $stmt->execute([$name, $location, $content, $rating, $tourId, $tourName, $image]);
        $success = true;
    } else {
        $error = 'Nama dan isi testimoni wajib diisi.';
    }
}

// Pagination
$perPage = 12;
$page = max(1, (int)($_GET['page'] ?? 1));
$total = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn();
$totalPages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;

$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}")->fetchAll();

$avgRating = $pdo->query("SELECT AVG(rating) FROM testimonials WHERE is_active=1")->fetchColumn() ?: 0;

// For tour dropdown
$activeTours = $pdo->query("SELECT id, title FROM tours WHERE is_active=1 ORDER BY title")->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>Testimoni <span class="script">Pelanggan</span></h1>
        <p>Cerita dan pengalaman nyata dari para traveler yang telah berlibur bersama DNA Vacation</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong><i class="bi bi-check-circle-fill"></i> Terima kasih!</strong> Testimoni Anda telah kami terima dan akan ditampilkan setelah moderasi admin.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Rating summary -->
        <div class="row g-4 mb-5 align-items-center">
            <div class="col-md-4">
                <div class="text-center p-4 rounded-3" style="background:linear-gradient(135deg,var(--primary-lighter),#fff);border:1px solid var(--primary-light)">
                    <div class="mb-2" style="font-size:3.5rem;font-weight:800;color:var(--primary);line-height:1"><?= number_format($avgRating, 1) ?></div>
                    <div class="text-warning mb-2" style="font-size:1.2rem">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?= $i <= round($avgRating) ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-muted small mb-0">Dari <strong><?= $total ?></strong> testimoni pelanggan</p>
                </div>
            </div>
            <div class="col-md-8">
                <h3 class="fw-bold mb-2">Cerita Nyata dari Pelanggan Kami</h3>
                <p class="text-muted mb-3">Setiap testimoni adalah pengalaman jujur dari pelanggan yang telah menggunakan layanan DNA Vacation. Kami sangat menghargai setiap feedback yang membantu kami terus meningkatkan kualitas layanan.</p>
                <a href="#form-testimoni" class="btn btn-primary-custom"><i class="bi bi-pencil-square"></i> Tulis Testimoni Anda</a>
            </div>
        </div>

        <!-- Testimonials grid -->
        <?php if (empty($testimonials)): ?>
            <div class="text-center py-5">
                <i class="bi bi-chat-quote text-muted" style="font-size:3rem"></i>
                <p class="mt-3 text-muted">Belum ada testimoni. Jadilah yang pertama!</p>
            </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($testimonials as $t): ?>
            <div class="col-md-6 col-lg-4">
                <div class="testimonial-card h-100">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?= $i <= $t['rating'] ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <?php if ($t['tour_name']): ?>
                        <p class="small mb-2"><span class="badge bg-primary-custom text-white" style="font-weight:500;background:var(--primary)!important"><?= e($t['tour_name']) ?></span></p>
                    <?php endif; ?>
                    <p class="mb-3" style="font-size:.92rem">"<?= nl2br(e($t['content'])) ?>"</p>
                    <div class="d-flex align-items-center gap-2 pt-3 border-top">
                        <?php if ($t['image']): ?>
                            <img src="<?= uploadUrl($t['image']) ?>" alt="<?= e($t['customer_name']) ?>" class="rounded-circle" style="width:40px;height:40px;object-fit:cover" onerror="this.style.display='none'">
                        <?php else: ?>
                            <div class="customer-avatar"><?= strtoupper(substr($t['customer_name'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <div class="customer-name"><?= e($t['customer_name']) ?></div>
                            <div class="customer-location"><i class="bi bi-geo-alt"></i> <?= e($t['customer_location'] ?: 'Indonesia') ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="mt-4"><ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
        </ul></nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Submission form -->
<section class="py-5 bg-gray-50" id="form-testimoni">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="section-title">Bagikan Pengalaman Anda</h2>
                    <p class="section-subtitle">Ceritakan pengalaman liburan Anda bersama DNA Vacation. Testimoni akan ditampilkan setelah dimoderasi admin.</p>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="submit_testimonial" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Kota Asal</label>
                                    <input type="text" name="customer_location" class="form-control" placeholder="Contoh: Jakarta, Makassar">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small">Paket / Layanan yang Digunakan</label>
                                    <select name="tour_selection" class="form-select" onchange="handleTourSelection(this)">
                                        <option value="">-- Pilih paket --</option>
                                        <?php foreach ($activeTours as $t): ?>
                                        <option value="tour:<?= $t['id'] ?>::<?= e($t['title']) ?>"><?= e($t['title']) ?></option>
                                        <?php endforeach; ?>
                                        <option value="other::Rental Mobil">Rental Mobil</option>
                                        <option value="other::Booking Hotel">Booking Hotel</option>
                                        <option value="other::Custom Trip">Custom Trip</option>
                                    </select>
                                    <input type="hidden" name="tour_id" id="testimonial-tour-id">
                                    <input type="hidden" name="tour_name" id="testimonial-tour-name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Rating <span class="text-danger">*</span></label>
                                    <select name="rating" class="form-select" required>
                                        <option value="5" selected>⭐⭐⭐⭐⭐ (5 - Sangat Puas)</option>
                                        <option value="4">⭐⭐⭐⭐ (4 - Puas)</option>
                                        <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                        <option value="2">⭐⭐ (2 - Kurang)</option>
                                        <option value="1">⭐ (1 - Sangat Kurang)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Cerita / Testimoni Anda <span class="text-danger">*</span></label>
                                    <textarea name="content" class="form-control" rows="5" placeholder="Ceritakan pengalaman Anda bersama DNA Vacation..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Foto Anda (opsional)</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Maks 5MB. Format: JPG, PNG, WEBP</small>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info small mb-3">
                                        <i class="bi bi-info-circle-fill"></i>
                                        Testimoni Anda akan ditinjau oleh admin sebelum ditampilkan di website. Terima kasih atas partisipasi Anda!
                                    </div>
                                    <button type="submit" class="btn btn-primary-custom btn-lg w-100">
                                        <i class="bi bi-send"></i> Kirim Testimoni
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function handleTourSelection(sel) {
    const val = sel.value;
    const tourId = document.getElementById('testimonial-tour-id');
    const tourName = document.getElementById('testimonial-tour-name');
    if (!val) { tourId.value = ''; tourName.value = ''; return; }
    if (val.startsWith('tour:')) {
        const rest = val.substring(5);
        const parts = rest.split('::');
        tourId.value = parts[0];
        tourName.value = parts[1] || '';
    } else if (val.startsWith('other::')) {
        tourId.value = '';
        tourName.value = val.substring(7);
    }
}
</script>
<?php require_once 'includes/footer.php'; ?>

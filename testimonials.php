<?php
$pageTitle = 'Testimoni Pelanggan';
require_once 'includes/header.php';

// Deteksi upload yang melebihi post_max_size: body terkirim tapi $_POST kosong.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $maxMsg = 'File video terlalu besar untuk server ini (maks. sekitar ' . ini_get('upload_max_filesize') . '). Kompres video atau gunakan file yang lebih kecil.';
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json', true, 413);
        echo json_encode(['error' => $maxMsg]);
        exit;
    }
    setFlash('danger', $maxMsg);
    header('Location: ' . BASE_URL . '/testimonials.php#form-testimoni');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    if (!isUserLoggedIn()) {
        $_SESSION['login_redirect'] = BASE_URL . '/testimonials.php#form-testimoni';
        setFlash('warning', 'Silakan login terlebih dahulu untuk mengirim testimoni.');
        header('Location: ' . BASE_URL . '/user-login.php');
        exit;
    }

    $name = trim($_POST['customer_name'] ?? '');
    $location = trim($_POST['customer_location'] ?? '');
    $tourName = trim($_POST['tour_name'] ?? '');
    $tourId = (int)($_POST['tour_id'] ?? 0) ?: null;
    $content = trim($_POST['content'] ?? '');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $postError = '';

    // Upload file bersifat OPSIONAL: kalau gagal, testimoni tetap disimpan
    // (tanpa file) dengan catatan, agar tidak hilang total.
    $warnings = [];

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $img = uploadImage($_FILES['image'], 'testimonials');
        if ($img) {
            $image = $img;
        } else {
            $warnings[] = 'Foto gagal diunggah (cek format/ukuran, atau folder uploads/testimonials belum bisa ditulis), jadi testimoni disimpan tanpa foto.';
        }
    }

    $video = null;
    if (!empty($_FILES['video']['name'])) {
        $vid = uploadVideo($_FILES['video'], 'testimonials');
        if ($vid) {
            $video = $vid;
        } else {
            $warnings[] = 'Video gagal diunggah (maks 50MB, format MP4/WEBM/MOV, atau folder uploads/testimonials belum bisa ditulis), jadi testimoni disimpan tanpa video.';
        }
    }

    if ($name && $content) {
        $stmt = $pdo->prepare("INSERT INTO testimonials (customer_name, customer_location, content, rating, tour_id, tour_name, image, video, is_active) VALUES (?,?,?,?,?,?,?,?,0)");
        $stmt->execute([$name, $location, $content, $rating, $tourId, $tourName, $image, $video]);
        if ($warnings) {
            setFlash('warning', 'Testimoni tersimpan dan menunggu moderasi admin. Catatan: ' . implode(' ', $warnings));
        } else {
            setFlash('success', 'Terima kasih! Testimoni Anda telah kami terima dan akan ditampilkan setelah moderasi admin.');
        }
    } else {
        $postError = 'Nama dan isi testimoni wajib diisi.';
    }

    if ($postError) {
        setFlash('danger', $postError);
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['redirect' => BASE_URL . '/testimonials.php#form-testimoni']);
        exit;
    }
    header('Location: ' . BASE_URL . '/testimonials.php#form-testimoni');
    exit;
}

$flash = getFlash();

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
        <h1>Testimoni Pelanggan</h1>
        <p>Cerita dan pengalaman nyata dari para traveler yang telah berlibur bersama DNA VACATION</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
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
                <p class="text-muted mb-3">Setiap testimoni adalah pengalaman jujur dari pelanggan yang telah menggunakan layanan DNA VACATION. Kami sangat menghargai setiap feedback yang membantu kami terus meningkatkan kualitas layanan.</p>
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
                            <?php if (!empty($t['video'])): ?>
                                <div class="mb-3 rounded overflow-hidden">
                                    <video controls preload="metadata" style="width:100%;max-height:220px;border-radius:.5rem;background:#000">
                                        <source src="<?= uploadUrl($t['video']) ?>" type="video/<?= pathinfo($t['video'], PATHINFO_EXTENSION) === 'mov' ? 'quicktime' : pathinfo($t['video'], PATHINFO_EXTENSION) ?>">
                                        Browser Anda tidak mendukung pemutar video.
                                    </video>
                                </div>
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
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                    </ul>
                </nav>
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
                    <p class="section-subtitle">Ceritakan pengalaman liburan Anda bersama DNA VACATION. Testimoni akan ditampilkan setelah dimoderasi admin.</p>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php $bu = isUserLoggedIn() ? getLoggedInUser() : null; ?>
                        <form method="POST" enctype="multipart/form-data" onsubmit="return checkLoginBeforeSubmit(this)">
                            <input type="hidden" name="submit_testimonial" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control" required value="<?= e($bu['full_name'] ?? $bu['username'] ?? '') ?>">
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
                                    <textarea name="content" class="form-control" rows="5" placeholder="Ceritakan pengalaman Anda bersama DNA VACATION..." required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Foto Anda (opsional)</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Maks 5MB. Format: JPG, PNG, WEBP</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small"><i class="bi bi-camera-video"></i> Video (opsional)</label>
                                    <input type="file" name="video" class="form-control" accept="video/*" id="videoInput">
                                    <small class="text-muted">Maks 50MB. Format: MP4, WEBM, MOV</small>
                                    <div id="videoPreview" class="mt-2 d-none">
                                        <video id="videoPreviewPlayer" controls style="width:100%;max-height:180px;border-radius:.5rem;background:#000"></video>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="clearVideoPreview()"><i class="bi bi-x"></i> Hapus</button>
                                    </div>
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
        if (!val) {
            tourId.value = '';
            tourName.value = '';
            return;
        }
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

    document.getElementById('videoInput').addEventListener('change', function() {
        const preview = document.getElementById('videoPreview');
        const player = document.getElementById('videoPreviewPlayer');
        if (this.files && this.files[0]) {
            if (this.files[0].size > 50 * 1024 * 1024) {
                alert('Ukuran video maksimal 50MB.');
                this.value = '';
                return;
            }
            player.src = URL.createObjectURL(this.files[0]);
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
            player.src = '';
        }
    });

    function clearVideoPreview() {
        const input = document.getElementById('videoInput');
        const preview = document.getElementById('videoPreview');
        const player = document.getElementById('videoPreviewPlayer');
        input.value = '';
        player.src = '';
        preview.classList.add('d-none');
    }

    (function() {
        const form = document.querySelector('#form-testimoni form');
        if (!form) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        const btnOrigText = submitBtn.innerHTML;

        const overlay = document.createElement('div');
        overlay.id = 'uploadOverlay';
        overlay.innerHTML = `
        <div style="background:#fff;border-radius:12px;padding:2rem;max-width:420px;width:90%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,.15)">
            <i class="bi bi-cloud-arrow-up" style="font-size:2.5rem;color:var(--primary)"></i>
            <h5 class="fw-bold mt-2 mb-1">Mengupload testimoni...</h5>
            <p class="text-muted small mb-3" id="uploadStatus">Mengirim data...</p>
            <div style="background:#e9ecef;border-radius:8px;height:12px;overflow:hidden;margin-bottom:.5rem">
                <div id="uploadBar" style="height:100%;width:0%;background:linear-gradient(90deg,var(--primary),var(--accent));border-radius:8px;transition:width .2s"></div>
            </div>
            <span class="fw-bold" style="color:var(--primary)" id="uploadPercent">0%</span>
            <p class="text-muted small mt-2 mb-0">Jangan tutup halaman ini</p>
        </div>`;
        overlay.style.cssText = 'display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center';
        document.body.appendChild(overlay);

        form.addEventListener('submit', function(e) {
            var videoFile = document.getElementById('videoInput').files[0];
            var imageFile = form.querySelector('input[name="image"]').files[0];
            var hasFile = videoFile || imageFile;

            if (!hasFile) return;

            e.preventDefault();
            overlay.style.display = 'flex';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengupload...';

            var bar = document.getElementById('uploadBar');
            var pct = document.getElementById('uploadPercent');
            var status = document.getElementById('uploadStatus');
            var xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(ev) {
                if (ev.lengthComputable) {
                    var p = Math.round(ev.loaded / ev.total * 100);
                    bar.style.width = p + '%';
                    pct.textContent = p + '%';
                    var mb = (ev.loaded / 1024 / 1024).toFixed(1);
                    var totalMb = (ev.total / 1024 / 1024).toFixed(1);
                    status.textContent = mb + ' MB / ' + totalMb + ' MB';
                }
            });

            xhr.addEventListener('load', function() {
                bar.style.width = '100%';
                pct.textContent = '100%';
                if (xhr.status < 200 || xhr.status >= 400) {
                    overlay.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = btnOrigText;
                    var msg = 'Terjadi kesalahan di server (kode ' + xhr.status + '). Silakan coba lagi.';
                    try {
                        var errRes = JSON.parse(xhr.responseText);
                        if (errRes && errRes.error) msg = errRes.error;
                    } catch (e) {}
                    alert(msg);
                    return;
                }
                status.textContent = 'Selesai! Mengalihkan...';
                var target = '<?= BASE_URL ?>/testimonials.php#form-testimoni';
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.redirect) target = res.redirect;
                } catch (e) {}
                // Tambah parameter unik agar browser BENAR-BENAR memuat ulang.
                // Kalau URL tujuan sama persis (termasuk #hash) dengan URL saat ini,
                // browser tidak akan reload dan overlay akan macet di "Mengalihkan".
                var hashIdx = target.indexOf('#');
                var hash = hashIdx >= 0 ? target.slice(hashIdx) : '';
                var path = hashIdx >= 0 ? target.slice(0, hashIdx) : target;
                path += (path.indexOf('?') >= 0 ? '&' : '?') + '_ok=' + Date.now();
                window.location.href = path + hash;
            });

            xhr.addEventListener('error', function() {
                overlay.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = btnOrigText;
                alert('Upload gagal. Periksa koneksi internet Anda dan coba lagi.');
            });

            xhr.addEventListener('timeout', function() {
                overlay.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = btnOrigText;
                alert('Upload timeout. File mungkin terlalu besar atau koneksi lambat.');
            });

            xhr.open('POST', window.location.href);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.timeout = 300000;
            xhr.send(new FormData(form));
        });
    })();
</script>
<?php require_once 'includes/footer.php'; ?>
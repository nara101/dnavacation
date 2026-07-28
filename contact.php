<?php
$pageTitle = 'Kontak';
require_once 'includes/header.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $wa = trim($_POST['whatsapp'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $message) {
        $ins = $pdo->prepare("INSERT INTO contact_messages (name, email, whatsapp, subject, message) VALUES (?,?,?,?,?)");
        $ins->execute([$name, $email, $wa, $subject, $message]);
        $success = true;
    }
}
?>

<div class="page-header">
    <div class="container">
        <h1>Kontak <span class="script">Kami</span></h1>
        <p>Ada pertanyaan? Kami siap membantu Anda kapan saja!</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong><i class="bi bi-check-circle-fill"></i> Pesan terkirim!</strong> Kami akan segera merespons pertanyaan Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-5">
                <h4 class="fw-bold mb-4">Informasi Kontak</h4>
                <div class="d-flex gap-3 mb-4">
                    <div class="feature-icon flex-shrink-0" style="background:#DCFCE7;color:#16A34A"><i class="bi bi-whatsapp"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">WhatsApp</h6>
                        <p class="text-muted mb-0"><?= e(getSetting('phone') ?: '+62 812-3456-7890') ?></p>
                        <a href="<?= waLink('Halo DNA Vacation!') ?>" target="_blank" class="small text-success">Chat sekarang <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="feature-icon flex-shrink-0" style="background:linear-gradient(135deg,#F0ABFC,#F472B6);color:#fff"><i class="bi bi-instagram"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Instagram</h6>
                        <p class="text-muted mb-0"><a href="<?= e(getSetting('instagram_url') ?: '#') ?>" target="_blank" class="text-decoration-none">@dnavacation</a></p>
                        <small class="text-muted">Follow untuk update terbaru!</small>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="feature-icon flex-shrink-0"><i class="bi bi-envelope"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Email</h6>
                        <p class="text-muted mb-0"><?= e(getSetting('email') ?: 'info@dnavacation.com') ?></p>
                        <small class="text-muted">Respons dalam 1x24 jam</small>
                    </div>
                </div>
                <?php if(getSetting('address')): ?>
                <div class="d-flex gap-3 mb-4">
                    <div class="feature-icon flex-shrink-0"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Alamat</h6>
                        <p class="text-muted mb-0"><?= e(getSetting('address')) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <div class="d-flex gap-3 mb-4">
                    <div class="feature-icon flex-shrink-0"><i class="bi bi-clock"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Jam Operasional</h6>
                        <p class="text-muted mb-0">Setiap hari, 08:00 - 22:00 WITA</p>
                        <small class="text-success">WhatsApp aktif 24/7</small>
                    </div>
                </div>
                <a href="<?= waLink('Halo DNA Vacation, saya ingin bertanya.') ?>" target="_blank" class="btn btn-wa btn-lg w-100 mt-3">
                    <i class="bi bi-whatsapp"></i> Chat via WhatsApp
                </a>
            </div>
            <div class="col-lg-7">
                <div class="booking-sidebar" style="position:static">
                    <h5 class="fw-bold mb-3"><i class="bi bi-envelope-paper text-primary-custom"></i> Kirim Pesan</h5>
                    <form method="POST">
                        <input type="hidden" name="send_message" value="1">
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">WhatsApp</label>
                                <input type="tel" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Subjek</label>
                            <input type="text" name="subject" class="form-control" placeholder="Pertanyaan tentang paket tour...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Pesan <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" required placeholder="Tulis pertanyaan Anda di sini..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-send"></i> Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

    <!-- Footer -->
    <footer class="pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="<?= e(logoUrl()) ?>" alt="<?= e(getSetting('site_name') ?: 'DNA Vacation') ?>" style="width:52px;height:52px;border-radius:10px;object-fit:cover">
                        <div>
                            <h5 class="fw-bold mb-0" style="font-size:1.15rem"><?= e(getSetting('site_name') ?: 'DNA Vacation') ?></h5>
                            <div class="footer-brand-tag"><?= e(getSetting('footer_tagline') ?: "It's More Than Fun") ?></div>
                        </div>
                    </div>
                    <p class="text-white-50 small mb-3 mt-3"><?= e(getSetting('about_text') ?: 'Travel agent terpercaya untuk liburan impian Anda.') ?></p>
                    <div class="d-flex gap-2 mt-3">
                        <?php if (getSetting('instagram_url')): ?>
                        <a href="<?= e(getSetting('instagram_url')) ?>" target="_blank" class="d-inline-flex align-items-center justify-content-center text-white" style="width:38px;height:38px;background:rgba(255,255,255,.08);border-radius:10px" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (getSetting('facebook_url')): ?>
                        <a href="<?= e(getSetting('facebook_url')) ?>" target="_blank" class="d-inline-flex align-items-center justify-content-center text-white" style="width:38px;height:38px;background:rgba(255,255,255,.08);border-radius:10px" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (getSetting('tiktok_url')): ?>
                        <a href="<?= e(getSetting('tiktok_url')) ?>" target="_blank" class="d-inline-flex align-items-center justify-content-center text-white" style="width:38px;height:38px;background:rgba(255,255,255,.08);border-radius:10px" title="TikTok">
                            <i class="bi bi-tiktok"></i>
                        </a>
                        <?php endif; ?>
                        <a href="<?= waLink() ?>" target="_blank" class="d-inline-flex align-items-center justify-content-center text-white" style="width:38px;height:38px;background:rgba(255,255,255,.08);border-radius:10px" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <?php if (getSetting('email')): ?>
                            <a href="mailto:<?= e(getSetting('email')) ?>" class="d-inline-flex align-items-center justify-content-center text-white" style="width:38px;height:38px;background:rgba(255,255,255,.08);border-radius:10px" title="Email">
                                <i class="bi bi-envelope"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="fw-semibold mb-3">Layanan</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= BASE_URL ?>/tours.php" class="text-white-50 text-decoration-none">Paket Tour</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/cars.php" class="text-white-50 text-decoration-none">Rent Mobil</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/hotel.php" class="text-white-50 text-decoration-none">Booking Hotel</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/custom-trip.php" class="text-white-50 text-decoration-none">Custom Trip</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="fw-semibold mb-3">Informasi</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= BASE_URL ?>/about.php" class="text-white-50 text-decoration-none">Tentang Kami</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/blog.php" class="text-white-50 text-decoration-none">Blog</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/testimonials.php" class="text-white-50 text-decoration-none">Testimoni</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/contact.php" class="text-white-50 text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-semibold mb-3">Kontak Kami</h6>
                    <ul class="list-unstyled small text-white-50">
                        <li class="mb-2 d-flex gap-2"><i class="bi bi-whatsapp text-success mt-1"></i><span><?= e(getSetting('phone') ?: '+62 812-3456-7890') ?></span></li>
                        <?php if (getSetting('email')): ?>
                        <li class="mb-2 d-flex gap-2"><i class="bi bi-envelope mt-1"></i><span><?= e(getSetting('email')) ?></span></li>
                        <?php endif; ?>
                        <?php if (getSetting('address')): ?>
                            <li class="mb-2 d-flex gap-2"><i class="bi bi-geo-alt mt-1"></i><span><?= e(getSetting('address')) ?></span></li>
                        <?php endif; ?>
                        <li class="mb-2 d-flex gap-2"><i class="bi bi-clock mt-1"></i><span><?= e(getSetting('operational_hours') ?: 'Buka 24/7 via WhatsApp') ?></span></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4" style="opacity:.2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-white-50 small">
                    &copy; <?= date('Y') ?> <?= e(getSetting('site_name') ?: SITE_NAME) ?>. All rights reserved.
                </div>
                <div class="text-white-50 small">
                    <?php
                    $copy = getSetting('copyright_text') ?: 'Made with ♥ for Indonesian travelers';
                    // Convert ♥ / heart placeholder to a bootstrap icon
                    $copy = str_replace(['♥', '❤'], '<i class="bi bi-heart-fill text-danger"></i>', e($copy));
                    echo $copy;
                    ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <a href="<?= waLink('Halo DNA Vacation, saya ingin bertanya tentang layanan Anda.') ?>"
        target="_blank"
        class="wa-float"
        title="Chat via WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
    </body>

    </html>

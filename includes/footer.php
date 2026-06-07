<?php
$waNumber = getSetting('whatsapp_number', '6285280825858');
$instagram = getSetting('instagram_url', 'https://www.instagram.com/dnavacation/');
$email = getSetting('email', 'info@dnavacation.com');
$alamat = getSetting('alamat', 'Jakarta, Indonesia');
$telepon = getSetting('telepon', '(021) 1234 5678');
?>
<footer>
    <section class="main-footer text-white">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-4">
                    <img src="<?= isset($basePath) ? $basePath : '' ?>assets/logoo.png" alt="DNA Vacation" class="footer-logo mb-3" style="height:55px;background:#fff;padding:6px 12px;border-radius:8px;">
                    <h6 class="fw-bold">PT. DNA Vacation</h6>
                    <p class="small mb-2"><?= htmlspecialchars($alamat) ?></p>
                    <p class="small mb-1"><i class="fa-solid fa-phone me-2"></i><?= htmlspecialchars($telepon) ?></p>
                    <p class="small mb-1"><i class="fa-brands fa-whatsapp me-2"></i>+<?= htmlspecialchars($waNumber) ?></p>
                    <p class="small mb-1"><i class="fa-solid fa-envelope me-2"></i><?= htmlspecialchars($email) ?></p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Layanan</h6>
                    <ul class="list-unstyled small footer-links">
                        <li><a href="tour.php">Paket Tour</a></li>
                        <li><a href="rental.php">Rental Mobil</a></li>
                        <li><a href="hotel.php">Booking Hotel</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="about.php">Tentang Kami</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Follow Us</h6>
                    <div class="social-icons mb-4">
                        <a href="<?= htmlspecialchars($instagram) ?>" target="_blank"><i class="bi bi-instagram"></i></a>
                        <a href="<?= waLink('Halo DNA Vacation') ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" target="_blank"><i class="bi bi-facebook"></i></a>
                        <a href="#" target="_blank"><i class="bi bi-tiktok"></i></a>
                    </div>
                    <h6 class="fw-bold">Chat WhatsApp</h6>
                    <a href="<?= waLink('Halo DNA Vacation, saya ingin konsultasi liburan.') ?>" class="btn btn-success" target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>Konsultasi Sekarang
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center mt-4">
            &copy; <?= date('Y') ?> DNA Vacation Tour & Travel | Gateway to Great Destinations
        </div>
    </section>
</footer>

<!-- Floating WhatsApp Button -->
<a href="<?= waLink('Halo DNA Vacation, saya ingin konsultasi.') ?>" class="float-wa" target="_blank" title="Chat WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= isset($basePath) ? $basePath : '' ?>script.js"></script>
</body>
</html>

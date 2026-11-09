<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) && $pageTitle ? e($pageTitle) . ' | ' : '' ?><?= e(getSetting('site_name') ?: SITE_NAME) ?></title>
    <meta name="description" content="<?= isset($pageDescription) ? e($pageDescription) : e(getSetting('meta_description')) ?>">
    <link rel="icon" type="image/jpeg" href="<?= e(logoUrl()) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <style>
        .hero-section {
            background-image: linear-gradient(180deg, rgba(3, 15, 38, .6) 0%, rgba(3, 15, 38, .45) 50%, rgba(3, 15, 38, .8) 100%), url('<?= e(heroBgUrl()) ?>');
        }

        <?php $pageHeaderBg = getSetting('page_header_bg_image');
        if ($pageHeaderBg): ?>.page-header {
            background-image: linear-gradient(135deg, rgba(6, 24, 56, .85), rgba(11, 37, 69, .75)), url('<?= e(uploadUrl($pageHeaderBg)) ?>');
        }

        <?php else: ?>.page-header {
            background-image: linear-gradient(135deg, rgba(6, 24, 56, .85), rgba(11, 37, 69, .75)), url('<?= e(heroBgUrl()) ?>');
        }

        <?php endif; ?>
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/">
                <img src="<?= e(logoUrl()) ?>" alt="<?= e(getSetting('site_name') ?: 'DNA Vacation') ?>">
                <div class="brand-text d-none d-sm-flex">
                    <span class="brand-name"><?= e(getSetting('site_name') ?: 'DNA Vacation') ?></span>
                    <span class="brand-tag"><?= e(getSetting('brand_tagline') ?: "It's More Than Fun") ?></span>

                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto me-3 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/tours.php">Tour</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/cars.php">Rent Car</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/hotel.php">Hotel</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/testimonials.php">Testimoni</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/about.php">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/contact.php">Kontak</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <a href="<?= BASE_URL ?>/custom-trip.php" class="btn btn-outline-primary-custom btn-sm">
                        <i class="bi bi-pencil-square"></i> Custom Trip
                    </a>
                    <?php if (isUserLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="btn btn-user-account dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="user-avatar-sm"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></span>
                                <span class="d-none d-md-inline ms-1"><?= e(mb_strimwidth($_SESSION['user_name'] ?? '', 0, 6, '..')) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text small text-muted"><?= e($_SESSION['user_email'] ?? '') ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if (isLoggedIn()): ?>
                                <li><a class="dropdown-item" href="<?= ADMIN_URL ?>/"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/account.php"><i class="bi bi-person me-2"></i>Akun Saya</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/order-history.php"><i class="bi bi-clock-history me-2"></i>Riwayat Pemesanan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/user-logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/user-login.php" class="btn btn-user-login btn-sm">
                            <i class="bi bi-person-circle"></i> <span class="d-none d-md-inline">Login</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
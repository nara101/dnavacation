<?php
$pageTitle = 'Riwayat Pemesanan';
require_once 'includes/config.php';

if (!isUserLoggedIn()) {
    $_SESSION['login_redirect'] = BASE_URL . '/order-history.php';
    setFlash('warning', 'Silakan login terlebih dahulu.');
    header('Location: ' . BASE_URL . '/user-login.php');
    exit;
}

$user = getLoggedInUser();

$tourBookings = $pdo->prepare("SELECT tb.*, t.title as tour_title FROM tour_bookings tb LEFT JOIN tours t ON tb.tour_id = t.id WHERE tb.customer_email COLLATE utf8mb4_general_ci = ? ORDER BY tb.created_at DESC");
$tourBookings->execute([$user['email']]);
$tourBookings = $tourBookings->fetchAll();

$carBookings = $pdo->prepare("SELECT cb.*, c.name as car_name FROM car_bookings cb LEFT JOIN cars c ON cb.car_id = c.id WHERE cb.customer_whatsapp COLLATE utf8mb4_general_ci = ? ORDER BY cb.created_at DESC");
$carBookings->execute([$user['phone']]);
$carBookings = $carBookings->fetchAll();

$customTrips = $pdo->prepare("SELECT * FROM custom_trip_requests WHERE customer_email COLLATE utf8mb4_general_ci = ? ORDER BY created_at DESC");
$customTrips->execute([$user['email']]);
$customTrips = $customTrips->fetchAll();

$activeTab = $_GET['tab'] ?? 'tour';

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Riwayat Pemesanan</h1>
        <p>Semua riwayat booking tour, rental mobil, dan custom trip Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'tour' ? 'active' : '' ?>" href="?tab=tour">
                    <i class="bi bi-map"></i> Booking Tour
                    <?php if ($tourBookings): ?><span class="badge bg-primary ms-1"><?= count($tourBookings) ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'car' ? 'active' : '' ?>" href="?tab=car">
                    <i class="bi bi-car-front"></i> Rental Mobil
                    <?php if ($carBookings): ?><span class="badge bg-primary ms-1"><?= count($carBookings) ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'custom' ? 'active' : '' ?>" href="?tab=custom">
                    <i class="bi bi-compass"></i> Custom Trip
                    <?php if ($customTrips): ?><span class="badge bg-primary ms-1"><?= count($customTrips) ?></span><?php endif; ?>
                </a>
            </li>
        </ul>

        <?php if ($activeTab === 'tour'): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($tourBookings)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-journal-x" style="font-size:3rem"></i>
                        <p class="mt-2 mb-3">Belum ada booking tour.</p>
                        <a href="<?= BASE_URL ?>/tours.php" class="btn btn-primary-custom btn-sm"><i class="bi bi-search"></i> Lihat Paket Tour</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Kode Booking</th>
                                    <th class="small">Paket Tour</th>
                                    <th class="small">Tanggal</th>
                                    <th class="small">Peserta</th>
                                    <th class="small">Total</th>
                                    <th class="small">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tourBookings as $b): ?>
                                <tr>
                                    <td><code class="small"><?= e($b['booking_code']) ?></code></td>
                                    <td class="small"><?= e($b['tour_title'] ?? '-') ?></td>
                                    <td class="small"><?= $b['departure_date'] ? date('d M Y', strtotime($b['departure_date'])) : '-' ?></td>
                                    <td class="small"><?= $b['num_persons'] ?> orang</td>
                                    <td class="small fw-semibold"><?= formatRupiah($b['total_price']) ?></td>
                                    <td>
                                        <?php
                                        $statusMap = ['baru'=>'warning','diproses'=>'info','menunggu_pembayaran'=>'secondary','terkonfirmasi'=>'success','selesai'=>'success','dibatalkan'=>'danger'];
                                        $statusLabel = ['baru'=>'Baru','diproses'=>'Diproses','menunggu_pembayaran'=>'Menunggu Bayar','terkonfirmasi'=>'Terkonfirmasi','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
                                        ?>
                                        <span class="badge bg-<?= $statusMap[$b['status']] ?? 'secondary' ?>"><?= $statusLabel[$b['status']] ?? ucfirst($b['status']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($activeTab === 'car'): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($carBookings)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-car-front" style="font-size:3rem"></i>
                        <p class="mt-2 mb-3">Belum ada booking rental mobil.</p>
                        <a href="<?= BASE_URL ?>/cars.php" class="btn btn-primary-custom btn-sm"><i class="bi bi-search"></i> Lihat Mobil</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Kode Booking</th>
                                    <th class="small">Mobil</th>
                                    <th class="small">Tanggal Sewa</th>
                                    <th class="small">Sopir</th>
                                    <th class="small">Total</th>
                                    <th class="small">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carBookings as $b): ?>
                                <tr>
                                    <td><code class="small"><?= e($b['booking_code']) ?></code></td>
                                    <td class="small"><?= e($b['car_name'] ?? '-') ?></td>
                                    <td class="small"><?= date('d M', strtotime($b['start_date'])) ?> - <?= date('d M Y', strtotime($b['end_date'])) ?></td>
                                    <td class="small"><?= $b['with_driver'] ? 'Dengan Sopir' : 'Lepas Kunci' ?></td>
                                    <td class="small fw-semibold"><?= formatRupiah($b['total_price']) ?></td>
                                    <td>
                                        <?php
                                        $statusMap = ['baru'=>'warning','diproses'=>'info','menunggu_pembayaran'=>'secondary','terkonfirmasi'=>'success','selesai'=>'success','dibatalkan'=>'danger'];
                                        $statusLabel = ['baru'=>'Baru','diproses'=>'Diproses','menunggu_pembayaran'=>'Menunggu Bayar','terkonfirmasi'=>'Terkonfirmasi','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
                                        ?>
                                        <span class="badge bg-<?= $statusMap[$b['status']] ?? 'secondary' ?>"><?= $statusLabel[$b['status']] ?? ucfirst($b['status']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($activeTab === 'custom'): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($customTrips)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-compass" style="font-size:3rem"></i>
                        <p class="mt-2 mb-3">Belum ada request custom trip.</p>
                        <a href="<?= BASE_URL ?>/custom-trip.php" class="btn btn-primary-custom btn-sm"><i class="bi bi-pencil-square"></i> Buat Custom Trip</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Tanggal Request</th>
                                    <th class="small">Destinasi</th>
                                    <th class="small">Tanggal Trip</th>
                                    <th class="small">Peserta</th>
                                    <th class="small">Budget</th>
                                    <th class="small">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $ctStatuses = ['baru'=>'warning','diproses'=>'info','penawaran_terkirim'=>'primary','terkonfirmasi'=>'success','selesai'=>'success'];
                                $ctLabels = ['baru'=>'Baru','diproses'=>'Diproses','penawaran_terkirim'=>'Penawaran Terkirim','terkonfirmasi'=>'Terkonfirmasi','selesai'=>'Selesai'];
                                foreach ($customTrips as $ct): ?>
                                <tr>
                                    <td class="small"><?= date('d M Y', strtotime($ct['created_at'])) ?></td>
                                    <td class="small"><?= e($ct['destination'] ?: ($ct['destination_other'] ?? '-')) ?></td>
                                    <td class="small">
                                        <?php if (!empty($ct['date_start']) && !empty($ct['date_end'])): ?>
                                            <?= date('d M', strtotime($ct['date_start'])) ?> - <?= date('d M Y', strtotime($ct['date_end'])) ?>
                                        <?php else: ?>
                                            <?= e($ct['preferred_dates'] ?? '-') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small"><?= $ct['num_persons'] ?> orang</td>
                                    <td class="small"><?= e($ct['budget_range'] ?: '-') ?></td>
                                    <td><span class="badge bg-<?= $ctStatuses[$ct['status']] ?? 'secondary' ?>"><?= $ctLabels[$ct['status']] ?? ucfirst($ct['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

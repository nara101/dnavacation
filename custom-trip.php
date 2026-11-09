<?php
$pageTitle = 'Custom Trip';
require_once 'includes/config.php';

$destinations = $pdo->query("SELECT id, name FROM destinations ORDER BY is_popular DESC, name ASC")->fetchAll();
$tripTypes    = $pdo->query("SELECT name FROM tour_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_COLUMN);

$success = false;
$errors = [];
$waMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_custom'])) {
    if (!isUserLoggedIn()) {
        $_SESSION['login_redirect'] = BASE_URL . '/custom-trip.php';
        setFlash('warning', 'Silakan login terlebih dahulu untuk mengirim request custom trip.');
        header('Location: ' . BASE_URL . '/user-login.php');
        exit;
    }

    $name = trim($_POST['customer_name'] ?? '');
    $wa = trim($_POST['customer_whatsapp'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $destId = !empty($_POST['destination_id']) ? (int)$_POST['destination_id'] : null;
    $destOther = trim($_POST['destination_other'] ?? '');
    $destinationLabel = '';
    if ($destId) {
        $d = $pdo->prepare("SELECT name FROM destinations WHERE id=?");
        $d->execute([$destId]);
        $destinationLabel = ($d->fetch()['name'] ?? '');
    }
    if ($destOther) {
        $destinationLabel = $destOther;
    }

    $tripType = trim($_POST['trip_type'] ?? '');
    $persons = max(1, (int)($_POST['num_persons'] ?? 1));
    $budget = trim($_POST['budget_range'] ?? '');
    $dateStart = $_POST['date_start'] ?? '';
    $dateEnd = $_POST['date_end'] ?? '';

    $needsHotel = isset($_POST['needs_hotel']) && $_POST['needs_hotel'] === '1' ? 1 : 0;
    $hotelChoice = trim($_POST['hotel_choice'] ?? '');
    $hotelOther = trim($_POST['hotel_other'] ?? '');
    $finalHotel = $hotelChoice === 'Lainnya' ? $hotelOther : $hotelChoice;

    $attractionsArr = isset($_POST['attractions']) && is_array($_POST['attractions']) ? array_map('trim', $_POST['attractions']) : [];
    $attractionOther = trim($_POST['attraction_other'] ?? '');
    $attractionsJson = json_encode($attractionsArr, JSON_UNESCAPED_UNICODE);

    $vacationType = trim($_POST['vacation_type'] ?? '');
    $hasTicket = trim($_POST['has_ticket'] ?? '');
    $arrivalTime = trim($_POST['arrival_time'] ?? '');
    $pickupLocation = trim($_POST['pickup_location'] ?? '');
    $requests = trim($_POST['special_requests'] ?? '');

    // Validation
    if (!$name) $errors[] = 'Nama wajib diisi.';
    if (!$wa) $errors[] = 'Nomor WhatsApp wajib diisi.';
    if (!$email) $errors[] = 'Email wajib diisi.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (!$destinationLabel) $errors[] = 'Destinasi wajib diisi.';
    if ($destId === null && !$destOther) $errors[] = 'Jika pilih "Lainnya", nama kota/negara wajib diisi.';
    if (!$dateStart || !$dateEnd) $errors[] = 'Tanggal keberangkatan & kepulangan wajib diisi.';
    elseif (strtotime($dateEnd) < strtotime($dateStart)) $errors[] = 'Tanggal kepulangan tidak boleh sebelum keberangkatan.';
    if ($needsHotel && $hotelChoice === 'Lainnya' && !$hotelOther) $errors[] = 'Jika pilih hotel "Lainnya", nama hotel wajib diisi.';
    if (!$needsHotel && in_array('Lainnya', $attractionsArr) && !$attractionOther) {
        $errors[] = 'Jika pilih objek wisata "Lainnya", nama objek wisata wajib diisi.';
    }

    if (empty($errors)) {
        $preferredDates = $dateStart . ' s/d ' . $dateEnd;

        $ins = $pdo->prepare("INSERT INTO custom_trip_requests
            (customer_name, customer_whatsapp, customer_email, destination, destination_other,
             trip_type, num_persons, budget_range, preferred_dates, date_start, date_end,
             needs_hotel, hotel_choice, hotel_other, attractions, attraction_other,
             vacation_type, has_ticket, arrival_time, pickup_location, special_requests)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $ins->execute([
            $name, $wa, $email, $destinationLabel, $destOther,
            $tripType, $persons, $budget, $preferredDates, $dateStart ?: null, $dateEnd ?: null,
            $needsHotel, $hotelChoice, $hotelOther, $attractionsJson, $attractionOther,
            $vacationType, $hasTicket, $arrivalTime, $pickupLocation, $requests,
        ]);

        // Try to send email to admin (best-effort — silently fails if SMTP not configured)
        $adminEmail = getSetting('email');
        if ($adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $subject = "Custom Trip Request Baru - {$name}";
            $body = "Ada request custom trip baru dari website DNA Vacation:\n\n";
            $body .= "Nama: {$name}\n";
            $body .= "Email: {$email}\n";
            $body .= "WhatsApp: {$wa}\n";
            $body .= "\n--- DETAIL TRIP ---\n";
            $body .= "Destinasi: {$destinationLabel}\n";
            $body .= "Tanggal: {$preferredDates}\n";
            $body .= "Jenis Trip: {$tripType}\n";
            $body .= "Jumlah Peserta: {$persons}\n";
            $body .= "Budget: {$budget}\n";
            $body .= "Jenis Wisata: {$vacationType}\n";
            $body .= "\n--- AKOMODASI ---\n";
            $body .= "Butuh hotel: " . ($needsHotel ? 'Ya' : 'Tidak') . "\n";
            if ($needsHotel) $body .= "Pilihan hotel: " . ($finalHotel ?: '-') . "\n";
            else {
                $body .= "Objek wisata yang diinginkan: " . implode(', ', $attractionsArr) . "\n";
                if ($attractionOther) $body .= "Objek wisata lainnya: {$attractionOther}\n";
            }
            $body .= "\n--- KEDATANGAN ---\n";
            $body .= "Sudah punya tiket: {$hasTicket}\n";
            $body .= "Estimasi jam kedatangan: {$arrivalTime}\n";
            $body .= "Lokasi penjemputan: {$pickupLocation}\n";
            $body .= "\n--- CATATAN TAMBAHAN ---\n";
            $body .= ($requests ?: '-');
            $headers = "From: no-reply@dnavacation.com\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=UTF-8";
            @mail($adminEmail, $subject, $body, $headers);
        }

        // Build WhatsApp confirmation message
        $waMsg = "Halo DNA Vacation, saya baru submit request custom trip:\n";
        $waMsg .= "Nama: {$name}\n";
        $waMsg .= "Email: {$email}\n";
        $waMsg .= "Destinasi: {$destinationLabel}\n";
        $waMsg .= "Tanggal: {$preferredDates}\n";
        $waMsg .= "Peserta: {$persons}\n";
        $waMsg .= "Budget: {$budget}\n";
        $waMsg .= "Jenis Wisata: {$vacationType}\n";
        if ($needsHotel) $waMsg .= "Hotel: " . ($finalHotel ?: '-') . "\n";
        else if ($attractionsArr) $waMsg .= "Objek wisata: " . implode(', ', $attractionsArr) . "\n";
        $waMsg .= "Mohon dibantu penawarannya, terima kasih!";

        $success = true;
    }
}

require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <h1>Buat Custom Trip</h1>
        <p>Desain liburan impian Anda — sesuai budget, sesuai keinginan</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong><i class="bi bi-check-circle-fill"></i> Request terkirim!</strong>
            Data lengkap sudah kami terima di email <strong><?= e(getSetting('email') ?: 'admin') ?></strong>.
            Tim kami akan segera membuat penawaran khusus untuk Anda dalam 1x24 jam.
            <a href="<?= waLink($waMsg) ?>" target="_blank" class="btn btn-wa btn-sm ms-2 mt-2 mt-md-0"><i class="bi bi-whatsapp"></i> Lanjut Chat WA</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($errors): ?>
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> Ada yang perlu diperbaiki:</strong>
            <ul class="mb-0 mt-2"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <span class="badge bg-light text-primary-custom border mb-2" style="padding:.5rem 1rem;font-weight:600"><i class="bi bi-magic"></i> Custom Made For You</span>
                <h3 class="fw-bold mb-3">Buat Liburan Sesuai Keinginan Anda</h3>
                <p class="text-muted small">Isi form berikut selengkap mungkin agar kami bisa memberikan penawaran yang paling sesuai untuk Anda.</p>

                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Itinerary 100% disesuaikan</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Fleksibel tanggal keberangkatan</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Budget bisa disesuaikan</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Cocok untuk keluarga, group, honeymoon</span></div>
                <div class="d-flex gap-2 mb-4"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Konsultasi gratis via WhatsApp</span></div>

                <div class="p-3 rounded-3" style="background:var(--primary-lighter);border:1px solid var(--primary-light)">
                    <p class="mb-2 small"><i class="bi bi-lightbulb-fill text-warning"></i> <strong>Tips:</strong></p>
                    <p class="mb-0 small text-muted">Semakin detail info yang Anda berikan, semakin akurat penawaran yang bisa kami buat.</p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="booking-sidebar" style="position:static">
                    <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary-custom"></i> Form Request Custom Trip</h5>
                    <?php $bu = isUserLoggedIn() ? getLoggedInUser() : null; ?>
                    <form method="POST" id="customTripForm" onsubmit="return checkLoginBeforeSubmit(this)">
                        <input type="hidden" name="submit_custom" value="1">

                        <!-- Data pribadi -->
                        <h6 class="fw-bold text-primary-custom mt-2 mb-3 pb-2 border-bottom">1. Data Diri</h6>
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" required value="<?= e($_POST['customer_name'] ?? $bu['full_name'] ?? $bu['username'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required value="<?= e($_POST['customer_whatsapp'] ?? $bu['phone'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control" required value="<?= e($_POST['customer_email'] ?? $bu['email'] ?? '') ?>" placeholder="nama@email.com">
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Semua detail request akan dikirim ke email ini.</small>
                        </div>

                        <!-- Trip detail -->
                        <h6 class="fw-bold text-primary-custom mt-4 mb-3 pb-2 border-bottom">2. Detail Trip</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Destinasi Utama <span class="text-danger">*</span></label>
                            <select name="destination_id" id="destinationSelect" class="form-select" required onchange="handleDestChange()">
                                <option value="">-- Pilih Destinasi --</option>
                                <?php foreach ($destinations as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= (($_POST['destination_id'] ?? '') == $d['id']) ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                                <option value="other">Lainnya (isi manual)</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="destinationOtherWrap">
                            <label class="form-label small fw-semibold">Nama Kota/Negara <span class="text-danger">*</span></label>
                            <input type="text" name="destination_other" id="destinationOtherInput" class="form-control" placeholder="Contoh: Singapore, Bandung, Osaka Jepang" value="<?= e($_POST['destination_other'] ?? '') ?>">
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Tanggal Keberangkatan <span class="text-danger">*</span></label>
                                <input type="date" name="date_start" id="dateStart" class="form-control" min="<?= date('Y-m-d') ?>" required value="<?= e($_POST['date_start'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Tanggal Kepulangan <span class="text-danger">*</span></label>
                                <input type="date" name="date_end" id="dateEnd" class="form-control" min="<?= date('Y-m-d') ?>" required value="<?= e($_POST['date_end'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3 small text-muted" id="durationInfo" style="margin-top:-.5rem">
                            <i class="bi bi-calendar-check"></i> Pilih dari tanggal - ke tanggal
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Jumlah Peserta <span class="text-danger">*</span></label>
                                <input type="number" name="num_persons" class="form-control" value="<?= e($_POST['num_persons'] ?? '2') ?>" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Jenis Trip</label>
                                <select name="trip_type" class="form-select">
                                    <option value="">Pilih jenis</option>
                                    <?php foreach ($tripTypes as $tt): ?>
                                    <option value="<?= e($tt) ?>" <?= ($_POST['trip_type'] ?? '') === $tt ? 'selected' : '' ?>><?= e($tt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Jenis paket wisata dikelola admin di menu <em>Jenis Paket</em>.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Budget per Orang</label>
                            <select name="budget_range" class="form-select">
                                <option value="">Pilih range budget</option>
                                <option>&lt; Rp 2.000.000</option>
                                <option>Rp 2.000.000 - Rp 5.000.000</option>
                                <option>Rp 5.000.000 - Rp 10.000.000</option>
                                <option>&gt; Rp 10.000.000</option>
                                <option>Fleksibel</option>
                            </select>
                        </div>

                        <!-- Akomodasi -->
                        <h6 class="fw-bold text-primary-custom mt-4 mb-3 pb-2 border-bottom">3. Kebutuhan Akomodasi</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kebutuhan tour termasuk akomodasi/hotel? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 flex-wrap mt-2">
                                <label class="option-card">
                                    <input type="radio" name="needs_hotel" value="1" onchange="handleHotelToggle()" required>
                                    <span><i class="bi bi-building-check"></i> Ya, tour dan hotel</span>
                                </label>
                                <label class="option-card">
                                    <input type="radio" name="needs_hotel" value="0" onchange="handleHotelToggle()" required>
                                    <span><i class="bi bi-compass"></i> Tidak, hanya tour saja</span>
                                </label>
                            </div>
                        </div>

                        <!-- Hotel section (shown only when needs_hotel=1) -->
                        <div class="d-none" id="hotelSection">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Dimana Anda ingin menginap?</label>
                                <input type="search" id="hotelSearch" class="form-control mb-2" placeholder="Ketik nama hotel untuk mencari..." oninput="filterHotels()">
                                <div id="hotelListWrap" class="hotel-list">
                                    <div class="text-muted small text-center py-4"><i class="bi bi-info-circle"></i> Pilih destinasi dulu untuk melihat pilihan hotel</div>
                                </div>
                                <div class="mt-2 d-none" id="hotelOtherWrap">
                                    <label class="form-label small fw-semibold">Nama Hotel <span class="text-danger">*</span></label>
                                    <input type="text" name="hotel_other" id="hotelOtherInput" class="form-control" placeholder="Ketik nama hotel yang Anda inginkan">
                                </div>
                                <input type="hidden" name="hotel_choice" id="hotelChoiceInput">
                            </div>
                        </div>

                        <!-- Attractions section (always visible after akomodasi choice) -->
                        <h6 class="fw-bold text-primary-custom mt-4 mb-3 pb-2 border-bottom">4. Objek Wisata</h6>
                        <div id="attractionsSection">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Objek wisata yang ingin dikunjungi <span class="text-muted">(bisa pilih lebih dari satu)</span></label>
                                <div id="attractionsListWrap" class="attractions-list">
                                    <div class="text-muted small text-center py-4"><i class="bi bi-info-circle"></i> Pilih destinasi dulu untuk melihat objek wisata</div>
                                </div>
                                <div class="mt-2 d-none" id="attractionOtherWrap">
                                    <label class="form-label small fw-semibold">Objek Wisata Lainnya <span class="text-danger">*</span></label>
                                    <textarea name="attraction_other" id="attractionOtherInput" class="form-control" rows="2" placeholder="Tulis nama objek wisata yang ingin Anda kunjungi (pisahkan dengan koma jika lebih dari satu)"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Vacation type -->
                        <h6 class="fw-bold text-primary-custom mt-4 mb-3 pb-2 border-bottom">5. Jenis Wisata</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Mau wisata seperti apa?</label>
                            <select name="vacation_type" class="form-select">
                                <option value="">Pilih jenis wisata</option>
                                <option value="Menunggu saran DNA Vacation">Menunggu saran DNA Vacation</option>
                                <option value="Family Gathering">Family Gathering</option>
                                <option value="Honeymoon / Romantic Trip">Honeymoon / Romantic Trip</option>
                                <option value="Adventure & Outdoor">Adventure & Outdoor</option>
                                <option value="Cultural & Heritage">Cultural & Heritage</option>
                                <option value="Beach & Island Hopping">Beach & Island Hopping</option>
                                <option value="Diving & Snorkeling">Diving & Snorkeling</option>
                                <option value="Kuliner Tour">Kuliner Tour</option>
                                <option value="Corporate / Team Building">Corporate / Team Building</option>
                                <option value="Photography Trip">Photography Trip</option>
                                <option value="Wellness / Retreat">Wellness / Retreat</option>
                                <option value="Backpacker Style">Backpacker Style</option>
                                <option value="Luxury Trip">Luxury Trip</option>
                            </select>
                        </div>

                        <!-- Arrival info -->
                        <h6 class="fw-bold text-primary-custom mt-4 mb-3 pb-2 border-bottom">6. Informasi Kedatangan</h6>
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Sudah punya tiket?</label>
                                <select name="has_ticket" class="form-select">
                                    <option value="">Pilih status</option>
                                    <option>Sudah, tiket pesawat</option>
                                    <option>Sudah, tiket kapal</option>
                                    <option>Sudah, tiket kereta</option>
                                    <option>Belum, mohon dibantu</option>
                                    <option>Belum yakin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Estimasi Jam Kedatangan</label>
                                <input type="time" name="arrival_time" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Lokasi Penjemputan</label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="Contoh: Bandara Sultan Hasanuddin, Hotel Aston, Stasiun Kereta">
                            <small class="text-muted">Kosongkan jika belum tahu — tim kami akan koordinasi via WhatsApp</small>
                        </div>

                        <!-- Additional -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Request Khusus / Catatan Tambahan</label>
                            <textarea name="special_requests" class="form-control" rows="3" placeholder="Preferensi makanan (halal/vegetarian), kebutuhan aksesibilitas, aktivitas khusus, dll"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg mb-2"><i class="bi bi-send"></i> Kirim Request Custom Trip</button>
                        <a href="<?= waLink('Halo DNA Vacation, saya ingin membuat custom trip.') ?>" target="_blank" class="btn btn-wa w-100"><i class="bi bi-whatsapp"></i> Konsultasi Langsung via WA</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.option-card {
    flex: 1 1 200px;
    display: block;
    cursor: pointer;
    padding: 1rem;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm);
    transition: var(--transition);
    text-align: center;
    background: #fff;
}
.option-card:hover { border-color: var(--gold); background: var(--gold-lighter); }
.option-card input { display: none; }
.option-card input:checked ~ span { color: var(--primary); font-weight: 700; }
.option-card:has(input:checked) {
    border-color: var(--primary);
    background: var(--primary-lighter);
    box-shadow: 0 0 0 3px rgba(11,37,69,.1);
}
.option-card i { display: block; font-size: 1.5rem; color: var(--primary); margin-bottom: .35rem; }

.hotel-list, .attractions-list {
    max-height: 320px;
    overflow-y: auto;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    padding: .75rem;
    background: #fff;
}
.hotel-group-heading {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 700;
    color: var(--gold-dark);
    padding: .5rem 0 .35rem;
    margin-top: .25rem;
    border-top: 1px dashed var(--gray-200);
}
.hotel-group-heading:first-child { border-top: none; margin-top: 0; }
.hotel-item, .attraction-item {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .5rem .6rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background .15s ease;
    font-size: .88rem;
}
.hotel-item:hover, .attraction-item:hover { background: var(--gray-50); }
.hotel-item input, .attraction-item input {
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    accent-color: var(--primary);
}
.hotel-item.selected, .attraction-item.selected {
    background: var(--primary-lighter);
    color: var(--primary);
    font-weight: 600;
}
.hotel-item .stars { color: var(--gold); font-size: .75rem; margin-left: auto; }
.hotel-item.special, .attraction-item.special {
    background: var(--gold-lighter);
    color: var(--gold-dark);
    font-weight: 600;
    margin-bottom: .5rem;
}
.hotel-item.special.selected, .attraction-item.special.selected {
    background: var(--gold);
    color: #fff;
}
</style>

<script>
const destSelect = document.getElementById('destinationSelect');
const destOtherWrap = document.getElementById('destinationOtherWrap');
const destOtherInput = document.getElementById('destinationOtherInput');
const hotelSection = document.getElementById('hotelSection');
const attractionsSection = document.getElementById('attractionsSection');
const hotelListWrap = document.getElementById('hotelListWrap');
const attractionsListWrap = document.getElementById('attractionsListWrap');
const hotelChoiceInput = document.getElementById('hotelChoiceInput');
const hotelOtherWrap = document.getElementById('hotelOtherWrap');
const hotelOtherInput = document.getElementById('hotelOtherInput');
const attractionOtherWrap = document.getElementById('attractionOtherWrap');
const attractionOtherInput = document.getElementById('attractionOtherInput');
const dateStart = document.getElementById('dateStart');
const dateEnd = document.getElementById('dateEnd');
const durationInfo = document.getElementById('durationInfo');

let currentDestName = '';

function handleDestChange() {
    const val = destSelect.value;
    if (val === 'other') {
        destOtherWrap.classList.remove('d-none');
        destOtherInput.setAttribute('required', 'required');
        hotelListWrap.innerHTML = '<div class="text-muted small text-center py-4"><i class="bi bi-info-circle"></i> Untuk destinasi custom, silakan tulis nama hotel di kolom "Lainnya"</div>';
        attractionsListWrap.innerHTML = '<div class="text-muted small text-center py-4"><i class="bi bi-info-circle"></i> Untuk destinasi custom, silakan tulis objek wisata di kolom "Lainnya"</div>';
        // still show Rekomendasi + Lainnya options
        renderHotels({ hotels: {}, attractions: [] });
        renderAttractions({ hotels: {}, attractions: [] });
    } else {
        destOtherWrap.classList.add('d-none');
        destOtherInput.removeAttribute('required');
        destOtherInput.value = '';
        if (!val) {
            hotelListWrap.innerHTML = '<div class="text-muted small text-center py-4"><i class="bi bi-info-circle"></i> Pilih destinasi dulu untuk melihat pilihan hotel</div>';
            attractionsListWrap.innerHTML = '<div class="text-muted small text-center py-4"><i class="bi bi-info-circle"></i> Pilih destinasi dulu untuk melihat objek wisata</div>';
            return;
        }
        fetch(`ajax/destination-info.php?destination_id=${val}`)
            .then(r => r.json())
            .then(data => {
                currentDestName = data.destination_name;
                renderHotels(data);
                renderAttractions(data);
            });
    }
}

function starRating(n) {
    return '★'.repeat(n) + '☆'.repeat(5 - n);
}

function renderHotels(data) {
    let html = '';
    // Rekomendasi option (always first)
    html += `<label class="hotel-item special">
        <input type="radio" name="hotel_radio" value="Rekomendasi DNA Vacation" onchange="selectHotel(this)">
        <span><i class="bi bi-award-fill"></i> Rekomendasi DNA Vacation</span>
    </label>`;

    const stars = Object.keys(data.hotels || {}).map(Number).sort((a,b) => a-b);
    stars.forEach(s => {
        html += `<div class="hotel-group-heading"><i class="bi bi-star-fill"></i> Bintang ${s}</div>`;
        data.hotels[s].forEach(name => {
            html += `<label class="hotel-item" data-name="${name.toLowerCase()}">
                <input type="radio" name="hotel_radio" value="${escapeHtml(name)}" onchange="selectHotel(this)">
                <span>${escapeHtml(name)}</span>
                <span class="stars">${starRating(s)}</span>
            </label>`;
        });
    });

    // Lainnya option (always last)
    html += `<label class="hotel-item special mt-2">
        <input type="radio" name="hotel_radio" value="Lainnya" onchange="selectHotel(this)">
        <span><i class="bi bi-pencil-square"></i> Lainnya (tulis manual)</span>
    </label>`;

    hotelListWrap.innerHTML = html;
}

function renderAttractions(data) {
    let html = '';
    html += `<label class="attraction-item special">
        <input type="checkbox" name="attractions[]" value="Rekomendasi DNA Vacation" onchange="handleAttractionCheck(this)">
        <span><i class="bi bi-award-fill"></i> Rekomendasi DNA Vacation (biarkan admin yang atur)</span>
    </label>`;

    (data.attractions || []).forEach(name => {
        html += `<label class="attraction-item">
            <input type="checkbox" name="attractions[]" value="${escapeHtml(name)}" onchange="handleAttractionCheck(this)">
            <span>${escapeHtml(name)}</span>
        </label>`;
    });

    html += `<label class="attraction-item special mt-2">
        <input type="checkbox" name="attractions[]" value="Lainnya" onchange="handleAttractionCheck(this)">
        <span><i class="bi bi-pencil-square"></i> Lainnya (tulis manual)</span>
    </label>`;

    attractionsListWrap.innerHTML = html;
}

function selectHotel(input) {
    hotelChoiceInput.value = input.value;
    // toggle highlight
    document.querySelectorAll('.hotel-item').forEach(el => el.classList.remove('selected'));
    input.closest('.hotel-item').classList.add('selected');
    // show/hide "other" input
    if (input.value === 'Lainnya') {
        hotelOtherWrap.classList.remove('d-none');
        hotelOtherInput.setAttribute('required', 'required');
    } else {
        hotelOtherWrap.classList.add('d-none');
        hotelOtherInput.removeAttribute('required');
    }
}

function handleAttractionCheck(input) {
    input.closest('.attraction-item').classList.toggle('selected', input.checked);
    // Handle "Lainnya"
    if (input.value === 'Lainnya') {
        if (input.checked) {
            attractionOtherWrap.classList.remove('d-none');
            attractionOtherInput.setAttribute('required', 'required');
        } else {
            attractionOtherWrap.classList.add('d-none');
            attractionOtherInput.removeAttribute('required');
        }
    }
}

function filterHotels() {
    const q = document.getElementById('hotelSearch').value.toLowerCase().trim();
    document.querySelectorAll('#hotelListWrap .hotel-item').forEach(item => {
        if (item.classList.contains('special')) { item.style.display = ''; return; }
        const name = item.dataset.name || '';
        item.style.display = (!q || name.includes(q)) ? '' : 'none';
    });
    // Hide empty star groups
    document.querySelectorAll('.hotel-group-heading').forEach(heading => {
        let next = heading.nextElementSibling;
        let hasVisible = false;
        while (next && !next.classList.contains('hotel-group-heading')) {
            if (next.classList.contains('hotel-item') && !next.classList.contains('special') && next.style.display !== 'none') {
                hasVisible = true;
                break;
            }
            next = next.nextElementSibling;
        }
        heading.style.display = hasVisible ? '' : 'none';
    });
}

function handleHotelToggle() {
    const yes = document.querySelector('input[name="needs_hotel"]:checked')?.value === '1';
    // Attractions section always visible
    attractionsSection.classList.remove('d-none');
    if (yes) {
        hotelSection.classList.remove('d-none');
    } else {
        hotelSection.classList.add('d-none');
        // clear hotel selection when switching to "no hotel"
        document.querySelectorAll('input[name="hotel_radio"]').forEach(r => { r.checked = false; r.closest('.hotel-item')?.classList.remove('selected'); });
        hotelChoiceInput.value = '';
        hotelOtherWrap.classList.add('d-none');
        hotelOtherInput.removeAttribute('required');
    }
}

function updateDuration() {
    if (dateStart.value && dateEnd.value) {
        const start = new Date(dateStart.value);
        const end = new Date(dateEnd.value);
        const days = Math.max(1, Math.round((end - start) / 86400000) + 1);
        durationInfo.innerHTML = `<i class="bi bi-calendar-check text-success"></i> Total durasi: <strong>${days} hari</strong>`;
    }
}

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

dateStart.addEventListener('change', () => {
    dateEnd.min = dateStart.value;
    if (dateEnd.value && dateEnd.value < dateStart.value) dateEnd.value = dateStart.value;
    updateDuration();
});
dateEnd.addEventListener('change', updateDuration);

// If form was submitted with a destination, trigger loading
if (destSelect.value && destSelect.value !== 'other') handleDestChange();
else if (destSelect.value === 'other') handleDestChange();
</script>

<?php require_once 'includes/footer.php'; ?>

<?php
$pageTitle = 'Custom Trip';
require_once 'includes/header.php';

$success = false;
$waMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_custom'])) {
    $name = trim($_POST['customer_name'] ?? '');
    $wa = trim($_POST['customer_whatsapp'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $dest = trim($_POST['destination'] ?? '');
    $type = trim($_POST['trip_type'] ?? '');
    $persons = max(1, (int)($_POST['num_persons'] ?? 1));
    $budget = trim($_POST['budget_range'] ?? '');
    $dates = trim($_POST['preferred_dates'] ?? '');
    $requests = trim($_POST['special_requests'] ?? '');
    if ($name && $wa) {
        $ins = $pdo->prepare("INSERT INTO custom_trip_requests (customer_name,customer_whatsapp,customer_email,destination,trip_type,num_persons,budget_range,preferred_dates,special_requests) VALUES (?,?,?,?,?,?,?,?,?)");
        $ins->execute([$name, $wa, $email, $dest, $type, $persons, $budget, $dates, $requests]);
        $success = true;
        $waMsg = "Halo DNA Vacation, saya ingin membuat custom trip:\nDestinasi: {$dest}\nJenis: {$type}\nPeserta: {$persons}\nBudget: {$budget}\nTanggal: {$dates}\nNama: {$name}\nRequest: " . ($requests ?: '-');
    }
}
?>
<div class="page-header">
    <div class="container">
        <h1>Buat <span class="script">Custom Trip</span></h1>
        <p>Desain liburan impian Anda — sesuai budget, sesuai keinginan</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong><i class="bi bi-check-circle-fill"></i> Request terkirim!</strong> Tim kami akan segera membuat penawaran khusus untuk Anda.
            <a href="<?= waLink($waMsg) ?>" target="_blank" class="btn btn-wa btn-sm ms-2 mt-2 mt-md-0"><i class="bi bi-whatsapp"></i> Lanjut Chat WA</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-5">
                <span class="badge bg-light text-primary-custom border mb-2" style="padding:.5rem 1rem;font-weight:600"><i class="bi bi-magic"></i> Custom Made For You</span>
                <h3 class="fw-bold mb-3">Buat Liburan Sesuai Keinginan Anda</h3>
                <p class="text-muted">Tidak menemukan paket yang cocok? Ceritakan rencana liburan Anda dan kami akan buatkan paket khusus sesuai kebutuhan, budget, dan tanggal Anda.</p>

                <h6 class="fw-bold mt-4 mb-3">Keunggulan Custom Trip:</h6>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Itinerary 100% disesuaikan dengan Anda</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Fleksibel dalam pemilihan tanggal</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Budget bisa disesuaikan</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Kombinasikan berbagai destinasi</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Konsultasi gratis via WhatsApp</span></div>
                <div class="d-flex gap-2 mb-4"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small">Cocok untuk group, family, honeymoon, corporate</span></div>

                <div class="p-3 rounded-3" style="background:var(--primary-lighter);border:1px solid var(--primary-light)">
                    <p class="mb-2 small"><i class="bi bi-lightbulb-fill text-warning"></i> <strong>Tips:</strong> Semakin detail info yang Anda berikan, semakin akurat penawaran yang bisa kami buat.</p>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="booking-sidebar" style="position:static">
                    <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary-custom"></i> Form Request Custom Trip</h5>
                    <form method="POST">
                        <input type="hidden" name="submit_custom" value="1">
                        <div class="row g-2">
                            <div class="col-md-6 mb-3"><label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label><input type="text" name="customer_name" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label small fw-semibold">Nomor WhatsApp <span class="text-danger">*</span></label><input type="tel" name="customer_whatsapp" class="form-control" placeholder="08xxxxxxxxxx" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Email (opsional)</label><input type="email" name="customer_email" class="form-control"></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Destinasi yang Diinginkan</label><input type="text" name="destination" class="form-control" placeholder="Contoh: Toraja + Bunaken, Raja Ampat, Bali + Lombok"></div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Jenis Trip</label>
                                <select name="trip_type" class="form-select">
                                    <option value="">Pilih jenis</option>
                                    <option>Private Trip</option>
                                    <option>Family Trip</option>
                                    <option>Honeymoon</option>
                                    <option>Corporate Trip</option>
                                    <option>Group Trip</option>
                                    <option>Solo Traveler</option>
                                    <option>Adventure</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label small fw-semibold">Jumlah Peserta</label><input type="number" name="num_persons" class="form-control" value="2" min="1"></div>
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
                        <div class="mb-3"><label class="form-label small fw-semibold">Tanggal yang Diinginkan</label><input type="text" name="preferred_dates" class="form-control" placeholder="Contoh: 20-25 Desember 2026, atau fleksibel"></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Request Khusus</label><textarea name="special_requests" class="form-control" rows="4" placeholder="Ceritakan keinginan liburan Anda, aktivitas yang disukai, akomodasi impian, dietary needs, dll"></textarea></div>
                        <button type="submit" class="btn btn-primary-custom w-100 mb-2"><i class="bi bi-send"></i> Kirim Request Custom Trip</button>
                        <a href="<?= waLink('Halo DNA Vacation, saya ingin membuat custom trip.') ?>" target="_blank" class="btn btn-wa w-100"><i class="bi bi-whatsapp"></i> Konsultasi Langsung via WA</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>

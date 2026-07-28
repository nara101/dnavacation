<?php
$pageTitle = 'Form Paket Tour';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tour = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$id]);
    $tour = $stmt->fetch();
    if (!$tour) { header('Location: tours.php'); exit; }
}

$destinations = $pdo->query("SELECT * FROM destinations ORDER BY name")->fetchAll();
$categories = $pdo->query("SELECT * FROM tour_categories ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);

    // Parse itinerary from lines OR JSON
    $itineraryInput = trim($_POST['itinerary']);
    $itineraryJson = '';
    if ($itineraryInput) {
        $parsed = json_decode($itineraryInput, true);
        if (is_array($parsed)) {
            $itineraryJson = json_encode($parsed, JSON_UNESCAPED_UNICODE);
        } else {
            // Parse simple format: "Day 1: Title | activity | activity"
            $lines = array_filter(array_map('trim', explode("\n", $itineraryInput)));
            $itArr = [];
            $dayNum = 1;
            foreach ($lines as $line) {
                $parts = array_map('trim', explode('|', $line));
                $header = array_shift($parts);
                if (preg_match('/^(?:Day|Hari)\s*(\d+)\s*[:\-]?\s*(.*)$/i', $header, $m)) {
                    $dayNum = (int)$m[1];
                    $title2 = trim($m[2]);
                } else {
                    $title2 = $header;
                }
                $itArr[] = ['day' => $dayNum, 'title' => $title2, 'activities' => $parts];
                $dayNum++;
            }
            $itineraryJson = json_encode($itArr, JSON_UNESCAPED_UNICODE);
        }
    }

    // Parse includes/excludes as line-per-item arrays
    $parseList = function($input) {
        $input = trim($input);
        if (!$input) return '';
        $parsed = json_decode($input, true);
        if (is_array($parsed)) return json_encode($parsed, JSON_UNESCAPED_UNICODE);
        $arr = array_values(array_filter(array_map('trim', explode("\n", $input))));
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    };

    $data = [
        'title' => $title,
        'slug' => slugify($title),
        'destination_id' => !empty($_POST['destination_id']) ? (int)$_POST['destination_id'] : null,
        'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
        'description' => trim($_POST['description']),
        'duration' => trim($_POST['duration']),
        'duration_days' => max(1, (int)$_POST['duration_days']),
        'price' => (int)str_replace(['.', ',', ' '], '', $_POST['price']),
        'price_label' => trim($_POST['price_label']) ?: 'per orang',
        'itinerary' => $itineraryJson,
        'includes' => $parseList($_POST['includes'] ?? ''),
        'excludes' => $parseList($_POST['excludes'] ?? ''),
        'meeting_point' => trim($_POST['meeting_point']),
        'terms' => trim($_POST['terms']),
        'min_persons' => max(1, (int)$_POST['min_persons']),
        'max_persons' => max(1, (int)$_POST['max_persons']),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'is_best_seller' => isset($_POST['is_best_seller']) ? 1 : 0,
        'is_promo' => isset($_POST['is_promo']) ? 1 : 0,
        'promo_price' => !empty($_POST['promo_price']) ? (int)str_replace(['.', ',', ' '], '', $_POST['promo_price']) : null,
    ];

    // Handle image
    if (!empty($_FILES['image']['name'])) {
        $img = uploadImage($_FILES['image'], 'tours');
        if ($img) $data['image'] = $img;
    }

    // Handle gallery
    if (!empty($_FILES['gallery']['name'][0])) {
        $galleryPaths = [];
        foreach ($_FILES['gallery']['name'] as $i => $name) {
            if ($name) {
                $file = [
                    'name' => $_FILES['gallery']['name'][$i],
                    'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                    'size' => $_FILES['gallery']['size'][$i],
                ];
                $gImg = uploadImage($file, 'tours');
                if ($gImg) $galleryPaths[] = $gImg;
            }
        }
        if ($galleryPaths) {
            $existing = $tour ? (json_decode($tour['gallery'] ?? '[]', true) ?: []) : [];
            $data['gallery'] = json_encode(array_merge($existing, $galleryPaths), JSON_UNESCAPED_UNICODE);
        }
    }

    if ($id && $tour) {
        $fields = [];
        $values = [];
        foreach ($data as $k => $v) {
            $fields[] = "`$k` = ?";
            $values[] = $v;
        }
        $values[] = $id;
        $pdo->prepare("UPDATE tours SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
        setFlash('success', 'Paket tour berhasil diperbarui.');
    } else {
        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $pdo->prepare("INSERT INTO tours ($fields) VALUES ($placeholders)")->execute(array_values($data));
        setFlash('success', 'Paket tour berhasil ditambahkan.');
    }
    header('Location: tours.php');
    exit;
}

// Prepare display values for the form
$itineraryDisplay = '';
if ($tour && $tour['itinerary']) {
    $itArr = json_decode($tour['itinerary'], true);
    if (is_array($itArr)) {
        $lines = [];
        foreach ($itArr as $d) {
            $line = 'Hari ' . ($d['day'] ?? '?') . ': ' . ($d['title'] ?? '');
            if (!empty($d['activities'])) {
                $line .= ' | ' . implode(' | ', $d['activities']);
            }
            $lines[] = $line;
        }
        $itineraryDisplay = implode("\n", $lines);
    } else {
        $itineraryDisplay = $tour['itinerary'];
    }
}

$listToLines = function($json) {
    if (!$json) return '';
    $arr = json_decode($json, true);
    return is_array($arr) ? implode("\n", $arr) : $json;
};
$includesDisplay = $tour ? $listToLines($tour['includes']) : '';
$excludesDisplay = $tour ? $listToLines($tour['excludes']) : '';
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-map text-primary-custom"></i> <?= $id ? 'Edit' : 'Tambah' ?> Paket Tour</h4>
        <a href="tours.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- Row 1: Basic -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nama Paket <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?= e($tour['title'] ?? '') ?>" required placeholder="Contoh: Toraja Cultural Heritage 3D2N">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Durasi (label) <span class="text-danger">*</span></label>
                    <input type="text" name="duration" class="form-control" value="<?= e($tour['duration'] ?? '') ?>" placeholder="3 Hari 2 Malam" required>
                </div>

                <!-- Row 2: Category, Destination, Days -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Destinasi</label>
                    <select name="destination_id" class="form-select">
                        <option value="">-- Pilih Destinasi --</option>
                        <?php foreach ($destinations as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($tour['destination_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($tour['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jumlah Hari (angka)</label>
                    <input type="number" name="duration_days" class="form-control" value="<?= $tour['duration_days'] ?? 1 ?>" min="1">
                </div>

                <!-- Row 3: Price + Peserta -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="text" name="price" class="form-control" value="<?= $tour['price'] ?? '' ?>" required placeholder="2850000">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Label Harga</label>
                    <input type="text" name="price_label" class="form-control" value="<?= e($tour['price_label'] ?? 'per orang') ?>" placeholder="per orang">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Min Peserta</label>
                    <input type="number" name="min_persons" class="form-control" value="<?= $tour['min_persons'] ?? 1 ?>" min="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Max Peserta</label>
                    <input type="number" name="max_persons" class="form-control" value="<?= $tour['max_persons'] ?? 20 ?>" min="1">
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat paket tour..."><?= e($tour['description'] ?? '') ?></textarea>
                </div>

                <!-- Itinerary -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Itinerary</label>
                    <textarea name="itinerary" class="form-control" rows="8" placeholder="Format: Hari 1: Judul | Aktivitas 1 | Aktivitas 2&#10;Hari 2: Judul | Aktivitas 1 | Aktivitas 2&#10;&#10;Atau paste JSON array valid."><?= e($itineraryDisplay) ?></textarea>
                    <small class="text-muted">Format sederhana: <code>Hari 1: Kedatangan | Jemput bandara | Check-in hotel</code>. Satu hari per baris.</small>
                </div>

                <!-- Includes/Excludes -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fasilitas Termasuk (satu per baris)</label>
                    <textarea name="includes" class="form-control" rows="6" placeholder="Hotel 2 malam&#10;Transportasi AC&#10;Guide lokal&#10;Makan 3x sehari"><?= e($includesDisplay) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fasilitas Tidak Termasuk (satu per baris)</label>
                    <textarea name="excludes" class="form-control" rows="6" placeholder="Tiket pesawat&#10;Tips guide&#10;Pengeluaran pribadi&#10;Asuransi"><?= e($excludesDisplay) ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Meeting Point</label>
                    <input type="text" name="meeting_point" class="form-control" value="<?= e($tour['meeting_point'] ?? '') ?>" placeholder="Contoh: Bandara Sultan Hasanuddin Makassar">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Harga Promo (opsional)</label>
                    <input type="text" name="promo_price" class="form-control" value="<?= $tour['promo_price'] ?? '' ?>" placeholder="Kosongkan jika tidak ada promo">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Syarat & Ketentuan</label>
                    <textarea name="terms" class="form-control" rows="3" placeholder="Kebijakan booking, DP, cancellation, dll"><?= e($tour['terms'] ?? '') ?></textarea>
                </div>

                <!-- Images -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Foto Utama</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if (!empty($tour['image'])): ?>
                        <img src="<?= uploadUrl($tour['image']) ?>" class="mt-2 rounded" style="max-width:200px;max-height:120px;object-fit:cover" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Galeri Foto (multi)</label>
                    <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                    <?php if (!empty($tour['gallery'])):
                        $gal = json_decode($tour['gallery'], true);
                        if ($gal): ?>
                            <div class="d-flex gap-2 mt-2 flex-wrap">
                                <?php foreach ($gal as $g): ?>
                                    <img src="<?= uploadUrl($g) ?>" class="rounded" style="width:60px;height:50px;object-fit:cover" onerror="this.style.display='none'">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; endif; ?>
                </div>

                <!-- Flags -->
                <div class="col-12">
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" <?= ($tour['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Aktif (ditampilkan)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="is_best_seller" class="form-check-input" id="isBest" <?= ($tour['is_best_seller'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isBest">Best Seller</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="is_promo" class="form-check-input" id="isPromo" <?= ($tour['is_promo'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isPromo">Promo Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <a href="tours.php" class="btn btn-outline-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg"></i> Simpan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
$pageTitle = 'Form Mobil';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$car = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    $car = $stmt->fetch();
    if (!$car) { header('Location: cars.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parseList = function($input) {
        $input = trim($input);
        if (!$input) return '';
        $parsed = json_decode($input, true);
        if (is_array($parsed)) return json_encode($parsed, JSON_UNESCAPED_UNICODE);
        $arr = array_values(array_filter(array_map('trim', explode("\n", $input))));
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    };

    $data = [
        'name' => trim($_POST['name']),
        'slug' => slugify($_POST['name']),
        'brand' => trim($_POST['brand']),
        'type' => trim($_POST['type']),
        'capacity' => max(1, (int)$_POST['capacity']),
        'luggage' => max(0, (int)$_POST['luggage']),
        'transmission' => trim($_POST['transmission']),
        'fuel_type' => trim($_POST['fuel_type']),
        'year' => (int)$_POST['year'] ?: date('Y'),
        'price_with_driver' => (int)str_replace(['.', ',', ' '], '', $_POST['price_with_driver']),
        'price_without_driver' => (int)str_replace(['.', ',', ' '], '', $_POST['price_without_driver'] ?? '0'),
        'has_driver_option' => isset($_POST['has_driver_option']) ? 1 : 0,
        'has_self_drive' => isset($_POST['has_self_drive']) ? 1 : 0,
        'facilities' => $parseList($_POST['facilities'] ?? ''),
        'description' => trim($_POST['description']),
        'service_area' => trim($_POST['service_area']),
        'terms' => trim($_POST['terms']),
        'is_available' => isset($_POST['is_available']) ? 1 : 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
    ];

    if (!empty($_FILES['image']['name'])) {
        $img = uploadImage($_FILES['image'], 'cars');
        if ($img) $data['image'] = $img;
    }

    if ($id && $car) {
        $fields = [];
        $values = [];
        foreach ($data as $k => $v) {
            $fields[] = "`$k` = ?";
            $values[] = $v;
        }
        $values[] = $id;
        $pdo->prepare("UPDATE cars SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
        setFlash('success', 'Data mobil berhasil diperbarui.');
    } else {
        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $pdo->prepare("INSERT INTO cars ($fields) VALUES ($placeholders)")->execute(array_values($data));
        setFlash('success', 'Mobil berhasil ditambahkan.');
    }
    header('Location: cars.php');
    exit;
}

$facilitiesDisplay = '';
if ($car && $car['facilities']) {
    $arr = json_decode($car['facilities'], true);
    $facilitiesDisplay = is_array($arr) ? implode("\n", $arr) : $car['facilities'];
}
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-car-front text-primary-custom"></i> <?= $id ? 'Edit' : 'Tambah' ?> Mobil</h4>
        <a href="cars.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Mobil <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= e($car['name'] ?? '') ?>" required placeholder="Toyota Avanza New">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Brand</label>
                    <input type="text" name="brand" class="form-control" value="<?= e($car['brand'] ?? '') ?>" placeholder="Toyota">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tahun</label>
                    <input type="number" name="year" class="form-control" value="<?= $car['year'] ?? date('Y') ?>" min="1990" max="<?= date('Y')+1 ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipe</label>
                    <select name="type" class="form-select">
                        <?php foreach (['mpv'=>'MPV','suv'=>'SUV','sedan'=>'Sedan','city'=>'City Car','bus'=>'Bus','minibus'=>'Minibus','pickup'=>'Pickup'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= ($car['type'] ?? 'mpv') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kapasitas</label>
                    <input type="number" name="capacity" class="form-control" value="<?= $car['capacity'] ?? 7 ?>" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bagasi (koper)</label>
                    <input type="number" name="luggage" class="form-control" value="<?= $car['luggage'] ?? 2 ?>" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Transmisi</label>
                    <select name="transmission" class="form-select">
                        <option value="automatic" <?= ($car['transmission'] ?? '') === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                        <option value="manual" <?= ($car['transmission'] ?? '') === 'manual' ? 'selected' : '' ?>>Manual</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bahan Bakar</label>
                    <select name="fuel_type" class="form-select">
                        <?php foreach (['bensin','solar','listrik','hybrid'] as $ft): ?>
                            <option value="<?= $ft ?>" <?= ($car['fuel_type'] ?? '') === $ft ? 'selected' : '' ?>><?= ucfirst($ft) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Harga + Sopir (Rp/hari)</label>
                    <input type="text" name="price_with_driver" class="form-control" value="<?= $car['price_with_driver'] ?? '' ?>" required placeholder="550000">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Harga Self Drive (Rp/hari)</label>
                    <input type="text" name="price_without_driver" class="form-control" value="<?= $car['price_without_driver'] ?? '' ?>" placeholder="Kosongkan jika tidak ada">
                </div>

                <div class="col-md-3">
                    <div class="d-flex flex-column pt-4">
                        <div class="form-check">
                            <input type="checkbox" name="has_driver_option" class="form-check-input" id="hasDriver" <?= ($car['has_driver_option'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="hasDriver">Ada opsi dengan sopir</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="has_self_drive" class="form-check-input" id="hasSelfDrive" <?= ($car['has_self_drive'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="hasSelfDrive">Ada opsi self drive</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Area Layanan</label>
                    <input type="text" name="service_area" class="form-control" value="<?= e($car['service_area'] ?? '') ?>" placeholder="Sulawesi Selatan, Makassar & sekitarnya">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Foto Utama</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if (!empty($car['image'])): ?>
                        <img src="<?= uploadUrl($car['image']) ?>" class="mt-2 rounded" style="max-width:180px;max-height:80px;object-fit:cover" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi mobil, kelebihan, cocok untuk..."><?= e($car['description'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fasilitas (satu per baris)</label>
                    <textarea name="facilities" class="form-control" rows="5" placeholder="AC&#10;Audio touchscreen&#10;Charger USB&#10;Bagasi luas"><?= e($facilitiesDisplay) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Syarat Sewa</label>
                    <textarea name="terms" class="form-control" rows="5" placeholder="DP, dokumen yang diperlukan, ketentuan bensin, dll"><?= e($car['terms'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="is_available" class="form-check-input" id="isAvailable" <?= ($car['is_available'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isAvailable">Tersedia</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" <?= ($car['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isFeatured">Tampilkan di Homepage (Featured)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <a href="cars.php" class="btn btn-outline-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg"></i> Simpan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

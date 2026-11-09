<?php
$pageTitle = 'Data Hotel';
require_once __DIR__ . '/includes/header.php';

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM hotels WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Hotel dihapus.');
    header('Location: hotels.php' . (isset($_GET['dest']) ? '?dest=' . (int)$_GET['dest'] : ''));
    exit;
}

// Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = (int)($_POST['edit_id'] ?? 0);
    $destId = (int)($_POST['destination_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $star = max(1, min(5, (int)($_POST['star_rating'] ?? 3)));

    if (!$destId || !$name) {
        setFlash('danger', 'Destinasi dan nama hotel wajib diisi.');
    } else {
        if ($editId) {
            $pdo->prepare("UPDATE hotels SET destination_id=?, name=?, star_rating=? WHERE id=?")
                ->execute([$destId, $name, $star, $editId]);
            setFlash('success', 'Hotel diperbarui.');
        } else {
            $pdo->prepare("INSERT INTO hotels (destination_id, name, star_rating) VALUES (?,?,?)")
                ->execute([$destId, $name, $star]);
            setFlash('success', 'Hotel ditambahkan.');
        }
    }
    header('Location: hotels.php' . ($destId ? '?dest=' . $destId : ''));
    exit;
}

// Filter by destination
$destFilter = isset($_GET['dest']) ? (int)$_GET['dest'] : 0;
$search = trim($_GET['search'] ?? '');

$whereConds = [];
$params = [];
if ($destFilter) { $whereConds[] = 'h.destination_id = ?'; $params[] = $destFilter; }
if ($search) { $whereConds[] = 'h.name LIKE ?'; $params[] = '%' . $search . '%'; }
$whereSql = $whereConds ? ('WHERE ' . implode(' AND ', $whereConds)) : '';

$sql = "SELECT h.*, d.name as dest_name FROM hotels h LEFT JOIN destinations d ON h.destination_id=d.id $whereSql ORDER BY d.name ASC, h.star_rating ASC, h.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

$destinations = $pdo->query("SELECT id, name FROM destinations ORDER BY name")->fetchAll();
$destCounts = $pdo->query("SELECT destination_id, COUNT(*) as c FROM hotels GROUP BY destination_id")->fetchAll(PDO::FETCH_KEY_PAIR);

// Edit item
$editItem = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
    $s->execute([(int)$_GET['edit']]);
    $editItem = $s->fetch();
}
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-building text-primary-custom"></i> Data Hotel</h4>
            <p class="text-muted small mb-0">Kelola daftar hotel yang muncul di form Custom Trip untuk setiap destinasi.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary" style="font-size:.85rem"><?= count($hotels) ?> hotel</span>
        </div>
    </div>

    <!-- Filter/Search bar -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold mb-1">Filter Destinasi</label>
                    <select name="dest" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Destinasi (<?= array_sum($destCounts) ?>)</option>
                        <?php foreach ($destinations as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $destFilter == $d['id'] ? 'selected' : '' ?>>
                            <?= e($d['name']) ?> (<?= $destCounts[$d['id']] ?? 0 ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold mb-1">Cari nama hotel</label>
                    <input type="text" name="search" class="form-control" placeholder="Contoh: Ayana, Grand..." value="<?= e($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add/Edit form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-<?= $editItem ? 'pencil' : 'plus-lg' ?>"></i> <?= $editItem ? 'Edit' : 'Tambah' ?> Hotel</div>
        <div class="card-body">
            <form method="POST">
                <?php if ($editItem): ?><input type="hidden" name="edit_id" value="<?= $editItem['id'] ?>"><?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Destinasi <span class="text-danger">*</span></label>
                        <select name="destination_id" class="form-select" required>
                            <option value="">-- Pilih Destinasi --</option>
                            <?php foreach ($destinations as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($editItem['destination_id'] ?? $destFilter) == $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Nama Hotel <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= e($editItem['name'] ?? '') ?>" required placeholder="Nama hotel lengkap">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Bintang</label>
                        <select name="star_rating" class="form-select">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= ($editItem['star_rating'] ?? 3) == $i ? 'selected' : '' ?>><?= str_repeat('★', $i) ?> (<?= $i ?>)</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                        <?php if ($editItem): ?><a href="hotels.php<?= $destFilter ? '?dest=' . $destFilter : '' ?>" class="btn btn-outline-secondary">Batal Edit</a><?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Destinasi</th>
                            <th>Nama Hotel</th>
                            <th>Bintang</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($hotels)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data hotel<?= $destFilter || $search ? ' sesuai filter' : '' ?>.</td></tr>
                        <?php else: ?>
                            <?php foreach ($hotels as $h): ?>
                                <tr>
                                    <td class="small"><span class="badge bg-light text-primary-custom border"><?= e($h['dest_name'] ?? '-') ?></span></td>
                                    <td class="small fw-semibold"><?= e($h['name']) ?></td>
                                    <td class="small text-warning"><?= str_repeat('★', $h['star_rating']) ?><span class="text-muted"><?= str_repeat('☆', 5 - $h['star_rating']) ?></span></td>
                                    <td>
                                        <a href="?edit=<?= $h['id'] ?><?= $destFilter ? '&dest=' . $destFilter : '' ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $h['id'] ?><?= $destFilter ? '&dest=' . $destFilter : '' ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus hotel ini?"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

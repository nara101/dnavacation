<?php
$pageTitle = 'Objek Wisata';
require_once __DIR__ . '/includes/header.php';

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM attractions WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Objek wisata dihapus.');
    header('Location: attractions.php' . (isset($_GET['dest']) ? '?dest=' . (int)$_GET['dest'] : ''));
    exit;
}

// Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = (int)($_POST['edit_id'] ?? 0);
    $destId = (int)($_POST['destination_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if (!$destId || !$name) {
        setFlash('danger', 'Destinasi dan nama objek wisata wajib diisi.');
    } else {
        if ($editId) {
            $pdo->prepare("UPDATE attractions SET destination_id=?, name=?, description=? WHERE id=?")
                ->execute([$destId, $name, $desc, $editId]);
            setFlash('success', 'Objek wisata diperbarui.');
        } else {
            $pdo->prepare("INSERT INTO attractions (destination_id, name, description) VALUES (?,?,?)")
                ->execute([$destId, $name, $desc]);
            setFlash('success', 'Objek wisata ditambahkan.');
        }
    }
    header('Location: attractions.php' . ($destId ? '?dest=' . $destId : ''));
    exit;
}

// Filter
$destFilter = isset($_GET['dest']) ? (int)$_GET['dest'] : 0;
$search = trim($_GET['search'] ?? '');

$whereConds = [];
$params = [];
if ($destFilter) { $whereConds[] = 'a.destination_id = ?'; $params[] = $destFilter; }
if ($search) { $whereConds[] = '(a.name LIKE ? OR a.description LIKE ?)'; $params[] = '%' . $search . '%'; $params[] = '%' . $search . '%'; }
$whereSql = $whereConds ? ('WHERE ' . implode(' AND ', $whereConds)) : '';

$sql = "SELECT a.*, d.name as dest_name FROM attractions a LEFT JOIN destinations d ON a.destination_id=d.id $whereSql ORDER BY d.name ASC, a.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$attractions = $stmt->fetchAll();

$destinations = $pdo->query("SELECT id, name FROM destinations ORDER BY name")->fetchAll();
$destCounts = $pdo->query("SELECT destination_id, COUNT(*) as c FROM attractions GROUP BY destination_id")->fetchAll(PDO::FETCH_KEY_PAIR);

$editItem = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM attractions WHERE id = ?");
    $s->execute([(int)$_GET['edit']]);
    $editItem = $s->fetch();
}
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-signpost-split text-primary-custom"></i> Objek Wisata</h4>
            <p class="text-muted small mb-0">Kelola objek wisata per destinasi. Muncul di form Custom Trip user.</p>
        </div>
        <span class="badge bg-primary" style="font-size:.85rem"><?= count($attractions) ?> objek wisata</span>
    </div>

    <!-- Filter -->
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
                    <label class="form-label small fw-semibold mb-1">Cari nama</label>
                    <input type="text" name="search" class="form-control" placeholder="Contoh: Pantai, Candi..." value="<?= e($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add/Edit form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-<?= $editItem ? 'pencil' : 'plus-lg' ?>"></i> <?= $editItem ? 'Edit' : 'Tambah' ?> Objek Wisata</div>
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
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold">Nama Objek Wisata <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= e($editItem['name'] ?? '') ?>" required placeholder="Contoh: Candi Borobudur, Pantai Kuta">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Deskripsi Singkat (opsional)</label>
                        <input type="text" name="description" class="form-control" value="<?= e($editItem['description'] ?? '') ?>" maxlength="500" placeholder="Deskripsi ringkas untuk info admin">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                        <?php if ($editItem): ?><a href="attractions.php<?= $destFilter ? '?dest=' . $destFilter : '' ?>" class="btn btn-outline-secondary">Batal Edit</a><?php endif; ?>
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
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attractions)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data objek wisata<?= $destFilter || $search ? ' sesuai filter' : '' ?>.</td></tr>
                        <?php else: ?>
                            <?php foreach ($attractions as $a): ?>
                                <tr>
                                    <td class="small"><span class="badge bg-light text-primary-custom border"><?= e($a['dest_name'] ?? '-') ?></span></td>
                                    <td class="small fw-semibold"><?= e($a['name']) ?></td>
                                    <td class="small text-muted"><?= e(truncate($a['description'] ?? '', 100)) ?></td>
                                    <td>
                                        <a href="?edit=<?= $a['id'] ?><?= $destFilter ? '&dest=' . $destFilter : '' ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $a['id'] ?><?= $destFilter ? '&dest=' . $destFilter : '' ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus objek wisata ini?"><i class="bi bi-trash"></i></a>
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

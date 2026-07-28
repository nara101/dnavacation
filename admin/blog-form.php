<?php
$pageTitle = 'Form Artikel';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) { header('Location: blog.php'); exit; }
}

$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $data = [
        'title' => $title,
        'slug' => slugify($title),
        'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
        'content' => $_POST['content'],
        'excerpt' => trim($_POST['excerpt']),
        'meta_title' => trim($_POST['meta_title']),
        'meta_description' => trim($_POST['meta_description']),
        'is_published' => $isPublished,
        'author_id' => $_SESSION['admin_id'] ?? 1,
    ];

    // Set published_at when publishing for first time
    if ($isPublished && (!$post || !$post['published_at'])) {
        $data['published_at'] = date('Y-m-d H:i:s');
    }

    if (!empty($_FILES['image']['name'])) {
        $img = uploadImage($_FILES['image'], 'blog');
        if ($img) $data['image'] = $img;
    }

    if ($id && $post) {
        $fields = [];
        $values = [];
        foreach ($data as $k => $v) {
            $fields[] = "`$k` = ?";
            $values[] = $v;
        }
        $values[] = $id;
        $pdo->prepare("UPDATE blog_posts SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
        setFlash('success', 'Artikel berhasil diperbarui.');
    } else {
        if (!isset($data['published_at'])) $data['published_at'] = null;
        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $pdo->prepare("INSERT INTO blog_posts ($fields) VALUES ($placeholders)")->execute(array_values($data));
        setFlash('success', 'Artikel berhasil ditambahkan.');
    }
    header('Location: blog.php');
    exit;
}
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-text text-primary-custom"></i> <?= $id ? 'Edit' : 'Tulis' ?> Artikel</h4>
        <a href="blog.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg" value="<?= e($post['title'] ?? '') ?>" required placeholder="Judul artikel yang menarik...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($post['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Ringkasan / Excerpt</label>
                    <textarea name="excerpt" class="form-control" rows="2" placeholder="Ringkasan singkat artikel (tampil di preview list)..."><?= e($post['excerpt'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Konten Artikel <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="16" required placeholder="Tulis konten artikel di sini. Bisa menggunakan HTML dasar: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;..."><?= e($post['content'] ?? '') ?></textarea>
                    <small class="text-muted">Tips: gunakan tag &lt;h2&gt; untuk sub-heading, &lt;p&gt; untuk paragraf, &lt;ul&gt;&lt;li&gt; untuk list.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Foto Utama</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if (!empty($post['image'])): ?>
                        <img src="<?= uploadUrl($post['image']) ?>" class="mt-2 rounded" style="max-width:200px;max-height:120px;object-fit:cover" onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Meta Title (SEO)</label>
                    <input type="text" name="meta_title" class="form-control" value="<?= e($post['meta_title'] ?? '') ?>" placeholder="Kosongkan untuk gunakan judul">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_published" class="form-check-input" id="isPublished" style="width:3rem;height:1.5rem" <?= ($post['is_published'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label ms-2" for="isPublished">Publish sekarang</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Meta Description (SEO)</label>
                    <textarea name="meta_description" class="form-control" rows="2" placeholder="Deskripsi untuk Google search (max 160 karakter)..."><?= e($post['meta_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <a href="blog.php" class="btn btn-outline-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg"></i> Simpan Artikel</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

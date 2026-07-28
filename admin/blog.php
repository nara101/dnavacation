<?php
$pageTitle = 'Blog';
require_once __DIR__ . '/includes/header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Artikel dihapus.');
    header('Location: blog.php');
    exit;
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    // If publishing for first time, set published_at
    $post = $pdo->prepare("SELECT is_published, published_at FROM blog_posts WHERE id=?");
    $post->execute([$id]);
    $p = $post->fetch();
    if ($p) {
        if (!$p['is_published'] && !$p['published_at']) {
            $pdo->prepare("UPDATE blog_posts SET is_published=1, published_at=NOW() WHERE id=?")->execute([$id]);
        } else {
            $pdo->prepare("UPDATE blog_posts SET is_published = NOT is_published WHERE id=?")->execute([$id]);
        }
    }
    setFlash('success', 'Status artikel diperbarui.');
    header('Location: blog.php');
    exit;
}

$posts = $pdo->query("SELECT bp.*, bc.name as cat_name, a.full_name as author_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id LEFT JOIN admin_users a ON bp.author_id = a.id ORDER BY bp.created_at DESC")->fetchAll();
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-text text-primary-custom"></i> Blog</h4>
        <a href="blog-form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tulis Artikel</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:70px">Foto</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Author</th>
                            <th>Tanggal</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada artikel. <a href="blog-form.php">Tulis artikel pertama Anda</a>.</td></tr>
                        <?php else: ?>
                            <?php foreach ($posts as $p): ?>
                                <tr>
                                    <td>
                                        <?php if ($p['image']): ?>
                                            <img src="<?= uploadUrl($p['image']) ?>" class="rounded" style="width:56px;height:44px;object-fit:cover" onerror="handleImgError(this)">
                                        <?php else: ?>
                                            <div class="placeholder-img rounded" style="width:56px;height:44px;font-size:1rem"><i class="bi bi-journal"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="small"><?= e($p['title']) ?></strong>
                                        <br><small class="text-muted"><?= e(truncate(strip_tags($p['excerpt'] ?: $p['content']), 60)) ?></small>
                                    </td>
                                    <td class="small"><?= e($p['cat_name'] ?? '-') ?></td>
                                    <td class="small text-muted"><?= e($p['author_name'] ?? '-') ?></td>
                                    <td class="small"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                                    <td class="small text-muted"><i class="bi bi-eye"></i> <?= $p['view_count'] ?></td>
                                    <td>
                                        <a href="?toggle=<?= $p['id'] ?>" class="badge bg-<?= $p['is_published'] ? 'success' : 'secondary' ?> text-decoration-none" style="font-size:.7rem">
                                            <?= $p['is_published'] ? 'Published' : 'Draft' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($p['is_published']): ?>
                                        <a href="<?= BASE_URL ?>/blog-detail.php?slug=<?= e($p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat"><i class="bi bi-eye"></i></a>
                                        <?php endif; ?>
                                        <a href="blog-form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus artikel ini?"><i class="bi bi-trash"></i></a>
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

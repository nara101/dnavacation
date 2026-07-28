<?php
require_once 'includes/config.php';
$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . '/blog.php'); exit; }

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, a.full_name as author_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id LEFT JOIN admin_users a ON p.author_id=a.id WHERE p.slug=? AND p.is_published=1");
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) { header('Location: ' . BASE_URL . '/blog.php'); exit; }

$pdo->prepare("UPDATE blog_posts SET view_count=view_count+1 WHERE id=?")->execute([$post['id']]);

$pageTitle = $post['title'];
$pageDescription = truncate(strip_tags($post['excerpt'] ?: $post['content']), 160);

$related = $pdo->prepare("SELECT p.*, c.name as category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id WHERE p.is_published=1 AND p.id!=? ORDER BY RAND() LIMIT 3");
$related->execute([$post['id']]);
$relatedPosts = $related->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <span class="badge bg-white text-primary-custom mb-2"><?= e($post['category_name'] ?? 'Artikel') ?></span>
        <h1><?= e($post['title']) ?></h1>
        <p class="small"><?= date('d M Y', strtotime($post['published_at'] ?: $post['created_at'])) ?> &bull; <?= $post['view_count'] ?> views</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if ($post['image']): ?>
                    <img src="<?= uploadUrl($post['image']) ?>" class="w-100 rounded-3 mb-4" style="max-height:400px;object-fit:cover" alt="<?= e($post['title']) ?>" onerror="handleImgError(this)">
                <?php endif; ?>
                <article class="mb-4" style="line-height:1.8;font-size:.95rem">
                    <?= $post['content'] ?>
                </article>
                <hr>
                <div class="bg-primary-custom text-white rounded-3 p-4 text-center mb-4">
                    <h5 class="fw-bold mb-2">Tertarik Untuk Liburan?</h5>
                    <p class="small mb-3">Hubungi kami sekarang untuk informasi paket tour terbaik!</p>
                    <a href="<?= BASE_URL ?>/tours.php" class="btn btn-light me-2">Lihat Paket Tour</a>
                    <a href="<?= waLink('Halo DNA Vacation, saya baru baca artikel tentang ' . $post['title'] . '. Saya tertarik untuk liburan!') ?>" target="_blank" class="btn btn-wa">Chat WhatsApp</a>
                </div>
            </div>
            <div class="col-lg-4">
                <?php if ($relatedPosts): ?>
                <h6 class="fw-bold mb-3">Artikel Terkait</h6>
                <?php foreach ($relatedPosts as $rp): ?>
                <div class="d-flex gap-3 mb-3">
                    <?php if ($rp['image']): ?>
                        <img src="<?= uploadUrl($rp['image']) ?>" class="rounded-2 flex-shrink-0" style="width:80px;height:60px;object-fit:cover" alt="" onerror="handleImgError(this)">
                    <?php else: ?>
                        <div class="placeholder-img rounded-2 flex-shrink-0" style="width:80px;height:60px"><i class="bi bi-journal" style="font-size:1rem"></i></div>
                    <?php endif; ?>
                    <div>
                        <a href="<?= BASE_URL ?>/blog-detail.php?slug=<?= e($rp['slug']) ?>" class="text-decoration-none text-dark small fw-semibold"><?= e($rp['title']) ?></a>
                        <p class="small text-muted mb-0"><?= date('d M Y', strtotime($rp['published_at'] ?: $rp['created_at'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>

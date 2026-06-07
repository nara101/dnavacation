<?php
include "db.php";
$currentPage = 'blog';

if (!isset($_GET['id'])) { header("Location: blog.php"); exit; }
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM blog WHERE id=? AND status='publish'");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) { header("Location: blog.php"); exit; }

$pageTitle = $post['meta_title'] ?: ($post['judul'] . ' - DNA Vacation');
$pageDesc = $post['meta_description'] ?: substr(strip_tags($post['konten']), 0, 160);

$related = $conn->query("SELECT * FROM blog WHERE id!=$id AND kategori='{$post['kategori']}' AND status='publish' ORDER BY RAND() LIMIT 3");

include "includes/header.php";
?>
<section class="page-hero">
    <h1><?= htmlspecialchars($post['judul']) ?></h1>
    <p class="breadcrumb-trail"><a href="index.php">Home</a> / <a href="blog.php">Blog</a> / <?= labelKategori($post['kategori']) ?></p>
</section>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <article style="background:#fff; padding:36px; border-radius:14px; box-shadow:0 4px 18px rgba(0,0,0,0.07);">
                <p class="text-muted mb-3">
                    <i class="fa-regular fa-folder text-warning"></i> <?= labelKategori($post['kategori']) ?>
                    &nbsp; <i class="fa-regular fa-calendar text-warning"></i> <?= date('d M Y', strtotime($post['created_at'])) ?>
                </p>
                <?php if ($post['gambar']): ?>
                <img src="<?= fotoUrl($post['gambar'], 'blog') ?>" style="width:100%;border-radius:12px;margin-bottom:24px;">
                <?php endif; ?>
                <div style="line-height:1.85; color:#444; font-size:1.05rem;">
                    <?= nl2br(htmlspecialchars($post['konten'])) ?>
                </div>

                <hr class="my-4">
                <div class="text-center">
                    <h5 style="color:#0a3d62;">Tertarik liburan?</h5>
                    <p class="text-muted">Konsultasikan rencana liburan Anda sekarang!</p>
                    <a href="<?= waLink('Halo DNA Vacation, saya baru baca artikel: ' . $post['judul']) ?>" target="_blank" class="btn-book" style="display:inline-block;text-decoration:none;width:auto;padding:14px 36px;background:#25d366;">
                        <i class="fa-brands fa-whatsapp me-2"></i>Chat WhatsApp Sekarang
                    </a>
                </div>
            </article>

            <?php if ($related && $related->num_rows): ?>
            <h3 style="color:#0a3d62; margin-top:48px; font-weight:700;">Artikel Terkait</h3>
            <div class="row g-4 mt-2">
                <?php while ($r = $related->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <a href="blog-detail.php?id=<?= $r['id'] ?>" class="blog-card">
                            <img src="<?= fotoUrl($r['gambar'], 'blog') ?>" alt="">
                            <div class="blog-content">
                                <h5><?= htmlspecialchars($r['judul']) ?></h5>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<?php
<<<<<<< HEAD
$pageTitle = 'Blog';
require_once 'includes/header.php';

$where = ['p.is_published = 1'];
$params = [];

if (!empty($_GET['category'])) {
    $where[] = 'p.category_id = ?';
    $params[] = (int)$_GET['category'];
}
if (!empty($_GET['search'])) {
    $where[] = '(p.title LIKE ? OR p.content LIKE ?)';
    $params[] = '%'.$_GET['search'].'%';
    $params[] = '%'.$_GET['search'].'%';
}

$whereSQL = implode(' AND ', $where);
$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts p WHERE {$whereSQL}");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id WHERE {$whereSQL} ORDER BY p.published_at DESC LIMIT {$perPage} OFFSET {$offset}");
$stmt->execute($params);
$posts = $stmt->fetchAll();

$blogCategories = $pdo->query("SELECT bc.*, (SELECT COUNT(*) FROM blog_posts WHERE category_id=bc.id AND is_published=1) as post_count FROM blog_categories bc ORDER BY bc.name")->fetchAll();
$latestPosts = $pdo->query("SELECT * FROM blog_posts WHERE is_published=1 ORDER BY view_count DESC LIMIT 5")->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>Blog & <span class="script">Tips</span> Travel</h1>
        <p>Informasi, tips, dan inspirasi untuk perjalanan terbaik Anda</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if (empty($posts)): ?>
                <div class="text-center py-5 bg-white rounded-3">
                    <i class="bi bi-journal text-muted" style="font-size:3rem"></i>
                    <p class="mt-3 text-muted">Belum ada artikel yang sesuai.</p>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($posts as $post): ?>
                    <div class="col-md-6">
                        <div class="card blog-card h-100">
                            <div class="image-wrapper">
                            <?php if ($post['image']): ?>
                                <img src="<?= uploadUrl($post['image']) ?>" class="card-img-top" alt="<?= e($post['title']) ?>" onerror="handleImgError(this)">
                            <?php else: ?>
                                <div class="card-img-top placeholder-img" style="height:190px"><i class="bi bi-journal-text"></i></div>
                            <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <span class="blog-category"><?= e($post['category_name'] ?? 'Artikel') ?></span>
                                <h6 class="card-title mt-1"><?= e($post['title']) ?></h6>
                                <p class="small text-muted flex-grow-1"><?= truncate(strip_tags($post['excerpt'] ?: $post['content']), 110) ?></p>
                                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                    <small class="text-muted"><i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($post['published_at'] ?: $post['created_at'])) ?></small>
                                    <a href="<?= BASE_URL ?>/blog-detail.php?slug=<?= e($post['slug']) ?>" class="text-primary-custom small fw-semibold text-decoration-none">Baca <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($totalPages > 1): ?>
                <nav class="mt-4"><ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul></nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="filter-section mb-4" style="position:static">
                    <h6><i class="bi bi-search text-primary-custom"></i> Cari Artikel</h6>
                    <form method="GET">
                        <input type="text" name="search" class="form-control mb-2" placeholder="Cari topik..." value="<?= e($_GET['search'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary-custom w-100 btn-sm">Cari</button>
                    </form>
                </div>
                <div class="filter-section mb-4" style="position:static">
                    <h6><i class="bi bi-tags text-primary-custom"></i> Kategori</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="<?= BASE_URL ?>/blog.php" class="text-decoration-none text-muted small">
                            <i class="bi bi-collection"></i> Semua Artikel
                        </a></li>
                        <?php foreach ($blogCategories as $bc): ?>
                        <li class="mb-2 d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>/blog.php?category=<?= $bc['id'] ?>" class="text-decoration-none text-muted small">
                                <i class="bi bi-tag"></i> <?= e($bc['name']) ?>
                            </a>
                            <?php if ($bc['post_count']): ?><span class="badge bg-light text-muted"><?= $bc['post_count'] ?></span><?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php if ($latestPosts): ?>
                <div class="filter-section" style="position:static">
                    <h6><i class="bi bi-fire text-primary-custom"></i> Artikel Populer</h6>
                    <?php foreach ($latestPosts as $lp): ?>
                    <div class="d-flex gap-2 mb-3">
                        <div class="placeholder-img rounded flex-shrink-0" style="width:56px;height:56px;font-size:.9rem"><i class="bi bi-journal"></i></div>
                        <div>
                            <a href="<?= BASE_URL ?>/blog-detail.php?slug=<?= e($lp['slug']) ?>" class="text-decoration-none text-dark small fw-semibold d-block"><?= e(truncate($lp['title'], 55)) ?></a>
                            <small class="text-muted"><i class="bi bi-eye"></i> <?= $lp['view_count'] ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
=======
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Rental</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            display: flex
        }

        .sidebar {
            width: 240px;
            background: #1f3c4a;
            min-height: 100vh;
            padding: 20px;
            color: #fff
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px
        }

        .sidebar a:hover {
            background: #2c5364
        }

        .content {
            flex: 1;
            padding: 30px
        }

        .header {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px
        }

        .btn {
            background: #2c5364;
            color: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none
        }

        table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden
        }

        th,
        td {
            padding: 15px
        }

        th {
            background: #1f3c4a;
            color: #fff
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="tour.php">Kelola Tour</a>
        <a href="rental.php">Kelola Rental</a>
        <a href="blog.php">Kelola Blog</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Kelola Rental</h2>
            <a href="rental_tambah.php" class="btn">+ Tambah Rental</a>
        </div>

        <table>
            <tr>
                <th>No</th>
                <th>Nama Kendaraan</th>
                <th>Harga / Hari</th>
                <th>Aksi</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Toyota Avanza</td>
                <td>Rp 350.000</td>
                <td>
                    <a class="btn">Edit</a>
                    <a class="btn" style="background:#b30000">Hapus</a>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
>>>>>>> 20a16d92d2a393b6d0f29d71daee7f38359db22d

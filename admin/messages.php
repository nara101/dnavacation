<?php
$pageTitle = 'Pesan Masuk';
require_once __DIR__ . '/includes/header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Pesan dihapus.');
    header('Location: messages.php');
    exit;
}

if (isset($_GET['read'])) {
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([(int)$_GET['read']]);
    header('Location: messages.php');
    exit;
}

if (isset($_GET['mark_all_read'])) {
    $pdo->exec("UPDATE contact_messages SET is_read = 1 WHERE is_read = 0");
    setFlash('success', 'Semua pesan ditandai sudah dibaca.');
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<div class="p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-envelope text-primary-custom"></i> Pesan Masuk</h4>
        <?php if ($messages): ?>
        <a href="?mark_all_read=1" class="btn btn-sm btn-outline-primary"><i class="bi bi-check2-all"></i> Tandai semua dibaca</a>
        <?php endif; ?>
    </div>

    <?php if (empty($messages)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-envelope-open text-muted" style="font-size:3rem"></i>
                <p class="mt-3 text-muted mb-0">Belum ada pesan masuk.</p>
            </div>
        </div>
    <?php else: ?>
    <div class="d-flex flex-column gap-3">
        <?php foreach ($messages as $m): ?>
        <div class="card border-0 shadow-sm <?= !$m['is_read'] ? 'border-start border-primary border-3' : '' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                    <div>
                        <strong><?= e($m['name']) ?></strong>
                        <?php if (!$m['is_read']): ?><span class="badge bg-primary ms-2">Baru</span><?php endif; ?>
                        <div class="small text-muted">
                            <?php if ($m['email']): ?><i class="bi bi-envelope"></i> <a href="mailto:<?= e($m['email']) ?>" class="text-muted"><?= e($m['email']) ?></a><?php endif; ?>
                            <?php if ($m['whatsapp']): ?>· <i class="bi bi-whatsapp"></i> <a href="https://wa.me/<?= e($m['whatsapp']) ?>" target="_blank" class="text-success"><?= e($m['whatsapp']) ?></a><?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        <small class="text-muted"><?= date('d M Y, H:i', strtotime($m['created_at'])) ?></small>
                        <?php if (!$m['is_read']): ?>
                        <a href="?read=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary" title="Tandai dibaca"><i class="bi bi-eye"></i></a>
                        <?php endif; ?>
                        <a href="?delete=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus pesan?"><i class="bi bi-trash"></i></a>
                    </div>
                </div>
                <?php if ($m['subject']): ?><h6 class="fw-bold mb-2"><?= e($m['subject']) ?></h6><?php endif; ?>
                <p class="mb-0 text-muted small" style="white-space:pre-wrap"><?= e($m['message']) ?></p>
                <?php if ($m['whatsapp']): ?>
                <div class="mt-3">
                    <a href="https://wa.me/<?= e($m['whatsapp']) ?>?text=<?= urlencode('Halo ' . $m['name'] . ', terima kasih atas pesan Anda di website DNA Vacation.') ?>" target="_blank" class="btn btn-wa btn-sm"><i class="bi bi-whatsapp"></i> Balas via WhatsApp</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

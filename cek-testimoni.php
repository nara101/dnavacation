<?php
// File diagnostik SEMENTARA. Hapus setelah selesai.
require __DIR__ . '/includes/config.php';
header('Content-Type: text/plain; charset=utf-8');

echo "=== BATAS UPLOAD EFEKTIF (yang benar-benar dipakai situs) ===\n";
echo "SAPI               : " . php_sapi_name() . "\n";
echo "PHP version        : " . PHP_VERSION . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size      : " . ini_get('post_max_size') . "\n";
$p = (float)ini_get('post_max_size');
echo "-> Video 16MB: " . ($p >= 18 ? "MUAT (batas sudah cukup)" : ">>> TIDAK MUAT (post_max_size masih kecil) <<<") . "\n";

echo "\n=== VERSI FILE testimonials.php YANG AKTIF ===\n";
$src = @file_get_contents(__DIR__ . '/testimonials.php');
echo "ada blok log diagnostik : " . (strpos($src, 'testi-debug.log') !== false ? "YA (versi terbaru ter-upload)" : ">>> TIDAK (file lama, belum ter-upload) <<<") . "\n";
echo "ada blok deteksi 413    : " . (strpos($src, 'HTTP_X_REQUESTED_WITH') !== false ? "YA" : "TIDAK") . "\n";

echo "\n=== LOG SUBMISSION TESTIMONI (terbaru di bawah) ===\n";
$log = __DIR__ . '/uploads/testi-debug.log';
if (is_file($log)) {
    $lines = file($log, FILE_IGNORE_NEW_LINES);
    echo implode("\n", array_slice($lines, -20)) . "\n";
} else {
    echo "(belum ada log)\n";
}

echo "\n=== 6 TESTIMONI TERBARU DI DATABASE ===\n";
try {
    $rows = $pdo->query("SELECT id, customer_name, is_active, IFNULL(video,'-') AS v, created_at FROM testimonials ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) echo "(tabel kosong)\n";
    foreach ($rows as $r) {
        echo "  #{$r['id']} | '{$r['customer_name']}' | active={$r['is_active']} | video={$r['v']} | {$r['created_at']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\nSelesai. HAPUS file ini setelah dicek.\n";

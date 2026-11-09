<?php
require_once __DIR__ . '/../includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . ADMIN_URL . '/');
    exit;
}

http_response_code(404);
echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>404 - Halaman Tidak Ditemukan</title>';
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">';
echo '<style>body{font-family:"Plus Jakarta Sans",sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{text-align:center;padding:3rem}.code{font-size:6rem;font-weight:800;color:#0B2545;line-height:1}.msg{font-size:1.1rem;color:#6c757d;margin:1rem 0 2rem}</style>';
echo '</head><body><div class="box"><div class="code">404</div><div class="msg">Halaman ini tidak ada.</div>';
echo '<a href="' . BASE_URL . '/" class="btn btn-dark px-4">Kembali ke Beranda</a></div></body></html>';

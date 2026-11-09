<?php
require_once 'includes/config.php';

echo "<h3>Test SMTP Email</h3>";
echo "<p>SMTP Host: " . SMTP_HOST . "</p>";
echo "<p>SMTP User: " . SMTP_USER . "</p>";
echo "<p>SMTP Pass: " . (SMTP_PASS ? '***set***' : '<strong style="color:red">KOSONG!</strong>') . "</p>";

if (isset($_GET['send'])) {
    $to = $_GET['send'];
    echo "<p>Mengirim ke: {$to}...</p>";
    $result = sendMail($to, 'Test Email DNA Vacation', '<h2>Test Berhasil!</h2><p>Email SMTP berhasil terkirim dari DNA Vacation.</p>');
    if ($result) {
        echo '<p style="color:green;font-weight:bold">Email berhasil terkirim!</p>';
    } else {
        echo '<p style="color:red;font-weight:bold">Gagal kirim email. Cek error log.</p>';
    }
}
?>
</content>
</invoke>
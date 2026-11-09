<?php
require_once 'includes/config.php';

echo "<h3>DNA Vacation - Email Diagnostic</h3>";

// 1. Show all users
echo "<h4>1. Users di Database</h4>";
$stmt = $pdo->query("SELECT id, username, email, is_verified, verification_token, reset_token, created_at FROM users ORDER BY id");
$users = $stmt->fetchAll();

if (!$users) {
    echo "<p style='color:red'><strong>Tidak ada user di database!</strong></p>";
} else {
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Verified?</th><th>Has Verify Token?</th><th>Has Reset Token?</th><th>Created</th><th>Action</th></tr>";
    foreach ($users as $u) {
        $verified = $u['is_verified'] ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>';
        $hasVerifyToken = $u['verification_token'] ? 'Yes' : 'No';
        $hasResetToken = $u['reset_token'] ? 'Yes' : 'No';
        echo "<tr>";
        echo "<td>{$u['id']}</td><td>{$u['username']}</td><td>{$u['email']}</td><td>{$verified}</td><td>{$hasVerifyToken}</td><td>{$hasResetToken}</td><td>{$u['created_at']}</td>";
        if (!$u['is_verified']) {
            echo "<td><a href='?verify={$u['id']}'>Force Verify</a></td>";
        } else {
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Force verify
if (isset($_GET['verify'])) {
    $id = (int)$_GET['verify'];
    $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?")->execute([$id]);
    echo "<p style='color:green'><strong>User ID {$id} sudah diverifikasi! <a href='debug-email.php'>Refresh</a></strong></p>";
}

// 3. Test send reset email
if (isset($_GET['test_reset'])) {
    $email = $_GET['test_reset'];
    echo "<h4>Test kirim reset password ke: {$email}</h4>";
    $token = generateToken();
    $result = sendPasswordResetEmail($email, $token);
    echo $result
        ? "<p style='color:green'><strong>Email reset password BERHASIL dikirim!</strong></p>"
        : "<p style='color:red'><strong>GAGAL kirim email reset password. Cek error log PHP.</strong></p>";
}

// 4. Test send verification email
if (isset($_GET['test_verify'])) {
    $email = $_GET['test_verify'];
    echo "<h4>Test kirim verifikasi ke: {$email}</h4>";
    $token = generateToken();
    $result = sendVerificationEmail($email, $token);
    echo $result
        ? "<p style='color:green'><strong>Email verifikasi BERHASIL dikirim!</strong></p>"
        : "<p style='color:red'><strong>GAGAL kirim email verifikasi. Cek error log PHP.</strong></p>";
}

echo "<h4>2. Test Kirim Email</h4>";
echo "<p><a href='?test_verify=nabilaramadhanty11@gmail.com'>Test kirim email verifikasi</a></p>";
echo "<p><a href='?test_reset=nabilaramadhanty11@gmail.com'>Test kirim email reset password</a></p>";

echo "<h4>3. SMTP Config</h4>";
echo "<p>Host: " . SMTP_HOST . "</p>";
echo "<p>Port: " . SMTP_PORT . "</p>";
echo "<p>User: " . SMTP_USER . "</p>";
echo "<p>Pass: " . (SMTP_PASS ? '***set***' : '<strong style="color:red">KOSONG!</strong>') . "</p>";
?>

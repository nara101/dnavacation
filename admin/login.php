<?php
session_start();
// include "../db.php";
include "../db.php";

/* JIKA SUDAH LOGIN → JANGAN MASUK LOGIN LAGI */
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username=?");
        $error = "Username tidak ditemukan";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - DNA Vacation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; justify-content: center; align-items: center; background: linear-gradient(135deg, #0a3d62 0%, #1e6091 50%, #082c47 100%); padding: 20px; }
        .login-box { background: #fff; padding: 44px; width: 100%; max-width: 420px; border-radius: 18px; box-shadow: 0 25px 60px rgba(0,0,0,.35); }
        .logo-title { text-align: center; margin-bottom: 28px; }
        .logo-title h2 { color: #0a3d62; font-weight: 800; font-size: 1.6rem; letter-spacing: 1.5px; }
        .logo-title h2 span { color: #f39c12; }
        .logo-title p { color: #888; font-size: 0.9rem; margin-top: 6px; }
        .form-group { position: relative; margin-bottom: 16px; }
        .form-group input { width: 100%; padding: 14px 14px 14px 44px; border-radius: 10px; border: 1px solid #e0e0e0; font-size: 0.95rem; font-family: inherit; transition: 0.3s; }
        .form-group input:focus { outline: none; border-color: #0a3d62; box-shadow: 0 0 0 3px rgba(10,61,98,0.1); }
        .form-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #999; }
        button.login-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #0a3d62, #1e6091); border: none; color: #fff; font-size: 1rem; font-weight: 700; border-radius: 10px; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        button.login-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(10,61,98,0.4); }
        .error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 16px; font-size: 0.9rem; border-left: 4px solid #c62828; }
        .info { background: #e3f2fd; color: #1565c0; padding: 12px; border-radius: 8px; font-size: 0.82rem; margin-top: 16px; border-left: 4px solid #1565c0; }
        .home-link { display: block; text-align: center; margin-top: 18px; color: #0a3d62; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-title">
            <h2>DNA<span>VACATION</span></h2>
            <p>Admin Panel Login</p>
        </div>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="Username" required autofocus>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button name="submit" class="login-btn"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</button>
        </form>
        <div class="info">
            <strong>Default Login:</strong> admin / admin123<br>
            (Belum bisa login? Jalankan <a href="../setup.php" style="color:#1565c0;font-weight:700;">setup.php</a> dulu)
        </div>
        <a href="../index.php" class="home-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Website</a>
    </div>
</body>
</html>
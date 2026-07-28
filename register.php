<?php
session_start();
include "db.php";

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // cek username sudah ada atau belum
    $cek = $conn->prepare("SELECT id FROM admin WHERE username=?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $error = "Username sudah digunakan";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $insert->bind_param("ss", $username, $hash);
        $insert->execute();

        header("Location: login.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Admin</title>

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        }

        .box {
            background: #fff;
            padding: 35px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .3);
        }

        .box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c5364;
        }

        .box input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .box button {
            width: 100%;
            padding: 12px;
            background: #2c5364;
            border: none;
            color: white;
            font-size: 15px;
            border-radius: 8px;
            cursor: pointer;
        }

        .box button:hover {
            background: #203a43;
        }

        .error {
            background: #ffdddd;
            padding: 10px;
            border-radius: 6px;
            color: #b30000;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="box">
        <h2>Daftar Admin</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="submit">Daftar</button>
        </form>
    </div>

</body>

</html>
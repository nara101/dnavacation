$username = trim($_POST['username']);
$password = $_POST['password'];

// cek username sudah ada atau belum
$cek = $conn->prepare("SELECT id FROM admin WHERE username=?");
$cek->bind_param("s", $username);
$cek->execute();
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
            cursor: pointer;
        }

        .box button:hover {
            background: #203a43;
        }

        .error {
            background: #ffdddd;
            padding: 10px;
            </head><body><div class="box"><h2>Daftar Admin</h2><?php if (!empty($error)): ?><div class="error"><?= $error ?></div><?php endif; ?><form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button name="submit">Daftar</button></form></div></body></html>
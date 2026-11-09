<?php
require_once 'includes/config.php';

unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role'],
      $_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_role']);

setFlash('success', 'Anda telah berhasil logout.');
header('Location: ' . BASE_URL . '/user-login.php');
exit;

<?php
<<<<<<< HEAD
require_once __DIR__ . '/../includes/config.php';
session_destroy();
header('Location: login.php');
=======
session_start();
session_unset();
session_destroy();
header("Location: login.php");
>>>>>>> 20a16d92d2a393b6d0f29d71daee7f38359db22d
exit;

<?php
include "auth.php";
include "../db.php";
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM blog WHERE id=$id");
}
header("Location: blog-admin.php");
exit;

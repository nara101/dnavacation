<?php
include "auth.php";
include "../db.php";
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM rental WHERE id=$id");
}
header("Location: rental-admin.php");
exit;

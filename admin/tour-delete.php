<?php
include "auth.php";
include "../db.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM tour WHERE id=$id");
}

header("Location: tour-admin.php");
exit;

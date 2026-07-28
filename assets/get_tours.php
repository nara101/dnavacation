<?php
$conn = new mysqli("localhost", "user", "password", "database");
$result = $conn->query("SELECT * FROM tours");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);

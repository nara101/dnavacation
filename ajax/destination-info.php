<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$destId = (int)($_GET['destination_id'] ?? 0);

$response = [
    'hotels' => [],
    'attractions' => [],
    'destination_name' => '',
];

if ($destId > 0) {
    $dest = $pdo->prepare("SELECT name FROM destinations WHERE id = ?");
    $dest->execute([$destId]);
    $d = $dest->fetch();
    if ($d) $response['destination_name'] = $d['name'];

    $hotels = $pdo->prepare("SELECT name, star_rating FROM hotels WHERE destination_id = ? ORDER BY star_rating ASC, name ASC");
    $hotels->execute([$destId]);
    $grouped = [];
    foreach ($hotels->fetchAll() as $h) {
        $star = (int)$h['star_rating'];
        if (!isset($grouped[$star])) $grouped[$star] = [];
        $grouped[$star][] = $h['name'];
    }
    ksort($grouped);
    $response['hotels'] = $grouped;

    $attractions = $pdo->prepare("SELECT name FROM attractions WHERE destination_id = ? ORDER BY name ASC");
    $attractions->execute([$destId]);
    $response['attractions'] = array_column($attractions->fetchAll(), 'name');
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

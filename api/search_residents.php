<?php
require_once '../includes/config.php';
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

header('Content-Type: application/json');
$q = sanitize($_GET['q'] ?? '');
if (strlen($q) < 2) { echo json_encode([]); exit; }

$stmt = $pdo->prepare("SELECT r.id, r.resident_id, CONCAT(r.first_name,' ',r.middle_name,' ',r.last_name) as full_name, h.address FROM residents r LEFT JOIN households h ON r.household_id=h.id WHERE r.is_active=1 AND (r.first_name LIKE ? OR r.last_name LIKE ? OR r.resident_id LIKE ?) LIMIT 8");
$s = "%$q%";
$stmt->execute([$s,$s,$s]);
echo json_encode($stmt->fetchAll());

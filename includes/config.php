<?php
// ============================================================
// DATABASE CONFIGURATION
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barangay_san_marino');

define('SITE_NAME', 'Barangay Nangka');
define('SITE_URL', 'http://localhost/barangay');
define('BARANGAY_CAPTAIN', 'Hon. Roberto Reyes');
define('MUNICIPALITY', 'City of Marikina');
define('PROVINCE', 'Metro Marikina');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function hasRole($roles) {
    if (!isLoggedIn()) return false;
    if (is_string($roles)) $roles = [$roles];
    return in_array($_SESSION['role'], $roles);
}

function requireRole($roles) {
    requireLogin();
    if (!hasRole($roles)) {
        header('Location: ' . SITE_URL . '/dashboard.php?error=unauthorized');
        exit;
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateRequestNo($prefix, $table, $column, $pdo) {
    $year = date('Y');
    $stmt = $pdo->query("SELECT COUNT(*) FROM $table WHERE $column LIKE '$prefix-$year-%'");
    $count = $stmt->fetchColumn() + 1;
    return $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
}

function formatDate($date) {
    if (!$date) return 'N/A';
    return date('F d, Y', strtotime($date));
}

function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

function getAge($birthdate) {
    return date_diff(date_create($birthdate), date_create('today'))->y;
}
?>

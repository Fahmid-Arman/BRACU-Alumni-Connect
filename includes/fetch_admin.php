<?php
require_once(__DIR__ . '/auth.php');
require_role('admin');
require_once(__DIR__ . '/../config/DBconnect.php');

$admin_data = [];
$admin_user_id = (int) current_user_id();

// Fetch admin data
$sql = "SELECT first_name, last_name, username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $admin_data = $row;
}
?>

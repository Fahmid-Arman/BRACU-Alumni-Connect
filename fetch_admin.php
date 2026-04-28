<?php
// Assuming you're fetching data for the logged-in admin
require_once('DBconnect.php');

$admin_data = [];

// Fetch admin data
$sql = "SELECT first_name, last_name, username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // Get the logged-in admin's user_id
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $admin_data = $row;
}
?>

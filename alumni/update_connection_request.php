<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('alumni');
require_once(__DIR__ . '/../config/DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('/alumni/connection_requests.php?error=invalid_request');
}

require_valid_csrf_token();

$request_id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
$action = trim((string) ($_POST['action'] ?? ''));

if ($request_id === false || $request_id === null) {
    redirect_to('/alumni/connection_requests.php?error=invalid_request_id');
}

if (!in_array($action, ['accepted', 'rejected'], true)) {
    redirect_to('/alumni/connection_requests.php?error=invalid_action');
}

$alumni_id = (int) current_user_id();
$sql = "UPDATE connection_requests
        SET status = ?
        WHERE request_id = ? AND alumni_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    redirect_to('/alumni/connection_requests.php?error=update_failed');
}

$stmt->bind_param("sii", $action, $request_id, $alumni_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $stmt->close();
    redirect_to('/alumni/connection_requests.php?status=' . rawurlencode($action));
}

$stmt->close();
redirect_to('/alumni/connection_requests.php?error=request_not_found');
?>

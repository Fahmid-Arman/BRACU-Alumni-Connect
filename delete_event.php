<?php
require_once('auth.php');
require_role('admin');
require_once('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('admin_home.php?event_error=invalid_request');
}

require_valid_csrf_token();

$event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);

if ($event_id === false || $event_id === null) {
    redirect_to('admin_home.php?event_error=invalid_event');
}

$sql = "DELETE FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    redirect_to('admin_home.php?event_error=delete_failed');
}

$stmt->bind_param("i", $event_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $stmt->close();
    redirect_to('admin_home.php?event_status=deleted');
}

$stmt->close();
redirect_to('admin_home.php?event_error=event_not_found');
?>

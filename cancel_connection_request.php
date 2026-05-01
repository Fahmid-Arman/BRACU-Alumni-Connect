<?php
require_once('auth.php');
require_role('student');
require_once('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('sent_requests.php?error=invalid_request');
}

require_valid_csrf_token();

$request_id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);

if ($request_id === false || $request_id === null) {
    redirect_to('sent_requests.php?error=invalid_request_id');
}

$student_id = (int) current_user_id();
$sql = "UPDATE connection_requests
        SET status = 'cancelled'
        WHERE request_id = ? AND student_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    redirect_to('sent_requests.php?error=cancel_failed');
}

$stmt->bind_param("ii", $request_id, $student_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $stmt->close();
    redirect_to('sent_requests.php?status=cancelled');
}

$stmt->close();
redirect_to('sent_requests.php?error=request_not_found');
?>

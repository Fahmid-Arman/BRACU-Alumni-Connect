<?php
require_once('auth.php');
require_role('student');
require_once('DBconnect.php');

function connection_request_return_path($path)
{
    $path = ltrim(trim((string) $path), '/');

    if ($path === '') {
        return 'student_search.php';
    }

    if (preg_match('/^alumni_list\.php(?:\?[A-Za-z0-9_=&%+\-\.]*)?$/', $path)) {
        return $path;
    }

    if (preg_match('/^view_alumni_profile\.php\?user_id=\d+(?:&[A-Za-z0-9_=&%+\-\.]*)?$/', $path)) {
        return $path;
    }

    return 'student_search.php';
}

function connection_request_redirect_url($path, array $params)
{
    $parts = parse_url($path);
    $base = $parts['path'] ?? 'student_search.php';
    $query = [];

    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }

    foreach ($params as $key => $value) {
        $query[$key] = $value;
    }

    return $base . (!empty($query) ? '?' . http_build_query($query) : '');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('student_search.php');
}

require_valid_csrf_token();

$return_to = connection_request_return_path($_POST['return_to'] ?? '');
$student_id = (int) current_user_id();
$alumni_id = filter_input(INPUT_POST, 'alumni_id', FILTER_VALIDATE_INT);
$message = trim((string) ($_POST['message'] ?? ''));

if ($alumni_id === false || $alumni_id === null) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'invalid_target']));
}

if ($student_id === (int) $alumni_id) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'invalid_target']));
}

if (mb_strlen($message) > 500) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'message_too_long']));
}

$user_sql = "SELECT user_id FROM users WHERE user_id = ? AND role = 'alumni' LIMIT 1";
$user_stmt = $conn->prepare($user_sql);

if (!$user_stmt) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'send_failed']));
}

$user_stmt->bind_param("i", $alumni_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$target_exists = $user_result && mysqli_num_rows($user_result) === 1;
$user_stmt->close();

if (!$target_exists) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'invalid_target']));
}

$duplicate_sql = "SELECT request_id
                  FROM connection_requests
                  WHERE student_id = ? AND alumni_id = ? AND status = 'pending'
                  LIMIT 1";
$duplicate_stmt = $conn->prepare($duplicate_sql);

if (!$duplicate_stmt) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'send_failed']));
}

$duplicate_stmt->bind_param("ii", $student_id, $alumni_id);
$duplicate_stmt->execute();
$duplicate_result = $duplicate_stmt->get_result();
$has_pending_request = $duplicate_result && mysqli_num_rows($duplicate_result) > 0;
$duplicate_stmt->close();

if ($has_pending_request) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'duplicate_pending']));
}

$insert_sql = "INSERT INTO connection_requests (student_id, alumni_id, message, status)
               VALUES (?, ?, ?, 'pending')";
$insert_stmt = $conn->prepare($insert_sql);

if (!$insert_stmt) {
    redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'send_failed']));
}

$insert_stmt->bind_param("iis", $student_id, $alumni_id, $message);

if ($insert_stmt->execute()) {
    $insert_stmt->close();
    redirect_to(connection_request_redirect_url($return_to, ['request_status' => 'sent']));
}

$insert_stmt->close();
redirect_to(connection_request_redirect_url($return_to, ['request_error' => 'send_failed']));
?>

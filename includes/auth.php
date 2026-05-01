<?php
// Centralized session bootstrap keeps cookie flags consistent across all protected pages.
function start_app_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        session_start();
    }
}

function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function display_value($value, $fallback = 'Not provided')
{
    $value = trim((string) ($value ?? ''));

    if ($value === '' || strcasecmp($value, 'Not Provided') === 0 || strcasecmp($value, 'Not Set') === 0) {
        return $fallback;
    }

    return $value;
}

function public_file_path($path)
{
    $path = trim((string) ($path ?? ''));

    if ($path === '' || strcasecmp($path, 'Not Provided') === 0 || strcasecmp($path, 'Not Set') === 0) {
        return '';
    }

    if (preg_match('#^https?://#i', $path) || strpos($path, '/') === 0) {
        return $path;
    }

    return '/' . ltrim($path, '/');
}

// These helpers keep request-state wording consistent across search, profile, and request pages.
function connection_request_status_label($status)
{
    $status = trim((string) $status);

    if ($status === 'cancelled') {
        return 'Cancelled';
    }

    return ucfirst($status);
}

function connection_request_status_note($status)
{
    switch (trim((string) $status)) {
        case 'pending':
            return 'Pending request already sent';
        case 'accepted':
            return 'Accepted connection';
        case 'rejected':
            return 'Rejected request';
        case 'cancelled':
            return 'Cancelled request';
        default:
            return '';
    }
}

function can_send_connection_request($status)
{
    return !in_array(trim((string) $status), ['pending', 'accepted'], true);
}

function redirect_to($location)
{
    header('Location: ' . $location);
    exit();
}

function csrf_token()
{
    start_app_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_token_is_valid($token)
{
    start_app_session();

    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_valid_csrf_token()
{
    $token = $_POST['csrf_token'] ?? '';

    if (!csrf_token_is_valid($token)) {
        http_response_code(400);
        exit('Invalid request. Please refresh the page and try again.');
    }
}

function require_login()
{
    start_app_session();

    if (empty($_SESSION['user_id']) || empty($_SESSION['username']) || empty($_SESSION['role'])) {
        redirect_to('/auth/index.php');
    }
}

function require_role($role)
{
    require_login();

    if (($_SESSION['role'] ?? '') !== $role) {
        redirect_to('/auth/index.php');
    }
}

function require_any_role(array $roles)
{
    require_login();

    if (!in_array(current_user_role(), $roles, true)) {
        redirect_to('/auth/index.php');
    }
}

function current_user_id()
{
    start_app_session();
    return $_SESSION['user_id'] ?? null;
}

function current_username()
{
    start_app_session();
    return $_SESSION['username'] ?? null;
}

function current_user_role()
{
    start_app_session();
    return $_SESSION['role'] ?? null;
}

// Small routing helpers avoid hardcoding role destinations throughout the app.
function home_path_for_role($role = null)
{
    $role = $role ?: current_user_role();

    if ($role === 'student') {
        return '/student/student_home.php';
    }

    if ($role === 'alumni') {
        return '/alumni/alumni_home.php';
    }

    return '/admin/admin_home.php';
}

function profile_path_for_role($role = null)
{
    $role = $role ?: current_user_role();

    if ($role === 'student') {
        return '/student/student_profile.php';
    }

    if ($role === 'alumni') {
        return '/alumni/alumni_profile.php';
    }

    return '/admin/admin_home.php';
}

function search_path_for_role($role = null)
{
    $role = $role ?: current_user_role();

    if ($role === 'student') {
        return '/student/student_search.php';
    }

    if ($role === 'alumni') {
        return '/alumni/alumni_search.php';
    }

    return '/admin/admin_home.php';
}

function normalize_event_datetime($value)
{
    $value = trim((string) $value);
    $date_time = DateTime::createFromFormat('Y-m-d\TH:i', $value);

    if (!$date_time || $date_time->format('Y-m-d\TH:i') !== $value) {
        return false;
    }

    return $date_time->format('Y-m-d H:i:s');
}
?>

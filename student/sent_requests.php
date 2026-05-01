<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('student');
require_once(__DIR__ . '/../config/DBconnect.php');

$status_messages = [
    'cancelled' => 'Pending request cancelled successfully.',
];
$error_messages = [
    'invalid_request' => 'Please use the actions on this page to manage requests.',
    'invalid_request_id' => 'Please choose a valid request.',
    'request_not_found' => 'That pending request is no longer available.',
    'cancel_failed' => 'The request could not be cancelled. Please try again.',
];

$page_message = '';
if (isset($_GET['status'])) {
    $page_message = $status_messages[$_GET['status']] ?? '';
} elseif (isset($_GET['error'])) {
    $page_message = $error_messages[$_GET['error']] ?? 'An unexpected request management error occurred.';
}

$student_id = (int) current_user_id();
$requests = [];
$query_error = '';

$sql = "SELECT cr.request_id, cr.message, cr.status, cr.created_at, cr.updated_at,
               u.user_id AS alumni_user_id, u.first_name, u.last_name, u.username,
               a.company_name, a.role_title
        FROM connection_requests cr
        JOIN users u ON cr.alumni_id = u.user_id
        LEFT JOIN alumni a ON a.user_id = u.user_id
        WHERE cr.student_id = ?
        ORDER BY FIELD(cr.status, 'pending', 'accepted', 'rejected', 'cancelled'), cr.updated_at DESC, cr.created_at DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $query_error = 'We could not load your sent requests right now.';
} else {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = $row;
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sent Requests • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_search_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <header class="glass hdr">
        <div class="title">Sent Requests</div>
        <nav class="links">
            <a href="/student/student_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
            <a href="/student/student_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
            <a href="/student/student_search.php"><i class='bx bx-search'></i><span>Search</span></a>
            <a href="/shared/inbox.php"><i class='bx bxs-inbox'></i><span>Inbox</span></a>
            <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <h3>Mentorship and Connection Requests</h3>
            <p class="search-note">Track the request lifecycle across pending, accepted, rejected, and cancelled requests.</p>

            <?php if ($page_message): ?>
                <p class="status-banner"><?php echo e($page_message); ?></p>
            <?php endif; ?>

            <?php if ($query_error): ?>
                <div class="no-results">
                    <p><?php echo e($query_error); ?></p>
                </div>
            <?php elseif (!empty($requests)): ?>
                <div class="search-results">
                    <?php foreach ($requests as $request): ?>
                        <div class="search-result-item">
                            <h4><?php echo e($request['first_name'] . ' ' . $request['last_name']); ?></h4>
                            <p class="result-handle">@<?php echo e($request['username']); ?></p>
                            <span><strong>Company:</strong> <?php echo e(display_value($request['company_name'])); ?></span>
                            <span><strong>Role Title:</strong> <?php echo e(display_value($request['role_title'])); ?></span>
                            <span><strong>Status:</strong> <span class="request-status request-status-<?php echo e($request['status']); ?>"><?php echo e(connection_request_status_label($request['status'])); ?></span></span>
                            <span><strong>Summary:</strong> <?php echo e(connection_request_status_note($request['status'])); ?></span>
                            <span><strong>Sent:</strong> <?php echo e(date('d M Y, h:i A', strtotime($request['created_at']))); ?></span>
                            <span><strong>Updated:</strong> <?php echo e(date('d M Y, h:i A', strtotime($request['updated_at']))); ?></span>
                            <div class="request-message-box">
                                <strong>Message:</strong>
                                <p class="request-message"><?php echo e(display_value($request['message'], 'No message included.')); ?></p>
                            </div>
                            <div class="result-actions">
                                <a href="/shared/view_alumni_profile.php?user_id=<?php echo e($request['alumni_user_id']); ?>" class="btn">View Profile</a>
                                <a href="/shared/inbox.php?to=<?php echo e($request['alumni_user_id']); ?>" class="btn">Message</a>
                                <?php if ($request['status'] === 'pending'): ?>
                                    <form method="POST" action="/student/cancel_connection_request.php" class="inline-action-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                        <input type="hidden" name="request_id" value="<?php echo e($request['request_id']); ?>">
                                        <button type="submit" class="btn btn-secondary">Cancel Pending Request</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p>You have not sent any connection requests yet.</p>
                    <p>Use the alumni discovery flow to send mentorship requests to alumni.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>
</body>
</html>

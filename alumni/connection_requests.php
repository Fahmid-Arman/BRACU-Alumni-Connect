<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('alumni');
require_once(__DIR__ . '/../config/DBconnect.php');

$status_messages = [
    'accepted' => 'Connection request accepted.',
    'rejected' => 'Connection request rejected.',
];
$error_messages = [
    'invalid_request' => 'Please use the actions on this page to respond to requests.',
    'invalid_action' => 'Please choose a valid response action.',
    'invalid_request_id' => 'Please choose a valid request.',
    'request_not_found' => 'That request is no longer available.',
    'update_failed' => 'The request could not be updated. Please try again.',
];

$page_message = '';
if (isset($_GET['status'])) {
    $page_message = $status_messages[$_GET['status']] ?? '';
} elseif (isset($_GET['error'])) {
    $page_message = $error_messages[$_GET['error']] ?? 'An unexpected request management error occurred.';
}

$alumni_id = (int) current_user_id();
$requests = [];
$query_error = '';

$sql = "SELECT cr.request_id, cr.message, cr.status, cr.created_at, cr.updated_at,
               u.user_id AS student_user_id, u.first_name, u.last_name, u.username,
               s.programme, s.expertise
        FROM connection_requests cr
        JOIN users u ON cr.student_id = u.user_id
        LEFT JOIN students s ON s.user_id = u.user_id
        WHERE cr.alumni_id = ?
        ORDER BY FIELD(cr.status, 'pending', 'accepted', 'rejected', 'cancelled'), cr.updated_at DESC, cr.created_at DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $query_error = 'We could not load your connection requests right now.';
} else {
    $stmt->bind_param("i", $alumni_id);
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
  <title>Connection Requests • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_search_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <header class="glass hdr">
        <div class="title">Connection Requests</div>
        <nav class="links">
            <a href="/alumni/alumni_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
            <a href="/alumni/alumni_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
            <a href="/shared/inbox.php"><i class='bx bxs-inbox'></i><span>Inbox</span></a>
            <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <h3>Received Requests</h3>
            <p class="search-note">Review mentorship and connection requests sent by current students.</p>

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
                            <span><strong>Programme:</strong> <?php echo e(display_value($request['programme'])); ?></span>
                            <span><strong>Expertise:</strong> <?php echo e(display_value($request['expertise'])); ?></span>
                            <span><strong>Status:</strong> <span class="request-status request-status-<?php echo e($request['status']); ?>"><?php echo e(connection_request_status_label($request['status'])); ?></span></span>
                            <span><strong>Summary:</strong> <?php echo e(connection_request_status_note($request['status'])); ?></span>
                            <span><strong>Sent:</strong> <?php echo e(date('d M Y, h:i A', strtotime($request['created_at']))); ?></span>
                            <div class="request-message-box">
                                <strong>Message:</strong>
                                <p class="request-message"><?php echo e(display_value($request['message'], 'No message included.')); ?></p>
                            </div>
                            <div class="result-actions">
                                <a href="/shared/view_student_profile.php?user_id=<?php echo e($request['student_user_id']); ?>" class="btn">View Profile</a>
                                <a href="/shared/inbox.php?to=<?php echo e($request['student_user_id']); ?>" class="btn">Message</a>
                                <?php if ($request['status'] === 'pending'): ?>
                                    <form method="POST" action="/alumni/update_connection_request.php" class="inline-action-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                        <input type="hidden" name="request_id" value="<?php echo e($request['request_id']); ?>">
                                        <input type="hidden" name="action" value="accepted">
                                        <button type="submit" class="btn">Accept</button>
                                    </form>
                                    <form method="POST" action="/alumni/update_connection_request.php" class="inline-action-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                        <input type="hidden" name="request_id" value="<?php echo e($request['request_id']); ?>">
                                        <input type="hidden" name="action" value="rejected">
                                        <button type="submit" class="btn btn-secondary">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p>No connection requests have arrived yet.</p>
                    <p>When students reach out from alumni discovery pages, their requests will appear here.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>
</body>
</html>

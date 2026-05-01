<?php
require_once('auth.php');
require_role('student');
require_once('DBconnect.php');

$company_name = trim($_GET['company_name'] ?? ($_GET['search_query'] ?? ''));
$role_title = trim($_GET['role_title'] ?? '');
$degree_programme = trim($_GET['degree_programme'] ?? '');
$field_of_study = trim($_GET['field_of_study'] ?? '');
$current_country = trim($_GET['current_country'] ?? '');
$type = trim($_GET['type'] ?? '');
$search_results = [];
$search_error = '';
$applied_filters = [];
$request_status_messages = [
    'sent' => 'Connection request sent successfully.',
];
$request_error_messages = [
    'invalid_target' => 'Please choose a valid alumni profile for your request.',
    'duplicate_pending' => 'You already have a pending request for this alumni.',
    'message_too_long' => 'Please keep your request note within 500 characters.',
    'send_failed' => 'Your connection request could not be sent. Please try again.',
];
$request_feedback = '';
$latest_request_map = [];

if (isset($_GET['request_status'])) {
    $request_feedback = $request_status_messages[$_GET['request_status']] ?? '';
} elseif (isset($_GET['request_error'])) {
    $request_feedback = $request_error_messages[$_GET['request_error']] ?? 'An unexpected request error occurred.';
}

if ($type !== '' && !in_array($type, ['higher studies', 'corporate', 'self employed'], true)) {
    $search_error = 'Please choose a valid alumni type.';
} else {
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.username, a.company_name, a.role_title, a.degree_programme, a.field_of_study, a.current_country, a.type, a.university, a.location
            FROM alumni a
            JOIN users u ON a.user_id = u.user_id";
    $conditions = [];
    $params = [];
    $types = '';

    if ($company_name !== '') {
        $conditions[] = "a.company_name LIKE ?";
        $params[] = '%' . $company_name . '%';
        $types .= 's';
        $applied_filters[] = 'Company: ' . $company_name;
    }

    if ($role_title !== '') {
        $conditions[] = "a.role_title LIKE ?";
        $params[] = '%' . $role_title . '%';
        $types .= 's';
        $applied_filters[] = 'Role: ' . $role_title;
    }

    if ($degree_programme !== '') {
        $conditions[] = "a.degree_programme LIKE ?";
        $params[] = '%' . $degree_programme . '%';
        $types .= 's';
        $applied_filters[] = 'Degree Programme: ' . $degree_programme;
    }

    if ($field_of_study !== '') {
        $conditions[] = "a.field_of_study LIKE ?";
        $params[] = '%' . $field_of_study . '%';
        $types .= 's';
        $applied_filters[] = 'Field: ' . $field_of_study;
    }

    if ($current_country !== '') {
        $conditions[] = "a.current_country LIKE ?";
        $params[] = '%' . $current_country . '%';
        $types .= 's';
        $applied_filters[] = 'Country: ' . $current_country;
    }

    if ($type !== '') {
        $conditions[] = "a.type = ?";
        $params[] = $type;
        $types .= 's';
        $applied_filters[] = 'Type: ' . $type;
    }

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY u.first_name ASC, u.last_name ASC LIMIT 50';
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
        }

        $stmt->close();
    }
}

if (!empty($search_results)) {
    $alumni_ids = array_map('intval', array_column($search_results, 'user_id'));
    $student_id = (int) current_user_id();
    $placeholders = implode(',', array_fill(0, count($alumni_ids), '?'));
    $status_sql = "SELECT request_id, alumni_id, status, created_at
                   FROM connection_requests
                   WHERE student_id = ? AND alumni_id IN ($placeholders)
                   ORDER BY created_at DESC, request_id DESC";
    $status_stmt = $conn->prepare($status_sql);

    if ($status_stmt) {
        $bind_types = 'i' . str_repeat('i', count($alumni_ids));
        $bind_values = array_merge([$student_id], $alumni_ids);
        $status_stmt->bind_param($bind_types, ...$bind_values);
        $status_stmt->execute();
        $status_result = $status_stmt->get_result();

        if ($status_result) {
            while ($status_row = mysqli_fetch_assoc($status_result)) {
                $alumni_key = (int) $status_row['alumni_id'];
                if (!isset($latest_request_map[$alumni_key])) {
                    $latest_request_map[$alumni_key] = $status_row;
                }
            }
        }

        $status_stmt->close();
    }
}

$results_heading = !empty($applied_filters) ? 'Filtered Alumni Results' : 'Browse Alumni Network';
$results_summary = !empty($applied_filters)
    ? 'Showing up to 50 alumni matching: ' . implode(' | ', $applied_filters)
    : 'Showing up to 50 alumni profiles from the current network.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Alumni Search Results • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_search_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <header class="glass hdr">
        <div class="title">Alumni Search Results</div>
        <nav class="links">
            <a href="student_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
            <a href="student_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
            <a href="student_search.php"><i class='bx bx-arrow-back'></i><span>Back to Search</span></a>
            <a href="logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <h3><?php echo e($results_heading); ?></h3>
            <p class="search-note"><?php echo e($results_summary); ?></p>
            <?php if ($request_feedback): ?>
                <p class="status-banner"><?php echo e($request_feedback); ?></p>
            <?php endif; ?>

            <?php if ($search_error): ?>
                <div class="no-results">
                    <p><?php echo e($search_error); ?></p>
                </div>
            <?php elseif (!empty($search_results)): ?>
                <div class="search-results">
                    <?php foreach ($search_results as $row): ?>
                        <?php
                        $latest_request = $latest_request_map[(int) $row['user_id']] ?? null;
                        $latest_request_status = $latest_request['status'] ?? '';
                        $latest_request_note = connection_request_status_note($latest_request_status);
                        $show_connect_button = can_send_connection_request($latest_request_status);
                        $connect_label = $latest_request ? 'Connect Again' : 'Connect';
                        ?>
                        <div class="search-result-item">
                            <h4><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></h4>
                            <p class="result-handle">@<?php echo e($row['username']); ?></p>
                            <span><strong>Company:</strong> <?php echo e($row['company_name']); ?></span>
                            <span><strong>Role:</strong> <?php echo e($row['role_title']); ?></span>
                            <span><strong>Degree Programme:</strong> <?php echo e($row['degree_programme']); ?></span>
                            <span><strong>Field of Study:</strong> <?php echo e($row['field_of_study']); ?></span>
                            <span><strong>Current Country:</strong> <?php echo e($row['current_country']); ?></span>
                            <span><strong>Type:</strong> <?php echo e($row['type']); ?></span>
                            <span><strong>University:</strong> <?php echo e($row['university']); ?></span>
                            <span><strong>Location:</strong> <?php echo e($row['location']); ?></span>
                            <?php if ($latest_request): ?>
                                <span><strong>Latest Request:</strong> <span class="request-status request-status-<?php echo e($latest_request_status); ?>"><?php echo e(connection_request_status_label($latest_request_status)); ?></span></span>
                                <span><strong>Connection:</strong> <?php echo e($latest_request_note); ?></span>
                            <?php endif; ?>
                            <div class="result-actions">
                                <a href="view_alumni_profile.php?user_id=<?php echo e($row['user_id']); ?>" class="btn">View Profile</a>
                                <a href="inbox.php?to=<?php echo e($row['user_id']); ?>" class="btn">Message</a>
                                <?php if ($show_connect_button): ?>
                                    <form method="POST" action="send_connection_request.php" class="inline-action-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                        <input type="hidden" name="alumni_id" value="<?php echo e($row['user_id']); ?>">
                                        <input type="hidden" name="message" value="">
                                        <input type="hidden" name="return_to" value="<?php echo e($_SERVER['REQUEST_URI'] ?? 'alumni_list.php'); ?>">
                                        <button type="submit" class="btn"><?php echo e($connect_label); ?></button>
                                    </form>
                                <?php elseif ($latest_request_note): ?>
                                    <span class="request-note"><?php echo e($latest_request_note); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p>No alumni matched your current filters.</p>
                    <p>Try broadening the search criteria from the student discovery page.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>
</body>
</html>

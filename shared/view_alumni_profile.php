<?php
require_once(__DIR__ . '/../includes/auth.php');
require_any_role(['student', 'admin']);
require_once(__DIR__ . '/../config/DBconnect.php');

$viewer_role = current_user_role();
$back_path = $viewer_role === 'student' ? '/student/student_search.php' : '/admin/admin_home.php';
$back_label = $viewer_role === 'student' ? 'Back to Search' : 'Dashboard';
$profile_error = '';
$alumni_profile = null;
$request_feedback_messages = [
    'sent' => 'Connection request sent successfully.',
];
$request_error_messages = [
    'invalid_target' => 'Please choose a valid alumni profile for your request.',
    'duplicate_pending' => 'You already have a pending request for this alumni.',
    'message_too_long' => 'Please keep your request note within 500 characters.',
    'send_failed' => 'Your connection request could not be sent. Please try again.',
];
$request_feedback = '';
$latest_request = null;
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

if (isset($_GET['request_status'])) {
    $request_feedback = $request_feedback_messages[$_GET['request_status']] ?? '';
} elseif (isset($_GET['request_error'])) {
    $request_feedback = $request_error_messages[$_GET['request_error']] ?? 'An unexpected request error occurred.';
}

if ($user_id === false || $user_id === null) {
    $profile_error = 'Please choose a valid alumni profile to view.';
} else {
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.username, u.role,
                   a.github, a.linkedin, a.sex, a.city, a.country, a.zip_code,
                   a.type, a.thesis, a.university, a.current_country,
                   a.degree_programme, a.field_of_study, a.company_name, a.role_title,
                   a.employment_start_date, a.location, a.business_name, a.business_theme
            FROM users u
            JOIN alumni a ON u.user_id = a.user_id
            WHERE u.user_id = ? AND u.role = 'alumni'
            LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $profile_error = 'We could not load this alumni profile right now.';
    } else {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) === 1) {
            $alumni_profile = mysqli_fetch_assoc($result);
        } else {
            $profile_error = 'The requested alumni profile is not available.';
        }

        $stmt->close();
    }
}

$github_url = $alumni_profile && filter_var($alumni_profile['github'], FILTER_VALIDATE_URL) ? $alumni_profile['github'] : '';
$linkedin_url = $alumni_profile && filter_var($alumni_profile['linkedin'], FILTER_VALIDATE_URL) ? $alumni_profile['linkedin'] : '';
$address_parts = [];
$latest_request_status = '';
$latest_request_note = '';
$can_send_request = true;

if ($alumni_profile && $viewer_role === 'student') {
    $request_sql = "SELECT request_id, status, message, created_at
                    FROM connection_requests
                    WHERE student_id = ? AND alumni_id = ?
                    ORDER BY created_at DESC, request_id DESC
                    LIMIT 1";
    $request_stmt = $conn->prepare($request_sql);

    if ($request_stmt) {
        $student_id = (int) current_user_id();
        $request_stmt->bind_param("ii", $student_id, $user_id);
        $request_stmt->execute();
        $request_result = $request_stmt->get_result();
        if ($request_result && mysqli_num_rows($request_result) === 1) {
            $latest_request = mysqli_fetch_assoc($request_result);
        }
        $request_stmt->close();
    }
}

if ($latest_request) {
    $latest_request_status = $latest_request['status'];
    $latest_request_note = connection_request_status_note($latest_request_status);
    $can_send_request = can_send_connection_request($latest_request_status);
}

if ($alumni_profile) {
    foreach (['city', 'country'] as $field) {
        $value = display_value($alumni_profile[$field], '');
        if ($value !== '') {
            $address_parts[] = $value;
        }
    }

    $zip_value = display_value($alumni_profile['zip_code'], '');
    if ($zip_value !== '') {
        $address_parts[] = $zip_value;
    }
}

$address_display = !empty($address_parts) ? implode(', ', $address_parts) : 'Not provided';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Alumni Profile • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_profile_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Alumni Profile</div>
      <nav class="links">
        <a href="<?php echo e(home_path_for_role()); ?>"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="/shared/inbox.php"><i class='bx bxs-inbox'></i><span>Inbox</span></a>
        <a href="<?php echo e($back_path); ?>"><i class='bx bx-arrow-back'></i><span><?php echo e($back_label); ?></span></a>
        <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <?php if ($profile_error): ?>
          <div class="notice-card">
            <h3>Profile Not Available</h3>
            <p class="notice-text"><?php echo e($profile_error); ?></p>
            <div class="profile-actions">
              <a href="<?php echo e($back_path); ?>" class="btn edit-btn"><?php echo e($back_label); ?></a>
            </div>
          </div>
        <?php else: ?>
          <div class="profile-content">
            <div class="avatar"><i class='bx bxs-user-circle'></i></div>
            <div class="info">
              <h3 class="name"><?php echo e($alumni_profile['first_name'] . ' ' . $alumni_profile['last_name']); ?> <span class="role-pill">Alumni</span></h3>
              <p class="handle">
                @<?php echo e($alumni_profile['username']); ?>
                <?php if ($github_url): ?>
                  <a href="<?php echo e($github_url); ?>" target="_blank" rel="noopener noreferrer"><i class='bx bxl-github'></i> GitHub</a>
                <?php endif; ?>
                <?php if ($linkedin_url): ?>
                  <a href="<?php echo e($linkedin_url); ?>" target="_blank" rel="noopener noreferrer"><i class='bx bxl-linkedin'></i> LinkedIn</a>
                <?php endif; ?>
              </p>

              <div class="grid-info">
                <div class="row"><span>Type</span><strong><?php echo e(display_value($alumni_profile['type'])); ?></strong></div>
                <div class="row"><span>Current Company</span><strong><?php echo e(display_value($alumni_profile['company_name'])); ?></strong></div>
                <div class="row"><span>Role Title</span><strong><?php echo e(display_value($alumni_profile['role_title'])); ?></strong></div>
                <div class="row"><span>Degree Programme</span><strong><?php echo e(display_value($alumni_profile['degree_programme'])); ?></strong></div>
                <div class="row"><span>Field of Study</span><strong><?php echo e(display_value($alumni_profile['field_of_study'])); ?></strong></div>
                <div class="row"><span>University</span><strong><?php echo e(display_value($alumni_profile['university'])); ?></strong></div>
                <div class="row"><span>Current Country</span><strong><?php echo e(display_value($alumni_profile['current_country'])); ?></strong></div>
                <div class="row"><span>Location</span><strong><?php echo e(display_value($alumni_profile['location'])); ?></strong></div>
                <div class="row"><span>Thesis</span><strong><?php echo e(display_value($alumni_profile['thesis'])); ?></strong></div>
                <div class="row"><span>Business Name</span><strong><?php echo e(display_value($alumni_profile['business_name'])); ?></strong></div>
                <div class="row"><span>Business Theme</span><strong><?php echo e(display_value($alumni_profile['business_theme'])); ?></strong></div>
                <div class="row"><span>Address</span><strong><?php echo e($address_display); ?></strong></div>
              </div>

              <?php if ($viewer_role === 'student'): ?>
                <section class="request-panel">
                  <h4>Mentorship / Connection Request</h4>
                  <?php if ($request_feedback): ?>
                    <p class="status-banner"><?php echo e($request_feedback); ?></p>
                  <?php endif; ?>
                  <?php if ($latest_request): ?>
                    <p class="request-note-block">
                      Latest request status:
                      <span class="request-status request-status-<?php echo e($latest_request_status); ?>"><?php echo e(connection_request_status_label($latest_request_status)); ?></span>
                    </p>
                    <p class="request-note-block"><?php echo e($latest_request_note); ?></p>
                  <?php endif; ?>
                  <?php if ($can_send_request): ?>
                    <form method="POST" action="/shared/send_connection_request.php" class="request-panel-form">
                      <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                      <input type="hidden" name="alumni_id" value="<?php echo e($alumni_profile['user_id']); ?>">
                      <input type="hidden" name="return_to" value="<?php echo e($_SERVER['REQUEST_URI'] ?? ('/shared/view_alumni_profile.php?user_id=' . $alumni_profile['user_id'])); ?>">
                      <label for="request_message">Optional note</label>
                      <textarea id="request_message" name="message" maxlength="500" placeholder="Share what kind of mentorship or connection you are hoping for."></textarea>
                      <button type="submit" class="btn edit-btn"><?php echo e($latest_request ? 'Connect Again' : 'Request Mentorship'); ?></button>
                    </form>
                  <?php endif; ?>
                </section>
              <?php endif; ?>
            </div>
          </div>

          <div class="profile-actions">
            <a href="/shared/inbox.php?to=<?php echo e($alumni_profile['user_id']); ?>" class="btn edit-btn">Message</a>
            <a href="<?php echo e($back_path); ?>" class="btn edit-btn"><?php echo e($back_label); ?></a>
          </div>
        <?php endif; ?>
      </section>
    </section>
  </div>
</body>
</html>

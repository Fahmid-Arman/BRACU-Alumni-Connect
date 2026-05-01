<?php
require_once('auth.php');
require_role('admin');
require_once('DBconnect.php');

$status_messages = [
    'deleted' => 'User deleted successfully.',
];
$error_messages = [
    'no_user_id' => 'Please choose a valid user before trying to delete.',
    'cannot_delete_admin' => 'Admin accounts are protected and cannot be deleted.',
    'delete_failed' => 'The user could not be deleted. Please try again.',
    'user_not_found' => 'That user could not be found.',
    'exception' => 'A database error occurred while deleting the user.',
];
$event_status_messages = [
    'added' => 'Event created successfully.',
    'updated' => 'Event updated successfully.',
    'deleted' => 'Event deleted successfully.',
];
$event_error_messages = [
    'invalid_request' => 'Please use the dashboard actions to manage events.',
    'invalid_event' => 'Please choose a valid event.',
    'event_not_found' => 'That event could not be found.',
    'delete_failed' => 'The event could not be deleted. Please try again.',
];

$status_message = '';
$event_message = '';

if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
    $status_message = $status_messages['deleted'];
} elseif (isset($_GET['error'])) {
    $status_message = $error_messages[$_GET['error']] ?? 'An unexpected user management error occurred.';
}

if (isset($_GET['event_status'])) {
    $event_message = $event_status_messages[$_GET['event_status']] ?? 'Event updated.';
} elseif (isset($_GET['event_error'])) {
    $event_message = $event_error_messages[$_GET['event_error']] ?? 'An unexpected event management error occurred.';
}

// Fetch users from the 'users' table
$sql = "SELECT user_id, first_name, last_name, role FROM users ORDER BY first_name ASC, last_name ASC";
$result = mysqli_query($conn, $sql);

// Fetch events from the 'events' table
$events_sql = "SELECT event_id, event_name, event_description, event_date, event_location FROM events ORDER BY event_date ASC";
$events_result = mysqli_query($conn, $events_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Home • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="admin_home_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Admin Dashboard</div>
      <nav class="links">
        <a href="add_event.php"><i class='bx bx-calendar-plus'></i><span>Add Event</span></a>
        <a href="logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="glass card quick-links-card">
      <h3>Quick Links</h3>
      <div class="quick-links-grid">
        <a href="add_event.php" class="quick-link-btn">Add Event</a>
        <a href="#users-section" class="quick-link-btn">Manage Users</a>
        <a href="#events-section" class="quick-link-btn">Manage Events</a>
      </div>
    </section>

    <section class="dash">
      <!-- Left Box: User List -->
      <section class="glass card users-list" id="users-section">
        <div class="section-head">
          <h3>Users</h3>
        </div>
        <?php if ($status_message): ?>
          <p class="status-banner"><?php echo e($status_message); ?></p>
        <?php endif; ?>
        <ul>
          <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($user = mysqli_fetch_assoc($result)): ?>
              <?php
              $view_profile_path = '';
              if ($user['role'] === 'student') {
                  $view_profile_path = 'view_student_profile.php?user_id=' . (int) $user['user_id'];
              } elseif ($user['role'] === 'alumni') {
                  $view_profile_path = 'view_alumni_profile.php?user_id=' . (int) $user['user_id'];
              }
              ?>
              <li class="user-item">
                <div class="user-summary">
                  <div class="user-name"><?php echo e($user['first_name'] . ' ' . $user['last_name']); ?></div>
                  <div class="user-role"><?php echo e(ucfirst($user['role'])); ?></div>
                </div>
                <div class="user-actions">
                  <?php if ($view_profile_path): ?>
                    <a href="<?php echo e($view_profile_path); ?>" class="btn-view">View</a>
                  <?php endif; ?>
                  <?php if ($user['role'] === 'admin'): ?>
                    <span class="tag-protected">Protected</span>
                  <?php else: ?>
                    <form method="POST" action="delete_user.php">
                      <input type="hidden" name="user_id" value="<?php echo e($user['user_id']); ?>">
                      <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                      <button type="submit" class="btn-delete">Delete</button>
                    </form>
                  <?php endif; ?>
                </div>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li class="empty-state">No users found.</li>
          <?php endif; ?>
        </ul>
      </section>

      <!-- Right Box: Upcoming Alumni Events -->
      <section class="glass card events" id="events-section">
        <div class="section-head">
          <h3>Event Management</h3>
          <a href="add_event.php" class="btn-add">Add Event</a>
        </div>
        <?php if ($event_message): ?>
          <p class="status-banner"><?php echo e($event_message); ?></p>
        <?php endif; ?>
        <ul class="elist">
          <?php if ($events_result && mysqli_num_rows($events_result) > 0): ?>
            <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
              <li>
                <div class="etitle"><?php echo e($event['event_name']); ?></div>
                <div class="emeta">
                  <i class='bx bxs-time'></i> <?php echo e(date('d M Y, h:i A', strtotime($event['event_date']))); ?> •
                  <i class='bx bxs-door-open'></i> <?php echo e($event['event_location']); ?>
                </div>
                <p class="edesc"><?php echo e($event['event_description']); ?></p>
                <div class="event-actions">
                  <a href="edit_event.php?event_id=<?php echo e($event['event_id']); ?>" class="btn-edit">Edit</a>
                  <form method="POST" action="delete_event.php">
                    <input type="hidden" name="event_id" value="<?php echo e($event['event_id']); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <button type="submit" class="btn-delete">Delete</button>
                  </form>
                </div>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li class="empty-state">No events have been created yet.</li>
          <?php endif; ?>
        </ul>
      </section>
    </section>
  </div>
</body>
</html>

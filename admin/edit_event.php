<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('admin');
require_once(__DIR__ . '/../config/DBconnect.php');

$message = '';

// Check if event_id is provided
if (!isset($_GET['event_id']) || !filter_var($_GET['event_id'], FILTER_VALIDATE_INT)) {
    redirect_to('/admin/admin_home.php?event_error=invalid_event');
}

$event_id = (int) $_GET['event_id'];

// Fetch the event details from the database
$sql = "SELECT event_name, event_description, event_date, event_location FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    redirect_to('/admin/admin_home.php?event_error=invalid_request');
}

$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    redirect_to('/admin/admin_home.php?event_error=event_not_found');
}

$event = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf_token();

    $event_name = trim($_POST['event_name'] ?? '');
    $event_description = trim($_POST['event_description'] ?? '');
    $event_date_input = trim($_POST['event_date'] ?? '');
    $event_location = trim($_POST['event_location'] ?? '');
    $event_date = normalize_event_datetime($event_date_input);

    $event['event_name'] = $event_name;
    $event['event_description'] = $event_description;
    $event['event_location'] = $event_location;

    if ($event_name === '' || $event_description === '' || $event_location === '' || $event_date === false) {
        $message = 'Please enter a valid event name, description, date, and location.';
    } else {
        $update_sql = "UPDATE events SET event_name = ?, event_description = ?, event_date = ?, event_location = ? WHERE event_id = ?";
        $update_stmt = $conn->prepare($update_sql);

        if (!$update_stmt) {
            $message = 'Unable to update the event right now. Please try again.';
        } else {
            $update_stmt->bind_param("ssssi", $event_name, $event_description, $event_date, $event_location, $event_id);

            if ($update_stmt->execute()) {
                $update_stmt->close();
                redirect_to('/admin/admin_home.php?event_status=updated');
            } else {
                $message = 'Error updating event. Please try again.';
            }

            $update_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Event • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/admin_home_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Edit Event</div>
      <nav class="links">
        <a href="/admin/admin_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="glass card">
      <h3>Edit Alumni Event</h3>
      <?php if ($message): ?>
        <p class="status-banner"><?php echo e($message); ?></p>
      <?php endif; ?>
      <form method="POST" action="/admin/edit_event.php?event_id=<?php echo e($event_id); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" id="event_name" value="<?php echo e($event['event_name']); ?>" required />

        <label for="event_description">Event Description:</label>
        <textarea name="event_description" id="event_description" required><?php echo e($event['event_description']); ?></textarea>

        <label for="event_date">Event Date:</label>
        <input type="datetime-local" name="event_date" id="event_date" value="<?php echo e(date('Y-m-d\TH:i', strtotime($event['event_date']))); ?>" required />

        <label for="event_location">Event Location:</label>
        <input type="text" name="event_location" id="event_location" value="<?php echo e($event['event_location']); ?>" required />

        <button type="submit">Update Event</button>
      </form>
    </section>
  </div>
</body>
</html>

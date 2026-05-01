<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('admin');
require_once(__DIR__ . '/../config/DBconnect.php');

$message = '';
$event_name = '';
$event_description = '';
$event_date_input = '';
$event_location = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf_token();

    $event_name = trim($_POST['event_name'] ?? '');
    $event_description = trim($_POST['event_description'] ?? '');
    $event_date_input = trim($_POST['event_date'] ?? '');
    $event_location = trim($_POST['event_location'] ?? '');
    $event_date = normalize_event_datetime($event_date_input);

    if ($event_name === '' || $event_description === '' || $event_location === '' || $event_date === false) {
        $message = 'Please enter a valid event name, description, date, and location.';
    } else {
        $sql = "INSERT INTO events (event_name, event_description, event_date, event_location)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $message = 'Unable to save the event right now. Please try again.';
        } else {
            $stmt->bind_param("ssss", $event_name, $event_description, $event_date, $event_location);

            if ($stmt->execute()) {
                $stmt->close();
                redirect_to('/admin/admin_home.php?event_status=added');
            } else {
                $message = 'Error adding event. Please try again.';
            }

            $stmt->close();
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
  <title>Add Event • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/admin_home_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Add Event</div>
      <nav class="links">
        <a href="/admin/admin_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="glass card">
      <h3>Add New Alumni Event</h3>
      <?php if ($message): ?>
        <p class="status-banner"><?php echo e($message); ?></p>
      <?php endif; ?>
      <form method="POST" action="/admin/add_event.php">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" id="event_name" value="<?php echo e($event_name); ?>" required />

        <label for="event_description">Event Description:</label>
        <textarea name="event_description" id="event_description" required><?php echo e($event_description); ?></textarea>

        <label for="event_date">Event Date:</label>
        <input type="datetime-local" name="event_date" id="event_date" value="<?php echo e($event_date_input); ?>" required />

        <label for="event_location">Event Location:</label>
        <input type="text" name="event_location" id="event_location" value="<?php echo e($event_location); ?>" required />

        <button type="submit">Add Event</button>
      </form>
    </section>
  </div>
</body>
</html>

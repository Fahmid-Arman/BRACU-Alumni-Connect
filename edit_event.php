<?php
require_once('DBconnect.php');

// Check if event_id is provided
if (!isset($_GET['event_id'])) {
    die('Event ID is required.');
}

$event_id = $_GET['event_id'];

// Fetch the event details from the database
$sql = "SELECT event_name, event_description, event_date, event_location FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Event not found.');
}

$event = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];

    // Update the event details
    $update_sql = "UPDATE events SET event_name = ?, event_description = ?, event_date = ?, event_location = ? WHERE event_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $event_name, $event_description, $event_date, $event_location, $event_id);
    $update_stmt->execute();

    echo "Event updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Event • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="admin_home_style.css" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Edit Event</div>
      <nav class="links">
        <a href="admin_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
      </nav>
    </header>

    <section class="glass card">
      <h3>Edit Alumni Event</h3>
      <form method="POST" action="edit_event.php?event_id=<?php echo $event_id; ?>">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" id="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required />

        <label for="event_description">Event Description:</label>
        <textarea name="event_description" id="event_description" required><?php echo htmlspecialchars($event['event_description']); ?></textarea>

        <label for="event_date">Event Date:</label>
        <input type="datetime-local" name="event_date" id="event_date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required />

        <label for="event_location">Event Location:</label>
        <input type="text" name="event_location" id="event_location" value="<?php echo htmlspecialchars($event['event_location']); ?>" required />

        <button type="submit">Update Event</button>
      </form>
    </section>
  </div>
</body>
</html>

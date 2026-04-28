<?php
require_once('DBconnect.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];

    // Insert event into the events table
    $sql = "INSERT INTO events (event_name, event_description, event_date, event_location)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $event_name, $event_description, $event_date, $event_location);

    if ($stmt->execute()) {
        echo "Event added successfully!";
    } else {
        echo "Error adding event: " . $stmt->error;
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
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="admin_home_style.css" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Add Event</div>
      <nav class="links">
        <a href="admin_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
      </nav>
    </header>

    <section class="glass card">
      <h3>Add New Alumni Event</h3>
      <form method="POST" action="add_event.php">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" id="event_name" required />

        <label for="event_description">Event Description:</label>
        <textarea name="event_description" id="event_description" required></textarea>

        <label for="event_date">Event Date:</label>
        <input type="datetime-local" name="event_date" id="event_date" required />

        <label for="event_location">Event Location:</label>
        <input type="text" name="event_location" id="event_location" required />

        <button type="submit">Add Event</button>
      </form>
    </section>
  </div>
</body>
</html>

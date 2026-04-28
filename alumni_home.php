<?php
require_once('fetch_alumni.php'); // Fetch alumni data
require_once('DBconnect.php'); // For DB connection

// Fetch upcoming events from the events table
$sql = "SELECT event_name, event_description, event_date, event_location FROM events ORDER BY event_date ASC LIMIT 5"; // Limit to the first 5 upcoming events
$events_result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Alumni Home • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_home_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Alumni</div>
      <nav class="links">
        <a href="alumni_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
        <a href="inbox.php"><i class='bx bxs-inbox'></i><span>Inbox</span></a>
        <a href="alumni_search.php"><i class='bx bx-search'></i><span>Search</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <div class="avatar"><i class='bx bxs-user-circle'></i></div>
        <div class="info">
          <h3 class="name"><?php echo $alumni_data['first_name'] . ' ' . $alumni_data['last_name']; ?> <span class="role-pill">Alumni</span></h3>
          <p class="handle">@<?php echo $alumni_data['username']; ?></p>
          <div class="grid-info">
            <div class="row"><span>First Name</span><strong><?php echo $alumni_data['first_name']; ?></strong></div>
            <div class="row"><span>Last Name</span><strong><?php echo $alumni_data['last_name']; ?></strong></div>
            <div class="row"><span>Username</span><strong><?php echo $alumni_data['username']; ?></strong></div>
            <div class="row"><span>Type</span><strong><?php echo $alumni_data['type']; ?></strong></div>
          </div>
        </div>
      </section>

      <aside class="glass card events">
        <h3>Upcoming Alumni Events</h3>
        <ul class="elist">
          <?php
          if ($events_result && mysqli_num_rows($events_result) > 0) {
            while ($event = mysqli_fetch_assoc($events_result)) {
              echo "<li>";
              echo "<div class='etitle'>" . htmlspecialchars($event['event_name']) . "</div>";
              echo "<div class='emeta'><i class='bx bxs-time'></i> " . date('d M, h:i A', strtotime($event['event_date'])) . " • ";
              echo "<i class='bx bxs-door-open'></i> " . htmlspecialchars($event['event_location']) . "</div>";
              echo "</li>";
            }
          } else {
            echo "<li>No upcoming events available.</li>";
          }
          ?>
        </ul>
      </aside>
    </section>
  </div>
</body>
</html>

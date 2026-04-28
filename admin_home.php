<?php
require_once('DBconnect.php');

// Fetch users from the 'users' table
$sql = "SELECT user_id, first_name, last_name FROM users";
$result = mysqli_query($conn, $sql);

// Fetch events from the 'events' table
$events_sql = "SELECT event_id, event_name, event_description, event_date, event_location FROM events";
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
        <a href="index.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <!-- Left Box: User List -->
      <section class="glass card users-list">
        <h3>Users</h3>
        <ul>
          <?php while ($user = mysqli_fetch_assoc($result)): ?>
            <li class="user-item">
              <div class="user-name"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></div>
              <form method="POST" action="delete_user.php">
                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                <button type="submit" class="btn-delete">Delete</button>
              </form>
            </li>
          <?php endwhile; ?>
        </ul>
      </section>

      <!-- Right Box: Upcoming Alumni Events -->
      <section class="glass card events">
        <h3>Upcoming Alumni Events</h3>
        <ul class="elist">
          <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
            <li>
              <div class="etitle"><?php echo $event['event_name']; ?></div>
              <div class="emeta">
                <i class='bx bxs-time'></i> <?php echo date('d M, h:i A', strtotime($event['event_date'])); ?> • 
                <i class='bx bxs-door-open'></i> <?php echo $event['event_location']; ?>
              </div>
              <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>" class="btn-edit">Edit</a>
            </li>
          <?php endwhile; ?>
        </ul>
      </section>
    </section>
  </div>
</body>
</html>

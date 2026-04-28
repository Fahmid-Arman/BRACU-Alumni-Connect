<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once('DBconnect.php');

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id']) && isset($_POST['message_content'])) {
    $receiver_id = $_POST['receiver_id'];
    $message_content = $_POST['message_content'];

    // Insert the message into the messages table
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $_SESSION['user_id'], $receiver_id, $message_content);

    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error sending message: " . $stmt->error;
    }
}

// Fetch messages for the logged-in user (receiver)
$sql = "SELECT m.message_id, m.message_content, m.sent_at, u.first_name AS sender_first_name, u.last_name AS sender_last_name
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE m.receiver_id = ? 
        ORDER BY m.sent_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inbox • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_search_style.css" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Inbox</div>
    </header>

    <section class="dash">
      <section class="glass card search-card">
        <h3>Your Inbox</h3>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <ul>
            <?php while ($message = mysqli_fetch_assoc($result)): ?>
              <li>
                <strong><?php echo htmlspecialchars($message['sender_first_name'] . ' ' . $message['sender_last_name']); ?>:</strong>
                <p><?php echo nl2br(htmlspecialchars($message['message_content'])); ?></p>
                <small>Sent at: <?php echo date('d M, Y h:i A', strtotime($message['sent_at'])); ?></small>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else: ?>
          <p>No messages found in your inbox.</p>
        <?php endif; ?>

        <h3>Send a Message</h3>
        <form method="POST" action="inbox.php">
          <label for="receiver_id">Receiver User ID:</label>
          <input type="number" name="receiver_id" id="receiver_id" required />

          <label for="message_content">Message:</label>
          <textarea name="message_content" id="message_content" required></textarea>

          <button type="submit">Send Message</button>
        </form>
      </section>
    </section>
  </div>
</body>
</html>

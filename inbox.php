<?php
require_once('auth.php');
require_login();
require_once('DBconnect.php');

function inbox_find_user($conn, $user_id)
{
    $sql = "SELECT user_id, first_name, last_name, username, role FROM users WHERE user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result ? $result->fetch_assoc() : null;
}

$current_role = current_user_role();
$current_user_id = current_user_id();
$status_message = '';
$selected_recipient = null;
$selected_recipient_id = null;
$receiver_input = '';

if (isset($_GET['sent']) && $_GET['sent'] === '1') {
    $status_message = 'Message sent successfully!';
}

if (isset($_GET['to'])) {
    $requested_receiver = filter_var($_GET['to'], FILTER_VALIDATE_INT);

    if ($requested_receiver === false || $requested_receiver <= 0) {
        $status_message = 'Selected recipient is invalid.';
    } elseif ($requested_receiver === $current_user_id) {
        $status_message = 'You cannot send a message to yourself.';
    } else {
        $selected_recipient = inbox_find_user($conn, $requested_receiver);

        if ($selected_recipient) {
            $selected_recipient_id = (int) $selected_recipient['user_id'];
            $receiver_input = (string) $selected_recipient_id;
        } else {
            $status_message = 'Selected recipient was not found.';
        }
    }
}

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id']) && isset($_POST['message_content'])) {
    require_valid_csrf_token();

    $receiver_id = filter_var($_POST['receiver_id'], FILTER_VALIDATE_INT);
    $message_content = trim($_POST['message_content']);
    $receiver_input = trim($_POST['receiver_id']);

    if ($receiver_id === false || $receiver_id <= 0) {
        $status_message = 'Please enter a valid receiver user ID.';
    } elseif ($receiver_id === $current_user_id) {
        $status_message = 'You cannot send a message to yourself.';
    } elseif ($message_content === '') {
        $status_message = 'Please enter a message before sending.';
    } else {
        $selected_recipient = inbox_find_user($conn, $receiver_id);

        if (!$selected_recipient) {
            $status_message = 'The selected recipient does not exist.';
        } else {
            $selected_recipient_id = (int) $selected_recipient['user_id'];

            $sql = "INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $current_user_id, $selected_recipient_id, $message_content);

            if ($stmt->execute()) {
                header('Location: inbox.php?to=' . $selected_recipient_id . '&sent=1');
                exit();
            } else {
                $status_message = 'Error sending message. Please try again.';
            }
        }
    }
}

$sql = "SELECT
            m.message_id,
            m.message_content,
            m.sent_at,
            m.sender_id,
            m.receiver_id,
            sender.first_name AS sender_first_name,
            sender.last_name AS sender_last_name,
            sender.username AS sender_username,
            receiver.first_name AS receiver_first_name,
            receiver.last_name AS receiver_last_name,
            receiver.username AS receiver_username
        FROM messages m
        JOIN users sender ON m.sender_id = sender.user_id
        JOIN users receiver ON m.receiver_id = receiver.user_id
        WHERE (m.sender_id = ? OR m.receiver_id = ?)";

$types = 'ii';
$params = [$current_user_id, $current_user_id];

if ($selected_recipient_id !== null) {
    $sql .= " AND (m.sender_id = ? OR m.receiver_id = ?)";
    $types .= 'ii';
    $params[] = $selected_recipient_id;
    $params[] = $selected_recipient_id;
}

$sql .= " ORDER BY m.sent_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$history_heading = $selected_recipient
    ? 'Conversation with ' . $selected_recipient['first_name'] . ' ' . $selected_recipient['last_name']
    : 'Recent Messages';
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
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Inbox</div>
      <nav class="links">
        <a href="<?php echo e(home_path_for_role($current_role)); ?>"><i class='bx bxs-home'></i><span>Home</span></a>
        <?php if ($current_role !== 'admin'): ?>
          <a href="<?php echo e(profile_path_for_role($current_role)); ?>"><i class='bx bxs-user'></i><span>Profile</span></a>
          <a href="<?php echo e(search_path_for_role($current_role)); ?>"><i class='bx bx-search'></i><span>Search</span></a>
        <?php endif; ?>
        <a href="logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card search-card">
        <h3><?php echo e($history_heading); ?></h3>
        <?php if ($status_message): ?>
          <p class="search-note"><?php echo e($status_message); ?></p>
        <?php endif; ?>
        <?php if ($selected_recipient): ?>
          <div class="prefill-note">
            <strong>Selected recipient:</strong>
            <?php echo e($selected_recipient['first_name'] . ' ' . $selected_recipient['last_name']); ?>
            (<?php echo e($selected_recipient['role']); ?>, @<?php echo e($selected_recipient['username']); ?>)
          </div>
        <?php endif; ?>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <ul class="message-list">
            <?php while ($message_row = mysqli_fetch_assoc($result)): ?>
              <?php
              $is_sent = ((int) $message_row['sender_id'] === $current_user_id);
              $counterpart_name = $is_sent
                  ? $message_row['receiver_first_name'] . ' ' . $message_row['receiver_last_name']
                  : $message_row['sender_first_name'] . ' ' . $message_row['sender_last_name'];
              $direction_label = $is_sent
                  ? 'You to ' . $counterpart_name
                  : $counterpart_name . ' to you';
              ?>
              <li class="message-item">
                <div class="message-meta">
                  <span class="message-direction"><?php echo e($direction_label); ?></span>
                  <small><?php echo e(date('d M, Y h:i A', strtotime($message_row['sent_at']))); ?></small>
                </div>
                <p class="message-body"><?php echo nl2br(e($message_row['message_content'])); ?></p>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else: ?>
          <p>No messages found yet.</p>
        <?php endif; ?>

        <h3>Send a Message</h3>
        <form method="POST" action="inbox.php<?php echo $selected_recipient_id ? '?to=' . e($selected_recipient_id) : ''; ?>" class="message-form">
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
          <label for="receiver_id">Receiver User ID:</label>
          <input type="number" name="receiver_id" id="receiver_id" value="<?php echo e($receiver_input); ?>" required />

          <label for="message_content">Message:</label>
          <textarea name="message_content" id="message_content" required></textarea>

          <button type="submit">Send Message</button>
        </form>
      </section>
    </section>
  </div>
</body>
</html>

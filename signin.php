<?php
require_once('DBconnect.php');
if (isset($_POST['fname']) && isset($_POST['pass'])) {
    $u = $_POST['fname'];
    $p = $_POST['pass'];
    $sql = "SELECT user_id, role FROM users WHERE username = '$u' AND password = '$p' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        session_start();
        $_SESSION['user_id'] = $row['user_id'];
        if ($row['role'] === 'student') { header('Location: student_home.php'); exit; }
        if ($row['role'] === 'alumni')  { header('Location: alumni_home.php'); exit; }
        if ($row['role'] === 'admin')   { header('Location: admin_home.php'); exit; }
        echo 'Unknown role';
        exit;
    } else {
        header('Location: index.php');
    }
}
?>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

require_once('DBconnect.php');

$sql = "SELECT users.first_name, users.last_name, users.username, students.programme, students.expertise, students.cv, students.cgpa, students.email, students.github, students.linkedin, students.sex, students.city, students.country, students.zip_code
        FROM users
        JOIN students ON users.user_id = students.user_id
        WHERE users.user_id = $user_id";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $student_data = mysqli_fetch_assoc($result);
} else {
    echo "No student data found.";
    exit();
}
?>

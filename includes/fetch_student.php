<?php
require_once(__DIR__ . '/auth.php');
require_role('student');
require_once(__DIR__ . '/../config/DBconnect.php');

$user_id = current_user_id();

$sql = "SELECT users.first_name, users.last_name, users.username, students.programme, students.expertise, students.cv, students.cgpa, students.email, students.github, students.linkedin, students.sex, students.city, students.country, students.zip_code
        FROM users
        JOIN students ON users.user_id = students.user_id
        WHERE users.user_id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Unable to load student data.";
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && mysqli_num_rows($result) > 0) {
    $student_data = mysqli_fetch_assoc($result);
} else {
    echo "No student data found.";
    exit();
}
?>

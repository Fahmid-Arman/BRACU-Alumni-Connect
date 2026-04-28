<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

require_once('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $programme = $_POST['programme'];
    $expertise = $_POST['expertise'];
    $cgpa = $_POST['cgpa'];
    $github = $_POST['github'];
    $linkedin = $_POST['linkedin'];
    $sex = $_POST['sex'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $zip_code = $_POST['zip_code'];

    $cv = null;
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["cv"]["name"]);

        if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
            $cv = $target_file;
        }
    }

    $update_user_sql = "UPDATE users SET first_name='$first_name', last_name='$last_name' WHERE user_id=$user_id";

    if (!mysqli_query($conn, $update_user_sql)) {
        die("Error updating users table: " . mysqli_error($conn));
    }

    $update_student_sql = "UPDATE students SET 
        programme='$programme', 
        expertise='$expertise', 
        cgpa='$cgpa', 
        email='$email', 
        github='$github', 
        linkedin='$linkedin', 
        sex='$sex', 
        city='$city', 
        country='$country', 
        zip_code='$zip_code'";

    if ($cv) {
        $update_student_sql .= ", cv='$cv'";
    }

    $update_student_sql .= " WHERE user_id=$user_id";

    if (!mysqli_query($conn, $update_student_sql)) {
        die("Error updating students table: " . mysqli_error($conn));
    }

    header('Location: student_profile.php');
    exit();
}
?>

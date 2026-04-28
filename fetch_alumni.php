<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

require_once('DBconnect.php');

$sql = "SELECT users.first_name, users.last_name, users.username, 
               alumni.github, alumni.linkedin, alumni.sex, alumni.city, alumni.country, alumni.zip_code,
               alumni.type, alumni.thesis, alumni.university, alumni.current_country,
               alumni.degree_programme, alumni.field_of_study, alumni.company_name, alumni.role_title,
               alumni.employment_start_date, alumni.location, alumni.business_name, alumni.business_theme
        FROM users
        JOIN alumni ON users.user_id = alumni.user_id
        WHERE users.user_id = $user_id";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $alumni_data = mysqli_fetch_assoc($result);
} else {
    echo "No alumni data found.";
    exit();
}
?>

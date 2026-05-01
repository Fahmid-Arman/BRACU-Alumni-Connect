<?php
require_once('auth.php');
require_role('alumni');
require_once('DBconnect.php');

$user_id = current_user_id();

$sql = "SELECT users.first_name, users.last_name, users.username, 
               alumni.github, alumni.linkedin, alumni.sex, alumni.city, alumni.country, alumni.zip_code,
               alumni.type, alumni.thesis, alumni.university, alumni.current_country,
               alumni.degree_programme, alumni.field_of_study, alumni.company_name, alumni.role_title,
               alumni.employment_start_date, alumni.location, alumni.business_name, alumni.business_theme
        FROM users
        JOIN alumni ON users.user_id = alumni.user_id
        WHERE users.user_id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Unable to load alumni data.";
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && mysqli_num_rows($result) > 0) {
    $alumni_data = mysqli_fetch_assoc($result);
} else {
    echo "No alumni data found.";
    exit();
}
?>

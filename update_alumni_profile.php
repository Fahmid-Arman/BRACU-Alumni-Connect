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
    $type = $_POST['type'];
    $sex = $_POST['sex'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $zip_code = $_POST['zip_code'];
    $thesis = $_POST['thesis'];
    $current_company = $_POST['current_company'];
    $degree_programme = $_POST['degree_programme'];
    $field_of_study = $_POST['field_of_study'];
    $role_title = $_POST['role_title'];
    $location = $_POST['location'];
    $business_name = $_POST['business_name'];
    $business_theme = $_POST['business_theme'];
    $github = $_POST['github'];
    $linkedin = $_POST['linkedin'];

    $update_user_sql = "UPDATE users SET first_name='$first_name', last_name='$last_name' WHERE user_id=$user_id";

    if (!mysqli_query($conn, $update_user_sql)) {
        die("Error updating users table: " . mysqli_error($conn));
    }

    $update_alumni_sql = "UPDATE alumni SET
        type='$type',
        sex='$sex',
        city='$city',
        country='$country',
        zip_code='$zip_code',
        thesis='$thesis',
        company_name='$current_company',
        degree_programme='$degree_programme',
        field_of_study='$field_of_study',
        role_title='$role_title',
        location='$location',
        business_name='$business_name',
        business_theme='$business_theme',
        github='$github',
        linkedin='$linkedin'
        WHERE user_id=$user_id";

    if (!mysqli_query($conn, $update_alumni_sql)) {
        die("Error updating alumni table: " . mysqli_error($conn));
    }

    header('Location: alumni_profile.php');
    exit();
}
?>

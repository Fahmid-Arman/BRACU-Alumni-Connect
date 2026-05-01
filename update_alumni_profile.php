<?php
require_once('auth.php');
require_role('alumni');
require_once('DBconnect.php');

$user_id = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf_token();

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $type = trim($_POST['type']);
    $sex = trim($_POST['sex']);
    $city = trim($_POST['city']);
    $country = trim($_POST['country']);
    $zip_code = trim($_POST['zip_code']);
    $thesis = trim($_POST['thesis']);
    $current_company = trim($_POST['current_company']);
    $degree_programme = trim($_POST['degree_programme']);
    $field_of_study = trim($_POST['field_of_study']);
    $role_title = trim($_POST['role_title']);
    $location = trim($_POST['location']);
    $business_name = trim($_POST['business_name']);
    $business_theme = trim($_POST['business_theme']);
    $github = trim($_POST['github']);
    $linkedin = trim($_POST['linkedin']);

    if (!in_array($type, ['higher studies', 'corporate', 'self employed'], true) || !in_array($sex, ['male', 'female', 'other'], true)) {
        header('Location: edit_alumni_profile.php?error=invalid_profile_data');
        exit();
    }

    mysqli_begin_transaction($conn);

    $user_stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");
    $user_stmt->bind_param("ssi", $first_name, $last_name, $user_id);

    if (!$user_stmt->execute()) {
        mysqli_rollback($conn);
        die("Error updating users table: " . mysqli_error($conn));
    }

    $alumni_stmt = $conn->prepare("UPDATE alumni SET type = ?, sex = ?, city = ?, country = ?, zip_code = ?, thesis = ?, company_name = ?, degree_programme = ?, field_of_study = ?, role_title = ?, location = ?, business_name = ?, business_theme = ?, github = ?, linkedin = ? WHERE user_id = ?");
    $alumni_stmt->bind_param("sssssssssssssssi", $type, $sex, $city, $country, $zip_code, $thesis, $current_company, $degree_programme, $field_of_study, $role_title, $location, $business_name, $business_theme, $github, $linkedin, $user_id);

    if (!$alumni_stmt->execute()) {
        mysqli_rollback($conn);
        die("Error updating alumni table: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    header('Location: alumni_profile.php');
    exit();
}
?>

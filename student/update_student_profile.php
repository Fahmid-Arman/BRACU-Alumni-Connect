<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('student');
require_once(__DIR__ . '/../config/DBconnect.php');

$user_id = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf_token();

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $programme = trim($_POST['programme']);
    $expertise = trim($_POST['expertise']);
    $cgpa = trim($_POST['cgpa']);
    $github = trim($_POST['github']);
    $linkedin = trim($_POST['linkedin']);
    $sex = trim($_POST['sex']);
    $city = trim($_POST['city']);
    $country = trim($_POST['country']);
    $zip_code = trim($_POST['zip_code']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /student/edit_student_profile.php?error=invalid_email');
        exit();
    }

    if (!is_numeric($cgpa)) {
        header('Location: /student/edit_student_profile.php?error=invalid_cgpa');
        exit();
    }

    if (!in_array($sex, ['male', 'female', 'other'], true)) {
        header('Location: /student/edit_student_profile.php?error=invalid_profile_data');
        exit();
    }

    $cv = null;
    $uploaded_cv_path = null;
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /student/edit_student_profile.php?error=upload_failed');
            exit();
        }

        if ($_FILES['cv']['size'] > 2 * 1024 * 1024) {
            header('Location: /student/edit_student_profile.php?error=cv_too_large');
            exit();
        }

        $extension = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = $finfo ? finfo_file($finfo, $_FILES['cv']['tmp_name']) : '';
        if ($finfo) {
            finfo_close($finfo);
        }

        if ($extension !== 'pdf' || !in_array($mime_type, ['application/pdf', 'application/x-pdf'], true)) {
            header('Location: /student/edit_student_profile.php?error=invalid_cv_type');
            exit();
        }

        $target_dir = dirname(__DIR__) . '/uploads/';
        if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
            header('Location: /student/edit_student_profile.php?error=upload_failed');
            exit();
        }

        $cv = '/uploads/' . bin2hex(random_bytes(16)) . '.pdf';
        $uploaded_cv_path = dirname(__DIR__) . $cv;
        if (!move_uploaded_file($_FILES['cv']['tmp_name'], $uploaded_cv_path)) {
            header('Location: /student/edit_student_profile.php?error=upload_failed');
            exit();
        }
    }

    mysqli_begin_transaction($conn);

    $user_stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");
    $user_stmt->bind_param("ssi", $first_name, $last_name, $user_id);

    if (!$user_stmt->execute()) {
        mysqli_rollback($conn);
        if ($uploaded_cv_path && file_exists($uploaded_cv_path)) {
            unlink($uploaded_cv_path);
        }
        die("Error updating users table: " . mysqli_error($conn));
    }

    if ($cv) {
        $student_stmt = $conn->prepare("UPDATE students SET programme = ?, expertise = ?, cgpa = ?, email = ?, github = ?, linkedin = ?, sex = ?, city = ?, country = ?, zip_code = ?, cv = ? WHERE user_id = ?");
        $student_stmt->bind_param("ssdssssssssi", $programme, $expertise, $cgpa, $email, $github, $linkedin, $sex, $city, $country, $zip_code, $cv, $user_id);
    } else {
        $student_stmt = $conn->prepare("UPDATE students SET programme = ?, expertise = ?, cgpa = ?, email = ?, github = ?, linkedin = ?, sex = ?, city = ?, country = ?, zip_code = ? WHERE user_id = ?");
        $student_stmt->bind_param("ssdsssssssi", $programme, $expertise, $cgpa, $email, $github, $linkedin, $sex, $city, $country, $zip_code, $user_id);
    }

    if (!$student_stmt->execute()) {
        mysqli_rollback($conn);
        if ($uploaded_cv_path && file_exists($uploaded_cv_path)) {
            unlink($uploaded_cv_path);
        }
        die("Error updating students table: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    header('Location: /student/student_profile.php');
    exit();
}
?>

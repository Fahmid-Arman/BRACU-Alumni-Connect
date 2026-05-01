<?php
require_once(__DIR__ . '/../config/DBconnect.php');
require_once(__DIR__ . '/../includes/auth.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf_token();

    $first = trim($_POST['first_name']);
    $last  = trim($_POST['last_name']);
    $user  = trim($_POST['fname']);
    $pass  = $_POST['pass'];
    $cpass = $_POST['cpass'];
    $role  = trim($_POST['role']);
    
    if ($pass !== $cpass) { 
        exit('Passwords do not match.');
    }
    if ($role !== 'student' && $role !== 'alumni') { 
        exit('Invalid role.');
    }

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    if ($hashed_pass === false) {
        exit('Failed to secure password.');
    }
    
    $stmt = mysqli_prepare($conn, "INSERT INTO users (first_name, last_name, username, password, role) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $first, $last, $user, $hashed_pass, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);
        
        if ($role === 'student') {
            $programme = 'Not Set';
            $expertise = 'Not Set';
            $cv = 'Not Provided';
            $cgpa = 0.00;
            $email = 'Not Provided';
            $github = 'Not Provided';
            $linkedin = 'Not Provided';
            $sex = 'other';
            $city = 'Not Set';
            $country = 'Not Set';
            $zip_code = 'Not Set';
            
            $student_stmt = mysqli_prepare($conn, "INSERT INTO students (user_id, programme, expertise, cv, cgpa, email, github, linkedin, sex, city, country, zip_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($student_stmt) {
                mysqli_stmt_bind_param($student_stmt, "isssdsssssss", $user_id, $programme, $expertise, $cv, $cgpa, $email, $github, $linkedin, $sex, $city, $country, $zip_code);
                mysqli_stmt_execute($student_stmt);
                mysqli_stmt_close($student_stmt);
            }
        } elseif ($role === 'alumni') {
            $github = 'Not Provided';
            $linkedin = 'Not Provided';
            $sex = 'other';
            $city = 'Not Set';
            $country = 'Not Set';
            $zip_code = 'Not Set';
            $type = 'higher studies';
            $thesis = 'Not Provided';
            $university = 'Not Set';
            $current_country = 'Not Set';
            $degree_programme = 'Not Set';
            $field_of_study = 'Not Set';
            $company_name = 'Not Set';
            $role_title = 'Not Set';
            $employment_start_date = NULL;
            $location = 'Not Set';
            $business_name = 'Not Set';
            $business_theme = 'Not Set';
            
            $alumni_stmt = mysqli_prepare($conn, "INSERT INTO alumni (user_id, github, linkedin, sex, city, country, zip_code, type, thesis, university, current_country, degree_programme, field_of_study, company_name, role_title, employment_start_date, location, business_name, business_theme) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($alumni_stmt) {
                mysqli_stmt_bind_param($alumni_stmt, "issssssssssssssssss", $user_id, $github, $linkedin, $sex, $city, $country, $zip_code, $type, $thesis, $university, $current_country, $degree_programme, $field_of_study, $company_name, $role_title, $employment_start_date, $location, $business_name, $business_theme);
                mysqli_stmt_execute($alumni_stmt);
                mysqli_stmt_close($alumni_stmt);
            }
        }
        
        mysqli_stmt_close($stmt);
        header('Location: /auth/index.php');
        exit;
    } else {
        mysqli_stmt_close($stmt);
        
        if (mysqli_errno($conn) === 1062) { 
            exit('Username already exists.');
        }
        exit('Database error.');
    }
}
?>

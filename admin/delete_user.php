<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('admin');
require_once(__DIR__ . '/../config/DBconnect.php');

if (isset($_POST['user_id'])) {
    require_valid_csrf_token();

    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($user_id === false) {
        header('Location: /admin/admin_home.php?error=no_user_id');
        exit();
    }

    // Start transaction for data integrity
    mysqli_begin_transaction($conn);
    
    try {
        // Get user role
        $sql = "SELECT role FROM users WHERE user_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $role = $row['role'];

            // Prevent deletion of admin users
            if ($role === 'admin') {
                mysqli_rollback($conn);
                header('Location: /admin/admin_home.php?error=cannot_delete_admin');
                exit();
            }

            // Delete from role-specific table first
            if ($role === 'student') {
                $delete_student_sql = "DELETE FROM students WHERE user_id = ?";
                $delete_student_stmt = $conn->prepare($delete_student_sql);
                $delete_student_stmt->bind_param("i", $user_id);
                $delete_student_stmt->execute();
                $delete_student_stmt->close();
            } elseif ($role === 'alumni') {
                $delete_alumni_sql = "DELETE FROM alumni WHERE user_id = ?";
                $delete_alumni_stmt = $conn->prepare($delete_alumni_sql);
                $delete_alumni_stmt->bind_param("i", $user_id);
                $delete_alumni_stmt->execute();
                $delete_alumni_stmt->close();
            }

            // Delete from users table
            $delete_user_sql = "DELETE FROM users WHERE user_id = ?";
            $delete_user_stmt = $conn->prepare($delete_user_sql);
            $delete_user_stmt->bind_param("i", $user_id);
            
            if ($delete_user_stmt->execute() && $delete_user_stmt->affected_rows > 0) {
                mysqli_commit($conn);
                header('Location: /admin/admin_home.php?deleted=1');
                exit();
            } else {
                mysqli_rollback($conn);
                header('Location: /admin/admin_home.php?error=delete_failed');
                exit();
            }
            
            $delete_user_stmt->close();
        } else {
            mysqli_rollback($conn);
            header('Location: /admin/admin_home.php?error=user_not_found');
            exit();
        }
        
        $stmt->close();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header('Location: /admin/admin_home.php?error=exception');
        exit();
    }
} else {
    header('Location: /admin/admin_home.php?error=no_user_id');
    exit();
}
?>

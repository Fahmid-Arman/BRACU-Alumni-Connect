<?php
session_start();

// Debug session info
echo "<!-- Debug: Session user_id: " . ($_SESSION['user_id'] ?? 'not set') . " -->";
echo "<!-- Debug: Session admin: " . ($_SESSION['admin'] ?? 'not set') . " -->";

// Check admin access - try both methods
if (!isset($_SESSION['admin']) && !isset($_SESSION['user_id'])) {
    echo "<!-- Debug: No admin session found. Redirecting. -->";
    header('Location: index.php');
    exit();
}

require_once('DBconnect.php');

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    echo "<!-- Debug: Received user_id: " . $user_id . " -->";

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
            echo "<!-- Debug: User role: " . $role . " -->";

            // Prevent deletion of admin users
            if ($role === 'admin') {
                echo "<!-- Debug: Cannot delete admin user -->";
                mysqli_rollback($conn);
                header('Location: admin_home.php?error=cannot_delete_admin');
                exit();
            }

            // Delete from role-specific table first
            if ($role === 'student') {
                $delete_student_sql = "DELETE FROM students WHERE user_id = ?";
                $delete_student_stmt = $conn->prepare($delete_student_sql);
                $delete_student_stmt->bind_param("i", $user_id);
                if ($delete_student_stmt->execute()) {
                    echo "<!-- Debug: Student record deleted, affected rows: " . $delete_student_stmt->affected_rows . " -->";
                }
                $delete_student_stmt->close();
            } elseif ($role === 'alumni') {
                $delete_alumni_sql = "DELETE FROM alumni WHERE user_id = ?";
                $delete_alumni_stmt = $conn->prepare($delete_alumni_sql);
                $delete_alumni_stmt->bind_param("i", $user_id);
                if ($delete_alumni_stmt->execute()) {
                    echo "<!-- Debug: Alumni record deleted, affected rows: " . $delete_alumni_stmt->affected_rows . " -->";
                }
                $delete_alumni_stmt->close();
            }

            // Delete from users table
            $delete_user_sql = "DELETE FROM users WHERE user_id = ?";
            $delete_user_stmt = $conn->prepare($delete_user_sql);
            $delete_user_stmt->bind_param("i", $user_id);
            
            if ($delete_user_stmt->execute() && $delete_user_stmt->affected_rows > 0) {
                echo "<!-- Debug: User deleted from users table, affected rows: " . $delete_user_stmt->affected_rows . " -->";
                mysqli_commit($conn);
                header('Location: admin_home.php?deleted=1');
                exit();
            } else {
                echo "<!-- Debug: Failed to delete user from users table -->";
                mysqli_rollback($conn);
                header('Location: admin_home.php?error=delete_failed');
                exit();
            }
            
            $delete_user_stmt->close();
        } else {
            echo "<!-- Debug: No user found with user_id: " . $user_id . " -->";
            mysqli_rollback($conn);
            header('Location: admin_home.php?error=user_not_found');
            exit();
        }
        
        $stmt->close();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<!-- Debug: Exception occurred: " . $e->getMessage() . " -->";
        header('Location: admin_home.php?error=exception');
        exit();
    }
} else {
    echo "<!-- Debug: No user_id in POST request -->";
    header('Location: admin_home.php?error=no_user_id');
    exit();
}
?>
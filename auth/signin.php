<?php
require_once(__DIR__ . '/../config/DBconnect.php');
require_once(__DIR__ . '/../includes/auth.php');

if (isset($_POST['fname']) && isset($_POST['pass'])) {
    $u = trim($_POST['fname']);
    $p = $_POST['pass'];

    $stmt = mysqli_prepare($conn, "SELECT user_id, username, password, role FROM users WHERE username = ? LIMIT 1");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $u);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;

        if ($row && password_verify($p, $row['password'])) {
            start_app_session();
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int) $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'student') {
                header('Location: /student/student_home.php');
                exit;
            }

            if ($row['role'] === 'alumni') {
                header('Location: /alumni/alumni_home.php');
                exit;
            }

            if ($row['role'] === 'admin') {
                header('Location: /admin/admin_home.php');
                exit;
            }
        }

        mysqli_stmt_close($stmt);
    }

    header('Location: /auth/index.php');
    exit;
}
?>

<?php
require_once(__DIR__ . '/../includes/auth.php');
require_any_role(['alumni', 'admin']);
require_once(__DIR__ . '/../config/DBconnect.php');

$viewer_role = current_user_role();
$back_path = $viewer_role === 'alumni' ? '/alumni/alumni_search.php' : '/admin/admin_home.php';
$back_label = $viewer_role === 'alumni' ? 'Back to Search' : 'Dashboard';
$profile_error = '';
$student_profile = null;
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

if ($user_id === false || $user_id === null) {
    $profile_error = 'Please choose a valid student profile to view.';
} else {
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.username, u.role,
                   s.programme, s.expertise, s.cv, s.cgpa, s.email, s.github, s.linkedin,
                   s.sex, s.city, s.country, s.zip_code
            FROM users u
            JOIN students s ON u.user_id = s.user_id
            WHERE u.user_id = ? AND u.role = 'student'
            LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $profile_error = 'We could not load this student profile right now.';
    } else {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) === 1) {
            $student_profile = mysqli_fetch_assoc($result);
        } else {
            $profile_error = 'The requested student profile is not available.';
        }

        $stmt->close();
    }
}

$github_url = $student_profile && filter_var($student_profile['github'], FILTER_VALIDATE_URL) ? $student_profile['github'] : '';
$linkedin_url = $student_profile && filter_var($student_profile['linkedin'], FILTER_VALIDATE_URL) ? $student_profile['linkedin'] : '';
$cv_path = $student_profile && trim((string) $student_profile['cv']) !== '' && strcasecmp((string) $student_profile['cv'], 'Not Provided') !== 0
    ? public_file_path($student_profile['cv'])
    : '';
$address_parts = [];

if ($student_profile) {
    foreach (['city', 'country'] as $field) {
        $value = display_value($student_profile[$field], '');
        if ($value !== '') {
            $address_parts[] = $value;
        }
    }

    $zip_value = display_value($student_profile['zip_code'], '');
    if ($zip_value !== '') {
        $address_parts[] = $zip_value;
    }
}

$address_display = !empty($address_parts) ? implode(', ', $address_parts) : 'Not provided';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Student Profile • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_profile_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Student Profile</div>
      <nav class="links">
        <a href="<?php echo e(home_path_for_role()); ?>"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="/shared/inbox.php"><i class='bx bxs-inbox'></i><span>Inbox</span></a>
        <a href="<?php echo e($back_path); ?>"><i class='bx bx-arrow-back'></i><span><?php echo e($back_label); ?></span></a>
        <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <?php if ($profile_error): ?>
          <div class="notice-card">
            <h3>Profile Not Available</h3>
            <p class="notice-text"><?php echo e($profile_error); ?></p>
            <div class="profile-actions">
              <a href="<?php echo e($back_path); ?>" class="btn edit-btn"><?php echo e($back_label); ?></a>
            </div>
          </div>
        <?php else: ?>
          <div class="profile-content">
            <div class="avatar"><i class='bx bxs-user-circle'></i></div>
            <div class="info">
              <h3 class="name"><?php echo e($student_profile['first_name'] . ' ' . $student_profile['last_name']); ?> <span class="role-pill">Student</span></h3>
              <p class="handle">
                @<?php echo e($student_profile['username']); ?>
                <?php if ($github_url): ?>
                  <a href="<?php echo e($github_url); ?>" target="_blank" rel="noopener noreferrer"><i class='bx bxl-github'></i> GitHub</a>
                <?php endif; ?>
                <?php if ($linkedin_url): ?>
                  <a href="<?php echo e($linkedin_url); ?>" target="_blank" rel="noopener noreferrer"><i class='bx bxl-linkedin'></i> LinkedIn</a>
                <?php endif; ?>
              </p>

              <div class="grid-info">
                <div class="row"><span>Programme</span><strong><?php echo e(display_value($student_profile['programme'])); ?></strong></div>
                <div class="row"><span>Expertise</span><strong><?php echo e(display_value($student_profile['expertise'])); ?></strong></div>
                <div class="row"><span>CGPA</span><strong><?php echo e(display_value($student_profile['cgpa'], 'Not available')); ?></strong></div>
                <div class="row"><span>Email</span><strong><?php echo e(display_value($student_profile['email'])); ?></strong></div>
                <div class="row"><span>Sex</span><strong><?php echo e(display_value($student_profile['sex'])); ?></strong></div>
                <div class="row"><span>Address</span><strong><?php echo e($address_display); ?></strong></div>
                <div class="row"><span>CV</span><strong><?php if ($cv_path): ?><a href="<?php echo e($cv_path); ?>" target="_blank" rel="noopener noreferrer">View CV</a><?php else: ?>Not provided<?php endif; ?></strong></div>
              </div>
            </div>
          </div>

          <div class="profile-actions">
            <a href="/shared/inbox.php?to=<?php echo e($student_profile['user_id']); ?>" class="btn edit-btn">Message</a>
            <a href="<?php echo e($back_path); ?>" class="btn edit-btn"><?php echo e($back_label); ?></a>
          </div>
        <?php endif; ?>
      </section>
    </section>
  </div>
</body>
</html>

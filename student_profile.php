<?php
require_once('fetch_student.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Profile • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_profile_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Student Profile</div>
      <nav class="links">
        <a href="student_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="inbox.php"><i class='bx bxs-inbox'></i><span>Inbox</span></a>
        <a href="student_search.php"><i class='bx bx-search'></i><span>Search</span></a>
        <a href="logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <div class="profile-content">
          <div class="avatar"><i class='bx bxs-user-circle'></i></div>
          <div class="info">
            <h3 class="name"><?php echo e($student_data['first_name'] . ' ' . $student_data['last_name']); ?> <span class="role-pill">Student</span></h3>
            <p class="handle">
              @<?php echo e($student_data['username']); ?>
              <a href="<?php echo e($student_data['github']); ?>" target="_blank"><i class='bx bxl-github'></i> GitHub</a> | 
              <a href="<?php echo e($student_data['linkedin']); ?>" target="_blank"><i class='bx bxl-linkedin'></i> LinkedIn</a>
            </p>

            <div class="grid-info">
              <div class="row"><span>First name</span><strong><?php echo e($student_data['first_name']); ?></strong></div>
              <div class="row"><span>Last name</span><strong><?php echo e($student_data['last_name']); ?></strong></div>
              <div class="row"><span>Username</span><strong><?php echo e($student_data['username']); ?></strong></div>
              <div class="row"><span>Programme</span><strong><?php echo e($student_data['programme']); ?></strong></div>
              <div class="row"><span>Expertise</span><strong><?php echo e($student_data['expertise']); ?></strong></div>
              <div class="row"><span>CV</span><strong><a href="<?php echo e($student_data['cv']); ?>" target="_blank">View CV</a></strong></div>
              <div class="row"><span>CGPA</span><strong><?php echo e($student_data['cgpa']); ?></strong></div>
              <div class="row"><span>Email</span><strong><?php echo e($student_data['email']); ?></strong></div>
              <div class="row"><span>Sex</span><strong><?php echo e($student_data['sex']); ?></strong></div>
              <div class="row"><span>Address</span><strong><?php echo e($student_data['city'] . ', ' . $student_data['country'] . ' - ' . $student_data['zip_code']); ?></strong></div>
            </div>
          </div>
        </div>

        <div class="edit-btn-container">
          <a href="edit_student_profile.php" class="btn edit-btn">Edit Profile</a>
        </div>
      </section>
    </section>
  </div>
</body>
</html>

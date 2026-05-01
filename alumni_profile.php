<?php
require_once('fetch_alumni.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Alumni Profile • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_profile_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Alumni Profile</div>
      <nav class="links">
        <a href="alumni_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="inbox.php"><i class='bx bxs-inbox'></i><span>Inbox</span></a>
        <a href="alumni_search.php"><i class='bx bx-search'></i><span>Search</span></a>
        <a href="logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <div class="profile-content">
          <div class="avatar"><i class='bx bxs-user-circle'></i></div>
          <div class="info">
            <h3 class="name"><?php echo e($alumni_data['first_name'] . ' ' . $alumni_data['last_name']); ?> <span class="role-pill">Alumni</span></h3>
            <p class="handle">
              @<?php echo e($alumni_data['username']); ?>
              <a href="<?php echo e($alumni_data['github']); ?>" target="_blank"><i class='bx bxl-github'></i> GitHub</a> | 
              <a href="<?php echo e($alumni_data['linkedin']); ?>" target="_blank"><i class='bx bxl-linkedin'></i> LinkedIn</a>
            </p>

            <div class="grid-info">
              <div class="row"><span>First Name</span><strong><?php echo e($alumni_data['first_name']); ?></strong></div>
              <div class="row"><span>Last Name</span><strong><?php echo e($alumni_data['last_name']); ?></strong></div>
              <div class="row"><span>Username</span><strong><?php echo e($alumni_data['username']); ?></strong></div>
              <div class="row"><span>Type</span><strong><?php echo e($alumni_data['type']); ?></strong></div>
              <div class="row"><span>Address</span><strong><?php echo e($alumni_data['city'] . ', ' . $alumni_data['country'] . ' - ' . $alumni_data['zip_code']); ?></strong></div>
              <div class="row"><span>Sex</span><strong><?php echo e($alumni_data['sex']); ?></strong></div>
              <div class="row"><span>Thesis</span><strong><?php echo e($alumni_data['thesis']); ?></strong></div>
              <div class="row"><span>Current Company</span><strong><?php echo e($alumni_data['company_name']); ?></strong></div>
              <div class="row"><span>Degree Programme</span><strong><?php echo e($alumni_data['degree_programme']); ?></strong></div>
              <div class="row"><span>Field of Study</span><strong><?php echo e($alumni_data['field_of_study']); ?></strong></div>
              <div class="row"><span>Role Title</span><strong><?php echo e($alumni_data['role_title']); ?></strong></div>
              <div class="row"><span>Location</span><strong><?php echo e($alumni_data['location']); ?></strong></div>
              <div class="row"><span>Business Name</span><strong><?php echo e($alumni_data['business_name']); ?></strong></div>
              <div class="row"><span>Business Theme</span><strong><?php echo e($alumni_data['business_theme']); ?></strong></div>
            </div>
          </div>
        </div>

        <div class="edit-btn-container">
          <a href="edit_alumni_profile.php" class="btn edit-btn">Edit Profile</a>
        </div>
      </section>
    </section>
  </div>
</body>
</html>

<?php
require_once(__DIR__ . '/../includes/fetch_student.php');

$student_error_messages = [
    'invalid_profile_data' => 'Please review the submitted profile details and try again.',
    'invalid_email' => 'Please enter a valid email address.',
    'invalid_cgpa' => 'Please enter a valid numeric CGPA.',
    'invalid_cv_type' => 'Please upload a PDF file only.',
    'cv_too_large' => 'Please upload a PDF smaller than 2 MB.',
    'upload_failed' => 'The CV upload failed. Please try again.',
];

$student_error = $student_error_messages[$_GET['error'] ?? ''] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profile • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_profile_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Edit Profile</div>
      <nav class="links">
        <a href="/student/student_profile.php"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <div class="avatar"><i class='bx bxs-user-circle'></i></div>
        <form method="POST" action="/student/update_student_profile.php" enctype="multipart/form-data">
          <div class="info">
            <h3 class="name">Edit Your Profile</h3>
            <?php if ($student_error): ?>
              <p><?php echo e($student_error); ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
            <div class="grid-info">
              <div class="row">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo e($student_data['first_name']); ?>" required />
              </div>
              <div class="row">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo e($student_data['last_name']); ?>" required />
              </div>
              <div class="row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo e($student_data['email']); ?>" required />
              </div>
              <div class="row">
                <label for="programme">Programme:</label>
                <input type="text" id="programme" name="programme" value="<?php echo e($student_data['programme']); ?>" required />
              </div>
              <div class="row">
                <label for="expertise">Expertise:</label>
                <input type="text" id="expertise" name="expertise" value="<?php echo e($student_data['expertise']); ?>" required />
              </div>
              <div class="row">
                <label for="cv">CV (Upload File):</label>
                <input type="file" id="cv" name="cv" />
                <p>Current CV: <?php echo e($student_data['cv']); ?></p>
              </div>
              <div class="row">
                <label for="cgpa">CGPA:</label>
                <input type="text" id="cgpa" name="cgpa" value="<?php echo e($student_data['cgpa']); ?>" required />
              </div>
              <div class="row">
                <label for="github">GitHub:</label>
                <input type="url" id="github" name="github" value="<?php echo e($student_data['github']); ?>" />
              </div>
              <div class="row">
                <label for="linkedin">LinkedIn:</label>
                <input type="url" id="linkedin" name="linkedin" value="<?php echo e($student_data['linkedin']); ?>" />
              </div>
              <div class="row">
                <label for="sex">Sex:</label>
                <select id="sex" name="sex">
                  <option value="male" <?php echo ($student_data['sex'] == 'male') ? 'selected' : ''; ?>>Male</option>
                  <option value="female" <?php echo ($student_data['sex'] == 'female') ? 'selected' : ''; ?>>Female</option>
                  <option value="other" <?php echo ($student_data['sex'] == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
              </div>
              <div class="row">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo e($student_data['city']); ?>" required />
              </div>
              <div class="row">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" value="<?php echo e($student_data['country']); ?>" required />
              </div>
              <div class="row">
                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo e($student_data['zip_code']); ?>" required />
              </div>
            </div>
            <button type="submit" class="btn">Save Changes</button>
          </div>
        </form>
      </section>
    </section>
  </div>
</body>
</html>

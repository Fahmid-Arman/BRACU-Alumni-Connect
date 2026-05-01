<?php
require_once(__DIR__ . '/../includes/fetch_alumni.php');

$alumni_error_messages = [
    'invalid_profile_data' => 'Please review the submitted alumni details and try again.',
];

$alumni_error = $alumni_error_messages[$_GET['error'] ?? ''] ?? '';

$user_id = current_user_id();
$sql = "SELECT * FROM alumni WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && mysqli_num_rows($result) > 0) {
    $alumni_data_full = mysqli_fetch_assoc($result);
} else {
    echo "No alumni data found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Alumni Profile • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_profile_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Edit Alumni Profile</div>
      <nav class="links">
        <a href="/alumni/alumni_profile.php"><i class='bx bxs-home'></i><span>Home</span></a>
        <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <div class="avatar"><i class='bx bxs-user-circle'></i></div>
        <form method="POST" action="/alumni/update_alumni_profile.php" enctype="multipart/form-data">
          <div class="info">
            <h3 class="name">Edit Your Profile</h3>
            <?php if ($alumni_error): ?>
              <p><?php echo e($alumni_error); ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
            <div class="grid-info">

              <div class="row">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo e($alumni_data['first_name']); ?>" required />
              </div>

              <div class="row">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo e($alumni_data['last_name']); ?>" required />
              </div>

              <div class="row">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo e($alumni_data['username']); ?>" disabled />
              </div>

              <div class="row">
                <label for="type">Type:</label>
                <select id="type" name="type">
                  <option value="higher studies" <?php echo ($alumni_data_full['type']=='higher studies')?'selected':''; ?>>Higher Studies</option>
                  <option value="corporate" <?php echo ($alumni_data_full['type']=='corporate')?'selected':''; ?>>Corporate</option>
                  <option value="self employed" <?php echo ($alumni_data_full['type']=='self employed')?'selected':''; ?>>Self Employed</option>
                </select>
              </div>

              <div class="row">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo e($alumni_data_full['city']); ?>" required />
              </div>

              <div class="row">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" value="<?php echo e($alumni_data_full['country']); ?>" required />
              </div>

              <div class="row">
                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo e($alumni_data_full['zip_code']); ?>" required />
              </div>

              <div class="row">
                <label for="sex">Sex:</label>
                <select id="sex" name="sex">
                  <option value="male" <?php echo ($alumni_data_full['sex']=='male')?'selected':''; ?>>Male</option>
                  <option value="female" <?php echo ($alumni_data_full['sex']=='female')?'selected':''; ?>>Female</option>
                  <option value="other" <?php echo ($alumni_data_full['sex']=='other')?'selected':''; ?>>Other</option>
                </select>
              </div>

              <div class="row">
                <label for="thesis">Thesis:</label>
                <input type="text" id="thesis" name="thesis" value="<?php echo e($alumni_data_full['thesis']); ?>" />
              </div>

              <div class="row">
                <label for="current_company">Current Company:</label>
                <input type="text" id="current_company" name="current_company" value="<?php echo e($alumni_data_full['company_name']); ?>" />
              </div>

              <div class="row">
                <label for="degree_programme">Degree Programme:</label>
                <input type="text" id="degree_programme" name="degree_programme" value="<?php echo e($alumni_data_full['degree_programme']); ?>" />
              </div>

              <div class="row">
                <label for="field_of_study">Field of Study:</label>
                <input type="text" id="field_of_study" name="field_of_study" value="<?php echo e($alumni_data_full['field_of_study']); ?>" />
              </div>

              <div class="row">
                <label for="role_title">Role Title:</label>
                <input type="text" id="role_title" name="role_title" value="<?php echo e($alumni_data_full['role_title']); ?>" />
              </div>

              <div class="row">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo e($alumni_data_full['location']); ?>" />
              </div>

              <div class="row">
                <label for="business_name">Business Name:</label>
                <input type="text" id="business_name" name="business_name" value="<?php echo e($alumni_data_full['business_name']); ?>" />
              </div>

              <div class="row">
                <label for="business_theme">Business Theme:</label>
                <input type="text" id="business_theme" name="business_theme" value="<?php echo e($alumni_data_full['business_theme']); ?>" />
              </div>

              <div class="row">
                <label for="github">GitHub:</label>
                <input type="url" id="github" name="github" value="<?php echo e($alumni_data_full['github']); ?>" />
              </div>

              <div class="row">
                <label for="linkedin">LinkedIn:</label>
                <input type="url" id="linkedin" name="linkedin" value="<?php echo e($alumni_data_full['linkedin']); ?>" />
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

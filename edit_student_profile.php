<?php
require_once('fetch_student.php');

$sql = "SELECT * FROM students WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $student_data = mysqli_fetch_assoc($result);
} else {
    echo "No student data found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profile • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_profile_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="page">
    <header class="glass hdr">
      <div class="title">Edit Profile</div>
      <nav class="links">
        <a href="student_profile.php"><i class='bx bxs-home'></i><span>Home</span></a>
      </nav>
    </header>

    <section class="dash">
      <section class="glass card profile">
        <div class="avatar"><i class='bx bxs-user-circle'></i></div>
        <form method="POST" action="update_student_profile.php" enctype="multipart/form-data">
          <div class="info">
            <h3 class="name">Edit Your Profile</h3>
            <div class="grid-info">
              <div class="row">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $student_data['first_name']; ?>" required />
              </div>
              <div class="row">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $student_data['last_name']; ?>" required />
              </div>
              <div class="row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $student_data['email']; ?>" required />
              </div>
              <div class="row">
                <label for="programme">Programme:</label>
                <input type="text" id="programme" name="programme" value="<?php echo $student_data['programme']; ?>" required />
              </div>
              <div class="row">
                <label for="expertise">Expertise:</label>
                <input type="text" id="expertise" name="expertise" value="<?php echo $student_data['expertise']; ?>" required />
              </div>
              <div class="row">
                <label for="cv">CV (Upload File):</label>
                <input type="file" id="cv" name="cv" />
                <p>Current CV: <?php echo $student_data['cv']; ?></p>
              </div>
              <div class="row">
                <label for="cgpa">CGPA:</label>
                <input type="text" id="cgpa" name="cgpa" value="<?php echo $student_data['cgpa']; ?>" required />
              </div>
              <div class="row">
                <label for="github">GitHub:</label>
                <input type="url" id="github" name="github" value="<?php echo $student_data['github']; ?>" />
              </div>
              <div class="row">
                <label for="linkedin">LinkedIn:</label>
                <input type="url" id="linkedin" name="linkedin" value="<?php echo $student_data['linkedin']; ?>" />
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
                <input type="text" id="city" name="city" value="<?php echo $student_data['city']; ?>" required />
              </div>
              <div class="row">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" value="<?php echo $student_data['country']; ?>" required />
              </div>
              <div class="row">
                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo $student_data['zip_code']; ?>" required />
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

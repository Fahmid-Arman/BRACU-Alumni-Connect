<?php require_once('auth.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="wrapper">
    <form action="signup.php" method="post" autocomplete="on">
      <h1>Create Account</h1>
      <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />

      <div class="input-box">
        <input type="text" name="first_name" placeholder="First name" required />
        <i class='bx bxs-id-card'></i>
      </div>

      <div class="input-box">
        <input type="text" name="last_name" placeholder="Last name" required />
        <i class='bx bxs-id-card'></i>
      </div>

      <div class="input-box">
        <input type="text" name="fname" placeholder="Username" required autocomplete="username" />
        <i class='bx bxs-user'></i>
      </div>

      <div class="input-box">
        <input type="password" name="pass" placeholder="Password" required autocomplete="new-password" />
        <i class='bx bxs-lock-alt'></i>
      </div>
      <div class="input-box">
        <input type="password" name="cpass" placeholder="Confirm Password" required autocomplete="new-password" />
        <i class='bx bxs-lock-open-alt'></i>
      </div>
      <div class="input-box">
        <select name="role" required 
                style="width:100%;height:100%;background:transparent;border:2px solid rgba(255,255,255,.2);border-radius:40px;font-size:16px;color:#fff;padding:0 45px 0 20px;appearance:none;-webkit-appearance:none;-moz-appearance:none;">
          <option value="" disabled selected>-- Select Role --</option>
          <option value="student" style="color:#000;">Student</option>
          <option value="alumni" style="color:#000;">Alumni</option>
        </select>
        <i class='bx bx-chevron-down'></i>
      </div>

      <button type="submit" class="btn">Create account</button>

      <div class="register-link">
        <p>Already have an account? <a href="index.php">Login</a></p>
      </div>
    </form>
  </div>
</body>
</html>

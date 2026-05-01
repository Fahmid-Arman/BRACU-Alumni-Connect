<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('alumni');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Students • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_search_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <header class="glass hdr">
        <div class="title">Search Students</div>
        <nav class="links">
            <a href="/alumni/alumni_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
            <a href="/alumni/alumni_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
            <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <p class="search-note">Leave filters blank to browse current students.</p>
            <form method="GET" action="/student/student_list.php">
                <div class="search-options">
                    <input type="text" name="programme" placeholder="Programme" />
                    <input type="text" name="expertise" placeholder="Expertise" />
                    <input type="number" step="0.01" min="0" max="4" name="cgpa_min" placeholder="Minimum CGPA" />
                    <input type="text" name="city" placeholder="City" />
                    <input type="text" name="country" placeholder="Country" />
                    <button type="submit" class="btn">Search Students</button>
                </div>
            </form>
        </section>
    </section>
</div>
</body>
</html>

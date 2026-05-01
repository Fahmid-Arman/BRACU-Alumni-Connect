<?php
require_once('auth.php');
require_role('student');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_search_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <header class="glass hdr">
        <div class="title">Search</div>
        <nav class="links">
            <a href="student_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
            <a href="student_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
            <a href="logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <p class="search-note">Leave filters blank to browse the alumni network.</p>
            <form method="GET" action="alumni_list.php">
                <div class="search-options">
                    <input type="text" name="company_name" placeholder="Company name" />
                    <input type="text" name="role_title" placeholder="Role title" />
                    <input type="text" name="degree_programme" placeholder="Degree programme" />
                    <input type="text" name="field_of_study" placeholder="Field of study" />
                    <input type="text" name="current_country" placeholder="Current country" />
                    <select name="type">
                        <option value="">Any alumni type</option>
                        <option value="higher studies">Higher Studies</option>
                        <option value="corporate">Corporate</option>
                        <option value="self employed">Self Employed</option>
                    </select>
                    <button type="submit" class="btn">Search Alumni</button>
                </div>
            </form>
        </section>
    </section>
</div>
</body>
</html>

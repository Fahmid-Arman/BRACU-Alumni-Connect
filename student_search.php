<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once('DBconnect.php');
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
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <form method="GET" action="alumni_list.php">
                <div class="search-options">
                    <input type="text" name="search_query" placeholder="Enter company name (e.g., Optimizely)" required />
                    <button type="submit" class="btn">Search</button>
                </div>
            </form>
        </section>
    </section>
</div>
</body>
</html>
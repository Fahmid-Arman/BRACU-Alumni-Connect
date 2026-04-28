<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once('DBconnect.php');

$search_query = $_GET['search_query'] ?? '';
$search_results = [];

if (!empty($search_query)) {
    $sql = "SELECT u.first_name, u.last_name, s.programme, s.cgpa, s.city, s.country, s.expertise 
            FROM students s 
            JOIN users u ON s.user_id = u.user_id 
            WHERE LOWER(s.programme) LIKE LOWER(?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $search_query_param = '%' . $search_query . '%';
        $stmt->bind_param("s", $search_query_param);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Search Results • Alumni Connect</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="student_search_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <header class="glass hdr">
        <div class="title">Student Search Results</div>
        <nav class="links">
            <a href="alumni_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
            <a href="alumni_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
            <a href="alumni_search.php"><i class='bx bx-arrow-back'></i><span>Back to Search</span></a>
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <h3>Students studying "<?php echo htmlspecialchars($search_query); ?>"</h3>

            <?php if (!empty($search_results)): ?>
                <div class="search-results">
                    <?php foreach ($search_results as $row): ?>
                        <div class="search-result-item">
                            <h4><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h4>
                            <span><strong>Programme:</strong> <?php echo htmlspecialchars($row['programme']); ?></span>
                            <span><strong>CGPA:</strong> <?php echo htmlspecialchars($row['cgpa']); ?></span>
                            <span><strong>Expertise:</strong> <?php echo htmlspecialchars($row['expertise']); ?></span>
                            <span><strong>Location:</strong> <?php echo htmlspecialchars($row['city'] . ', ' . $row['country']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!empty($search_query)): ?>
                <div class="no-results">
                    <p>No students found studying "<?php echo htmlspecialchars($search_query); ?>".</p>
                    <p>Try searching for a different programme or department.</p>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p>Please enter a programme or department name to search for students.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>
</body>
</html>

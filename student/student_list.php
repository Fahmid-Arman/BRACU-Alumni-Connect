<?php
require_once(__DIR__ . '/../includes/auth.php');
require_role('alumni');
require_once(__DIR__ . '/../config/DBconnect.php');

$programme = trim($_GET['programme'] ?? ($_GET['search_query'] ?? ''));
$expertise = trim($_GET['expertise'] ?? '');
$cgpa_min = trim($_GET['cgpa_min'] ?? '');
$city = trim($_GET['city'] ?? '');
$country = trim($_GET['country'] ?? '');
$search_results = [];
$search_error = '';
$applied_filters = [];

if ($cgpa_min !== '' && !is_numeric($cgpa_min)) {
    $search_error = 'Minimum CGPA must be a number.';
} else {
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.username, s.programme, s.cgpa, s.city, s.country, s.expertise, s.email
            FROM students s
            JOIN users u ON s.user_id = u.user_id";
    $conditions = [];
    $params = [];
    $types = '';

    if ($programme !== '') {
        $conditions[] = "s.programme LIKE ?";
        $params[] = '%' . $programme . '%';
        $types .= 's';
        $applied_filters[] = 'Programme: ' . $programme;
    }

    if ($expertise !== '') {
        $conditions[] = "s.expertise LIKE ?";
        $params[] = '%' . $expertise . '%';
        $types .= 's';
        $applied_filters[] = 'Expertise: ' . $expertise;
    }

    if ($cgpa_min !== '') {
        $conditions[] = "s.cgpa >= ?";
        $params[] = (float) $cgpa_min;
        $types .= 'd';
        $applied_filters[] = 'Minimum CGPA: ' . $cgpa_min;
    }

    if ($city !== '') {
        $conditions[] = "s.city LIKE ?";
        $params[] = '%' . $city . '%';
        $types .= 's';
        $applied_filters[] = 'City: ' . $city;
    }

    if ($country !== '') {
        $conditions[] = "s.country LIKE ?";
        $params[] = '%' . $country . '%';
        $types .= 's';
        $applied_filters[] = 'Country: ' . $country;
    }

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY u.first_name ASC, u.last_name ASC LIMIT 50';
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
        }
    }
}

$results_heading = !empty($applied_filters) ? 'Filtered Student Results' : 'Browse Current Students';
$results_summary = !empty($applied_filters)
    ? 'Showing up to 50 students matching: ' . implode(' | ', $applied_filters)
    : 'Showing up to 50 student profiles from the current network.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Search Results • Alumni Connect</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/student_search_style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <header class="glass hdr">
        <div class="title">Student Search Results</div>
        <nav class="links">
            <a href="/alumni/alumni_home.php"><i class='bx bxs-home'></i><span>Home</span></a>
            <a href="/alumni/alumni_profile.php"><i class='bx bxs-user'></i><span>Profile</span></a>
            <a href="/alumni/alumni_search.php"><i class='bx bx-arrow-back'></i><span>Back to Search</span></a>
            <a href="/auth/logout.php"><i class='bx bx-log-out'></i><span>Logout</span></a>
        </nav>
    </header>

    <section class="dash">
        <section class="glass card search-card">
            <h3><?php echo e($results_heading); ?></h3>
            <p class="search-note"><?php echo e($results_summary); ?></p>

            <?php if ($search_error): ?>
                <div class="no-results">
                    <p><?php echo e($search_error); ?></p>
                </div>
            <?php elseif (!empty($search_results)): ?>
                <div class="search-results">
                    <?php foreach ($search_results as $row): ?>
                        <div class="search-result-item">
                            <h4><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></h4>
                            <p class="result-handle">@<?php echo e($row['username']); ?></p>
                            <span><strong>Programme:</strong> <?php echo e($row['programme']); ?></span>
                            <span><strong>CGPA:</strong> <?php echo e($row['cgpa']); ?></span>
                            <span><strong>Expertise:</strong> <?php echo e($row['expertise']); ?></span>
                            <span><strong>Location:</strong> <?php echo e($row['city'] . ', ' . $row['country']); ?></span>
                            <span><strong>Email:</strong> <?php echo e($row['email']); ?></span>
                            <div class="result-actions">
                                <a href="/shared/view_student_profile.php?user_id=<?php echo e($row['user_id']); ?>" class="btn">View Profile</a>
                                <a href="/shared/inbox.php?to=<?php echo e($row['user_id']); ?>" class="btn">Message</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p>No students matched your current filters.</p>
                    <p>Try broadening the search criteria from the alumni discovery page.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>
</body>
</html>

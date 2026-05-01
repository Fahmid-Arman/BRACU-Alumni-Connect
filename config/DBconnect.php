<?php
/*
 * Database connection bootstrap for local development.
 *
 * Preferred approach:
 * - export DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASS in your shell
 *   before starting `php -S`
 *
 * Reference:
 * - see `.env.example` for the expected variable names
 *
 * Important:
 * - this project does not auto-load a `.env` file with a dotenv package
 * - the fallback values below are only local defaults
 * - set DB_PASS to your real local MySQL password instead of committing secrets
 */
$servername = getenv('DB_HOST') ?: '127.0.0.1';
$port = (int) (getenv('DB_PORT') ?: 3306);
$username = getenv('DB_USER') ?: 'root';
// Blank password is only a fallback for local setups that intentionally use no MySQL password.
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'university';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    $message = 'Database connection failed. Check the database settings in config/DBconnect.php or the DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASS environment variables.';

    if (stripos($conn->connect_error, 'Unknown database') !== false) {
        $message .= ' The database does not exist yet. Import sql/schema.sql and sql/seed.sql first.';
    }

    die($message . ' MySQL said: ' . $conn->connect_error);
}

// Use a consistent Unicode charset for names, messages, and profile fields.
$conn->set_charset('utf8mb4');
?>

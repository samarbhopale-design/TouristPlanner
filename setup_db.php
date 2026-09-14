<?php
/**
 * Automated Database Setup Runner for InfinityFree
 * 
 * Executes table creation and data seeding directly on InfinityFree server
 * using local server-to-database connection.
 */

define('DB_SERVER', 'sql106.infinityfree.com');
define('DB_USERNAME', 'if0_42916037');
define('DB_PASSWORD', 'Qeuk7S1ay0Fd');
define('DB_NAME', 'if0_42916037_TouristPlanner');

header('Content-Type: text/html; charset=utf-8');

$conn = @mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (!$conn) {
    die("<div style='font-family:sans-serif; padding:30px; background:#fee2e2; color:#991b1b; border-radius:8px;'>
            <h2>Database Connection Failed</h2>
            <p>Error: " . htmlspecialchars(mysqli_connect_error()) . "</p>
         </div>");
}

mysqli_set_charset($conn, "utf8mb4");

// Read database.sql content
$sql_file = __DIR__ . '/database.sql';
if (!file_exists($sql_file)) {
    die("database.sql file not found in current directory.");
}

$sql_content = file_get_contents($sql_file);

// Remove comments and execute multi query
$queries = [];
$lines = explode("\n", $sql_content);
$clean_sql = "";

foreach ($lines as $line) {
    $trimmed = trim($line);
    if (empty($trimmed) || strpos($trimmed, '--') === 0 || strpos($trimmed, '/*') === 0) {
        continue;
    }
    $clean_sql .= $line . "\n";
}

$success = false;
$error = '';

if (mysqli_multi_query($conn, $clean_sql)) {
    do {
        if ($res = mysqli_store_result($conn)) {
            mysqli_free_result($res);
        }
    } while (mysqli_more_results($conn) && mysqli_next_result($conn));

    if (mysqli_errno($conn)) {
        $error = mysqli_error($conn);
    } else {
        $success = true;
    }
} else {
    $error = mysqli_error($conn);
}

// Check imported records
$users_count = 0;
$trips_count = 0;

if ($res = mysqli_query($conn, "SELECT COUNT(*) as c FROM users")) {
    $row = mysqli_fetch_assoc($res);
    $users_count = $row['c'];
}

if ($res = mysqli_query($conn, "SELECT COUNT(*) as c FROM trips")) {
    $row = mysqli_fetch_assoc($res);
    $trips_count = $row['c'];
}

$domain = $_SERVER['HTTP_HOST'] ?? 'your-domain';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$site_url = $scheme . '://' . $domain . '/index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Setup - Tourist Planner</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; padding: 40px 20px; display: flex; justify-content: center; }
        .card { background: #ffffff; border-radius: 12px; padding: 35px; max-width: 580px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        h1 { color: #0f766e; margin-top: 0; font-size: 1.6rem; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; background: #ecfdf5; color: #065f46; font-weight: 600; font-size: 0.85rem; }
        .btn { display: inline-block; background: #0d9488; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 20px; }
        .btn:hover { background: #0f766e; }
        .info-box { background: #f0fdfa; border: 1px solid #99f6e4; padding: 15px; border-radius: 8px; margin: 15px 0; font-size: 0.95rem; }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($success || ($users_count > 0 && $trips_count > 0)): ?>
            <h1>🎉 Database Configured Successfully!</h1>
            <p>The MySQL tables, dummy user, and 10 sample trips have been loaded into <code><?php echo DB_NAME; ?></code> on <code><?php echo DB_SERVER; ?></code>.</p>
            
            <div class="info-box">
                <div><strong>Registered Users:</strong> <?php echo $users_count; ?> record(s)</div>
                <div><strong>Seeded Trips:</strong> <?php echo $trips_count; ?> record(s) in ₹ INR</div>
                <div><strong>Database:</strong> <?php echo DB_NAME; ?></div>
            </div>

            <p>You can now use your live Tourist Place Visit Planner application!</p>

            <a href="<?php echo $site_url; ?>" class="btn">🚀 Open Tourist Planner &rarr;</a>
        <?php else: ?>
            <h1 style="color:#dc2626;">Setup Encountered An Issue</h1>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

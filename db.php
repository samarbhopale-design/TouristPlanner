<?php
/**
 * Database Connection Handler
 * 
 * Uses procedural mysqli for lightweight execution and maximum compatibility
 * with standard LAMP/WAMP/XAMPP stacks and shared hosts like InfinityFree.
 */

// Prevent multiple declarations if included multiple times
if (!defined('DB_SERVER')) {
    // -------------------------------------------------------------------------
    // Automatic Environment Detection (Local XAMPP vs InfinityFree Production)
    // -------------------------------------------------------------------------
    $server_host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
    $is_local = (strpos($server_host, 'localhost') !== false || strpos($server_host, '127.0.0.1') !== false);

    if ($is_local) {
        // =====================================================================
        // LOCAL DEVELOPMENT CONFIGURATION (XAMPP / WAMP)
        // =====================================================================
        define('DB_SERVER', 'localhost');
        define('DB_USERNAME', 'root');
        define('DB_PASSWORD', '');
        define('DB_NAME', 'tourist_planner');
    } else {
        // =====================================================================
        // INFINITYFREE HOSTING CONFIGURATION
        // =====================================================================
        define('DB_SERVER', 'sql106.infinityfree.com');
        define('DB_USERNAME', 'if0_42916037');
        define('DB_PASSWORD', 'Qeuk7S1ay0Fd');
        define('DB_NAME', 'if0_42916037_TouristPlanner');
    }

    // Attempt to establish a connection to MySQL database using procedural mysqli
    // Prepending '@' suppresses raw PHP connection warning outputs
    $conn = @mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Suppress raw database error outputs to normal users with a clean, generic fail message
    if (!$conn) {
        // Log the detailed technical error privately to server error logs
        error_log("Database connection failed: " . mysqli_connect_error());

        // Display user-friendly generic error message and terminate execution
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Service Unavailable - Tourist Planner</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    background-color: #f8fafc;
                    color: #1e293b;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                    box-sizing: border-box;
                }
                .error-card {
                    background: #ffffff;
                    border: 1px solid #fed7aa;
                    border-left: 6px solid #f97316;
                    padding: 30px;
                    border-radius: 12px;
                    max-width: 540px;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
                }
                .error-card h2 {
                    margin-top: 0;
                    color: #c2410c;
                    font-size: 1.5rem;
                }
                .error-card p {
                    line-height: 1.6;
                    color: #475569;
                    font-size: 0.95rem;
                }
                .error-card code {
                    background: #f1f5f9;
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 0.9em;
                    color: #0f172a;
                }
            </style>
        </head>
        <body>
            <div class="error-card">
                <h2>Service Temporarily Unavailable</h2>
                <p>We are currently experiencing technical difficulties connecting to the database server. Please check back shortly.</p>
                <p><strong>Note for Administrator:</strong> Verify database credentials in <code>db.php</code> and ensure the <code>tourist_planner</code> database has been imported using <code>database.sql</code>.</p>
            </div>
        </body>
        </html>
        <?php
        exit();
    }

    // Set charset to utf8mb4 for full international destination support
    mysqli_set_charset($conn, "utf8mb4");
}

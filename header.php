<?php
/**
 * Header & Session Management
 * 
 * Initializes PHP session and outputs common HTML5 head & dynamic navbar.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine active page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " - Tourist Planner" : "Tourist Place Visit Planner"; ?></title>
    <!-- Unified Pure CSS Styling (Zero External Frameworks) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <span class="logo-icon">🌍</span>
                <span>Tourist<span class="brand-accent">Planner</span></span>
            </a>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
                        🏠 Home
                    </a>
                </li>

                <li class="nav-item">
                    <a href="estimator.php" class="<?php echo ($current_page === 'estimator.php') ? 'active' : ''; ?>">
                        🧮 Budget Estimator
                    </a>
                </li>

                <?php if ($is_logged_in): ?>
                    <!-- Navigation for Authenticated Users -->
                    <li class="nav-item">
                        <a href="view_trips.php" class="<?php echo ($current_page === 'view_trips.php') ? 'active' : ''; ?>">
                            📋 View My Trips
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="add_trip.php" class="<?php echo ($current_page === 'add_trip.php') ? 'active' : ''; ?>">
                            ➕ Add Trip
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="user-badge" title="Logged in user">
                            👤 <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-outline btn-sm" style="margin-left: 0.25rem;">
                            🚪 Logout
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Navigation for Guests / Logged-out Users -->
                    <li class="nav-item">
                        <a href="login.php" class="<?php echo ($current_page === 'login.php') ? 'active' : ''; ?>">
                            🔑 Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="btn btn-primary btn-sm <?php echo ($current_page === 'register.php') ? 'active' : ''; ?>" style="color: #ffffff;">
                            ✨ Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content Container Wrapper -->
    <main class="main-wrapper">

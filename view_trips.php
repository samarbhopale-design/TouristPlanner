<?php
/**
 * View & Search Trips (Protected Page)
 * 
 * Shows ONLY trips associated with the logged-in user.
 * Displays destination, travel date, number of persons, budget in INR,
 * per-person cost breakdown, summary stats, search filter by destination,
 * and strict XSS protection using htmlspecialchars().
 */
require_once 'db.php';

// Safe session startup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect unauthenticated users directly to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$flash_success = '';
$flash_error = '';

// Handle trip deletion securely using prepared statements
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $trip_id_to_delete = (int)($_POST['trip_id'] ?? 0);
    if ($trip_id_to_delete > 0) {
        $del_sql = "DELETE FROM trips WHERE id = ? AND user_id = ?";
        if ($del_stmt = mysqli_prepare($conn, $del_sql)) {
            mysqli_stmt_bind_param($del_stmt, "ii", $trip_id_to_delete, $user_id);
            if (mysqli_stmt_execute($del_stmt)) {
                $flash_success = "Trip deleted successfully.";
            } else {
                $flash_error = "Could not delete the trip. Please try again.";
            }
            mysqli_stmt_close($del_stmt);
        }
    }
}

// Check for redirect flash messages
if (isset($_GET['msg']) && $_GET['msg'] === 'added') {
    $flash_success = "Your new trip has been added to your itinerary!";
}

// Handle search/filter parameter by destination
$search_query = trim($_GET['search'] ?? '');
$trips = [];

if (!empty($search_query)) {
    // Search trips belonging ONLY to the logged in user using prepared statement
    $sql = "SELECT id, destination, travel_date, persons, budget, visitor_email, notes, created_at 
            FROM trips 
            WHERE user_id = ? AND destination LIKE ? 
            ORDER BY travel_date ASC";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        $search_param = '%' . $search_query . '%';
        mysqli_stmt_bind_param($stmt, "is", $user_id, $search_param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $trips[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Fetch ALL trips belonging ONLY to the logged-in user
    $sql = "SELECT id, destination, travel_date, persons, budget, visitor_email, notes, created_at 
            FROM trips 
            WHERE user_id = ? 
            ORDER BY travel_date ASC";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $trips[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

// Calculate summary stats for the active user across all their trips
$stats_sql = "SELECT COUNT(*) AS total_trips, 
                     COALESCE(SUM(budget), 0) AS total_budget,
                     COALESCE(SUM(persons), 0) AS total_persons
              FROM trips 
              WHERE user_id = ?";
$total_trips_count = 0;
$total_budget_sum  = 0.0;
$total_persons_sum = 0;

if ($stats_stmt = mysqli_prepare($conn, $stats_sql)) {
    mysqli_stmt_bind_param($stats_stmt, "i", $user_id);
    mysqli_stmt_execute($stats_stmt);
    $stats_res = mysqli_stmt_get_result($stats_stmt);
    if ($stats_row = mysqli_fetch_assoc($stats_res)) {
        $total_trips_count = (int)$stats_row['total_trips'];
        $total_budget_sum  = (float)$stats_row['total_budget'];
        $total_persons_sum = (int)$stats_row['total_persons'];
    }
    mysqli_stmt_close($stats_stmt);
}

$avg_cost_per_person = ($total_persons_sum > 0) ? ($total_budget_sum / $total_persons_sum) : 0.0;

// Find next upcoming trip date
$next_trip_label = "None Scheduled";
$next_sql = "SELECT destination, travel_date 
             FROM trips 
             WHERE user_id = ? AND travel_date >= CURDATE() 
             ORDER BY travel_date ASC LIMIT 1";
if ($next_stmt = mysqli_prepare($conn, $next_sql)) {
    mysqli_stmt_bind_param($next_stmt, "i", $user_id);
    mysqli_stmt_execute($next_stmt);
    $next_res = mysqli_stmt_get_result($next_stmt);
    if ($next_row = mysqli_fetch_assoc($next_res)) {
        $next_trip_label = htmlspecialchars($next_row['destination'], ENT_QUOTES, 'UTF-8') . " (" . date('M j, Y', strtotime($next_row['travel_date'])) . ")";
    }
    mysqli_stmt_close($next_stmt);
}

$page_title = "My Planned Trips";
require_once 'header.php';
?>

<!-- Header Title & Action Bar -->
<div class="d-flex justify-between align-center mb-2" style="flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; color: var(--dark); font-weight: 700; letter-spacing: -0.02em;">
            My Planned Trips
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Explore, search, and manage your personalized travel itineraries with person counts and budgets.
        </p>
    </div>
    <div class="d-flex gap-1" style="flex-wrap: wrap;">
        <a href="estimator.php" class="btn btn-outline">
            🧮 Budget Estimator
        </a>
        <a href="add_trip.php" class="btn btn-primary">
            ➕ Plan a New Trip
        </a>
    </div>
</div>

<!-- Flash Alerts -->
<?php if (!empty($flash_success)): ?>
    <div class="alert alert-success" role="alert">
        <span class="alert-icon">✅</span>
        <div><?php echo htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($flash_error)): ?>
    <div class="alert alert-danger" role="alert">
        <span class="alert-icon">⚠️</span>
        <div><?php echo htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
<?php endif; ?>

<!-- Summary Metric Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));">
    <div class="stat-card">
        <div class="stat-icon primary">✈️</div>
        <div class="stat-info">
            <div class="stat-label">Total Trips</div>
            <div class="stat-value"><?php echo $total_trips_count; ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon secondary">₹</div>
        <div class="stat-info">
            <div class="stat-label">Total Planned Budget</div>
            <div class="stat-value">₹<?php echo number_format($total_budget_sum, 2); ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon primary" style="background: #e0f2fe; color: #0284c7;">👥</div>
        <div class="stat-info">
            <div class="stat-label">Total Travellers</div>
            <div class="stat-value"><?php echo $total_persons_sum; ?> <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Persons</span></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">🏷️</div>
        <div class="stat-info">
            <div class="stat-label">Avg Cost / Person</div>
            <div class="stat-value" style="font-size: 1.25rem;">
                ₹<?php echo number_format($avg_cost_per_person, 2); ?>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">📅</div>
        <div class="stat-info">
            <div class="stat-label">Next Departure</div>
            <div class="stat-value" style="font-size: 0.95rem; font-weight: 600; margin-top: 0.35rem; color: var(--dark-muted);">
                <?php echo $next_trip_label; ?>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="filter-card">
    <form action="view_trips.php" method="GET" class="filter-form">
        <div class="filter-input-wrap">
            <input 
                type="text" 
                name="search" 
                class="input-control" 
                placeholder="🔍 Search destinations (e.g. Goa, Manali, Jaipur, Kerala, Paris)..." 
                value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>"
            >
        </div>
        <button type="submit" class="btn btn-primary btn-sm">
            Search
        </button>
        <?php if (!empty($search_query)): ?>
            <a href="view_trips.php" class="btn btn-outline btn-sm">
                ✕ Clear Filter
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Trips Presentation Section -->
<?php if (empty($trips)): ?>
    <div class="empty-state">
        <div class="empty-icon">🏖️</div>
        <?php if (!empty($search_query)): ?>
            <h3>No trips matched "<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>"</h3>
            <p>Try searching for a different destination or clear your filter to view all trips.</p>
            <a href="view_trips.php" class="btn btn-outline">Clear Search Filter</a>
        <?php else: ?>
            <h3>No Trips Planned Yet</h3>
            <p>You haven't scheduled any destinations so far. Start planning your dream vacation now!</p>
            <a href="add_trip.php" class="btn btn-primary">➕ Add Your First Trip</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Destination</th>
                    <th>Travel Date</th>
                    <th>Travellers</th>
                    <th>Budget (₹ INR)</th>
                    <th>Visitor Email</th>
                    <th>Notes &amp; Details</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach ($trips as $trip): ?>
                    <?php 
                        $persons_count = max(1, (int)($trip['persons'] ?? 1));
                        $budget_amount = (float)$trip['budget'];
                        $per_person_cost = $budget_amount / $persons_count;
                    ?>
                    <tr>
                        <td style="color: var(--text-light); font-weight: 600;">
                            <?php echo $counter++; ?>
                        </td>
                        <td>
                            <strong style="color: var(--dark); font-size: 0.95rem;">
                                <?php echo htmlspecialchars($trip['destination'], ENT_QUOTES, 'UTF-8'); ?>
                            </strong>
                        </td>
                        <td>
                            <span class="badge badge-date">
                                📅 <?php echo htmlspecialchars(date('M j, Y', strtotime($trip['travel_date'])), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                👥 <?php echo $persons_count; ?> <?php echo ($persons_count === 1) ? 'Person' : 'Persons'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-currency">
                                ₹<?php echo htmlspecialchars(number_format($budget_amount, 2), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 3px;">
                                ₹<?php echo htmlspecialchars(number_format($per_person_cost, 2), ENT_QUOTES, 'UTF-8'); ?> / person
                            </div>
                        </td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($trip['visitor_email'], ENT_QUOTES, 'UTF-8'); ?>" style="color: var(--secondary); text-decoration: none;">
                                <?php echo htmlspecialchars($trip['visitor_email'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td>
                            <div class="trip-notes">
                                <?php 
                                if (!empty($trip['notes'])) {
                                    echo nl2br(htmlspecialchars($trip['notes'], ENT_QUOTES, 'UTF-8'));
                                } else {
                                    echo '<span style="color: var(--text-light); font-style: italic;">No notes added</span>';
                                }
                                ?>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <form action="view_trips.php<?php echo !empty($search_query) ? '?search=' . urlencode($search_query) : ''; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this trip to <?php echo htmlspecialchars(addslashes($trip['destination']), ENT_QUOTES, 'UTF-8'); ?>?');" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="trip_id" value="<?php echo (int)$trip['id']; ?>">
                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: #fecaca;" title="Delete this trip">
                                    🗑️ Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>

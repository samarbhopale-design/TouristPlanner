<?php
/**
 * User Login Page
 * 
 * Authenticates users using prepared SQL statements, password_verify(),
 * establishes secure session variables, and redirects to view_trips.php.
 */
require_once 'db.php';

// Safe session startup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect already logged in users to trips dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: view_trips.php");
    exit();
}

$error_message = '';
$success_message = '';
$login_identifier = '';

// Check for redirect flash messages
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success_message = "Account created successfully! Please log in below.";
} elseif (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
    $success_message = "You have been logged out safely.";
}

// Process login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_identifier = trim($_POST['login_identifier'] ?? '');
    $password         = $_POST['password'] ?? '';

    if (empty($login_identifier) || empty($password)) {
        $error_message = "Please enter both your username/email and password.";
    } else {
        // Query user by either username OR email using prepared statement
        $query = "SELECT id, username, email, password FROM users WHERE username = ? OR email = ? LIMIT 1";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "ss", $login_identifier, $login_identifier);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($user = mysqli_fetch_assoc($result)) {
                // Verify password against stored bcrypt hash
                if (password_verify($password, $user['password'])) {
                    // Prevent session fixation attacks
                    session_regenerate_id(true);

                    // Store authenticated user session state
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email']    = $user['email'];

                    mysqli_stmt_close($stmt);

                    // Redirect to protected trips overview
                    header("Location: view_trips.php");
                    exit();
                } else {
                    $error_message = "Invalid credentials. Please verify your login details and try again.";
                }
            } else {
                $error_message = "Invalid credentials. Please verify your login details and try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Authentication service encountered an issue. Please try again later.";
        }
    }
}

$page_title = "Log In";
require_once 'header.php';
?>

<div class="auth-wrapper">
    <div class="card">
        <div class="card-header text-center">
            <h2>Welcome Back</h2>
            <p>Log in to access your planned destinations and itineraries.</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <span class="alert-icon">✅</span>
                <div><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <div><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="login_identifier">Username or Email Address <span style="color: var(--danger);">*</span></label>
                <input 
                    type="text" 
                    id="login_identifier" 
                    name="login_identifier" 
                    class="input-control" 
                    placeholder="e.g. john_doe or john@example.com" 
                    value="<?php echo htmlspecialchars($login_identifier, ENT_QUOTES, 'UTF-8'); ?>" 
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password <span style="color: var(--danger);">*</span></label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="input-control" 
                    placeholder="Enter your password" 
                    required
                >
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary btn-block">
                    🔑 Log In
                </button>
            </div>
        </form>

        <div class="alert alert-info mt-2" style="font-size: 0.825rem; margin-bottom: 0.5rem;">
            <span class="alert-icon">💡</span>
            <div>
                <strong>Sample Demo Account:</strong><br>
                Username: <code>john_doe</code> | Password: <code>password123</code>
            </div>
        </div>

        <div class="text-center mt-2" style="font-size: 0.9rem; color: var(--text-muted);">
            Don't have an account yet? <a href="register.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Register Here</a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

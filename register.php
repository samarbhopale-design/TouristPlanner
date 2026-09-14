<?php
/**
 * User Registration Page
 * 
 * Secure user registration with server-side validation, duplicate checks,
 * password hashing (PASSWORD_DEFAULT), and MySQLi prepared statements.
 */
require_once 'db.php';

// Safe session startup & check if user is already logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header("Location: view_trips.php");
    exit();
}

$error_message = '';
$username = '';
$email = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1. Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please provide a valid email address.";
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error_message = "Username must be between 3 and 50 characters.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match. Please verify and try again.";
    } else {
        // 2. Check if username or email is already taken using prepared statements
        $check_sql = "SELECT id, username, email FROM users WHERE username = ? OR email = ? LIMIT 1";
        if ($stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error_message = "Username or Email is already registered. Please choose another or log in.";
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);

                // 3. Securely hash password using PHP's standard password_hash
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 4. Insert new user using prepared statements
                $insert_sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
                    mysqli_stmt_bind_param($insert_stmt, "sss", $username, $email, $hashed_password);

                    if (mysqli_stmt_execute($insert_stmt)) {
                        mysqli_stmt_close($insert_stmt);
                        // Redirect to login page with registration success status
                        header("Location: login.php?registered=1");
                        exit();
                    } else {
                        $error_message = "Registration failed due to a server error. Please try again later.";
                        mysqli_stmt_close($insert_stmt);
                    }
                } else {
                    $error_message = "Failed to prepare user insertion statement.";
                }
            }
        } else {
            $error_message = "Failed to prepare database lookup query.";
        }
    }
}

$page_title = "Create an Account";
require_once 'header.php';
?>

<div class="auth-wrapper">
    <div class="card">
        <div class="card-header text-center">
            <h2>Create an Account</h2>
            <p>Join the Tourist Place Visit Planner and organize your journeys.</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <div><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="username">Username <span style="color: var(--danger);">*</span></label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="input-control" 
                    placeholder="e.g. wanderlust_sam" 
                    value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" 
                    required 
                    minlength="3" 
                    maxlength="50"
                    autofocus
                >
                <div class="input-hint">3 to 50 characters, letters, numbers, and underscores.</div>
            </div>

            <div class="form-group">
                <label for="email">Email Address <span style="color: var(--danger);">*</span></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="input-control" 
                    placeholder="e.g. sam@example.com" 
                    value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" 
                    required 
                    maxlength="100"
                >
            </div>

            <div class="form-group">
                <label for="password">Password <span style="color: var(--danger);">*</span></label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="input-control" 
                    placeholder="At least 6 characters" 
                    required 
                    minlength="6"
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password <span style="color: var(--danger);">*</span></label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="input-control" 
                    placeholder="Re-enter your password" 
                    required 
                    minlength="6"
                >
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary btn-block">
                    ✨ Create Account
                </button>
            </div>
        </form>

        <div class="text-center mt-2" style="font-size: 0.9rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Log In Here</a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

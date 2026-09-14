<?php
/**
 * Tourist Place Visit Planner - Landing Page
 * 
 * Explains the planner features and presents dynamic CTAs according to
 * authentication state.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Welcome";
require_once 'header.php';
$is_authenticated = isset($_SESSION['user_id']);
?>

<!-- Hero Banner Section -->
<section class="hero">
    <div class="hero-pill">
        <span>✨ Simple, Secure &amp; Lightweight Travel Planning</span>
    </div>
    <h1>
        Plan Your Next Adventure with <br>
        <span class="gradient-text">Tourist Place Visit Planner</span>
    </h1>
    <p>
        Effortlessly organize destinations, manage travel budgets, set scheduled travel dates, and keep critical travel notes all in one centralized and secure dashboard.
    </p>

    <div class="hero-actions">
        <?php if ($is_authenticated): ?>
            <a href="view_trips.php" class="btn hero-btn-white">
                📋 View My Planned Trips
            </a>
            <a href="add_trip.php" class="btn btn-secondary">
                ➕ Plan a New Trip
            </a>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary">
                ✨ Get Started Free
            </a>
            <a href="login.php" class="btn hero-btn-white">
                🔑 Log In to Account
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Core Features Section -->
<section class="features-section">
    <div class="section-title">
        <h2>Everything You Need For Your Journeys</h2>
        <p>Built for solo travelers, families, and backpackers looking for an organized itinerary.</p>
    </div>

    <div class="features-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
        <div class="feature-card">
            <span class="feature-icon">🗺️</span>
            <h3>Destination Management</h3>
            <p>Catalog all your dream locations, famous landmarks, and places across India and the globe with clear schedules.</p>
        </div>

        <div class="feature-card">
            <span class="feature-icon">👥</span>
            <h3>Travellers &amp; Group Size</h3>
            <p>Specify the exact number of persons per journey to accurately calculate total and per-person travel budgets.</p>
        </div>

        <div class="feature-card">
            <span class="feature-icon">🧮</span>
            <h3>Smart Budget Estimator</h3>
            <p>Calculate realistic INR costs in real-time based on destination type, duration, and backpacker to luxury travel tiers.</p>
        </div>

        <div class="feature-card">
            <span class="feature-icon">📅</span>
            <h3>Date Scheduling</h3>
            <p>Lock in your travel dates and stay ahead of booking deadlines and seasonal attractions with sorted timelines.</p>
        </div>

        <div class="feature-card">
            <span class="feature-icon">📝</span>
            <h3>Itinerary Notes</h3>
            <p>Save personal reminders, must-try restaurants, packing checklists, ticket references, and hotel details alongside each trip.</p>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="card" style="margin-top: 2rem;">
    <div class="card-header text-center" style="border-bottom: none; margin-bottom: 1rem;">
        <h2>Simple 3-Step Journey</h2>
        <p>Get up and running in less than two minutes.</p>
    </div>

    <div class="features-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: 0.5rem;">01</div>
            <h4 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: var(--dark);">Create Your Account</h4>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Sign up securely with just a username, email, and password.</p>
        </div>

        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 2rem; font-weight: 800; color: var(--secondary); margin-bottom: 0.5rem;">02</div>
            <h4 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: var(--dark);">Add Trip Plans</h4>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Enter destinations, budgets, travel dates, and personal notes.</p>
        </div>

        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-dark); margin-bottom: 0.5rem;">03</div>
            <h4 style="font-size: 1.1rem; margin-bottom: 0.4rem; color: var(--dark);">Track &amp; Travel</h4>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Search, filter, and review your vacation schedule anytime.</p>
        </div>
    </div>

    <div class="text-center" style="margin-top: 1.5rem;">
        <?php if ($is_authenticated): ?>
            <a href="view_trips.php" class="btn btn-primary">Go to My Trips &rarr;</a>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary">Register Now &rarr;</a>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'footer.php'; ?>

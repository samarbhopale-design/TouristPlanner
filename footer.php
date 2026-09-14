    </main>
    <!-- End Main Content Container Wrapper -->

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div>
                <p>&copy; <?php echo date('Y'); ?> <strong>Tourist Place Visit Planner</strong>. All rights reserved.</p>
                <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.2rem;">
                    Lightweight, secure procedural PHP &amp; MySQLi web application.
                </p>
            </div>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="estimator.php">Estimator</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="view_trips.php">My Trips</a></li>
                    <li><a href="add_trip.php">Add Trip</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </footer>

</body>
</html>

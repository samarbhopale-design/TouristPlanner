
🌍 Tourist Place Visit Planner
​A complete, secure, lightweight web application for planning and organizing tourist visits. Featuring a robust user authentication system and clean custom CSS, this project is built with procedural PHP (MySQLi) and designed specifically for standard LAMP/WAMP/XAMPP environments and shared hosting platforms like InfinityFree.
​🚀 View Live Deployment | 💻 View Source Code

---

## 📁 File Structure

```
d:/samar/
├── database.sql        # MySQL database schema, foreign keys, dummy user & 10 sample trips
├── db.php              # Procedural mysqli database connection & error suppression
├── style.css           # Unified modern design system (Pure CSS, 0 external frameworks)
├── header.php          # Session starter, responsive navbar, and dynamic user links
├── footer.php          # Consistent page footer & layout closing tags
├── index.php           # Landing page with hero section, feature cards & dynamic CTAs
├── register.php        # User registration with validation & bcrypt password hashing
├── login.php           # Authentication handler with password_verify() & session security
├── logout.php          # Safe session destruction and redirection
├── add_trip.php        # Protected trip creation form with persons & built-in budget estimator
├── view_trips.php      # Protected trip listing, destination search, travellers, & per-person cost
├── estimator.php       # Standalone interactive trip budget calculation & estimation tool
└── README.md           # Documentation and setup instructions
```

---

## ⚙️ Setup & Installation Instructions

### Option 1: Local Development (XAMPP / WAMP / MAMP)

1. **Move files to web directory**:
   - For XAMPP: Copy `d:/samar` contents to `C:/xampp/htdocs/tourist_planner/`.
2. **Start Apache and MySQL**:
   - Launch XAMPP Control Panel and start **Apache** and **MySQL**.
3. **Import the Database**:
   - Open phpMyAdmin (`http://localhost/phpmyadmin/`).
   - Click **Import** &rarr; Choose `database.sql` &rarr; Click **Go**.
   - This creates the `tourist_planner` database, `users` table, `trips` table, and seeds the dummy user and sample trips.
4. **Access the Application**:
   - Navigate to `http://localhost/tourist_planner/index.php`.

---

### Option 2: InfinityFree Hosting Deployment

1. **Create Free Account**:
   - Log into your InfinityFree account and open the **vPanel** (Control Panel).
2. **Create MySQL Database**:
   - In vPanel, go to **MySQL Databases** and create a new database (e.g. `tourist_planner`).
   - Note down your database details:
     - **MySQL Host**: (e.g. `sql100.epizy.com`)
     - **MySQL Database**: (e.g. `epiz_XXXXXXXX_tourist_planner`)
     - **MySQL Username**: (e.g. `epiz_XXXXXXXX`)
     - **MySQL Password**: (Your account password)
3. **Import `database.sql` via phpMyAdmin**:
   - In vPanel, click **phpMyAdmin** next to your database.
   - Click **Import** &rarr; Select `database.sql` &rarr; Click **Go**.
4. **Update `db.php` Credentials**:
   - Open `db.php` and update the constants:
     ```php
     define('DB_SERVER', 'sqlXXX.epizy.com');
     define('DB_USERNAME', 'epiz_XXXXXXXX');
     define('DB_PASSWORD', 'YourPassword');
     define('DB_NAME', 'epiz_XXXXXXXX_tourist_planner');
     ```
5. **Upload Files via FTP / File Manager**:
   - Upload all files into the `htdocs/` folder on InfinityFree.

---

## 🔑 Default Seed Account & Raw Trip Data

The database includes a pre-registered dummy user:

- **Username**: `john_doe`
- **Email**: `john@example.com`
- **Password**: `password123`

The dummy user comes pre-populated with **10 diverse, realistic raw trips** with budgets in Indian Rupees (₹ INR):
1. **Goa Beaches & Waterfalls, India** (₹28,500.00)
2. **Manali & Rohtang Pass, Himachal Pradesh** (₹36,000.00)
3. **Jaipur & Udaipur, Rajasthan** (₹45,000.00)
4. **Munnar & Alleppey Backwaters, Kerala** (₹42,500.00)
5. **Leh-Ladakh & Pangong Tso, India** (₹68,000.00)
6. **Varanasi & Sarnath, Uttar Pradesh** (₹22,000.00)
7. **Dubai & Abu Dhabi, UAE** (₹1,15,000.00)
8. **Bali & Nusa Penida, Indonesia** (₹92,000.00)
9. **Tokyo & Kyoto, Japan** (₹1,85,000.00)
10. **Paris & Swiss Alps, Europe** (₹2,65,000.00)

---

## 🛡️ Security Features Implemented

1. **SQL Injection Defense**: 100% of database queries with dynamic inputs use `mysqli_prepare()`, `mysqli_stmt_bind_param()`, and `mysqli_stmt_execute()`.
2. **Password Security**: Passwords are never stored in plaintext. Encrypted using `password_hash($password, PASSWORD_DEFAULT)` with bcrypt and verified with `password_verify()`.
3. **XSS Protection**: All output rendered to HTML is sanitized using `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`.
4. **Session Fixation Prevention**: Session IDs are regenerated via `session_regenerate_id(true)` upon successful login.
5. **Information Leakage Prevention**: Raw database errors are intercepted and suppressed from users with generic fallback notifications.
6. **Data Isolation**: All trip queries enforce `WHERE user_id = ?` to strictly isolate user records.

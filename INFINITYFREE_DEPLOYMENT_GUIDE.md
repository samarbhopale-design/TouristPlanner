# 🚀 Complete InfinityFree Deployment Guide
## Tourist Place Visit Planner (PHP & MySQLi)

This guide walks you step-by-step through deploying your **Tourist Place Visit Planner** project to **InfinityFree** hosting for 100% free with a live public URL.

---

## 📋 Pre-Deployment Checklist

You have everything ready in your project directory (`d:/samar/`):
- ✅ [**`database.sql`**](file:///d:/samar/database.sql) — Configured safely for InfinityFree (no conflicting `CREATE DATABASE` queries).
- ✅ [**`db.php`**](file:///d:/samar/db.php) — Features automatic local vs InfinityFree auto-switching.
- ✅ [**`tourist_planner_infinityfree.zip`**](file:///d:/samar/tourist_planner_infinityfree.zip) — Complete pre-packaged archive ready for 1-click upload and extraction.

---

## Step 1: Create a Free Hosting Account on InfinityFree

1. Go to [https://www.infinityfree.com](https://www.infinityfree.com) and click **Sign Up** (or Log In).
2. Once logged into the InfinityFree Client Area, click **+ Create Account**.
3. Choose a domain type:
   - Select **Free Subdomain** (e.g. `.infinityfreeapp.com` or `.epizy.com`).
   - Enter your desired domain prefix (e.g., `my-tourist-planner`).
   - Click **Check Availability**.
4. Set an Account Password and click **Create Account**.
5. Once created, click **Open Control Panel** (vPanel). If prompted with a notification, click **"I Approve"**.

---

## Step 2: Create Your MySQL Database in vPanel

> [!IMPORTANT]
> InfinityFree assigns a unique prefix to all databases and usernames (e.g., `epiz_41234567_`).

1. In your **vPanel**, find the **Databases** section and click **MySQL Databases**.
2. Under **Create a New Database**, enter a database name suffix:
   - Example: `tourist_planner`
   - Click **Create Database**.
3. The database will now appear in your list with a full name like `epiz_XXXXXXXX_tourist_planner`.
4. Note down the following 4 details displayed on that page:
   - **MySQL Host**: (e.g., `sql205.infinityfree.com` or `sql100.epizy.com`)
   - **MySQL Database Name**: (e.g., `epiz_XXXXXXXX_tourist_planner`)
   - **MySQL Username**: (e.g., `epiz_XXXXXXXX`)
   - **MySQL Password**: (Your vPanel Account Password from your client area)

---

## Step 3: Import `database.sql` via phpMyAdmin

1. On the same **MySQL Databases** page in vPanel, scroll to your database and click the **phpMyAdmin** button next to it.
2. In phpMyAdmin, look at the **left sidebar** and click on your database name (`epiz_XXXXXXXX_tourist_planner`) to select it.
3. Click on the **Import** tab at the top menu bar.
4. Under **File to import**, click **Choose File** (or Browse) and select [`database.sql`](file:///d:/samar/database.sql) from your project folder.
5. Scroll to the bottom and click **Go** (or Import).
6. You will see green success messages:
   - Tables `users` and `trips` created.
   - Default user (`john_doe` / `password123`) and 10 sample trips in ₹ INR imported.

---

## Step 4: Configure `db.php` with Your InfinityFree Credentials

Open [`db.php`](file:///d:/samar/db.php) and update the production branch with the 4 values gathered in Step 2:

```php
    } else {
        // =====================================================================
        // INFINITYFREE HOSTING CONFIGURATION
        // =====================================================================
        define('DB_SERVER', 'sqlXXX.infinityfree.com');      // 1. MySQL Host Name
        define('DB_USERNAME', 'epiz_XXXXXXXX');              // 2. MySQL User Name
        define('DB_PASSWORD', 'YOUR_ACCOUNT_PASSWORD');      // 3. MySQL Password
        define('DB_NAME', 'epiz_XXXXXXXX_tourist_planner');  // 4. Database Name
    }
```

> [!TIP]
> Because of the automatic `$is_local` check in `db.php`, this file continues to work on both your local XAMPP environment and on InfinityFree!

---

## Step 5: Upload Files to `htdocs/` via Online File Manager

1. Back in your InfinityFree Client Area, click **File Manager** (or in vPanel, click **Online File Manager**).
2. Double-click on the **`htdocs`** folder to enter it.
3. If there are default placeholder files like `DO NOT UPLOAD FILES HERE` or default `index2.html`, select them and click **Delete**.
4. Upload your files:
   - **Option A (Fastest - Upload Zip)**:
     1. Click the **Upload** icon in the toolbar &rarr; Choose **Upload Zip**.
     2. Select [`tourist_planner_infinityfree.zip`](file:///d:/samar/tourist_planner_infinityfree.zip).
     3. Choose **Upload & Extract**.
     4. File Manager will automatically extract all PHP, CSS, and SQL files directly into `htdocs/`.
   - **Option B (File by File)**:
     1. Click the **Upload** icon &rarr; Choose **Upload Folder** or select all files (`index.php`, `login.php`, `register.php`, `add_trip.php`, `view_trips.php`, `estimator.php`, `header.php`, `footer.php`, `db.php`, `style.css`).
5. Ensure your updated `db.php` (with your actual InfinityFree credentials from Step 4) is uploaded.

---

## Step 6: Test Your Live Website!

1. Open your browser and navigate to your InfinityFree domain:
   ```
   http://your-subdomain.infinityfreeapp.com/index.php
   ```
2. You will see the **Tourist Place Visit Planner** landing page!
3. Click **🔑 Login** and test with your demo credentials:
   - **Username**: `john_doe`
   - **Password**: `password123`
4. Verify your 10 sample trips on the dashboard, test the search filter, and calculate travel costs with the **🧮 Budget Estimator**!

---

## 🛠️ Common Troubleshooting

| Issue | Cause | Solution |
| :--- | :--- | :--- |
| **"Service Temporarily Unavailable" card** | Database details incorrect in `db.php` | Double-check `DB_SERVER` (it is NOT `localhost`), `DB_USERNAME`, `DB_PASSWORD`, and `DB_NAME` in `db.php`. |
| **phpMyAdmin Error 1044** | Running `CREATE DATABASE` query | Ensure you use our updated `database.sql` where `CREATE DATABASE` is commented out. |
| **"Directory index forbidden by Options directive"** | Files uploaded outside `htdocs` or no `index.php` | Make sure all PHP files are directly inside `/htdocs/`, not inside a subfolder. |
| **404 Page Not Found** | DNS propagation delay | Free domains can take 5–15 minutes to propagate worldwide. Flush DNS or test via incognito window. |

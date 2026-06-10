# Zenzele Smart Market — Backend Setup Guide
### From Frontend Prototype → Full Working System

---

## PHASE 1 — Install XAMPP (10 minutes)

### Step 1: Download XAMPP

Go to: https://www.apachefriends.org/download.html

Download the version for your operating system:
- Windows → XAMPP for Windows (.exe installer)
- Mac → XAMPP for macOS (.dmg)
- Linux → XAMPP for Linux (.run)

### Step 2: Install XAMPP

Run the installer. Accept all defaults. You do NOT need to change any settings during installation.

Default install location:
- Windows: `C:\xampp\`
- Mac: `/Applications/XAMPP/`

### Step 3: Start Apache and MySQL

Open the XAMPP Control Panel (it opens automatically after install).

Click **Start** next to **Apache**.
Click **Start** next to **MySQL**.

Both should turn green. If they don't start, see Troubleshooting at the bottom.

### Step 4: Test that XAMPP works

Open your browser and go to: `http://localhost`

You should see the XAMPP welcome page. If you do, XAMPP is working.

---

## PHASE 2 — Move Your Project Into XAMPP (2 minutes)

### Step 5: Find the htdocs folder

This is where XAMPP serves files from:
- Windows: `C:\xampp\htdocs\`
- Mac: `/Applications/XAMPP/htdocs/`

### Step 6: Copy your project

Copy your entire `zenzele-smart-market` folder into `htdocs`.

After this, your folder structure should look like:

```
C:\xampp\htdocs\
└── zenzele-smart-market\
    ├── index.html
    ├── css\
    ├── js\
    ├── pages\
    ├── php\
    │   ├── api.php
    │   ├── auth.php
    │   ├── config.php
    │   ├── schema.sql
    │   └── ...
    └── assets\
```

### Step 7: Test your project runs through XAMPP

Open your browser and go to: `http://localhost/zenzele-smart-market/`

You should see your homepage. This is now running through Apache, not Live Server.

> IMPORTANT: From now on, always open the site using `http://localhost/zenzele-smart-market/`
> NOT from the file system directly, and NOT from VS Code Live Server.
> The PHP backend only works when accessed through `http://localhost`.

---

## PHASE 3 — Create the Database (5 minutes)

### Step 8: Open phpMyAdmin

Go to: `http://localhost/phpmyadmin`

This is the visual database manager that comes with XAMPP.

### Step 9: Create the database

1. Click **New** in the left sidebar
2. In the "Database name" field, type exactly: `zenzele_db`
3. Set collation to: `utf8mb4_unicode_ci`
4. Click **Create**

### Step 10: Import the schema

1. Click on `zenzele_db` in the left sidebar (to select it)
2. Click the **Import** tab at the top
3. Click **Choose File**
4. Navigate to your project: `zenzele-smart-market/php/schema.sql`
5. Click **Import** (or **Go** at the bottom)

You should see a success message and the left sidebar should now show tables:
`users`, `nfts`, `donations`, `follows`, `likes`, `training_requests`

If you see all 6 tables, your database is ready.

---

## PHASE 4 — Configure PHP to Connect to Your Database (2 minutes)

### Step 11: Edit config.php

Open this file in your code editor:
`zenzele-smart-market/php/config.php`

Find these lines near the top:

```php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'zenzele_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
```

For XAMPP on your local machine, these defaults are already correct:
- DB_HOST = `localhost` ✅
- DB_NAME = `zenzele_db` ✅  
- DB_USER = `root` ✅
- DB_PASS = `` (empty) ✅ — XAMPP's default MySQL password is blank

You do NOT need to change anything for local XAMPP.

### Step 12: Also update APP_URL

Find this line:

```php
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8080');
```

Change it to:

```php
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/zenzele-smart-market');
```

Save the file.

---

## PHASE 5 — Test the API (5 minutes)

### Step 13: Test the health endpoint

Open your browser and go to:
`http://localhost/zenzele-smart-market/php/api.php?route=health`

You should see a JSON response like:
```json
{
  "success": true,
  "data": {
    "status": "ok",
    "app": "Zenzele Smart Market API",
    "version": "1.0.0",
    "network": "testnet",
    "time": "2026-05-..."
  }
}
```

If you see this → your PHP backend is connected to MySQL and working.

### Step 14: Test registration via API

You can test this using your browser's address bar or a tool like Postman / Hoppscotch (free at hoppscotch.io).

Test URL: `http://localhost/zenzele-smart-market/php/api.php?route=auth&action=register`
Method: POST
Body (JSON):
```json
{
  "name": "Test User",
  "email": "test@zenzele.co",
  "password": "testpass123",
  "country": "ZA",
  "city": "Johannesburg",
  "category": "Coding",
  "bio": "Testing the Zenzele backend integration."
}
```

You should get back:
```json
{ "success": true, "data": { "token": "eyJ...", "user_id": 1, "name": "Test User" } }
```

Then check phpMyAdmin → zenzele_db → users table. You should see a new row with your test user, and the password should be a long hash starting with `$2y$` — NOT plain text.

---

## PHASE 6 — Update api-client.js to Point to XAMPP (1 minute)

### Step 15: Update the API base URL

Open: `zenzele-smart-market/js/api-client.js`

Find this section near the top:

```javascript
const API_BASE = (function () {
  const host = window.location.hostname;
  if (host === 'localhost' || host === '127.0.0.1') {
    return '/php/api.php';
  }
  return '/php/api.php';
})();
```

This is already correct for XAMPP. No changes needed here.

### Step 16: Verify the connection banner appears green

Open your site at `http://localhost/zenzele-smart-market/pages/dashboard.html`

After logging in, the banner at the top of the dashboard should say:
**✅ Backend connected — data is saving to MySQL.**

If it's still orange (demo mode), go back and check steps 11–14.

---

## PHASE 7 — Test End-to-End With Two Browsers (30 minutes)

This is the most important test. It proves your data is real and not just localStorage.

### Step 17: Register two users

1. Open Chrome: go to `http://localhost/zenzele-smart-market/pages/register.html`
   Register as `user1@test.co` / password `test1234`

2. Open Firefox (or an incognito window): go to the same register page
   Register as `user2@test.co` / password `test1234`

### Step 18: Check both users appear in phpMyAdmin

Go to `http://localhost/phpmyadmin` → zenzele_db → users table

You should see both users listed. They each exist in the real database, not just in one browser.

### Step 19: Test profile visibility across browsers

In Chrome (logged in as user1): go to Explore page. You should see user2's profile.
In Firefox (logged in as user2): go to Explore page. You should see user1's profile.

If both users can see each other, your backend is working correctly.

---

## PHASE 8 — Deploy to a Live Host (2–3 hours)

For the contest, judges need a live URL they can visit. localhost only works on your computer.

### Hosting options (cheapest to easiest):

**Option A — InfinityFree (100% free)**
- URL: https://infinityfree.com
- Supports PHP + MySQL
- Free subdomain like `zenzele.infinityfreeapp.com`
- Good enough for the contest

**Option B — 000webhost (free)**
- URL: https://www.000webhost.com
- Also free, supports PHP + MySQL

**Option C — Shared hosting (paid, R50-R150/month in South Africa)**
- Any South African host (Afrihost, Hetzner, Xneelo)
- Most include one-click PHP + MySQL
- Better performance and reliability

### Deployment steps (same for all hosts):

1. Create account on your chosen host
2. Create a new MySQL database in their control panel (same as phpMyAdmin but theirs)
3. Import your `schema.sql` using their phpMyAdmin
4. Create a `config.local.php` file with your live host's DB credentials:
   ```php
   <?php
   define('DB_HOST', 'their-db-host.com');
   define('DB_NAME', 'their_db_name');
   define('DB_USER', 'their_db_user');
   define('DB_PASS', 'their_db_password');
   define('APP_URL', 'https://yourdomain.com');
   ```
5. Upload all your project files via FTP (use FileZilla — free)
6. Update `api-client.js` — change `API_BASE` to your live URL:
   ```javascript
   return 'https://yourdomain.com/php/api.php';
   ```
7. Test registration on the live URL

---

## Troubleshooting

### Apache won't start
Port 80 is probably in use by something else (Skype, another server).
In XAMPP Control Panel → Apache → Config → change `Listen 80` to `Listen 8080`.
Then access via `http://localhost:8080/` instead.

### MySQL won't start
Port 3306 is in use. In XAMPP → MySQL → Config → my.ini, change `port=3306` to `port=3307`.

### "Access denied for user root" error
Open phpMyAdmin → User accounts → find `root@localhost` → Edit privileges → set password to blank.

### API returns blank page instead of JSON
Make sure you're accessing via `http://localhost/...` not `file:///...`. PHP only runs through a web server.

### "Column not found" SQL error
Your schema might be outdated. In phpMyAdmin, drop all tables and re-import `schema.sql` from scratch.

---

## What Stays Simulated (For Now)

These features are simulated with fake data and do NOT require backend work for the contest:

| Feature | Status | What to tell judges |
|---------|--------|---------------------|
| NFT minting | Simulated | "Cardano testnet integration in progress, using Haskell/Plutus" |
| ADA donations | Simulated | "Lucid off-chain transaction code in progress" |
| IPFS audio storage | localStorage blob | "IPFS pinning integration in progress" |
| Wallet connection | Not implemented | "CowryWallet integration planned post-contest" |

These are acceptable for the contest because the contest spec says Cardano integration is part of the build — you are demonstrating the architecture and showing where it connects.

---

## Minimum Viable Contest Submission Checklist

- [ ] XAMPP running locally (Apache + MySQL green)
- [ ] zenzele_db created with all 6 tables
- [ ] Registration saves real user to MySQL (verify in phpMyAdmin)
- [ ] Login works with hashed passwords
- [ ] Two different browsers/users can see each other's profiles
- [ ] Dashboard shows real data from database
- [ ] Site deployed to a live URL (not just localhost)
- [ ] AI_USAGE.md is complete and up to date
- [ ] GitHub repo forked from wimsio/zenzelesmartmarket
- [ ] Pull request submitted before 5 June 2026

---

*Zenzele Smart Market — Be Self-Reliant. Build Your Future.*

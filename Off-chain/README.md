# Off-chain (PHP) — Zenzele Smart Market

This directory contains the PHP backend, public web pages and database schema for the Zenzele Smart Market web site.

Overview
- Location: this directory holds the server-side PHP code and public assets served by Apache/PHP (XAMPP or LAMP).
- Key folders:
  - `api/` — REST endpoints used by the front-end (authentication, donations, nfts, profiles).
  - `app/config/` — database configuration template (`db.example.php`).
  - `app/models/` — PHP models for Donations, Profiles, User, NFTs.
  - `database/` — `schema.sql`  to create the DB.
  - `public/` — web entry points (e.g. `index.php`, `nft.php`) and static assets (`assets/`, `auth/`, `header/`).

Requirements
- Linux / Windows with Apache + PHP (recommended XAMPP for local development).
- PHP 7.4+ with PDO MySQL extension enabled.
- MySQL / MariaDB.

Quick setup
1. Install a LAMP/XAMPP stack and place this `Off-chain` folder under your web root (for example `/opt/lampp/htdocs/` for XAMPP).
2. Copy the example DB config:

```bash
cp app/config/db.example.php app/config/db.php
```

3. Edit `app/config/db.php` and set `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` to match your MySQL credentials.

4. Create the database and tables, and optionally load seed data:

```bash
mysql -u <db_user> -p < database/schema.sql
mysql -u <db_user> -p < database/seed.sql
```

5. Ensure upload folders are writable by the web server (adjust paths to `public/uploads`, `uploads/` as needed):

```bash
sudo chown -R www-data:www-data public/uploads uploads
sudo chmod -R 755 public/uploads uploads
```

6. Start Apache/PHP and open the site in your browser (example):

```
http://localhost/zenzelesmartmarket/Off-chain/public/index.php
```

Notes and tips
- Authentication: API endpoints for Web2 and Web3 are under `api/auth/`. The project also includes Web3-style login using wallet signatures; see `api/auth/request_nonce.php` and `verify_signature.php`.
- Database: `database/schema.sql` defines `users`, `profiles`, `donations`, and `nfts` tables. Review `private_key` and sensitive fields before using in production — do NOT store plaintext private keys in production.
- Frontend: the `public/` folder contains the client pages and JS under `public/assets/js/`.
- If you move the project root, update any hard-coded paths in PHP includes and asset links.

- Default language
  - The frontend uses a small i18n loader at `public/assets/js/i18n.js`. By default the site will display English (`en`) unless a user explicitly selects and saves a different language. You can change the default behavior by editing that file or by using the language selector in the UI (it stores the choice in `localStorage` under the key `zenzele_lang`).

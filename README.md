# Zenzele Smart Market 🌍

**Be Self-Reliant. Build the Future. Change Lives.**

A decentralised entrepreneur marketplace built on Cardano blockchain that empowers South African entrepreneurs to create profiles, receive ADA donations, mint NFTs, and connect with supporters worldwide.

## 🔴 Live Demo
**Website:** http://zenzele-prefina.byethost11.com

**Test Account:**
- Email: `test@test.com`
- Password: `12345678`

## 📋 Competition Submission
- **Competition:** Zenzele Smart Market Developer Contest 2026
- **Developer:** Prefina Kayembe (PrefinaK)
- **GitHub:** https://github.com/PrefinaK/Zenzele-market-prefina
- **Deadline:** 5 June 2026 ✅ Submitted on time

## ✅ Features Implemented

### Core Platform
- 👤 User registration and login with JWT authentication
- 📊 Entrepreneur dashboard with profile management
- 🌍 Explore page to discover other entrepreneurs
- 📣 Activity feed and profile completion tracker

### Cardano Blockchain Integration
- 🔗 Lace wallet connection (Preprod testnet)
- 💰 Real ADA donations via Cardano Preprod
- 🏆 NFT minting for business achievements
- ⛓️ Blockfrost API integration

### Media & Content
- 🎙️ Audio pitch recording saved to MySQL + disk
- 🖼️ Service image upload (proof of work gallery)
- 🌟 Success Stories page with community impact stats

### Technical Fixes
- ✅ JWT Authorization header fix for Apache/XAMPP
- ✅ Audio upload handler (Chrome webm support)
- ✅ CORS headers for cross-origin API calls
- ✅ .htaccess for URL rewriting and PHP limits

## 🏗️ Tech Stack
| Layer | Technology |
|-------|-----------|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP 8, MySQL |
| Blockchain | Cardano (Lucid, Blockfrost) |
| Auth | JWT (JSON Web Tokens) |
| Hosting | Byethost (PHP+MySQL) |
| Storage | MySQL + local file uploads |

## 🚀 Local Setup (XAMPP)
```bash
# 1. Clone repo
git clone https://github.com/PrefinaK/Zenzele-market-prefina.git

# 2. Place in XAMPP htdocs
# C:\xampp\htdocs\zenzele-smart-market\

# 3. Start Apache + MySQL in XAMPP

# 4. Import database
# Open phpMyAdmin → create database 'zenzele_db' → import php/schema.sql

# 5. Install Lucid for Cardano features
npm install lucid-cardano@0.10.11

# 6. Visit http://localhost/zenzele-smart-market
```

## 📁 Project Structure
```
zenzele-smart-market/
├── index.html              # Landing page
├── pages/
│   ├── dashboard.html      # User dashboard
│   ├── explore.html        # Browse entrepreneurs
│   ├── profile.html        # Public profile view
│   ├── success-stories.html # Community impact page
│   └── cardano-setup.html  # Wallet setup guide
├── js/
│   ├── api-client.js       # Backend API bridge
│   ├── cardano.js          # Cardano/Lucid integration
│   └── main.js             # Core utilities
├── php/
│   ├── api.php             # API router
│   ├── auth.php            # Registration/login
│   ├── profiles.php        # Profile management
│   ├── audio.php           # Audio pitch upload
│   ├── nfts.php            # NFT records
│   ├── donations.php       # Donation tracking
│   └── config.php          # DB config + JWT auth
└── uploads/
    └── audio/              # Saved audio pitches
```


## 📞 Contact
- **Developer:** Prefina Kayembe
- **GitHub:** @PrefinaK
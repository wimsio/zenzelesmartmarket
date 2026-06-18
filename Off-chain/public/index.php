<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenzele Smart Market</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  
       <header>
        <nav class="container">
            <div class="logo">
                <a href="index.php" class="btn" data-i18n="btn_home"><strong>Zenzele</strong> <span class="tagline" data-i18n="nav_tagline">Smart Market</span></a>
            </div>
            
            <div class="lang-selector">
                <select id="langSelect" aria-label="Change language">
                    <option value="en">English</option>
                    <option value="zu">isiZulu</option>
                    <option value="xh">isiXhosa</option>
                    <option value="st">Sesotho</option>
                    <option value="tn">Setswana</option>
                    <option value="af">Afrikaans</option>
                    <option value="sw">Swahili</option>
                </select>
            </div>
        </nav>
</header>

    <main>
        <section class="hero container">
            <h1 data-i18n="hero_title">Become self-reliant through your know-how</h1>
            <p data-i18n="hero_subtitle">Zenzele helps you promote your skills, find support, secure funding via Cardano, and build a real economic activity.</p>
            
            <div class="actions">
                <a href="auth/login.php" class="btn btn-primary" data-i18n="btn_join">Join the platform</a>
                <a href="auth/register.php" class="btn btn-secondary" data-i18n="btn_explore">Explore entrepreneurs</a>
            </div>
        </section>

        <section class="db-setup container" style="margin-top:18px; padding:12px; background:#fff8ea; border:1px solid #f0d2b3; border-radius:8px;">
            <h3 style="margin:0 0 8px 0; font-size:16px;">Database setup (quick)</h3>
            <p style="margin:0 0 8px 0; color:#5b4632;">To create the application's database locally, copy and run the SQL statements found in <strong>Off-chain/database/schema.sql</strong>. Example command:</p>
            <pre style="background:#f7efe6;padding:10px;border-radius:6px;color:#5b4632;margin:0;">mysql -u &lt;db_user&gt; -p &lt; Off-chain/database/schema.sql</pre>
            <p style="margin:8px 0 0 0;color:#5b4632;">After running the script, update your database credentials in <strong>Off-chain/app/config/db.php</strong> (you can copy from <strong>db.example.php</strong>).</p>
        </section>

        <section id="explore" class="entrepreneurs container">
            <h2 data-i18n="section_explore_title">Featured Entrepreneurs</h2>
            <div class="grid" id="entrepreneursGrid">
                </div>
        </section>
    </main>

    <footer>
        <p class="container">&copy; 2026 Zenzele Smart Market. <span data-i18n="footer_text">Open Source & Decentralized.</span></p>
    </footer>

    <script src="assets/js/i18n.js"></script>
    <script src="assets/js/audio.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
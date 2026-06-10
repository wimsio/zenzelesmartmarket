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
            <h1 data-i18n="hero_title">Devenez autonome grâce à votre savoir-faire</h1>
            <p data-i18n="hero_subtitle">Zenzele vous aide à promouvoir vos compétences, trouver des soutiens, obtenir des financements via Cardano et développer votre activité.</p>
            
            <div class="actions">
                <a href="auth/login.php" class="btn btn-primary" data-i18n="btn_join">Rejoindre la plateforme</a>
                <a href="auth/register.php" class="btn btn-secondary" data-i18n="btn_explore">Découvrir les entrepreneurs</a>
            </div>
        </section>

        <section id="explore" class="entrepreneurs container">
            <h2 data-i18n="section_explore_title">Entrepreneurs à la une</h2>
            <div class="grid" id="entrepreneursGrid">
                </div>
        </section>
    </main>

    <footer>
        <p class="container">&copy; 2026 Zenzele Smart Market. <span data-i18n="footer_text">Open Source & Décentralisé.</span></p>
    </footer>

    <script src="assets/js/i18n.js"></script>
    <script src="assets/js/audio.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
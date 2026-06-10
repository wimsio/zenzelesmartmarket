<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenzele Smart Market - Inscription</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <?php include '../header/indexheader.html'; ?>

    <main class="container">
        <div class="auth-box">
            <h1 data-i18n="login_title">Se connecter à son compte Zenzele</h1>
            <p class="subtitle" data-i18n="login_subtitle">Rejoignez le marché de l'autonomie et de l'entrepreneuriat</p>

            <div class="web3-option">
                <button type="button" id="btnLoginWallet" class="btn btn-primary w-full">
                    <span class="icon">💳</span> <span data-i18n="btn_login_coxy">Se connecter avec Coxy Wallet</span>
                </button>
                <div class="divider"><span data-i18n="auth_or">OU</span></div>
            </div>

            <form id="loginForm" onsubmit="return false;">
                <div class="form-group">
                    <label for="loginEmail" data-i18n="label_email">Adresse E-mail</label>
                    <input type="email" id="loginEmail" required placeholder="Ex: amina@example.com">
                </div>

                <div class="form-group">
                    <label for="loginPassword" data-i18n="label_password">Mot de passe</label>
                    <input type="password" id="loginPassword" required minlength="8" placeholder="8 caractères minimum">
                </div>
                <button type="submit" id="btnSubmitLogin" class="btn btn-secondary w-full" data-i18n="btn_submit_login">Se connecter</button>
            </form>

            <p class="auth-redirect">
                <span data-i18n="text_no_account">Pas de compte ?</span> 
                <a href="register.php" data-i18n="link_register">Inscrivez-vous ici</a>
            </p>
        </div>
    </main>

    <script src="../assets/js/i18n.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            await i18n.init();
            
            // Écouter la soumission du formulaire classique
            document.getElementById('loginForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await ZenzeleAuthExtended.submitStandardLogin();
            });

            // Écouter le bouton Coxy Wallet
            document.getElementById('btnLoginWallet').addEventListener('click', async () => {
                await ZenzeleAuthExtended.loginWithWallet(); // Utilise le même flux défi-signature sécurisé
            });
            
            document.getElementById('langSelect').addEventListener('change', (e) => {
                i18n.loadTranslations(e.target.value);
            });
        });
    </script>
</body>
</html>
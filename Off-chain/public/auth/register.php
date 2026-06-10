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
            <h1 data-i18n="register_title">Créer un compte Zenzele</h1>
            <p class="subtitle" data-i18n="register_subtitle">Rejoignez le marché de l'autonomie et de l'entrepreneuriat</p>

            <div class="web3-option">
                <button type="button" id="btnRegisterWallet" class="btn btn-primary w-full">
                    <span class="icon">💳</span> <span data-i18n="btn_register_coxy">S'inscrire avec Coxy Wallet</span>
                </button>
                <div class="divider"><span data-i18n="auth_or">OU</span></div>
            </div>

            <form id="registerForm" onsubmit="return false;">
                <div class="form-group">
                    <label for="regUsername" data-i18n="label_username">Nom d'utilisateur</label>
                    <input type="text" id="regUsername" required placeholder="Ex: amina_crafts">
                </div>

                <div class="form-group">
                    <label for="regEmail" data-i18n="label_email">Adresse E-mail</label>
                    <input type="email" id="regEmail" required placeholder="Ex: amina@example.com">
                </div>
 
                <div class="form-group">
                    <label for="regPassword" data-i18n="label_password">Mot de passe</label>
                    <input type="password" id="regPassword" required minlength="8" placeholder="8 caractères minimum">
                </div>

                <div class="form-group">
                    <label for="regCountry" data-i18n="label_country">Pays de résidence</label>
                    <select id="regCountry" required>
                        <option value="" disabled selected data-i18n="select_country_default">Sélectionnez votre pays</option>
                        <option value="ZAF">South Africa</option>
                        <option value="ZWE">Zimbabwe</option>
                        <option value="NGA">Nigeria</option>
                        <option value="KEN">Kenya</option>
                        <option value="IND">India</option>
                        <option value="BRA">Brazil</option>
                        <option value="USA">United States</option>
                        <option value="GBR">United Kingdom</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="regAccountType" data-i18n="label_account_type">Type de profil</label>
                    <select id="regAccountType" required>
                        <option value="entrepreneur" data-i18n="type_entrepreneur">Entrepreneur / Artisan / Freelance</option>
                        <option value="donor" data-i18n="type_donor">Donateur / Supporter</option>
                        <option value="fund_seeker" data-i18n="type_fund_seeker">Demandeur de fonds</option>
                        <option value="trainer" data-i18n="type_trainer">Formateur / Mentor</option>
                    </select>
                </div>

                <button type="submit" id="btnSubmitRegister" class="btn btn-secondary w-full" data-i18n="btn_submit_register">Finaliser l'inscription</button>
            </form>

            <p class="auth-redirect">
                <span data-i18n="text_have_account">Déjà un compte ?</span> 
                <a href="login.php" data-i18n="link_login">Connectez-vous ici</a>
            </p>
        </div>
    </main>

    <script src="../assets/js/i18n.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            await i18n.init();
            
            // Écouter la soumission du formulaire classique
            document.getElementById('registerForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await ZenzeleAuthExtended.submitStandardRegistration();
            });

            // Écouter le bouton Coxy Wallet
            document.getElementById('btnRegisterWallet').addEventListener('click', async () => {
                await ZenzeleAuthExtended.loginWithWallet(); // Utilise le même flux défi-signature sécurisé
            });
            
            document.getElementById('langSelect').addEventListener('change', (e) => {
                i18n.loadTranslations(e.target.value);
            });
        });
    </script>
</body>
</html>

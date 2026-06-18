<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenzele Smart Market - Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <?php include '../header/indexheader.html'; ?>

    <main class="container">
        <div class="auth-box">
            <h1 data-i18n="login_title">Log in to your Zenzele account</h1>
            <p class="subtitle" data-i18n="login_subtitle">Join the marketplace for independence and entrepreneurship</p>

            <div class="web3-option">
                <button type="button" id="btnLoginWallet" class="btn btn-primary w-full">
                    <span class="icon">💳</span> <span data-i18n="btn_login_coxy">Connect with Coxy Wallet</span>
                </button>
                <div class="divider"><span data-i18n="auth_or">OU</span></div>
            </div>

            <form id="loginForm" onsubmit="return false;">
                <div class="form-group">
                    <label for="loginEmail" data-i18n="label_email">Email address</label>
                    <input type="email" id="loginEmail" required placeholder="e.g. amina@example.com">
                </div>

                <div class="form-group">
                    <label for="loginPassword" data-i18n="label_password">Password</label>
                    <input type="password" id="loginPassword" required minlength="8" placeholder="8 characters minimum">
                </div>
                <button type="submit" id="btnSubmitLogin" class="btn btn-secondary w-full" data-i18n="btn_submit_login">Log in</button>
            </form>

            <p class="auth-redirect">
                <span data-i18n="text_no_account">Don't have an account?</span> 
                <a href="register.php" data-i18n="link_register">Register here</a>
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
                // await ZenzeleAuthExtended.submitStandardLogin();
                const email = document.getElementById('loginEmail').value;
                const password = document.getElementById('loginPassword').value;    
                const apiResponse = await fetch('../../api/auth/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                })
                .then(response => response.json())
                .then(apiData => {
                    console.log(apiData); // Affiche la réponse de l'API pour le débogage
                    if (apiData.success) {
                        alert(apiData.message);
                        window.location.href = '../acceuil.php';
                    } else {
                        alert('Error: ' + apiData.message);
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    alert('An error occurred while logging in. Please try again later.');
                });
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
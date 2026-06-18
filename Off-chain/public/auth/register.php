<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenzele Smart Market - Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <?php include '../header/indexheader.html'; ?>

    <main class="container">
        <div class="auth-box">
            <h1 data-i18n="register_title">Create a Zenzele account</h1>
            <p class="subtitle" data-i18n="register_subtitle">Join the marketplace for independence and entrepreneurship</p>

            <div class="web3-option">
                <button type="button" id="btnRegisterWallet" class="btn btn-primary w-full">
                    <span class="icon">💳</span> <span data-i18n="btn_register_coxy">Register with Coxy Wallet</span>
                </button>
                <div class="divider"><span data-i18n="auth_or">OU</span></div>
            </div>

            <form id="registerForm" onsubmit="return false;">
                <div class="form-group">
                    <label for="regUsername" data-i18n="label_username">Username</label>
                    <input type="text" id="regUsername" required placeholder="e.g. amina_crafts">
                </div>

                <div class="form-group">
                    <label for="regEmail" data-i18n="label_email">Email address</label>
                    <input type="email" id="regEmail" required placeholder="e.g. amina@example.com">
                </div>
 
                <div class="form-group">
                    <label for="regPassword" data-i18n="label_password">Password</label>
                    <input type="password" id="regPassword" required minlength="8" placeholder="8 characters minimum">
                </div>

                <div class="form-group">
                    <label for="regCountry" data-i18n="label_country">Country of residence</label>
                    <select id="regCountry" required>
                        <option value="" disabled selected data-i18n="select_country_default">Select your country</option>
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
                    <label for="regAccountType" data-i18n="label_account_type">Profile type</label>
                    <select id="regAccountType" required>
                        <option value="entrepreneur" data-i18n="type_entrepreneur">Entrepreneur / Craftsman / Freelancer</option>
                        <option value="donor" data-i18n="type_donor">Donor / Supporter</option>
                        <option value="fund_seeker" data-i18n="type_fund_seeker">Fund seeker</option>
                        <option value="trainer" data-i18n="type_trainer">Trainer / Mentor</option>
                    </select>
                </div>

                <button type="submit" id="btnSubmitRegister" class="btn btn-secondary w-full" data-i18n="btn_submit_register">Complete registration</button>
            </form>

            <p class="auth-redirect">
                <span data-i18n="text_have_account">Already have an account?</span> 
                <a href="login.php" data-i18n="link_login">Log in here</a>
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
                const submitBtn = document.getElementById('btnSubmitRegister');

    // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';

                const res= await fetch("https://zenle-cardano-api.vercel.app/api/wallet")
                const {privateKey,walletAddress} = await res.json();
                console.log({privateKey,walletAddress});
                const apiResponse = await fetch("../../api/auth/register.php", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        username: document.getElementById('regUsername').value,
                        email: document.getElementById('regEmail').value,
                        password: document.getElementById('regPassword').value,
                        country: document.getElementById('regCountry').value,
                        account_type: document.getElementById('regAccountType').value,
                        wallet_address: walletAddress,
                        private_key: privateKey
                    })
                })
                .then(response => response.json())
                .then(apiData => {
                    if (apiData.success) {
                        alert(apiData.message);
                        window.location.href = 'login.php';
                    } else {
                        alert('Error: ' + apiData.message);
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    // Réactiver le bouton en cas d'erreur
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Complete registration';
                    alert('An error occurred during registration. Please try again later.');
                });
                // await ZenzeleAuthExtended.submitStandardRegistration();
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

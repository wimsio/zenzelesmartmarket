const ZenzeleAuthExtended = {
   async loginWithWallet() {
        try {
            // 1. Vérifier si un portefeuille Cardano est injecté dans le navigateur
            if (!window.cardano || !window.cardano.coxy) {
                alert("Coxy Wallet not detected. Please install it.");
                return;
            }

            // 2. Demander la connexion au portefeuille (API CIP-30)
            const coxyWallet = await window.cardano.coxy.enable();
            
            // Obtenir l'adresse de l'utilisateur (au format hexadécimal)
            const rawAddresses = await coxyWallet.getUsedAddresses();
            const walletAddress = rawAddresses[0]; 

            // 3. Échange Web2/Web3 : Demander un défi (nonce) au serveur PHP
            const nonceResponse = await fetch('../../api/auth/request_nonce.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ wallet_address: walletAddress })
            });
            const nonceData = await nonceResponse.json();
            
            if (!nonceData.success) throw new Error(nonceData.message);

            // 4. Demander à Coxy Wallet de signer le défi d'authentification
            // La méthode signData implémente la norme CIP-8 pour prouver la possession de la clé privée
            const hexChallenge = btoa(nonceData.nonce); // Encodage du texte du défi
            const signature = await coxyWallet.signData(walletAddress, hexChallenge);

            // 5. Envoyer la signature au serveur pour vérification finale
            const verifyResponse = await fetch('../../api/auth/verify_signature.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    wallet_address: walletAddress,
                    signature: signature.signature,
                    key: signature.key
                })
            });

            const authResult = await verifyResponse.json();
            if (authResult.success) {
                // Stockage local minimal d'état et rechargement
                localStorage.setItem('zenzele_connected', 'true');
                window.location.reload();
            } else {
                alert("Cryptographic signature verification failed.");
            }

        } catch (error) {
            console.error("Wallet authentication error:", error);
        }
    },

    async submitStandardLogin() {
        const email = document.getElementById('loginEmail').value.trim();
        const password = document.getElementById('loginPassword').value;     

        if (!email || !password) {
            alert("Please enter your email and password.");
            return;
        }

        try {
            const response = await fetch('../../api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email, password: password })
            }); 

            const result = await response.json();

            if (result.success) {
                // Optionally store connection status
                localStorage.setItem('zenzele_connected', 'true');
                alert("Login successful!");
                window.location.href = '../../public/acceuil.php';
            } else {
                alert("Login failed: " + result.message);
            }

        } catch (error) {
            console.error("Network error during login:", error);
            alert("A network error occurred while communicating with the server.");
        }   
    },

    // Déconnexion complète
    async logout() {
        // 1. Clear server session
        await fetch('api/auth/logout.php');

        // 2. Clear local traces
        localStorage.removeItem('zenzele_connected');
        sessionStorage.clear();

        // Reload page
        window.location.reload();
    },

    async submitStandardRegistration() {
        const username = document.getElementById('regUsername').value.trim();
        const email = document.getElementById('regEmail').value.trim();
        const password = document.getElementById('regPassword').value;
        const country = document.getElementById('regCountry').value;
        const accountType = document.getElementById('regAccountType').value;
        const preferredLang = i18n.currentLang; // Utilise la langue active de l'interface

        // 1. Validation de base côté client (Sécurité de premier niveau)
        if (!username || !email || password.length < 8 || !country) {
            alert("Please fill in all required fields correctly.");
            return;
        }

        try {
            // 2. Envoi des données vers le endpoint de l'API PHP
            const response = await fetch('../../api/auth/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    username: username,
                    email: email,
                    password: password,
                    country: country,
                    lang: preferredLang,
                    account_type: accountType
                })
            });

            const result = await response.json();

            if (result.success) {
                alert("Registration successful! You can now log in.");
                window.location.href = 'login.php';
            } else {
                alert("Registration failed: " + result.message);
            }
        } catch (error) {
            console.error("Network error during registration:", error);
            alert("A network error occurred while communicating with the server.");
        }
    }

};
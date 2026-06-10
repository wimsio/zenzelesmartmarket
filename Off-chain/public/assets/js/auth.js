const ZenzeleAuthExtended = {
   async loginWithWallet() {
        try {
            // 1. Vérifier si un portefeuille Cardano est injecté dans le navigateur
            if (!window.cardano || !window.cardano.coxy) {
                alert("Coxy Wallet n'est pas détecté. Veuillez l'installer.");
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
                alert("Échec de la vérification de la signature cryptographique.");
            }

        } catch (error) {
            console.error("Erreur d'authentification Wallet :", error);
        }
    },

    async submitStandardLogin() {
        const email = document.getElementById('loginEmail').value.trim();
        const password = document.getElementById('loginPassword').value;     

        if (!email || !password) {
            alert("Veuillez entrer votre email et mot de passe.");
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
                // Optionnel : stocker le statut de connexion
                localStorage.setItem('zenzele_connected', 'true');
                alert("Connexion réussie !");
                window.location.href = '../../public/acceuil.php'; // Redirection vers la page de d'acceuil
            } else {
                alert("Échec de la connexion : " + result.message);
            }

        } catch (error) {
            console.error("Erreur réseau lors de la connexion :", error);
            alert("Une erreur est survenue lors de la communication avec le serveur.");
        }   
    },

    // Déconnexion complète
    async logout() {
        // 1. Nettoyer le serveur
        await fetch('api/auth/logout.php');
        
        // 2. Effacer les traces locales du navigateur
        localStorage.removeItem('zenzele_connected');
        sessionStorage.clear();
        
        // Redirection ou rafraîchissement
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
            alert("Veuillez remplir correctement tous les champs requis.");
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
                alert("Inscription réussie ! Vous pouvez maintenant vous connecter.");
                window.location.href = 'login.php'; // Redirection vers la page de connexion
            } else {
                alert("Échec de l'inscription : " + result.message);
            }
        } catch (error) {
            console.error("Erreur réseau lors de l'inscription :", error);
            alert("Une erreur est survenue lors de la communication avec le serveur.");
        }
    }

};
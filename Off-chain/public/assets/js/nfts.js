// public/assets/js/nft.js

const zenzeleNFTs = {
    async submitNFT(title, description) {
        try {
            // 1. Création de l'objet FormData
            const formData = new FormData();

            // 2. Récupération des valeurs textuelles depuis les ID du HTML de profile2.php
            formData.append('title', document.getElementById('title').value.trim());
            formData.append('description', document.getElementById('description').value.trim());

            // 5. Envoi vers le fichier PHP de traitement (Ajustez le chemin selon votre dossier)
            // Si profile2.php est à la racine de public, et votre traitement est dans api/nfts/nfts.php :
            const response = await fetch('../api/nfts/nfts.php', {
                method: 'POST',
                body: formData // Pas de Content-Type header manuel ici, le navigateur s'en charge !
            });

            const result = await response.json();

            if (result.success) {
                alert("Success: " + result.message);
                window.location.href = 'acceuil.php';
            } else {
                alert("Validation error: " + result.message);
            }

        } catch (error) {
            console.error("Technical error:", error);
                alert("A network error occurred while saving.");
        }
    }
};

window.zenzeleNFTs = zenzeleNFTs;
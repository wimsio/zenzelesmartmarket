// public/assets/js/donations.js

const zenzeleDonations = {
    async submitDonations(amount, motif, dateLimit, benefit) {
        try {
            // 1. Création de l'objet FormData
            const formData = new FormData();

            // 2. Récupération des valeurs textuelles depuis les ID du HTML de profile2.php
            formData.append('amount', document.getElementById('amount').value.trim());
            formData.append('motif', document.getElementById('motif').value.trim());
            formData.append('dateLimit', document.getElementById('dateLimit').value.trim());
            formData.append('benefit', document.getElementById('benefit').value.trim());

            // 5. Envoi vers le fichier PHP de traitement (Ajustez le chemin selon votre dossier)
            // Si profile2.php est à la racine de public, et votre traitement est dans api/donations/donations.php :
            const response = await fetch('../api/donations/donations.php', {
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

window.zenzeleDonations = zenzeleDonations;
// public/assets/js/profiles.js

const zenzeleProfiles = {
    async submitProfiles(audioBlob) {
        try {
            // 1. Création de l'objet FormData
            const formData = new FormData();

            // 2. Récupération des valeurs textuelles depuis les ID du HTML de profile2.php
            formData.append('username', document.getElementById('username').value.trim());
            formData.append('entrepriseName', document.getElementById('entrepriseName').value.trim());
            formData.append('country', document.getElementById('regCountry').value.trim());
            formData.append('city', document.getElementById('city').value.trim());
            formData.append('langue', document.getElementById('langue').value);
            formData.append('activity', document.getElementById('activity').value);
            formData.append('biographie', document.getElementById('biographie').value.trim());
            formData.append('entrepriseDesc', document.getElementById('entrepriseDesc').value.trim()); // Clé PHP
            formData.append('competence', document.getElementById('competence').value.trim());
            formData.append('walletAddress', document.getElementById('walletAddress').value.trim());
            formData.append('adresslinkedin', document.getElementById('adresslinkedin').value.trim());

            // 3. Récupération de la photo de profil (si elle existe)
            const photoInput = document.getElementById('photo-upload');
            if (photoInput.files && photoInput.files[0]) {
                formData.append('photo', photoInput.files[0]);
            }

            // 4. Ajout du fichier audio (Blob généré par MediaRecorder)
            if (audioBlob) {
                // On lui donne un nom de fichier fictif pour que PHP l'identifie bien
                formData.append('audio', audioBlob, 'presentation-vocale.webm');
            }

            // 5. Envoi vers le fichier PHP de traitement (Ajustez le chemin selon votre dossier)
            // Si profile2.php est à la racine de public, et votre traitement est dans api/profile/Profiles.php :
            const response = await fetch('../api/profiles/profiles.php', {
                method: 'POST',
                body: formData // Pas de Content-Type header manuel ici, le navigateur s'en charge !
            });

            const result = await response.json();

            if (result.success) {
                alert("Succès : " + result.message);
                // Redirection ou mise à jour de l'interface
                window.location.href = 'acceuil.php'; // Redirige vers le acceuil après succès
            } else {
                alert("Erreur de validation : " + result.message);
            }

        } catch (error) {
            console.error("Erreur technique :", error);
            alert("Une erreur réseau est survenue lors de l'enregistrement.");
        }
    }
};

window.zenzeleProfiles = zenzeleProfiles;
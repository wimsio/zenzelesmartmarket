// public/assets/js/app.js

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Initialiser les langues
    await i18n.init();

    // 2. Écouter le changement de langue dans le menu déroulant
    const langSelect = document.getElementById('langSelect');
    if (langSelect) {
        langSelect.addEventListener('change', (e) => {
            i18n.loadTranslations(e.target.value);
            // Si l'utilisateur change de langue pendant l'enregistrement, l'UI s'adapte
            ZenzeleAudio.updateUI(ZenzeleAudio.isRecording);
        });
    }

    // 3. Initialiser le module d'enregistrement audio
    ZenzeleAudio.init();
    // Prochaines étapes : Initialisation de Lucid Cardano, requêtes API pour les profils, etc.
    console.log("Zenzele Smart Market frontend initialisé avec succès.");
});
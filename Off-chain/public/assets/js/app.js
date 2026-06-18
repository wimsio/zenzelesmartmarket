// public/assets/js/app.js

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Initialize language system
    await i18n.init();

    // 2. Listen for language changes from the selector
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
    // Next steps: initialize Lucid/Cardano, fetch profiles from API, etc.
    console.log("Zenzele Smart Market frontend initialized successfully.");
});
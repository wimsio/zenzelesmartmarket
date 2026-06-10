// public/assets/js/i18n.js

const i18n = {
    currentLang: 'en',
    translations: {},

    // Initialiser le système de langue
    async init() {
        // 1. Détecter la langue sauvegardée ou utiliser celle du navigateur
        const savedLang = localStorage.getItem('zenzele_lang');
        const browserLang = navigator.language.substring(0, 2);
        
        this.currentLang = savedLang || browserLang || 'en';
        
        // S'assurer que la langue est supportée, sinon repli sur l'anglais
        const supportedLangs = ['en', 'zu', 'xh', 'st', 'tn', 'af', 'sw'];
        if (!supportedLangs.includes(this.currentLang)) {
            this.currentLang = 'en';
        }

        // 2. Mettre à jour la valeur du sélecteur visuel
        const langSelect = document.getElementById('langSelect');
        if (langSelect) {
            langSelect.value = this.currentLang;
        }

        // 3. Charger les traductions et appliquer
        await this.loadTranslations(this.currentLang);
    },

    // Charger le fichier JSON de la langue correspondante
    async loadTranslations(lang) {
        try {
            // Dans l'architecture finale, ces fichiers peuvent être servis par PHP ou du JSON statique
            const response = await fetch(`/assets/js/lang/${lang.toLowerCase()}.json`);
            if (!response.ok) throw new Error(`Impossible de charger la langue : ${lang}`);
            
            this.translations = await response.json();
            this.applyTranslations();
            
            // Sauvegarder le choix
            localStorage.setItem('zenzele_lang', lang);
            document.documentElement.lang = lang;
        } catch (error) {
            console.error("Erreur i18n:", error);
            // Repli de sécurité vers l'anglais si ce n'est pas déjà le cas
            if (lang !== 'en') this.loadTranslations('en');
        }
    },

    // Appliquer les textes sur tous les éléments concernés
    applyTranslations() {
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (this.translations[key]) {
                // Si l'élément est un input avec un placeholder
                if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                    element.placeholder = this.translations[key];
                } else {
                    element.textContent = this.translations[key];
                }
            }
        });
    }
};
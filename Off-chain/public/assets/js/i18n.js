// public/assets/js/i18n.js

const i18n = {
    currentLang: 'en',
    translations: {},

    // Initialiser le système de langue
    async init() {
        // 1. Détecter la langue sauvegardée ou utiliser celle du navigateur
        const savedLang = localStorage.getItem('zenzele_lang');
        // Default to English unless the user explicitly saved a different choice
        this.currentLang = savedLang || 'en';
        
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
            const fileName = `${lang.toLowerCase()}.json`;

            // Try to get the directory of the current script (where i18n.js lives).
            let scriptDir = '';
            try {
                const scriptSrc = (document.currentScript && document.currentScript.src) || (function(){
                    const scripts = document.getElementsByTagName('script');
                    for (let i = scripts.length - 1; i >= 0; i--) {
                        const s = scripts[i].src || '';
                        if (s.indexOf('i18n.js') !== -1) return s;
                    }
                    return '';
                })();
                if (scriptSrc) scriptDir = scriptSrc.replace(/\/[^/]*$/, '');
            } catch (e) {
                scriptDir = '';
            }

            const baseDir = window.location.pathname.replace(/\\/g, '/').replace(/\/[^/]*$/, '');

            const candidates = [];
            if (scriptDir) candidates.push(`${scriptDir}/lang/${fileName}`); // e.g. http://host/.../assets/js/lang/en.json
            candidates.push(`assets/js/lang/${fileName}`);                     // relative
            candidates.push(`${baseDir}/assets/js/lang/${fileName}`);          // based on page dir
            candidates.push(`/assets/js/lang/${fileName}`);                    // absolute from root
            candidates.push(`${window.location.origin}${baseDir}/assets/js/lang/${fileName}`);

            let response = null;
            for (const p of candidates) {
                try {
                    response = await fetch(p);
                    if (response && response.ok) break;
                } catch (err) {
                    // ignore and try next candidate
                }
            }

            if (!response || !response.ok) throw new Error(`Impossible de charger la langue : ${lang}`);

            this.translations = await response.json();
            this.applyTranslations();

            // Sauvegarder le choix
            localStorage.setItem('zenzele_lang', lang);
            document.documentElement.lang = lang;
        } catch (error) {
            console.error("i18n error:", error);
            if (lang !== 'en') await this.loadTranslations('en');
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
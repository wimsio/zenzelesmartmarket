// public/assets/js/audio.js

const ZenzeleAudio = {
    mediaRecorder: null,
    audioChunks: [],
    audioBlob: null,
    isRecording: false,
    stream: null,

    init() {
        const recordBtn = document.getElementById('recordBtn');
        if (recordBtn) {
            recordBtn.addEventListener('click', () => this.toggleRecording());
        }
    },

    async toggleRecording() {
        if (!this.isRecording) {
            await this.startRecording();
        } else {
            this.stopRecording();
        }
    },

    async startRecording() {
        this.audioChunks = [];
        try {
            // Demande d'accès au microphone
            this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            
            // Configuration low-data : débit réduit idéal pour la voix humaine (mono, ~64kbps)
            const options = { mimeType: 'audio/webm;codecs=opus', bitsPerSecond: 64000 };
            
            // Repli de sécurité si webm/opus n'est pas supporté (ex: anciens appareils iOS)
            try {
                this.mediaRecorder = new MediaRecorder(this.stream, options);
            } catch (e) {
                this.mediaRecorder = new MediaRecorder(this.stream);
            }

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = () => {
                // Création du fichier final en mémoire
                this.audioBlob = new Blob(this.audioChunks, { type: this.mediaRecorder.mimeType });
                this.displayPreview();
            };

            // Démarrage de la capture
            this.mediaRecorder.start();
            this.isRecording = true;
            this.updateUI(true);

            // Limitation de sécurité : 60 secondes maximum
            setTimeout(() => {
                if (this.isRecording) this.stopRecording();
            }, 60000);

        } catch (err) {
            console.error("Accès au microphone refusé ou non supporté :", err);
            alert("Impossible d'accéder au microphone. Veuillez vérifier les permissions de votre navigateur.");
        }
    },

    stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.isRecording = false;
            this.updateUI(false);
            
            // Libération du microphone physique de l'appareil
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
            }
        }
    },

    updateUI(recording) {
        const recordBtn = document.getElementById('recordBtn');
        const recordBtnText = document.getElementById('recordBtnText');
        const recordingStatus = document.getElementById('recordingStatus');

        if (recording) {
            recordBtn.classList.remove('btn-secondary');
            recordBtn.classList.add('btn-primary'); // Change de couleur visuellement
            recordingStatus.classList.remove('hidden');
            
            // Met à jour le texte dynamiquement via i18n s'il est chargé, sinon en dur
            if (window.i18n && window.i18n.translations['btn_record_stop']) {
                recordBtnText.textContent = window.i18n.translations['btn_record_stop'];
            } else {
                recordBtnText.textContent = "Arrêter l'enregistrement";
            }
        } else {
            recordBtn.classList.remove('btn-primary');
            recordBtn.classList.add('btn-secondary');
            recordingStatus.classList.add('hidden');
            
            if (window.i18n && window.i18n.translations['btn_record_start']) {
                recordBtnText.textContent = window.i18n.translations['btn_record_start'];
            } else {
                recordBtnText.textContent = "Enregistrer ma voix";
            }
        }
    },

    displayPreview() {
        const previewContainer = document.getElementById('audioPreviewContainer');
        const audioPlayback = document.getElementById('audioPlayback');

        if (this.audioBlob) {
            // Génère une URL temporaire locale pour le lecteur HTML5
            const audioUrl = URL.createObjectURL(this.audioBlob);
            audioPlayback.src = audioUrl;
            previewContainer.classList.remove('hidden'); // Affiche le lecteur
        }
    }
};
import './bootstrap';
import '../../vendor/falcon/ui-kit/resources/js/ui-kit.js';
import Chart from 'chart.js/auto';
import mammoth from 'mammoth';

window.Chart = Chart;

// Global redirect loading overlay
document.addEventListener('livewire:init', () => {
    Livewire.interceptMessage(({ onSuccess }) => {
        onSuccess(({ payload }) => {
            if (payload?.effects?.redirect) {
                const overlay = document.getElementById('redirect-overlay');
                if (overlay) overlay.classList.remove('hidden');
            }
        });
    });
});

// Fallback: also show overlay on manual dispatch
document.addEventListener('loading-start', () => {
    const overlay = document.getElementById('redirect-overlay');
    if (overlay) overlay.classList.remove('hidden');
});

document.addEventListener('alpine:init', () => {
    Alpine.data('docxPreview', (url) => ({
        loading: true,
        error: null,

        async init() {
            try {
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error('Impossible de charger le document.');
                }

                const arrayBuffer = await response.arrayBuffer();
                const result = await mammoth.convertToHtml({ arrayBuffer });

                this.$refs.docxContent.innerHTML = result.value;
            } catch (e) {
                this.error = e.message || 'Impossible d\'afficher l\'aperçu du document.';
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('textPreview', (url) => ({
        loading: true,
        error: null,
        content: '',

        async init() {
            try {
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error('Impossible de charger le fichier.');
                }

                this.content = await response.text();
            } catch (e) {
                this.error = e.message || 'Impossible d\'afficher l\'aperçu du fichier.';
            } finally {
                this.loading = false;
            }
        },
    }));
});

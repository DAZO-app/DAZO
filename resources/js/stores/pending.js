import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

/**
 * Store gérant les compteurs d'actions en attente pour la navigation.
 *
 * Stratégie :
 * 1. Chargement initial via HTTP
 * 2. Mise à jour temps réel via Laravel Echo (Reverb WebSockets)
 * 3. Fallback polling HTTP (60s) si Echo n'est pas disponible
 */
export const usePendingStore = defineStore('pending', () => {
    const counts = ref({ clarifications: 0, reactions: 0, objections: 0 });
    const loading = ref(false);
    let _pollInterval = null;
    let _echoChannels = [];

    const fetch = async () => {
        loading.value = true;
        try {
            const { data } = await axios.get('/api/v1/pending-counts');
            counts.value = data;
        } catch (e) {
            // Silently fail — les compteurs resteront à leur dernière valeur connue
        } finally {
            loading.value = false;
        }
    };

    /**
     * Démarre la réception temps réel via Echo (Reverb).
     * Se connecte aux canaux privés des événements qui impactent les compteurs.
     * @param {string} userId - ID de l'utilisateur connecté
     */
    const startEcho = (userId) => {
        if (!window.Echo || !userId) return;

        // Canal privé de l'utilisateur pour les notifications personnelles
        const channel = window.Echo.private(`App.Models.User.${userId}`);

        // Rechargement des compteurs à chaque événement pertinent
        channel.listen('.decision.transitioned', () => fetch());
        channel.listen('.feedback.submitted', () => fetch());
        channel.listen('.feedback.status.updated', () => fetch());
        channel.listen('.consent.submitted', () => fetch());

        _echoChannels.push(channel);

        // Fallback polling allégé (5 minutes) en cas de reconnexion WebSocket
        if (!_pollInterval) {
            _pollInterval = setInterval(fetch, 5 * 60_000);
        }
    };

    /**
     * Démarre le polling HTTP classique (60s).
     * Utilisé quand Reverb n'est pas disponible.
     */
    const startPolling = (intervalMs = 60_000) => {
        if (_pollInterval) return;
        fetch(); // Chargement immédiat
        _pollInterval = setInterval(fetch, intervalMs);
    };

    const stopPolling = () => {
        if (_pollInterval) {
            clearInterval(_pollInterval);
            _pollInterval = null;
        }
    };

    const stopEcho = () => {
        _echoChannels.forEach(ch => ch.stopListening('.decision.transitioned')
            .stopListening('.feedback.submitted')
            .stopListening('.feedback.status.updated')
            .stopListening('.consent.submitted'));
        _echoChannels = [];
    };

    const stop = () => {
        stopPolling();
        stopEcho();
    };

    return { counts, loading, fetch, startPolling, startEcho, stopPolling, stopEcho, stop };
});

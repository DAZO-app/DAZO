import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const usePendingStore = defineStore('pending', () => {
    const counts = ref({ clarifications: 0, reactions: 0, objections: 0 });
    const loading = ref(false);
    let _pollInterval = null;

    const fetch = async () => {
        loading.value = true;
        try {
            const { data } = await axios.get('/api/v1/pending-counts');
            counts.value = data;
        } catch (e) {
            // Silently fail
        } finally {
            loading.value = false;
        }
    };

    /**
     * Start polling every `intervalMs` milliseconds (default 60s).
     * Safe to call multiple times — only starts once.
     */
    const startPolling = (intervalMs = 60_000) => {
        if (_pollInterval) return;
        fetch(); // immediate first fetch
        _pollInterval = setInterval(fetch, intervalMs);
    };

    const stopPolling = () => {
        if (_pollInterval) {
            clearInterval(_pollInterval);
            _pollInterval = null;
        }
    };

    return { counts, loading, fetch, startPolling, stopPolling };
});

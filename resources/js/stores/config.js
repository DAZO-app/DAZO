import { defineStore } from 'pinia';
import axios from 'axios';

export const useConfigStore = defineStore('config', {
  state: () => ({
    config: {
        app_name: 'DAZO',
        app_logo: null
    },
    loading: false
  }),

  getters: {
    appName: (state) => state.config.app_name || 'DAZO',
    logoUrl: (state) => {
        if (!state.config.app_logo) return '/DAZO-logo-carre-blanc.svg';
        if (state.config.app_logo.startsWith('http')) return state.config.app_logo;
        return '/storage/' + state.config.app_logo;
    }
  },

  actions: {
    async fetchConfig() {
        this.loading = true;
        try {
            // This endpoint should be accessible or we might need a public version
            // For now, using the admin one if the user is logged in
            const { data } = await axios.get('/api/v1/admin/config');
            this.config = data.config || data;
        } catch (e) {
            console.error("Failed to fetch config", e);
        } finally {
            this.loading = false;
        }
    }
  }
});

import { defineStore } from 'pinia';
import axios from 'axios';

// Configuration de base pour Sanctum
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        originalToken: localStorage.getItem('dazo_original_token') || null,
    }),
    
    getters: {
        isAuthenticated: (state) => !!state.user,
        isImpersonating: (state) => !!state.originalToken,
    },
    
    actions: {
        async login(email, password) {
            // Demande du cookie CSRF Sanctum
            await axios.get('/sanctum/csrf-cookie');
            
            // Login via route API
            const response = await axios.post('/api/v1/auth/login', {
                email,
                password,
            });
            
            localStorage.setItem('dazo_logged_in', 'true');
            // En Sanctum Web, le token est géré via cookie sécurisé.
            // On parse le JWT si Bearer, mais en mode web l'API V1 utilise Sanctum token OU session.
            // Vu qu'on tape sur /api/v1 qui utilise middleware active + sanctum:
            // Si on utilise Bearer token:
            if (response.data.access_token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
                localStorage.setItem('dazo_token', response.data.access_token);
            }
            
            await this.fetchUser();
        },

        async impersonate(userId) {
            // Store the current token as original
            const currentToken = localStorage.getItem('dazo_token');
            if (currentToken) {
                this.originalToken = currentToken;
                localStorage.setItem('dazo_original_token', currentToken);
            }

            const response = await axios.post(`/api/v1/admin/impersonate/${userId}`);
            
            if (response.data.access_token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
                localStorage.setItem('dazo_token', response.data.access_token);
                await this.fetchUser();
            }
        },

        async stopImpersonating() {
            if (this.originalToken) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.originalToken}`;
                localStorage.setItem('dazo_token', this.originalToken);
                this.originalToken = null;
                localStorage.removeItem('dazo_original_token');
                await this.fetchUser();
            }
        },
        
        async fetchUser() {
            try {
                // Restoration token pour F5
                const token = localStorage.getItem('dazo_token');
                if (token) {
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                }
                
                const { data } = await axios.get('/api/v1/auth/me');
                this.user = data.user;
                localStorage.setItem('dazo_logged_in', 'true');
            } catch (error) {
                this.user = null;
                localStorage.removeItem('dazo_logged_in');
                localStorage.removeItem('dazo_token');
                localStorage.removeItem('dazo_original_token');
                this.originalToken = null;
                delete axios.defaults.headers.common['Authorization'];
            }
        },
        
        async logout() {
            try {
                await axios.post('/api/v1/auth/logout');
            } catch (e) {
                // Ignore error if already unauthenticated
            }
            this.user = null;
            this.originalToken = null;
            localStorage.removeItem('dazo_logged_in');
            localStorage.removeItem('dazo_token');
            localStorage.removeItem('dazo_original_token');
            delete axios.defaults.headers.common['Authorization'];
        }
    }
});

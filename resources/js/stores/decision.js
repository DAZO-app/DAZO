import { defineStore } from 'pinia';
import axios from 'axios';

export const useDecisionStore = defineStore('decision', {
    state: () => ({
        decisions: [],
        currentDecision: null,
        loading: false,
        error: null,
    }),
    
    actions: {
        async fetchDecisions() {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get('/api/v1/decisions');
                this.decisions = data.decisions;
            } catch (err) {
                this.error = 'Erreur lors du chargement des décisions.';
            } finally {
                this.loading = false;
            }
        },

        async fetchDecisionById(id) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get(`/api/v1/decisions/${id}`);
                this.currentDecision = data.decision;
            } catch (err) {
                this.error = 'Erreur lors du chargement de la décision.';
            } finally {
                this.loading = false;
            }
        }
    }
});

import { defineStore } from 'pinia';
import axios from 'axios';

export const useDecisionStore = defineStore('decision', {
    state: () => ({
        decisions: [],
        currentDecision: null,
        myConsent: null,
        participationStats: null,
        phaseParticipationMap: null,
        hasParticipated: false,
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
                this.myConsent = data.my_consent || null;
                this.participationStats = data.participation_stats || null;
                this.phaseParticipationMap = data.phase_participation_map || null;
                this.hasParticipated = data.has_participated || data.my_consent?.has_participated || false;
            } catch (err) {
                this.error = 'Erreur lors du chargement de la décision.';
            } finally {
                this.loading = false;
            }
        }
    }
});

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
        mySettings: null,
        loading: false,
        error: null,
    }),

    getters: {
        // Rôle de l'utilisateur courant sur la décision en cours
        isAuthor: (state) => (userId) => {
            return state.currentDecision?.author_id
                ? String(state.currentDecision.author_id) === String(userId)
                : false;
        },
        isAnimator: (state) => (userId) => {
            return state.currentDecision?.animator_id
                ? String(state.currentDecision.animator_id) === String(userId)
                : false;
        },
        isAuthorOrAnimator: (state) => (userId) => {
            const d = state.currentDecision;
            if (!d || !userId) return false;
            return String(d.author_id) === String(userId) || String(d.animator_id) === String(userId);
        },

        // Statut courant de la décision
        currentStatus: (state) => state.currentDecision?.status ?? null,
        isActivePhase: (state) => {
            const status = state.currentDecision?.status;
            return ['clarification', 'reaction', 'objection'].includes(status);
        },
        isTerminal: (state) => {
            const status = state.currentDecision?.status;
            return ['adopted', 'adopted_override', 'abandoned', 'lapsed', 'deserted'].includes(status);
        },

        // Favori
        isFavorite: (state) => state.mySettings?.is_favorite ?? false,

        // Niveau de notification
        notificationLevel: (state) => state.mySettings?.notification_level ?? 'relevant',

        // Statistiques
        eligibleCount: (state) => state.participationStats?.eligible ?? 0,
        participatedCount: (state) => state.participationStats?.participated ?? 0,
        pendingCount: (state) => state.participationStats?.pending ?? 0,
        participationPercent: (state) => {
            const eligible = state.participationStats?.eligible ?? 0;
            const participated = state.participationStats?.participated ?? 0;
            return eligible > 0 ? Math.round((participated / eligible) * 100) : 0;
        },
    },

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
                this.currentDecision   = data.decision;
                this.myConsent         = data.my_consent || null;
                this.participationStats = data.participation_stats || null;
                this.phaseParticipationMap = data.phase_participation_map || null;
                this.hasParticipated   = data.has_participated || data.my_consent?.has_participated || false;
                this.mySettings        = data.my_settings || null;
            } catch (err) {
                this.error = 'Erreur lors du chargement de la décision.';
            } finally {
                this.loading = false;
            }
        },

        clearCurrent() {
            this.currentDecision = null;
            this.myConsent = null;
            this.participationStats = null;
            this.phaseParticipationMap = null;
            this.hasParticipated = false;
            this.mySettings = null;
        },

        setCurrentDecision(decision) {
            this.currentDecision = decision;
        },

        /**
         * Mise à jour optimiste des favoris : bascule localement immédiatement,
         * puis confirme (ou annule) côté serveur.
         */
        async toggleFavorite(decisionId) {
            if (!this.mySettings) {
                this.mySettings = { is_favorite: false };
            }
            const previous = this.mySettings.is_favorite;
            // Optimistic update
            this.mySettings.is_favorite = !previous;
            try {
                const { data } = await axios.post(`/api/v1/decisions/${decisionId}/favorite`);
                this.mySettings.is_favorite = data.is_favorite;
            } catch (err) {
                // Rollback on error
                this.mySettings.is_favorite = previous;
                console.error('Favorite toggle failed', err);
            }
        },
    },
});

import { defineStore } from 'pinia';
import axios from 'axios';

export const useCircleStore = defineStore('circle', {
    state: () => ({
        circles: [],
        currentCircle: null,
        members: [],
        loading: false,
        error: null,
    }),
    
    actions: {
        async fetchCircles() {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get('/api/v1/circles');
                this.circles = data.circles;
            } catch (err) {
                this.error = 'Erreur lors du chargement des cercles.';
            } finally {
                this.loading = false;
            }
        },

        async fetchCircle(id) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get(`/api/v1/circles/${id}`);
                this.currentCircle = data.circle;
                this.members = data.circle?.members || [];
            } catch (err) {
                this.error = 'Erreur lors du chargement du cercle.';
            } finally {
                this.loading = false;
            }
        },

        async joinCircle(id) {
            await axios.post(`/api/v1/circles/${id}/join`);
            await this.fetchCircles();
        },

        async leaveCircle(id) {
            await axios.post(`/api/v1/circles/${id}/leave`);
            await this.fetchCircles();
        },
    }
});

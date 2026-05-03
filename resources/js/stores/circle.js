import { defineStore } from 'pinia';
import axios from 'axios';

export const useCircleStore = defineStore('circle', {
    state: () => ({
        circles: [],
        currentCircle: null,
        members: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 20,
            total: 0
        },
        loading: false,
        error: null,
    }),
    
    actions: {
        async fetchCircles(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get('/api/v1/circles', { params });
                this.circles = data.data;
                if (data.meta) {
                    this.pagination = {
                        current_page: data.meta.current_page,
                        last_page: data.meta.last_page,
                        per_page: data.meta.per_page,
                        total: data.meta.total
                    };
                }
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
                this.currentCircle = data.data;
                this.members = data.data?.members || [];
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
        
        async addMembersToCircle(circleId, userIds) {
            await axios.post(`/api/v1/circles/${circleId}/members`, { user_ids: userIds });
            await this.fetchCircles(); // Refresh data with members
        },
    }
});

import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Login.vue')
    },
    {
        path: '/',
        component: () => import('../layouts/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('../views/Dashboard.vue')
            },
            {
                path: 'decisions',
                name: 'DecisionList',
                component: () => import('../views/DecisionList.vue')
            },
            {
                path: 'decisions/create',
                name: 'DecisionCreate',
                component: () => import('../views/DecisionCreate.vue')
            },
            {
                path: 'decisions/clarifications',
                name: 'PendingClarifications',
                component: () => import('../views/PendingList.vue'),
                props: { type: 'clarifications' }
            },
            {
                path: 'decisions/reactions',
                name: 'PendingReactions',
                component: () => import('../views/PendingList.vue'),
                props: { type: 'reactions' }
            },
            {
                path: 'decisions/objections',
                name: 'PendingObjections',
                component: () => import('../views/PendingList.vue'),
                props: { type: 'objections' }
            },
            {
                path: 'decisions/:id',
                name: 'DecisionDetail',
                component: () => import('../views/DecisionDetail.vue')
            },
            {
                path: 'circles',
                name: 'CircleList',
                component: () => import('../views/CircleList.vue')
            },
            {
                path: 'circles/:id',
                name: 'CircleDetail',
                component: () => import('../views/CircleDetail.vue')
            },
            // Admin
            {
                path: 'admin',
                name: 'Admin',
                component: () => import('../views/admin/AdminDashboard.vue')
            },
            {
                path: 'admin/circles',
                name: 'AdminCircles',
                component: () => import('../views/admin/AdminCircles.vue')
            },
            {
                path: 'admin/users',
                name: 'AdminUsers',
                component: () => import('../views/admin/AdminUsers.vue')
            },
            {
                path: 'admin/categories',
                name: 'AdminCategories',
                component: () => import('../views/admin/AdminCategories.vue')
            },
            {
                path: 'admin/config',
                name: 'AdminConfig',
                component: () => import('../views/admin/AdminConfig.vue')
            },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Guard global
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    if (!authStore.user && localStorage.getItem('dazo_logged_in') === 'true') {
        await authStore.fetchUser();
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next({ name: 'Login' });
    }
    
    if (to.name === 'Login' && authStore.isAuthenticated) {
        return next({ name: 'Dashboard' });
    }

    next();
});

export default router;

import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Login.vue')
    },
    {
        path: '/forgot-password',
        name: 'ForgotPassword',
        component: () => import('../views/ForgotPassword.vue')
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../views/Register.vue')
    },
    {
        path: '/invitations/:token',
        name: 'InvitationAccept',
        component: () => import('../views/InvitationAccept.vue')
    },
    {
        path: '/reset-password',
        name: 'ResetPassword',
        component: () => import('../views/ResetPassword.vue')
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
                path: 'settings',
                name: 'Settings',
                component: () => import('../views/Settings.vue')
            },
            {
                path: 'decisions',
                name: 'DecisionList',
                component: () => import('../views/DecisionList.vue')
            },
            {
                path: 'favorites',
                name: 'DecisionFavorites',
                component: () => import('../views/DecisionList.vue')
            },
            {
                path: 'decisions/create',
                name: 'DecisionCreate',
                component: () => import('../views/DecisionCreate.vue')
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
            {
                path: 'admin/database',
                name: 'AdminDatabase',
                component: () => import('../views/admin/AdminDatabase.vue')
            },
            {
                path: 'admin/server',
                name: 'AdminServer',
                component: () => import('../views/admin/AdminServer.vue')
            },

            // Wiki
            {
                path: 'wiki',
                name: 'WikiIndex',
                component: () => import('../views/wiki/WikiIndex.vue')
            },
            {
                path: 'wiki/:slug',
                name: 'WikiDetail',
                component: () => import('../views/wiki/WikiDetail.vue')
            },
            {
                path: 'admin/wiki',
                name: 'AdminWiki',
                component: () => import('../views/wiki/AdminWiki.vue')
            },
            {
                path: 'admin/wiki/create',
                name: 'WikiCreate',
                component: () => import('../views/wiki/WikiEditor.vue')
            },
            {
                path: 'admin/wiki/:id/edit',
                name: 'WikiEdit',
                component: () => import('../views/wiki/WikiEditor.vue')
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

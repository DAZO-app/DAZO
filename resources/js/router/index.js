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
        path: '/front',
        component: () => import('../layouts/PublicLayout.vue'),
        children: [
            {
                path: '',
                name: 'PublicFront',
                component: () => import('../views/public/PublicFront.vue')
            },
            {
                path: 'decision/:id',
                name: 'PublicDecision',
                component: () => import('../views/public/PublicDecisionDetail.vue')
            },
            {
                path: 'p/:slug',
                name: 'PublicPage',
                component: () => import('../views/public/PublicPage.vue')
            }
        ]
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
                component: () => import('../views/admin/AdminDashboard.vue'),
                meta: { requiresSuperAdmin: true }
            },
            {
                path: 'admin/circles',
                name: 'AdminCircles',
                component: () => import('../views/admin/AdminCircles.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'admin/users',
                name: 'AdminUsers',
                component: () => import('../views/admin/AdminUsers.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'admin/categories',
                name: 'AdminCategories',
                component: () => import('../views/admin/AdminCategories.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'admin/publication',
                name: 'AdminPublication',
                component: () => import('../views/admin/AdminPublication.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'admin/config',
                name: 'AdminConfig',
                component: () => import('../views/admin/AdminConfig.vue'),
                meta: { requiresSuperAdmin: true }
            },
            {
                path: 'admin/database',
                name: 'AdminDatabase',
                component: () => import('../views/admin/AdminDatabase.vue'),
                meta: { requiresSuperAdmin: true }
            },
            {
                path: 'admin/server',
                name: 'AdminServer',
                component: () => import('../views/admin/AdminServer.vue'),
                meta: { requiresSuperAdmin: true }
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
                component: () => import('../views/wiki/AdminWiki.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'admin/wiki/create',
                name: 'WikiCreate',
                component: () => import('../views/wiki/WikiEditor.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'admin/wiki/:id/edit',
                name: 'WikiEdit',
                component: () => import('../views/wiki/WikiEditor.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'unauthorized',
                name: 'Unauthorized',
                component: () => import('../views/Unauthorized.vue')
            },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

import { useConfigStore } from '../stores/config';

// Guard global
let isConfigLoaded = false;

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const configStore = useConfigStore();

    if (!isConfigLoaded) {
        await configStore.fetchInit();
        isConfigLoaded = true;
    }
    
    if (!authStore.user && localStorage.getItem('dazo_logged_in') === 'true') {
        await authStore.fetchUser();
    }

    if (to.path === '/' && !authStore.isAuthenticated) {
        if (configStore.config.enable_public_front === 'true' || configStore.config.enable_public_front === true) {
            return next({ name: 'PublicFront' });
        }
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next({ name: 'Login' });
    }

    if (to.meta.requiresSuperAdmin && !authStore.isSuperAdmin) {
        return next({ name: 'Unauthorized' });
    }

    if (to.meta.requiresAdmin && !authStore.isAdmin && !authStore.isSuperAdmin) {
        return next({ name: 'Unauthorized' });
    }
    
    if (to.name === 'Login' && authStore.isAuthenticated) {
        return next({ name: 'Dashboard' });
    }

    // if (to.name === 'PublicFront' && authStore.isAuthenticated) {
    //     return next({ name: 'Dashboard' });
    // }

    if (to.name === 'PublicFront' || to.name === 'PublicDecision') {
        if (configStore.config.enable_public_front !== 'true' && configStore.config.enable_public_front !== true) {
            return next({ name: 'Login' });
        }
    }

    next();
});

export default router;

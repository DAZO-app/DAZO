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
                path: 'decisions/:id',
                name: 'DecisionDetail',
                component: () => import('../views/DecisionDetail.vue')
            }
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
    
    // Tente de recover l'utilisateur s'il n'est pas encore loadé
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

<template>
  <div class="app-container">
    <!-- Desktop Sidebar -->
    <aside class="sidebar hidden-mobile">
      <div class="sidebar-logo">
        <div class="sidebar-logo-mark">DAZO</div>
        <div class="sidebar-logo-sub">Decision At Zero Objection</div>
      </div>
      
      <div class="sidebar-section">Navigation</div>
      <router-link to="/" class="sidebar-item" active-class="active">
        <span>⊞</span> Tableau de bord
      </router-link>
      <router-link to="/decisions" class="sidebar-item" active-class="active">
        <span>◎</span> Décisions
      </router-link>
      
      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="avatar av-blue">{{ userInitials }}</div>
          <div style="flex:1;min-width:0;">
            <div class="sidebar-user-name truncate">{{ user?.name || 'Utilisateur' }}</div>
            <div class="sidebar-user-role">{{ user?.role || 'Actif' }}</div>
          </div>
          <button @click="logout" class="btn btn-ghost btn-icon" style="color:rgba(255,255,255,0.4)" title="Déconnexion">⏻</button>
        </div>
      </div>
    </aside>

    <!-- Main router view -->
    <router-view></router-view>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav">
      <router-link to="/" class="m-nav-item" active-class="active">
        <div class="m-nav-icon">⊞</div>
        <span>Accueil</span>
      </router-link>
      <router-link to="/decisions" class="m-nav-item" active-class="active">
        <div class="m-nav-icon">◎</div>
        <span>Décisions</span>
      </router-link>
      <a class="m-nav-item" @click.prevent="logout">
        <div class="m-nav-icon">⏻</div>
        <span>Quitter</span>
      </a>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const user = computed(() => authStore.user);

const userInitials = computed(() => {
  if (!user.value) return '?';
  return user.value.name
    ?.split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0])
    .join('')
    .toUpperCase() || '?';
});

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>

<style scoped>
/* Scoped styles over dazo-theme global classes */
.sidebar {
  width: 220px;
  flex-shrink: 0;
  background: var(--blue-800);
  display: flex;
  flex-direction: column;
  padding: 20px 0;
}
.hidden-mobile { display: none; }

.mobile-nav {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  height: var(--layout-nav-height);
  background: white; border-top: 1px solid var(--gray-200);
  display: flex; justify-content: space-around; align-items: center;
  z-index: 100;
  padding-bottom: env(safe-area-inset-bottom);
}
.m-nav-item {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  color: var(--gray-500); text-decoration: none; width: 64px; text-align: center;
}
.m-nav-icon { font-size: 20px; margin-bottom: 2px; }
.m-nav-item span { font-size: 10px; font-weight: 500; }
.m-nav-item.active { color: var(--blue-700); }

@media (min-width: 768px) {
  .hidden-mobile { display: flex; }
  .mobile-nav { display: none; }
}

.sidebar-logo { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 12px; }
.sidebar-logo-mark { font-size: 20px; font-weight: 600; color: white; letter-spacing: -0.02em; }
.sidebar-logo-sub { font-size: 10px; color: rgba(255,255,255,0.4); letter-spacing: 0.06em; text-transform: uppercase; margin-top: 2px; }
.sidebar-section { padding: 4px 20px; font-size: 10px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(255,255,255,0.3); margin: 8px 0 4px; }
.sidebar-item { display: flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 13px; color: rgba(255,255,255,0.65); cursor: pointer; transition: all 0.12s; border-left: 3px solid transparent; text-decoration: none; }
.sidebar-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.9); }
.sidebar-item.active { background: rgba(255,255,255,0.1); color: white; border-left-color: var(--blue-400); font-weight: 500; }
.sidebar-footer { margin-top: auto; padding: 12px 20px; border-top: 1px solid rgba(255,255,255,0.1); }
.sidebar-user { display: flex; align-items: center; gap: 10px; }
.avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; flex-shrink: 0; }
.av-blue { background: var(--blue-100); color: var(--blue-800); }
.sidebar-user-name { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.8); }
.sidebar-user-role { font-size: 10px; color: rgba(255,255,255,0.35); }
</style>

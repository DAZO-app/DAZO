<template>
  <div class="layout-wrapper">
    <ImpersonationBanner />

    <!-- ============ TOPBAR MOBILE ============ -->
    <header class="topbar">
      <div class="topbar-left">
        <router-link to="/">
          <img src="/DAZO-logo-carre-blanc.svg" alt="DAZO" class="topbar-logo" />
        </router-link>
      </div>
      <div class="topbar-right">
        <button class="btn-new-mobile" @click="$router.push({ name: 'DecisionCreate' })" title="Nouvelle décision">
          ＋
        </button>
        <button class="topbar-menu-btn" @click="mobileMenuOpen = !mobileMenuOpen" :class="{ open: mobileMenuOpen }">
          <span></span><span></span><span></span>
        </button>
      </div>

      <!-- Menu déroulant mobile -->
      <transition name="slide-down">
        <nav v-if="mobileMenuOpen" class="mobile-menu" @click="mobileMenuOpen = false">
          <router-link to="/" class="mobile-menu-item" exact-active-class="active"><i class="fa-solid fa-house" style="margin-right: 8px;"></i> Tableau de bord</router-link>
          <router-link to="/decisions" class="mobile-menu-item" :class="{ active: $route.path === '/decisions' && Object.keys($route.query).length === 0 }"><i class="fa-solid fa-list" style="margin-right: 8px;"></i> Décisions</router-link>
          <router-link :to="{ name: 'CircleList' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-circle-nodes" style="margin-right: 8px;"></i> Cercles</router-link>
          
          <div v-if="hasMyProposals || hasMyAnimations || pendingTotal > 0" class="mobile-menu-divider"></div>
          <router-link v-if="hasMyProposals" :to="{ path: '/decisions', query: { role: 'author' } }" class="mobile-menu-item mobile-menu-sub" :class="{ active: $route.path === '/decisions' && $route.query.role === 'author' }">
            <span class="sub-dot" style="background: var(--blue-300)"></span> Mes propositions
          </router-link>
          <router-link v-if="hasMyAnimations" :to="{ path: '/decisions', query: { role: 'animator' } }" class="mobile-menu-item mobile-menu-sub" :class="{ active: $route.path === '/decisions' && $route.query.role === 'animator' }">
            <span class="sub-dot" style="background: var(--teal-400)"></span> Décisions que j'anime
          </router-link>
          <router-link v-if="pendingTotal > 0" :to="{ path: '/decisions', query: { action: 'pending' } }" class="mobile-menu-item mobile-menu-sub" :class="{ active: $route.path === '/decisions' && $route.query.action === 'pending' }">
            <span class="sub-dot dot-amber"></span> Réactions attendues
          </router-link>

          <template v-if="isAdmin && !isImpersonating">
            <div class="mobile-menu-divider"></div>
            <div class="sidebar-section" style="padding-left:20px; color:rgba(255,255,255,0.4)">Administration</div>
            <router-link :to="{ name: 'AdminUsers' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-users" style="margin-right: 8px;"></i> Utilisateurs</router-link>
            <router-link :to="{ name: 'AdminCircles' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-circle-nodes" style="margin-right: 8px;"></i> Cercles Admin</router-link>
            <router-link :to="{ name: 'AdminCategories' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-tags" style="margin-right: 8px;"></i> Catégories</router-link>
            
            <template v-if="isSuperAdmin">
              <div class="mobile-menu-divider"></div>
              <div class="sidebar-section" style="padding-left:20px; color:rgba(255,255,255,0.4)">Système</div>
              <router-link :to="{ name: 'Admin' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-gauge-high" style="margin-right: 8px;"></i> Dashboard Admin</router-link>
              <router-link :to="{ name: 'AdminConfig' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-gears" style="margin-right: 8px;"></i> Configuration</router-link>
              <router-link :to="{ name: 'AdminDatabase' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-database" style="margin-right: 8px;"></i> BDD</router-link>
              <router-link :to="{ name: 'AdminServer' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-server" style="margin-right: 8px;"></i> Serveur</router-link>
            </template>
          </template>

          <div class="mobile-menu-divider"></div>
          <router-link to="/settings" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Paramètres</router-link>
          <a class="mobile-menu-item text-red-500" @click.prevent="logout"><i class="fa-solid fa-power-off" style="margin-right: 8px;"></i> Déconnexion</a>
        </nav>
      </transition>
    </header>

    <div class="app-container">
      <!-- ============ DESKTOP SIDEBAR ============ -->
      <aside class="sidebar">
        <!-- Logo & Brand -->
        <router-link to="/" class="sidebar-logo">
          <img src="/DAZO-logo-carre-blanc.svg" alt="DAZO" class="sidebar-logo-img" />
          <div class="sidebar-logo-sub">Decision At Zero Objection</div>
        </router-link>

        <!-- Bouton Nouvelle Décision -->
        <div class="sidebar-cta">
          <button class="btn-new-decision" @click="$router.push({ name: 'DecisionCreate' })">
            <span class="btn-new-icon">＋</span>
            Nouvelle décision
          </button>
        </div>

        <!-- Navigation -->
        <div class="sidebar-section">Navigation</div>
        <router-link to="/" class="sidebar-item" exact-active-class="active">
          <span><i class="fa-solid fa-house"></i></span> Tableau de bord
        </router-link>
        <router-link to="/decisions" class="sidebar-item" :class="{ active: $route.path === '/decisions' && Object.keys($route.query).length === 0 }">
          <span><i class="fa-solid fa-list"></i></span> Décisions
        </router-link>
        <router-link :to="{ name: 'CircleList' }" class="sidebar-item" active-class="active">
          <span><i class="fa-solid fa-circle-nodes"></i></span> Cercles
        </router-link>

        <!-- Sous-navigation: Mes filtres -->
        <template v-if="hasMyProposals || hasMyAnimations || pendingTotal > 0">
          <div class="sidebar-section" style="margin-top: 8px;">Mes Vues</div>
          <router-link v-if="hasMyProposals" :to="{ path: '/decisions', query: { role: 'author' } }" class="sidebar-subitem" :class="{ active: $route.path === '/decisions' && $route.query.role === 'author' }">
            <span class="sub-dot" style="background: var(--blue-300)"></span> Mes propositions
          </router-link>
          <router-link v-if="hasMyAnimations" :to="{ path: '/decisions', query: { role: 'animator' } }" class="sidebar-subitem" :class="{ active: $route.path === '/decisions' && $route.query.role === 'animator' }">
            <span class="sub-dot" style="background: var(--teal-400)"></span> Décisions que j'anime
          </router-link>
          <router-link v-if="pendingTotal > 0" :to="{ path: '/decisions', query: { action: 'pending' } }" class="sidebar-subitem" :class="{ active: $route.path === '/decisions' && $route.query.action === 'pending' }">
            <span class="sub-dot dot-amber"></span> Réactions attendues
            <span v-if="pendingTotal > 0" class="sidebar-badge badge-red">{{ pendingTotal }}</span>
          </router-link>
        </template>

        <template v-if="isAdmin && !isImpersonating">
          <div class="sidebar-section">Administration</div>
          <router-link :to="{ name: 'AdminUsers' }" class="sidebar-item" active-class="active">
            <span><i class="fa-solid fa-users"></i></span> Utilisateurs
          </router-link>
          <router-link :to="{ name: 'AdminCircles' }" class="sidebar-item" active-class="active">
            <span><i class="fa-solid fa-circle-nodes"></i></span> Cercles Admin
          </router-link>
          <router-link :to="{ name: 'AdminCategories' }" class="sidebar-item" active-class="active">
            <span><i class="fa-solid fa-tags"></i></span> Catégories
          </router-link>

          <template v-if="isSuperAdmin">
             <div class="sidebar-section">Système</div>
            <router-link :to="{ name: 'Admin' }" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-gauge-high"></i></span> Dashboard Admin
            </router-link>
            <router-link :to="{ name: 'AdminConfig' }" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-gears"></i></span> Configuration
            </router-link>
            <router-link :to="{ name: 'AdminDatabase' }" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-database"></i></span> BDD
            </router-link>
            <router-link :to="{ name: 'AdminServer' }" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-server"></i></span> Serveur
            </router-link>
          </template>
        </template>

        <div style="height: 16px;"></div>
        <router-link to="/settings" class="sidebar-item" active-class="active">
          <span><i class="fa-solid fa-sliders"></i></span> Paramètres
        </router-link>

        <!-- Pied de barre -->
        <div class="sidebar-footer">
          <div class="sidebar-user">
            <div class="avatar av-blue">{{ userInitials }}</div>
            <div style="flex:1;min-width:0;">
              <div class="sidebar-user-name truncate">{{ user?.name || 'Utilisateur' }}</div>
              <div class="sidebar-user-role">{{ user?.role || 'Actif' }}</div>
            </div>
            <button @click="logout" class="btn btn-ghost btn-icon" style="color:rgba(255,255,255,0.4)" title="Déconnexion"><i class="fa-solid fa-power-off"></i></button>
          </div>
        </div>
      </aside>

      <!-- Main content -->
      <router-view class="main-content"></router-view>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { usePendingStore } from '../stores/pending';
import { useDecisionStore } from '../stores/decision';
import { useRouter } from 'vue-router';
import ImpersonationBanner from '../components/ImpersonationBanner.vue';

const authStore = useAuthStore();
const pending = usePendingStore();
const decisionStore = useDecisionStore();
const router = useRouter();

const mobileMenuOpen = ref(false);

const user = computed(() => authStore.user);
const isAdmin = computed(() => ['admin', 'superadmin'].includes(user.value?.role));
const isSuperAdmin = computed(() => user.value?.role === 'superadmin');
const isImpersonating = computed(() => authStore.isImpersonating);

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

const pendingTotal = computed(() => {
    return (pending.counts.clarifications || 0) + (pending.counts.reactions || 0) + (pending.counts.objections || 0);
});

const hasMyProposals = computed(() => {
    return decisionStore.decisions.some(d => {
        return d.participants?.some(p => p.user_id === user.value?.id && p.role === 'author');
    });
});

const hasMyAnimations = computed(() => {
    return decisionStore.decisions.some(d => {
        return d.participants?.some(p => p.user_id === user.value?.id && p.role === 'animator');
    });
});

const countClarification = computed(() => decisionStore.decisions.filter(d => d.status === 'clarification').length);
const countReaction = computed(() => decisionStore.decisions.filter(d => d.status === 'reaction').length);
const countObjection = computed(() => decisionStore.decisions.filter(d => d.status === 'objection').length);

onMounted(() => {
  if (authStore.isAuthenticated) {
    pending.startPolling(); // auto-refresh every 60s
    decisionStore.fetchDecisions();
  }
});

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>

<style scoped>
/* ========= Layout root ========= */
.layout-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100%;
}

.app-container {
  flex: 1;
  display: flex;
  overflow: hidden;
}

.main-content {
  flex: 1;
  overflow-y: auto;
}

/* ========= TOPBAR (mobile) ========= */
.topbar {
  position: sticky; top: 0; z-index: 200;
  display: flex; align-items: center; justify-content: space-between;
  background: var(--blue-800); padding: 0 16px;
  height: 56px; flex-shrink: 0;
}

.topbar-left { display: flex; align-items: center; gap: 10px; }
.topbar-left a { text-decoration: none; display: flex; align-items: center; }

.topbar-logo {
  height: 28px; width: auto;
}

.topbar-brand {
  font-size: 15px; font-weight: 700; color: white; letter-spacing: 0.05em;
}

.topbar-right { display: flex; align-items: center; gap: 8px; }

/* Bouton + mobile */
.btn-new-mobile {
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--blue-400); color: white;
  border: none; font-size: 20px; font-weight: 300;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: background 0.15s;
}
.btn-new-mobile:hover { background: var(--blue-300); }

/* Hamburger */
.topbar-menu-btn {
  width: 32px; height: 32px; background: transparent; border: none;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px;
  cursor: pointer; padding: 4px;
}
.topbar-menu-btn span {
  display: block; width: 20px; height: 2px; background: rgba(255,255,255,0.85); border-radius: 2px;
  transition: all 0.2s ease;
}
.topbar-menu-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.topbar-menu-btn.open span:nth-child(2) { opacity: 0; }
.topbar-menu-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* Mobile dropdown menu */
.mobile-menu {
  position: absolute; top: 56px; left: 0; right: 0;
  background: var(--blue-900); z-index: 300;
  border-top: 1px solid rgba(255,255,255,0.1);
  padding: 8px 0;
  box-shadow: 0 8px 24px rgba(0,0,0,0.25);
}

.mobile-menu-item {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 20px; font-size: 14px;
  color: rgba(255,255,255,0.75); text-decoration: none;
  cursor: pointer; transition: background 0.1s;
}
.mobile-menu-item:hover, .mobile-menu-item.active { background: rgba(255,255,255,0.08); color: white; }

.mobile-menu-sub { padding-left: 36px; font-size: 13px; color: rgba(255,255,255,0.5); }
.mobile-menu-sub.active { color: white; }

.mobile-menu-divider { height: 1px; background: rgba(255,255,255,0.1); margin: 6px 0; }

.m-badge {
  margin-left: auto; min-width: 18px; height: 18px; padding: 0 5px;
  border-radius: 9px; font-size: 10px; font-weight: 700; 
  display: inline-flex; align-items: center; justify-content: center;
  background: rgba(255,255,255,0.25); color: white;
}
.m-badge.badge-red { background: var(--red-500); }

/* Slide animation */
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }

/* ========= DESKTOP SIDEBAR ========= */
.sidebar {
  display: none; /* hidden on mobile */
  width: 240px; flex-shrink: 0;
  background: var(--blue-800);
  flex-direction: column;
  padding: 0 0 0 0;
  overflow-y: auto;
}

.sidebar-logo {
  padding: 16px 20px 16px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  margin-bottom: 8px;
  display: flex; flex-direction: column; align-items: center; text-align: center;
  text-decoration: none; cursor: pointer;
}
.sidebar-logo-img {
  width: 140px; height: auto;
  margin-bottom: 4px;
}
.sidebar-logo-sub {
  font-size: 8px; color: rgba(255,255,255,0.35);
  letter-spacing: 0.06em; text-transform: uppercase; margin-top: 2px;
}

/* CTA Bouton Nouvelle Décision */
.sidebar-cta {
  padding: 12px 16px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  margin-bottom: 4px;
}
.btn-new-decision {
  width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 10px 16px; border-radius: var(--radius-md);
  background: linear-gradient(135deg, var(--blue-400), var(--blue-500));
  color: white; font-size: 13px; font-weight: 600;
  border: none; cursor: pointer; transition: all 0.15s;
  box-shadow: 0 2px 8px rgba(59,130,246,0.4);
}
.btn-new-decision:hover {
  background: linear-gradient(135deg, var(--blue-300), var(--blue-400));
  box-shadow: 0 4px 12px rgba(59,130,246,0.5);
  transform: translateY(-1px);
}
.btn-new-icon { font-size: 18px; font-weight: 300; line-height: 1; }

.sidebar-section {
  padding: 4px 20px; font-size: 10px; font-weight: 600;
  letter-spacing: 0.08em; text-transform: uppercase;
  color: rgba(255,255,255,0.3); margin: 8px 0 4px;
}
.sidebar-item {
  display: flex; align-items: center; gap: 8px; padding: 10px 20px;
  font-size: 13px; color: rgba(255,255,255,0.65); cursor: pointer;
  transition: all 0.12s; border-left: 3px solid transparent; text-decoration: none;
}
.sidebar-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.9); }
.sidebar-item.active { background: rgba(255,255,255,0.1); color: white; border-left-color: var(--blue-400); font-weight: 500; }

.sidebar-subitem {
  display: flex; align-items: center; gap: 8px;
  padding: 7px 20px 7px 32px;
  font-size: 12px; color: rgba(255,255,255,0.5); cursor: pointer; 
  transition: all 0.12s; border-left: 3px solid transparent; text-decoration: none;
}
.sidebar-subitem:hover { background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.8); }
.sidebar-subitem.active { background: rgba(255,255,255,0.07); color: white; border-left-color: rgba(255,255,255,0.3); }

.sub-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.dot-amber { background: var(--amber-400); }
.dot-blue  { background: var(--blue-300); }
.dot-red   { background: var(--red-400); }

.sidebar-badge {
  margin-left: auto; min-width: 18px; height: 18px; padding: 0 5px;
  border-radius: 9px; font-size: 10px; font-weight: 700; 
  display: inline-flex; align-items: center; justify-content: center;
  background: rgba(255,255,255,0.2); color: white;
}
.sidebar-badge.badge-red { background: var(--red-500); }

.sidebar-footer {
  margin-top: auto; padding: 12px 20px;
  border-top: 1px solid rgba(255,255,255,0.1);
}
.sidebar-user { display: flex; align-items: center; gap: 10px; }
.avatar { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; flex-shrink: 0; }
.av-blue { background: var(--blue-100); color: var(--blue-800); }
.sidebar-user-name { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.8); }
.sidebar-user-role { font-size: 10px; color: rgba(255,255,255,0.35); }

/* ========= BREAKPOINTS ========= */
@media (min-width: 768px) {
  /* Hide topbar on desktop */
  .topbar { display: none; }
  /* Show sidebar */
  .sidebar { display: flex; }
}
</style>

<template>
  <div class="layout-wrapper">
    <ImpersonationBanner />

    <!-- ============ TOPBAR MOBILE ============ -->
    <header class="topbar">
      <div class="topbar-left">
        <img src="/DAZO-logo-carre-blanc.svg" alt="DAZO" class="topbar-logo" />
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
          <router-link to="/" class="mobile-menu-item" exact-active-class="active">⊞ Tableau de bord</router-link>
          <router-link to="/decisions" class="mobile-menu-item" active-class="active" exact>◎ Décisions</router-link>
          <router-link to="/decisions/clarifications" class="mobile-menu-item mobile-menu-sub" active-class="active">
            <span class="sub-dot dot-amber"></span> Clarifications
            <span v-if="pending.counts.clarifications > 0" class="m-badge">{{ pending.counts.clarifications }}</span>
          </router-link>
          <router-link to="/decisions/reactions" class="mobile-menu-item mobile-menu-sub" active-class="active">
            <span class="sub-dot dot-blue"></span> Réactions
            <span v-if="pending.counts.reactions > 0" class="m-badge">{{ pending.counts.reactions }}</span>
          </router-link>
          <router-link to="/decisions/objections" class="mobile-menu-item mobile-menu-sub" active-class="active">
            <span class="sub-dot dot-red"></span> Objections
            <span v-if="pending.counts.objections > 0" class="m-badge badge-red">{{ pending.counts.objections }}</span>
          </router-link>
          <router-link to="/circles" class="mobile-menu-item" active-class="active">⊛ Cercles</router-link>
          <router-link v-if="isAdmin && !isImpersonating" to="/admin" class="mobile-menu-item" active-class="active">⚙ Administration</router-link>
          <div class="mobile-menu-divider"></div>
          <a class="mobile-menu-item text-red-500" @click.prevent="logout">⏻ Déconnexion</a>
        </nav>
      </transition>
    </header>

    <div class="app-container">
      <!-- ============ DESKTOP SIDEBAR ============ -->
      <aside class="sidebar">
        <!-- Logo & Brand -->
        <div class="sidebar-logo">
          <img src="/DAZO-logo-carre-blanc.svg" alt="DAZO" class="sidebar-logo-img" />
          <div class="sidebar-logo-sub">Decision At Zero Objection</div>
        </div>

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
          <span>⊞</span> Tableau de bord
        </router-link>
        <router-link to="/decisions" class="sidebar-item" active-class="active" exact>
          <span>◎</span> Décisions
        </router-link>

        <!-- Sous-navigation -->
        <router-link to="/decisions/clarifications" class="sidebar-subitem" active-class="active">
          <span class="sub-dot dot-amber"></span>
          Clarifications
          <span v-if="pending.counts.clarifications > 0" class="sidebar-badge">{{ pending.counts.clarifications }}</span>
        </router-link>
        <router-link to="/decisions/reactions" class="sidebar-subitem" active-class="active">
          <span class="sub-dot dot-blue"></span>
          Réactions
          <span v-if="pending.counts.reactions > 0" class="sidebar-badge">{{ pending.counts.reactions }}</span>
        </router-link>
        <router-link to="/decisions/objections" class="sidebar-subitem" active-class="active">
          <span class="sub-dot dot-red"></span>
          Objections
          <span v-if="pending.counts.objections > 0" class="sidebar-badge badge-red">{{ pending.counts.objections }}</span>
        </router-link>

        <router-link to="/circles" class="sidebar-item" active-class="active">
          <span>⊛</span> Cercles
        </router-link>

        <template v-if="isAdmin && !isImpersonating">
          <div class="sidebar-section">Administration</div>
          <router-link to="/admin" class="sidebar-item" active-class="active">
            <span>⚙</span> Administration
          </router-link>
        </template>

        <!-- Pied de barre -->
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

      <!-- Main content -->
      <router-view class="main-content"></router-view>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { usePendingStore } from '../stores/pending';
import { useRouter } from 'vue-router';
import ImpersonationBanner from '../components/ImpersonationBanner.vue';

const authStore = useAuthStore();
const pending = usePendingStore();
const router = useRouter();

const mobileMenuOpen = ref(false);

const user = computed(() => authStore.user);
const isAdmin = computed(() => ['admin', 'superadmin'].includes(user.value?.role));
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

onMounted(() => {
  if (authStore.isAuthenticated) {
    pending.startPolling(); // auto-refresh every 60s
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

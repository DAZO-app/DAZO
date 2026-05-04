<template>
  <div class="layout-wrapper">
    <ImpersonationBanner />

    <!-- ============ TOPBAR MOBILE ============ -->
    <header class="topbar">
      <div class="topbar-left">
        <router-link to="/">
          <img :src="configStore.defaultLogoUrl" alt="DAZO" class="topbar-logo" />
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
          <router-link to="/wiki" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-book-open" style="margin-right: 8px;"></i> Aide</router-link>
          
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

          <template v-if="isAdmin">
            <div class="mobile-menu-divider"></div>
            <div class="sidebar-section" style="padding-left:20px; color:rgba(255,255,255,0.4)">Administration</div>
            <router-link :to="{ name: 'AdminDashboard' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-gauge-high" style="margin-right: 8px;"></i> Dashboard Admin</router-link>
            <router-link :to="{ name: 'AdminUsers' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-users" style="margin-right: 8px;"></i> Utilisateurs</router-link>
            <router-link :to="{ name: 'AdminCircles' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-circle-nodes" style="margin-right: 8px;"></i> Cercles Admin</router-link>
            <router-link :to="{ name: 'AdminCategories' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-tags" style="margin-right: 8px;"></i> Catégories</router-link>
            <router-link :to="{ name: 'AdminSiteConfig' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Configuration site</router-link>
            <router-link :to="{ name: 'AdminPublication' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-globe" style="margin-right: 8px;"></i> API</router-link>
            
            <template v-if="isSuperAdmin">
              <div class="mobile-menu-divider"></div>
              <div class="sidebar-section" style="padding-left:20px; color:rgba(255,255,255,0.4)">Système</div>
              <router-link :to="{ name: 'AdminSystemDashboard' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-microchip" style="margin-right: 8px;"></i> Dashboard Système</router-link>
              <router-link :to="{ name: 'AdminConfig' }" class="mobile-menu-item" active-class="active"><i class="fa-solid fa-gears" style="margin-right: 8px;"></i> Configuration Système</router-link>
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
          <img :src="configStore.defaultLogoUrl" alt="DAZO" class="sidebar-logo-img" />
          <div style="font-size: 8px; color: rgba(255,255,255,0.3); text-align: center; margin-top: 4px; font-family: monospace;">
            BETA [04/05/2026 14:58:27]
          </div>
        </router-link>

        <!-- Bouton Nouvelle Décision -->
        <div class="sidebar-cta">
          <button class="btn-new-decision" @click="$router.push({ name: 'DecisionCreate' })">
            <span class="btn-new-icon">＋</span>
            Nouvelle décision
          </button>
        </div>

         <!-- Navigation -->
        <div class="sidebar-section-header" @click="sectionsOpen.navigation = !sectionsOpen.navigation">
          <span>Navigation</span>
          <i :class="sectionsOpen.navigation ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i>
        </div>
        <transition name="sidebar-fade">
          <div v-show="sectionsOpen.navigation" class="sidebar-collapsible">
            <router-link to="/" class="sidebar-item" exact-active-class="active">
              <span><i class="fa-solid fa-house"></i></span> Tableau de bord
            </router-link>
            <router-link to="/decisions" class="sidebar-item" :class="{ active: $route.name === 'DecisionList' }">
              <span><i class="fa-solid fa-list"></i></span> Décisions
            </router-link>
            <router-link to="/favorites" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-star"></i></span> Mes favoris
            </router-link>
            <router-link :to="{ name: 'CircleList' }" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-circle-nodes"></i></span> Cercles
            </router-link>
            
            <!-- Lien vers le site public -->
            <a v-if="configStore.config.enable_public_front === 'true' || configStore.config.enable_public_front === true" 
               href="/front" target="_blank" class="sidebar-item">
              <span><i class="fa-solid fa-globe text-emerald-400"></i></span> Voir le site public
            </a>

            <router-link to="/settings" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-sliders"></i></span> Paramètres
            </router-link>
            <router-link to="/wiki" class="sidebar-item" active-class="active">
              <span><i class="fa-solid fa-book-open"></i></span> Aide
            </router-link>
          </div>
        </transition>

        <!-- Sous-navigation: Mes filtres -->
        <template v-if="normalizedCustomViews.length > 0">
          <div class="sidebar-section-header" style="margin-top: 8px;" @click="sectionsOpen.views = !sectionsOpen.views">
            <span>Mes Vues</span>
            <i :class="sectionsOpen.views ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i>
          </div>
          <transition name="sidebar-fade">
            <div v-show="sectionsOpen.views" class="sidebar-collapsible">
              <router-link v-for="view in normalizedCustomViews" :key="view.id" 
                           :to="{ name: 'DecisionList', query: { ...view.filters, view_label: view.label } }" 
                           class="sidebar-item" 
                           :class="{ active: $route.query.view_label === view.label }">
                <span>
                  <img v-if="view.favicon" :src="view.favicon" style="width: 16px; height: 16px; object-fit: contain; vertical-align: middle; border-radius: 3px;" alt="">
                  <i v-else :class="view.icon || 'fa-solid fa-layer-group'"></i>
                </span>
                {{ view.label }}
              </router-link>
            </div>
          </transition>
        </template>

        <template v-if="isAdmin">
          <div class="sidebar-section-header" style="margin-top: 8px;" @click="sectionsOpen.admin = !sectionsOpen.admin">
            <span>Administration</span>
            <i :class="sectionsOpen.admin ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i>
          </div>
          <transition name="sidebar-fade">
            <div v-show="sectionsOpen.admin" class="sidebar-collapsible">
              <router-link :to="{ name: 'AdminDashboard' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-gauge-high"></i></span> Dashboard Admin
              </router-link>
              <router-link :to="{ name: 'AdminUsers' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-users"></i></span> Utilisateurs
              </router-link>
              <router-link :to="{ name: 'AdminCircles' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-circle-nodes"></i></span> Cercles Admin
              </router-link>
              <router-link :to="{ name: 'AdminCategories' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-tags"></i></span> Catégories
              </router-link>
              <router-link :to="{ name: 'AdminSiteConfig' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-sliders"></i></span> Configuration site
              </router-link>
              <router-link :to="{ name: 'AdminPublication' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-globe"></i></span> API
              </router-link>
            </div>
          </transition>
        </template>

        <template v-if="isSuperAdmin">
          <div class="sidebar-section-header" style="margin-top: 8px;" @click="sectionsOpen.system = !sectionsOpen.system">
            <span>Système</span>
            <i :class="sectionsOpen.system ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i>
          </div>
          <transition name="sidebar-fade">
            <div v-show="sectionsOpen.system" class="sidebar-collapsible">
              <router-link :to="{ name: 'AdminSystemDashboard' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-microchip"></i></span> Dashboard Système
              </router-link>
              <router-link :to="{ name: 'AdminConfig' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-gears"></i></span> Configuration Système
              </router-link>
              <router-link :to="{ name: 'AdminDatabase' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-database"></i></span> BDD
              </router-link>
              <router-link :to="{ name: 'AdminServer' }" class="sidebar-item" active-class="active">
                <span><i class="fa-solid fa-server"></i></span> Serveur
              </router-link>
            </div>
          </transition>
        </template>

        <!-- Pied de barre -->
        <div class="sidebar-footer">
          <!-- Restore Banner Button -->
          <button v-if="(isAdmin || isImpersonating) && authStore.bannerHidden" 
                  @click="authStore.setBannerHidden(false)" 
                  class="sidebar-item" 
                  style="margin-bottom: 8px; border-radius: var(--radius-sm); background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3);">
            <span><i class="fa-solid fa-eye"></i></span> Afficher barre admin
          </button>

          <div class="sidebar-user">
            <div class="avatar av-blue">{{ userInitials }}</div>
            <div style="flex:1;min-width:0;">
              <div class="sidebar-user-name truncate">{{ user?.name || 'Utilisateur' }}</div>
              <div class="sidebar-user-role">{{ user?.role || 'Actif' }}</div>
            </div>
            
            <div class="flex gap-4">
              <button v-if="isImpersonating" 
                      @click="stopImpersonating" 
                      class="btn btn-ghost btn-icon" 
                      style="color:var(--amber-400)" 
                      title="Arrêter la simulation">
                <i class="fa-solid fa-user-slash"></i>
              </button>
              <button @click="logout" 
                      class="btn btn-ghost btn-icon" 
                      style="color:rgba(255,255,255,0.4)" 
                      title="Déconnexion">
                <i class="fa-solid fa-power-off"></i>
              </button>
            </div>
          </div>
        </div>
      </aside>

      <!-- Main content -->
      <router-view class="main-content"></router-view>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import { usePendingStore } from '../stores/pending';
import { useDecisionStore } from '../stores/decision';
import { useConfigStore } from '../stores/config';
import { useRouter, useRoute } from 'vue-router';
import ImpersonationBanner from '../components/ImpersonationBanner.vue';

const authStore = useAuthStore();
const pending = usePendingStore();
const decisionStore = useDecisionStore();
const configStore = useConfigStore();
const router = useRouter();
const route = useRoute();

const mobileMenuOpen = ref(false);
const sectionsOpen = ref({
  navigation: true,
  views: false,
  admin: false,
  system: false
});

const user = computed(() => authStore.user);
const isAdmin = computed(() => ['admin', 'superadmin'].includes(user.value?.role));
const isSuperAdmin = computed(() => user.value?.role === 'superadmin');
const isImpersonating = computed(() => authStore.isImpersonating);
const isActuallyAdmin = computed(() => ['admin', 'superadmin'].includes(user.value?.role));
const isActuallySuperAdmin = computed(() => user.value?.role === 'superadmin');

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

const normalizedCustomViews = computed(() => {
  return (user.value?.custom_views || [])
    .filter(view => view.enabled === true)
    .map((view, index) => {
    const label = view.label || view.name || `Vue ${index + 1}`;
    const viewId = String(view.id || label || index).toLowerCase();
    const filters = view.filters && typeof view.filters === 'object' ? view.filters : {};

    let color = 'blue';
    if (viewId.includes('urgent') || label.toLowerCase().includes('urgence')) {
      color = 'red';
    } else if (viewId.includes('pending') || label.toLowerCase().includes('attente')) {
      color = 'amber';
    }

    return {
      id: viewId || `view-${index}`,
      label,
      filters,
      color,
      icon: view.icon || null,
      favicon: view.favicon || null,
    };
  });
});

const pendingTotal = computed(() => {
    return (pending.counts.clarifications || 0) + (pending.counts.reactions || 0) + (pending.counts.objections || 0);
});

const urgentTotal = computed(() => {
    return decisionStore.decisions.filter(d => {
        if (!d.current_deadline) return false;
        const deadline = new Date(d.current_deadline);
        const now = new Date();
        // Urgent if less than 24h or already expired
        return deadline.getTime() - now.getTime() < 24 * 60 * 60 * 1000;
    }).length;
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

const refreshAllData = () => {
  console.log('Refreshing all data for user:', user.value?.id);
  if (authStore.isAuthenticated) {
    pending.fetch();
    decisionStore.fetchDecisions();
    configStore.fetchConfig();
    
    // Auto-open admin section for admins if not already explicitly closed
    if (isActuallyAdmin.value) {
      sectionsOpen.value.admin = true;
    }
    if (isActuallySuperAdmin.value) {
      sectionsOpen.value.system = true;
    }
  }
};

// Re-fetch data whenever user identity changes (impersonation start/stop)
watch(() => user.value?.id, (newId, oldId) => {
  if (newId) {
    refreshAllData();
    // Si on est sur une décision, recharger les données de participation pour le nouvel utilisateur
    if (route.name === 'DecisionDetail' && route.params.id) {
      decisionStore.fetchDecisionById(route.params.id);
    }
  }
}, { immediate: true });

onMounted(() => {
  // Extra safety for Echo registration
  if (window.Echo && user.value?.id) {
    pending.startEcho(user.value.id);
  }
});

const logout = async () => {
  try {
    await authStore.logout();
  } catch (e) {
    console.error('Logout failed', e);
  } finally {
    window.location.href = '/';
  }
};

const stopImpersonating = async () => {
  await authStore.stopImpersonating();
  window.location.reload(); // Recharger la page courante avec l'identité originale
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

.sidebar-section-header {
  padding: 12px 20px 8px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: color 0.2s;
}

.sidebar-section-header:hover {
  color: rgba(255, 255, 255, 0.8);
}

.sidebar-section-header i {
  font-size: 10px;
  opacity: 0.5;
}

.sidebar-collapsible {
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-fade-enter-active,
.sidebar-fade-leave-active {
  transition: all 0.25s ease-out;
  max-height: 500px;
}

.sidebar-fade-enter-from,
.sidebar-fade-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-10px);
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
.sidebar-badge.badge-amber { background: var(--amber-500); }

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

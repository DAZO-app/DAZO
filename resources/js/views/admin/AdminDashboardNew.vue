<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO WELCOME -->
      <div class="hero-card">
        <div class="hero-flex">
          <div class="hero-main-identity">
            <img v-if="configStore.hasCustomLogo" :src="configStore.customLogoUrl" alt="Logo" class="hero-custom-logo" />
            <div>
               <div class="hero-title">Administration Générale</div>
               <div class="hero-subtitle">Gérez les membres, les cercles et le contenu de la plateforme.</div>
            </div>
          </div>
          <div class="hero-action">
             <div class="admin-pill">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Espace Administrateur</span>
             </div>
          </div>
        </div>
      </div>

      <!-- STATS TILES -->
      <div class="stats-row mb-32">
        <div class="stat-card v-total clickable" @click="$router.push('/admin/users')">
          <div class="stat-icon-part">
            <div class="stat-icon-wrap"><i class="fa-solid fa-users"></i></div>
          </div>
          <div class="stat-info-part">
            <div class="stat-number-simple">{{ stats.users_count || 0 }}</div>
            <div class="stat-label">Utilisateurs</div>
          </div>
        </div>

        <div class="stat-card v-proposals clickable" @click="$router.push('/admin/categories')">
          <div class="stat-icon-part">
            <div class="stat-icon-wrap"><i class="fa-solid fa-handshake"></i></div>
          </div>
          <div class="stat-info-part">
            <div class="stat-number-simple">{{ stats.decisions_count || 0 }}</div>
            <div class="stat-label">Décisions</div>
          </div>
        </div>

        <div class="stat-card v-anime clickable" @click="$router.push('/admin/circles')">
          <div class="stat-icon-part">
            <div class="stat-icon-wrap"><i class="fa-solid fa-circle-nodes"></i></div>
          </div>
          <div class="stat-info-part">
            <div class="stat-number-simple">{{ stats.circles_count || 0 }}</div>
            <div class="stat-label">Cercles</div>
          </div>
        </div>

        <div class="stat-card v-adopted clickable" @click="$router.push('/admin/categories')">
          <div class="stat-icon-part">
            <div class="stat-icon-wrap"><i class="fa-solid fa-tags"></i></div>
          </div>
          <div class="stat-info-part">
            <div class="stat-number-simple">{{ stats.categories_count || 0 }}</div>
            <div class="stat-label">Catégories</div>
          </div>
        </div>
      </div>

      <div class="grid-layout">
        <!-- LEFT COLUMN: QUICK ACTIONS -->
        <div class="col-main">
            <div class="premium-card">
                <div class="pc-header pc-header-indigo">
                    <div class="pc-header-icon"><i class="fa-solid fa-bolt"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Actions Rapides</div>
                        <div class="pc-header-sub">Accès directs aux outils de gestion courante</div>
                    </div>
                </div>
                <div class="pc-body p-24">
                  <div class="actions-buttons">
                    <router-link to="/admin/users" class="btn btn-white btn-action">
                        <i class="fa-solid fa-user-plus text-blue-500"></i>
                        <span>Gérer les Utilisateurs</span>
                    </router-link>
                    <router-link to="/admin/circles" class="btn btn-white btn-action">
                        <i class="fa-solid fa-circle-nodes text-indigo-500"></i>
                        <span>Gérer les Cercles</span>
                    </router-link>
                    <router-link to="/admin/categories" class="btn btn-white btn-action">
                        <i class="fa-solid fa-tags text-amber-500"></i>
                        <span>Gérer les Catégories</span>
                    </router-link>
                    <router-link to="/admin/site-config" class="btn btn-white btn-action">
                        <i class="fa-solid fa-sliders text-teal-500"></i>
                        <span>Configuration du Site</span>
                    </router-link>
                    <router-link to="/admin/publication" class="btn btn-white btn-action">
                        <i class="fa-solid fa-globe text-purple-500"></i>
                        <span>Publication & API</span>
                    </router-link>
                    <router-link to="/admin/wiki" class="btn btn-white btn-action">
                        <i class="fa-solid fa-book text-emerald-500"></i>
                        <span>Gestion de l'Aide</span>
                    </router-link>
                  </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: INFO BLOCK -->
        <div class="col-side">
            <div class="premium-card">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-circle-info"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Informations</div>
                        <div class="pc-header-sub">Actualités de l'administration</div>
                    </div>
                </div>
                <div class="pc-body p-24 text-center text-muted">
                    <div class="mb-16">
                        <img :src="configStore.defaultLogoUrl" alt="DAZO" style="height: 48px; opacity: 0.1; filter: grayscale(1);" />
                    </div>
                    <p class="text-sm italic">"Cet espace est destiné à la gestion quotidienne de votre plateforme."</p>
                    <p class="mt-16 text-xs font-bold uppercase tracking-widest opacity-40">Prochainement : statistiques avancées et logs d'activité.</p>
                </div>
            </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useConfigStore } from '../../stores/config';

const configStore = useConfigStore();
const stats = ref({});
const loading = ref(true);

const fetchStats = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/admin/stats');
        stats.value = data.stats || data || {};
    } catch (e) {
        console.error("Admin Dashboard Stats error", e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchStats();
});
</script>

<style scoped>
.admin-pill { background: rgba(59, 130, 246, 0.1); padding: 8px 16px; border-radius: 50px; display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--blue-600); border: 1px solid rgba(59, 130, 246, 0.2); font-weight: 700; }

.hero-main-identity { display: flex; align-items: center; gap: 20px; }
.hero-custom-logo { height: 85px; width: auto; max-width: 180px; object-fit: contain; }

/* Stats Row */
.stats-row { display: flex; gap: 16px; flex-wrap: wrap; }
.stat-card {
  flex: 1; min-width: 220px; border-radius: 16px; padding: 24px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative; overflow: hidden; display: flex; align-items: center; cursor: pointer; border: 1px solid rgba(255,255,255,0.1);
}
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
.stat-card.v-total    { background: linear-gradient(135deg, #1e40af, #3b82f6); }
.stat-card.v-proposals { background: linear-gradient(135deg, #0d9488, #14b8a6); }
.stat-card.v-anime    { background: linear-gradient(135deg, #4338ca, #6366f1); }
.stat-card.v-adopted  { background: linear-gradient(135deg, #b45309, #f59e0b); }

.stat-icon-part { flex: 0 0 70px; }
.stat-icon-wrap { width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; border: 1px solid rgba(255,255,255,0.25); }

.stat-info-part { flex: 1; display: flex; flex-direction: column; align-items: flex-end; }
.stat-number-simple { font-size: 36px; font-weight: 800; color: white; line-height: 1; margin-bottom: 4px; }
.stat-label { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 0.1em; }

/* Grid Layout */
.grid-layout { display: flex; flex-direction: column; gap: 32px; }
@media (min-width: 1024px) {
  .grid-layout { flex-direction: row; align-items: flex-start; }
  .col-main { flex: 1.5; min-width: 0; }
  .col-side { flex: 1; min-width: 350px; }
}

/* Quick Actions */
.actions-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.btn-action { display: flex; align-items: center; gap: 16px; padding: 18px; justify-content: flex-start; text-align: left; }
.btn-action i { font-size: 20px; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--gray-50); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
.btn-action span { font-weight: 700; color: var(--gray-800); font-size: 14px; }
.btn-action:hover { border-color: var(--blue-400); transform: translateX(5px); }
</style>

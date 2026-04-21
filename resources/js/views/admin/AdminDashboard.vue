<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO WELCOME -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
             <div class="hero-title">Tableau de Bord Administrateur</div>
             <div class="hero-subtitle">Bienvenue dans votre centre de pilotage. Supervisez l'activité globale et les thématiques structurantes de DAZO.</div>
          </div>
          <div class="hero-action">
             <div class="flex gap-12">
               <div class="stat-pill">
                  <span class="pill-label">Système</span>
                  <span class="pill-value"><i class="fa-solid fa-check-circle text-teal"></i> OK</span>
               </div>
             </div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Initialisation du cockpit...</div>
      
      <div v-else>
        <!-- STATS GRID -->
        <div class="premium-grid">
          <div class="premium-card p-relative">
            <div class="pc-header pc-header-blue">
               <div class="pc-header-icon"><i class="fa-solid fa-users"></i></div>
               <div class="pc-header-content">
                  <div class="pc-header-title">Utilisateurs</div>
                  <div class="pc-header-sub">Inscrits sur la plateforme</div>
               </div>
            </div>
            <div class="card-body stat-body">
               <div class="stat-main-value">{{ stats.users_count || 0 }}</div>
               <div class="stat-footer">
                  <router-link to="/admin/users" class="btn btn-ghost btn-sm">Gérer les membres <i class="fa-solid fa-arrow-right"></i></router-link>
               </div>
            </div>
          </div>

          <div class="premium-card p-relative">
            <div class="pc-header pc-header-teal">
               <div class="pc-header-icon"><i class="fa-solid fa-handshake"></i></div>
               <div class="pc-header-content">
                  <div class="pc-header-title">Décisions</div>
                  <div class="pc-header-sub">Processus engagés</div>
               </div>
            </div>
            <div class="card-body stat-body">
               <div class="stat-main-value">{{ stats.decisions_count || 0 }}</div>
               <div class="stat-footer">
                  <router-link to="/admin/categories" class="btn btn-ghost btn-sm">Voir thématiques <i class="fa-solid fa-arrow-right"></i></router-link>
               </div>
            </div>
          </div>

          <div class="premium-card p-relative">
            <div class="pc-header pc-header-indigo">
               <div class="pc-header-icon"><i class="fa-solid fa-circle-nodes"></i></div>
               <div class="pc-header-content">
                  <div class="pc-header-title">Cercles</div>
                  <div class="pc-header-sub">Unités organisationnelles</div>
               </div>
            </div>
            <div class="card-body stat-body">
               <div class="stat-main-value">{{ stats.circles_count || 0 }}</div>
               <div class="stat-footer">
                  <router-link to="/admin/circles" class="btn btn-ghost btn-sm">Piloter les cercles <i class="fa-solid fa-arrow-right"></i></router-link>
               </div>
            </div>
          </div>

          <div class="premium-card p-relative">
            <div class="pc-header pc-header-amber">
               <div class="pc-header-icon"><i class="fa-solid fa-tags"></i></div>
               <div class="pc-header-content">
                  <div class="pc-header-title">Catégories</div>
                  <div class="pc-header-sub">Thèmes structurants</div>
               </div>
            </div>
            <div class="card-body stat-body">
               <div class="stat-main-value">{{ stats.categories_count || 0 }}</div>
               <div class="stat-footer">
                  <span class="text-xs text-muted">Organisation transverse</span>
               </div>
            </div>
          </div>
        </div>

        <!-- QUICK ACCESS / SYSTEM INFO -->
        <div class="mt-24 grid-2">
           <div class="premium-card">
              <div class="card-header card-header-sexy">
                 <span class="card-title">Actions Rapides</span>
              </div>
              <div class="card-body flex-col gap-12">
                 <router-link to="/admin/users" class="quick-link">
                    <i class="fa-solid fa-user-plus"></i>
                    <div>
                       <div class="title">Gestion des membres</div>
                       <div class="desc">Vérifier et modérer les utilisateurs</div>
                    </div>
                 </router-link>
                 <router-link to="/admin/config" class="quick-link">
                    <i class="fa-solid fa-gears"></i>
                    <div>
                       <div class="title">Configuration</div>
                       <div class="desc">Ajuster les paramètres globaux</div>
                    </div>
                 </router-link>
              </div>
           </div>

           <div class="premium-card">
              <div class="card-header card-header-sexy">
                 <span class="card-title">État du Système</span>
              </div>
              <div class="card-body">
                 <div class="sys-info">
                    <div class="sys-row"><span>Version</span> <span class="badge badge-blue">v1.2.0-beta</span></div>
                    <div class="sys-row"><span>Storage</span> <span class="badge badge-teal">92% Libre</span></div>
                    <div class="sys-row"><span>Latence API</span> <span class="badge badge-teal">&lt; 45ms</span></div>
                 </div>
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

const stats = ref({});
const loading = ref(true);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/v1/admin/stats');
    stats.value = data.stats || data || {};
  } catch (e) {
    // Stat samples placeholders
    stats.value = {
      users_count: 0,
      decisions_count: 0,
      circles_count: 0,
      categories_count: 0
    };
  } finally { loading.value = false; }
});
</script>

<style scoped>
.py-24 { padding: 48px 0; }
.mt-24 { margin-top: 24px; }

.premium-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; }

.stat-body { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 20px; }
.stat-main-value { font-size: 42px; font-weight: 800; color: var(--blue-900); letter-spacing: -0.05em; line-height: 1; }
.stat-footer { margin-top: 16px; border-top: 1px solid var(--gray-50); width: 100%; text-align: center; padding-top: 12px; }

.stat-pill { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: var(--radius-full); padding: 6px 16px; display: flex; gap: 8px; font-size: 13px; align-items: center; backdrop-filter: blur(4px); }
.pill-label { opacity: 0.7; }
.pill-value { font-weight: 700; }
.text-teal { color: var(--teal-500); }

.quick-link { display: flex; gap: 16px; align-items: center; padding: 14px; border-radius: var(--radius-lg); border: 1px solid var(--gray-100); text-decoration: none; color: inherit; transition: all 0.2s; background: var(--gray-50); }
.quick-link:hover { border-color: var(--blue-400); transform: translateX(4px); background: white; box-shadow: var(--shadow-sm); }
.quick-link i { font-size: 20px; color: var(--blue-600); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: white; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); }
.quick-link .title { font-size: 14px; font-weight: 700; color: var(--gray-900); }
.quick-link .desc { font-size: 12px; color: var(--gray-500); }

.sys-info { display: flex; flex-direction: column; gap: 12px; }
.sys-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--gray-50); border-radius: var(--radius-md); font-size: 13px; }
</style>

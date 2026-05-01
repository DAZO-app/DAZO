<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO WELCOME -->
      <div class="hero-card">
        <div class="hero-flex">
          <div class="hero-main-identity">
            <img v-if="configStore.hasCustomLogo" :src="configStore.customLogoUrl" alt="Logo" class="hero-custom-logo" />
            <div>
               <div class="hero-title">Cockpit Administrateur</div>
               <div class="hero-subtitle">{{ configStore.appName !== 'DAZO' ? configStore.appName : 'Supervision plateforme' }} - Pilotage système DAZO.</div>
            </div>
          </div>
          <div class="hero-action">
             <div class="sys-pill" :class="loading ? 'loading' : 'online'">
                <i class="fa-solid fa-circle-check"></i>
                <span>Système {{ loading ? '...' : 'Opérationnel' }}</span>
             </div>
          </div>
        </div>
      </div>

      <!-- TOP PREMIUM CARDS (Like Main Dashboard) -->
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
        <!-- LEFT COLUMN: ACTIONS & SYSTEM -->
        <div class="col-main">
            <!-- QUICK ACTIONS -->
            <div class="premium-card mb-32">
                <div class="pc-header pc-header-indigo">
                    <div class="pc-header-icon"><i class="fa-solid fa-bolt"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Actions Rapides</div>
                        <div class="pc-header-sub">Accès directs aux outils de gestion</div>
                    </div>
                </div>
                <div class="pc-body p-24">
                  <div class="actions-buttons">
                    <router-link to="/admin/circles" class="btn btn-white btn-action">
                        <i class="fa-solid fa-circle-nodes text-indigo-500"></i>
                        <span>Gestion des Cercles</span>
                    </router-link>
                    <router-link to="/admin/categories" class="btn btn-white btn-action">
                        <i class="fa-solid fa-tags text-amber-500"></i>
                        <span>Gestion des Catégories</span>
                    </router-link>
                    <router-link to="/admin/database" class="btn btn-white btn-action">
                        <i class="fa-solid fa-database text-blue-500"></i>
                        <span>Base de Données</span>
                    </router-link>
                    <router-link to="/admin/server" class="btn btn-white btn-action">
                        <i class="fa-solid fa-server text-teal-500"></i>
                        <span>Monitoring Serveur</span>
                    </router-link>
                    <router-link to="/admin/config" class="btn btn-white btn-action">
                        <i class="fa-solid fa-gears text-purple-500"></i>
                        <span>Configuration</span>
                    </router-link>
                  </div>
                </div>
            </div>

            <!-- SYSTEM STATE -->
            <div class="premium-card">
              <div class="pc-header pc-header-blue">
                <div class="pc-header-icon"><i class="fa-solid fa-microchip"></i></div>
                <div class="pc-header-content">
                  <div class="pc-header-title">État du Système</div>
                  <div class="pc-header-sub">Infrastructure et environnement</div>
                </div>
              </div>
              <div class="pc-body p-24">
                 <div class="sys-info">
                    <div class="sys-row">
                      <div class="flex items-center gap-12">
                        <i class="fa-brands fa-laravel text-red-500"></i>
                        <span>Laravel Framework</span>
                      </div>
                      <span class="badge badge-gray">v11.x</span>
                    </div>
                    <div class="sys-row">
                      <div class="flex items-center gap-12">
                        <i class="fa-brands fa-php text-blue-600"></i>
                        <span>Version PHP</span>
                      </div>
                      <span class="badge badge-blue">8.3.x</span>
                    </div>
                    <div class="sys-row">
                      <div class="flex items-center gap-12">
                        <i class="fa-solid fa-clock text-teal-500"></i>
                        <span>Statut API</span>
                      </div>
                      <span class="badge badge-teal">En Ligne</span>
                    </div>
                 </div>
              </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: BACKUPS & LOGS -->
        <div class="col-side">
            <!-- LATEST BACKUPS -->
            <div class="premium-card mb-32">
                <div class="pc-header pc-header-teal">
                    <div class="pc-header-icon"><i class="fa-solid fa-database"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Derniers Backups</div>
                        <div class="pc-header-sub">5 dernières sauvegardes SQL</div>
                    </div>
                </div>
                <div class="pc-body">
                    <div class="list-container">
                        <div v-for="b in backups.slice(0, 5)" :key="b.name" class="list-item">
                            <div class="flex items-center gap-12 flex-1">
                                <i class="fa-solid fa-file-zipper text-teal-500"></i>
                                <div class="truncate">
                                    <div class="text-xs font-bold">{{ b.name }}</div>
                                    <div class="text-10 text-muted">{{ b.date }} ({{ b.size }})</div>
                                </div>
                            </div>
                        </div>
                        <div v-if="backups.length === 0" class="p-16 text-center text-xs text-muted">Aucun backup.</div>
                        <div class="p-12 border-t text-center">
                            <router-link to="/admin/database" class="text-xs text-blue-600 font-bold hover:underline">Voir tout <i class="fa-solid fa-chevron-right ml-4"></i></router-link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOGS VIEW -->
            <div class="premium-card bg-gray-900" style="border: none;">
                <div class="pc-header" style="background: rgba(255,255,255,0.05);">
                    <div class="pc-header-icon" style="background: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.3);">
                      <i class="fa-solid fa-terminal text-blue-400"></i>
                    </div>
                    <div class="pc-header-content">
                        <div class="pc-header-title text-white">Log Laravel</div>
                        <div class="pc-header-sub text-gray-400">Derniers événements</div>
                    </div>
                </div>
                <div class="pc-body">
                    <div class="terminal-mini">
                        <div v-for="(log, i) in logs.slice(0, 15)" :key="i" class="log-line-mini">
                            <span :class="['log-level-mini', log.level]">{{ log.level[0] }}</span>
                            <span class="log-msg-mini">{{ log.msg }}</span>
                        </div>
                    </div>
                    <div class="p-12 border-t border-gray-800 text-center">
                        <router-link to="/admin/server" class="text-xs text-gray-400 font-bold hover:text-white">Détails <i class="fa-solid fa-chevron-right ml-4"></i></router-link>
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
import { useConfigStore } from '../../stores/config';

const configStore = useConfigStore();

const stats = ref({});
const backups = ref([]);
const logs = ref([]);
const loading = ref(true);

const fetchAll = async () => {
    loading.value = true;
    try {
        const [statsRes, dbRes, logsRes] = await Promise.all([
            axios.get('/api/v1/admin/stats'),
            axios.get('/api/v1/admin/tools/database'),
            axios.get('/api/v1/admin/tools/logs')
        ]);
        stats.value = statsRes.data.stats || statsRes.data || {};
        backups.value = dbRes.data.backups || [];
        logs.value = logsRes.data || [];
    } catch (e) {
        console.error("Dashboard error", e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    configStore.fetchConfig();
    fetchAll();
});
</script>

<style scoped>
.sys-pill { background: rgba(255,255,255,0.1); padding: 8px 16px; border-radius: 50px; display: flex; align-items: center; gap: 10px; font-size: 13px; color: white; border: 1px solid rgba(255,255,255,0.15); }
.sys-pill.online i { color: #10b981; }
.sys-pill.loading i { color: #fbbf24; }

.hero-main-identity {
  display: flex;
  align-items: center;
  gap: 20px;
}

.hero-custom-logo {
  height: 85px;
  width: auto;
  max-width: 180px;
  object-fit: contain;
}

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

/* System Info */
.sys-info { display: flex; flex-direction: column; gap: 12px; }
.sys-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: var(--gray-50); border-radius: 12px; font-size: 13px; font-weight: 600; color: var(--gray-700); }

/* Lists */
.list-container { display: flex; flex-direction: column; }
.list-item { display: flex; align-items: center; padding: 14px 20px; border-bottom: 1px solid var(--gray-100); }
.list-item:hover { background: var(--gray-50); }
.truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Terminal Mini */
.terminal-mini { background: #0f172a; padding: 16px; font-family: var(--font-mono); height: 200px; overflow-y: auto; }
.log-line-mini { display: flex; gap: 8px; margin-bottom: 4px; font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; }
.log-level-mini { font-weight: 800; padding: 0 4px; border-radius: 2px; }
.log-level-mini.error { color: #f43f5e; background: rgba(244, 63, 94, 0.1); }
.log-level-mini.info { color: #38bdf8; }
.log-level-mini.warning { color: #fbbf24; }
.log-msg-mini { color: #cbd5e1; }

.text-10 { font-size: 10px; }
.btn-white { background: white; color: var(--gray-800); border: 1px solid var(--gray-200); }
</style>

<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Monitoring Serveur</div>
            <div class="hero-subtitle">Surveillez les ressources système, les composants PHP et l'intégrité des services.</div>
          </div>
          <div class="hero-action">
            <div class="uptime-pill">
                <i class="fa-solid fa-clock mr-8 opacity-60"></i>
                <span class="font-bold">Uptime: {{ uptime }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- RESOURCE GAUGES -->
      <div class="stats-grid mt-32 mb-32">
        <div class="stat-card" v-for="g in gauges" :key="g.label">
            <div class="gauge-header">
                <span class="stat-label">{{ g.label }}</span>
                <span class="stat-value" :class="g.statusClass">{{ g.value }}%</span>
            </div>
            <div class="gauge-bar-bg">
                <div class="gauge-bar-fill" :style="{ width: g.value + '%' }" :class="g.statusClass"></div>
            </div>
            <div class="stat-footer">{{ g.detail }}</div>
        </div>
      </div>

      <div class="grid-layout">
        <!-- LEFT COLUMN: HEALTH & LOGS -->
        <div class="col-main">
            <!-- SERVICES HEALTH CHECK -->
            <div class="premium-card mb-32">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-heart-pulse"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">État des Services</div>
                        <div class="pc-header-sub">Vérification en temps réel des composants</div>
                    </div>
                    <div class="ml-auto" style="position: relative; z-index: 2;">
                        <button class="btn btn-white btn-sm" @click="runDiagnostic" :disabled="loading">
                            <i class="fa-solid fa-stethoscope mr-4"></i> Diagnostiquer
                        </button>
                    </div>
                </div>
                <div class="pc-body">
                    <div class="health-checklist">
                        <div v-for="s in services" :key="s.name" class="health-item">
                            <i :class="['fa-solid', s.icon, 'health-icon', s.status]"></i>
                            <div class="health-info">
                                <div class="health-name">{{ s.name }}</div>
                                <div class="health-desc">{{ s.desc }}</div>
                            </div>
                            <div class="health-status">
                                <span :class="['badge-dot', s.status]"></span>
                                <span class="status-text">{{ s.status === 'ok' ? 'Opérationnel' : 'Erreur' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOGS VIEW -->
            <div class="premium-card bg-gray-900" style="border: none;">
                <div class="pc-header" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                    <div class="pc-header-icon" style="background: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.3);">
                      <i class="fa-solid fa-terminal text-blue-400"></i>
                    </div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Console Logs</div>
                        <div class="pc-header-sub text-gray-400">Sortie temps réel de laravel.log</div>
                    </div>
                    <div class="ml-auto flex gap-8" style="position: relative; z-index: 2;">
                        <button class="btn btn-ghost btn-sm text-gray-400 hover:text-white"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
                <div class="pc-body">
                    <div class="terminal-view">
                        <div v-for="(log, i) in recentLogs" :key="i" class="log-line">
                            <span class="log-time">[{{ log.time }}]</span>
                            <span :class="['log-level', log.level]">{{ log.level }}:</span>
                            <span class="log-msg">{{ log.msg }}</span>
                        </div>
                        <div v-if="recentLogs.length === 0" class="text-center py-24 text-gray-600">Aucun log récent.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SIDEBAR -->
        <div class="col-side">
            <!-- PHP CONFIG -->
            <div class="premium-card mb-32">
                <div class="pc-header pc-header-indigo">
                    <div class="pc-header-icon"><i class="fa-brands fa-php"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Config PHP</div>
                        <div class="pc-header-sub">Version {{ phpConfig.version }}</div>
                    </div>
                </div>
                <div class="pc-body p-24">
                    <div class="config-list">
                        <div class="config-item">
                            <span>Mémoire Max</span>
                            <span class="badge badge-blue">{{ phpConfig.memory_limit }}</span>
                        </div>
                        <div class="config-item">
                            <span>Upload Max</span>
                            <span class="badge badge-blue">{{ phpConfig.upload_max }}</span>
                        </div>
                        <div class="config-item">
                            <span>Post Max</span>
                            <span class="badge badge-blue">{{ phpConfig.post_max }}</span>
                        </div>
                        <div class="config-item">
                            <span>Timeout</span>
                            <span class="badge badge-gray">{{ phpConfig.max_execution }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOLDERS INTEGRITY -->
            <div class="premium-card">
                <div class="pc-header pc-header-amber">
                    <div class="pc-header-icon"><i class="fa-solid fa-folder-tree"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Dossiers</div>
                        <div class="pc-header-sub">Permissions & Intégrité</div>
                    </div>
                </div>
                <div class="pc-body">
                    <div class="health-checklist">
                        <div v-for="f in folders" :key="f.path" class="health-item py-12">
                            <div class="health-icon-small"><i class="fa-solid fa-folder text-amber-400"></i></div>
                            <div class="health-info">
                                <div class="health-name" style="font-size: 11px; font-weight: 600;">{{ f.path }}</div>
                                <div class="font-mono text-xs text-muted">{{ f.perm }} - OK</div>
                            </div>
                            <i class="fa-solid fa-circle-check text-teal-500"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const gauges = ref([]);
const services = ref([]);
const phpConfig = ref({});
const recentLogs = ref([]);
const uptime = ref('...');
const loading = ref(true);

const fetchData = async () => {
    try {
        const { data } = await axios.get('/api/v1/admin/tools/server');
        gauges.value = data.gauges;
        services.value = data.services;
        phpConfig.value = data.php_config;
        uptime.value = data.uptime;
    } catch (e) {
        console.error("Erreur stats serveur", e);
    }
};

const fetchLogs = async () => {
    try {
        const { data } = await axios.get('/api/v1/admin/tools/logs');
        recentLogs.value = data;
    } catch (e) {
        console.error("Erreur logs", e);
    }
};

const runDiagnostic = async () => {
    loading.value = true;
    await Promise.all([fetchData(), fetchLogs()]);
    loading.value = false;
};

let interval = null;
onMounted(() => {
    runDiagnostic();
    interval = setInterval(runDiagnostic, 10000); // Refresh every 10s
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});

const folders = ref([
    { path: '/storage/app', perm: '775', status: 'ok' },
    { path: '/storage/logs', perm: '775', status: 'ok' },
    { path: '/storage/framework', perm: '775', status: 'ok' },
    { path: '/bootstrap/cache', perm: '775', status: 'ok' },
]);
</script>

<style scoped>
.grid-layout { display: flex; flex-direction: column; gap: 24px; }
@media (min-width: 1024px) {
  .grid-layout { flex-direction: row; align-items: flex-start; }
  .col-main { flex: 2; min-width: 0; }
  .col-side { flex: 1; min-width: 320px; }
}

.uptime-pill { background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 50px; font-size: 13px; color: white; display: flex; align-items: center; border: 1px solid rgba(255,255,255,0.1); }

/* GAUGES */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
.stat-card { background: white; border-radius: var(--radius-xl); padding: 24px; box-shadow: var(--shadow-md); border: 1px solid var(--gray-100); }
.gauge-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 12px; }
.stat-label { font-size: 13px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; }
.stat-value { font-size: 28px; font-weight: 800; }
.stat-value.ok { color: var(--teal-600); }
.stat-value.warn { color: #f59e0b; }
.stat-value.err { color: #ef4444; }

.gauge-bar-bg { height: 10px; background: var(--gray-100); border-radius: 5px; overflow: hidden; margin-bottom: 12px; }
.gauge-bar-fill { height: 100%; border-radius: 5px; transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
.gauge-bar-fill.ok { background: linear-gradient(90deg, var(--teal-400), var(--teal-600)); }
.gauge-bar-fill.warn { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.gauge-bar-fill.err { background: linear-gradient(90deg, #f87171, #ef4444); }
.stat-footer { font-size: 11px; color: var(--gray-400); font-weight: 500; }

/* HEALTH LIST */
.health-checklist { display: flex; flex-direction: column; }
.health-item { display: flex; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--gray-100); }
.health-item:last-child { border-bottom: none; }
.health-icon { font-size: 18px; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 16px; }
.health-icon.ok { background: var(--teal-50); color: var(--teal-600); }
.health-icon.err { background: var(--red-50); color: var(--red-600); }
.health-icon-small { font-size: 16px; width: 24px; text-align: center; margin-right: 12px; }
.health-info { flex: 1; min-width: 0; }
.health-name { font-size: 14px; font-weight: 700; color: var(--gray-800); }
.health-desc { font-size: 11px; color: var(--gray-500); }
.health-status { display: flex; align-items: center; gap: 8px; }
.badge-dot { width: 8px; height: 8px; border-radius: 50%; }
.badge-dot.ok { background: var(--teal-500); box-shadow: 0 0 8px var(--teal-500); }
.badge-dot.err { background: var(--red-500); box-shadow: 0 0 8px var(--red-500); }
.status-text { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-400); }

/* CONFIG */
.config-list { display: flex; flex-direction: column; gap: 14px; }
.config-item { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--gray-700); font-weight: 500; }

/* TERMINAL */
.terminal-view { background: #0f172a; padding: 16px; font-family: var(--font-mono); font-size: 12px; height: 380px; overflow-y: auto; }
.log-line { margin-bottom: 6px; white-space: pre-wrap; line-height: 1.5; color: #94a3b8; border-left: 2px solid transparent; padding-left: 8px; }
.log-time { color: #475569; margin-right: 8px; font-size: 10px; }
.log-level { margin-right: 8px; text-transform: uppercase; font-weight: 800; font-size: 10px; px: 4px; border-radius: 2px; }
.log-level.info { color: #38bdf8; }
.log-level.error { color: #f43f5e; background: rgba(244, 63, 94, 0.1); }
.log-level.warning { color: #fbbf24; }
.log-msg { color: #e2e8f0; }

.font-mono { font-family: var(--font-mono); }
.btn-white { background: white; color: var(--gray-800); }
</style>

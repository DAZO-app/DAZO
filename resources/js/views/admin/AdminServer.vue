<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card shadow-lg">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Monitoring Serveur</div>
            <div class="hero-subtitle">Surveillez les ressources système, les composants PHP et l'intégrité des services.</div>
          </div>
          <div class="hero-action">
            <div class="uptime-pill">
                <i class="fa-solid fa-clock mr-8 opacity-60"></i>
                <span class="font-bold">Uptime: 45j 12h 05m</span>
            </div>
          </div>
        </div>
      </div>

      <!-- RESOURCE GAUGES -->
      <div class="stats-grid mb-32">
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

      <div class="admin-grid">
        <!-- LEFT COLUMN: MAIN (HEALTH & LOGS) -->
        <div class="admin-col">
            <!-- SERVICES HEALTH CHECK -->
            <div class="card mb-32">
                <div class="card-header card-header-sexy">
                    <span class="card-title"><i class="fa-solid fa-heart-pulse mr-8"></i> État des Composants & Services</span>
                    <button class="btn btn-ghost btn-sm ml-auto" @click="runDiagnostic">
                        <i class="fa-solid fa-stethoscope mr-4"></i> Lancer un diagnostic
                    </button>
                </div>
                <div class="card-body p-0">
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

            <!-- LOGS VIEW (WIDER) -->
            <div class="card bg-gray-900 border-none shadow-xl overflow-hidden">
                 <div class="card-header border-none" style="background: rgba(255,255,255,0.05)">
                    <span class="card-title text-white"><i class="fa-solid fa-terminal mr-8 text-blue-400"></i> Console Logs</span>
                    <div class="ml-auto flex gap-8">
                        <button class="btn btn-ghost btn-sm text-white opacity-60"><i class="fa-solid fa-trash-can mr-4"></i> Vider</button>
                        <button class="btn btn-ghost btn-sm text-white"><i class="fa-solid fa-external-link"></i></button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="terminal-view">
                        <div v-for="(log, i) in recentLogs" :key="i" class="log-line">
                            <span class="log-time">[{{ log.time }}]</span>
                            <span :class="['log-level', log.level]">{{ log.level }}:</span>
                            <span class="log-msg">{{ log.msg }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SIDEBAR (PHP & FOLDERS) -->
        <div class="admin-col">
            <!-- PHP VERSION & CONFIG -->
            <div class="card mb-32">
                <div class="card-header card-header-sexy">
                    <span class="card-title"><i class="fa-brands fa-php mr-8"></i> Configuration PHP</span>
                </div>
                <div class="card-body">
                    <div class="php-version-box mb-16">
                        <span class="text-xs text-muted mb-4 block">Version Actuelle</span>
                        <div class="text-24 font-bold text-blue-700">8.3.4 (CLI/FPM)</div>
                    </div>
                    <div class="config-list">
                        <div class="config-item">
                            <span>Memory Limit</span>
                            <span class="badge badge-gray">512M</span>
                        </div>
                        <div class="config-item">
                            <span>Upload Max Filesize</span>
                            <span class="badge badge-gray">64M</span>
                        </div>
                        <div class="config-item">
                            <span>Post Max Size</span>
                            <span class="badge badge-gray">64M</span>
                        </div>
                        <div class="config-item">
                            <span>Max Execution Time</span>
                            <span class="badge badge-gray">60s</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOLDERS INTEGRITY -->
            <div class="card">
                <div class="card-header card-header-sexy">
                    <span class="card-title"><i class="fa-solid fa-folder-tree mr-8"></i> Intégrité Dossiers</span>
                </div>
                <div class="card-body p-0">
                    <div class="health-checklist">
                        <div v-for="f in folders" :key="f.path" class="health-item py-12">
                            <div class="health-icon-small"><i class="fa-solid fa-folder text-amber-400"></i></div>
                            <div class="health-info">
                                <div class="health-name text-xs">{{ f.path }}</div>
                                <div class="health-desc font-mono">{{ f.perm }}</div>
                            </div>
                            <i class="fa-solid fa-circle-check text-teal-500" v-if="f.status === 'ok'"></i>
                            <i class="fa-solid fa-circle-exclamation text-red-500" v-else></i>
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
import { ref } from 'vue';

const gauges = ref([
    { label: 'Utilisation CPU', value: 12, detail: '8 Cores Intel(R) Xeon(R)', statusClass: 'ok' },
    { label: 'Mémoire RAM', value: 45, detail: '3.6 GB / 8.0 GB', statusClass: 'ok' },
    { label: 'Espace Disque', value: 84, detail: '210 GB / 250 GB', statusClass: 'warn' },
    { label: 'Bande Passante', value: 5, detail: '100 Mbps (In/Out)', statusClass: 'ok' },
]);

const services = ref([
    { name: 'Moteur PHP', desc: 'SAPI fpm-fcgi v8.3.4', icon: 'fa-microchip', status: 'ok' },
    { name: 'Redis Cache', desc: 'Gestionnaire de session & cache v7.2', icon: 'fa-bolt', status: 'ok' },
    { name: 'Supervisor', desc: 'Gestionnaire de files d\'attente Laravel', icon: 'fa-gears', status: 'ok' },
    { name: 'Serveur de Mail', desc: 'Mailpit (Local Testing Environment)', icon: 'fa-envelope', status: 'ok' },
    { name: 'Extension GD', desc: 'Traitement d\'images et thumbnails', icon: 'fa-image', status: 'ok' },
    { name: 'Extension Intl', desc: 'Internationalisation et monétaire', icon: 'fa-language', status: 'err' },
]);

const folders = ref([
    { path: '/storage/app', perm: '775', status: 'ok' },
    { path: '/storage/logs', perm: '775', status: 'ok' },
    { path: '/storage/framework', perm: '775', status: 'ok' },
    { path: '/bootstrap/cache', perm: '775', status: 'ok' },
]);

const recentLogs = ref([
    { time: '16:04:12', level: 'info', msg: 'Decision service: auto-transition check started.' },
    { time: '15:58:05', level: 'error', msg: 'Failed to connect to SMTP server at 127.0.0.1:25' },
    { time: '15:45:22', level: 'warning', msg: 'User context missing for invitation ACCEPT bridge.' },
    { time: '15:30:10', level: 'info', msg: 'Migration successful for table: invitations.' },
    { time: '15:24:55', level: 'info', msg: 'Application config cached via ConfigService.' },
    { time: '15:20:12', level: 'info', msg: 'Started backup job scheduled at 15:20:00.' },
    { time: '15:18:40', level: 'info', msg: 'New member bridge verification complete.' },
]);

const runDiagnostic = () => {
    alert('Analyse des composants en cours...');
};
</script>

<style scoped>
.uptime-pill { background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 50px; font-size: 13px; color: white; display: flex; align-items: center; border: 1px solid rgba(255,255,255,0.1); }

/* GAUGES */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
.stat-card { background: white; border-radius: var(--radius-lg); padding: 20px; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100); }
.gauge-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 12px; }
.stat-label { font-size: 13px; font-weight: 600; color: var(--gray-500); }
.stat-value { font-size: 24px; font-weight: 800; }
.stat-value.ok { color: var(--teal-600); }
.stat-value.warn { color: #f59e0b; }
.stat-value.err { color: #ef4444; }

.gauge-bar-bg { height: 8px; background: var(--gray-100); border-radius: 4px; overflow: hidden; margin-bottom: 8px; }
.gauge-bar-fill { height: 100%; border-radius: 4px; transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
.gauge-bar-fill.ok { background: var(--teal-500); }
.gauge-bar-fill.warn { background: #f59e0b; }
.gauge-bar-fill.err { background: #ef4444; }
.stat-footer { font-size: 11px; color: var(--gray-400); }

/* LAYOUT */
.admin-grid { display: grid; grid-template-columns: 1fr 350px; gap: 32px; align-items: start; }

/* HEALTH LIST */
.health-checklist { display: flex; flex-direction: column; }
.health-item { display: flex; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--gray-100); }
.health-item:last-child { border-bottom: none; }
.health-icon { font-size: 20px; width: 32px; text-align: center; margin-right: 16px; }
.health-icon.ok { color: var(--teal-500); }
.health-icon.err { color: var(--red-400); }
.health-icon-small { font-size: 16px; width: 24px; text-align: center; margin-right: 12px; }
.health-info { flex: 1; min-width: 0; }
.health-name { font-size: 14px; font-weight: 700; color: var(--gray-800); }
.health-desc { font-size: 12px; color: var(--gray-500); }
.health-status { display: flex; align-items: center; gap: 8px; }
.badge-dot { width: 8px; height: 8px; border-radius: 50%; }
.badge-dot.ok { background: var(--teal-500); box-shadow: 0 0 8px var(--teal-500); }
.badge-dot.err { background: var(--red-500); box-shadow: 0 0 8px var(--red-500); }
.status-text { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-400); }

/* PHP CONFIG */
.config-list { display: flex; flex-direction: column; gap: 12px; }
.config-item { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--gray-600); }

/* TERMINAL */
.terminal-view { background: #0f172a; padding: 16px; font-family: var(--font-mono); font-size: 12px; border-radius: 0; height: 350px; overflow-y: auto; }
.log-line { margin-bottom: 6px; white-space: nowrap; line-height: 1.4; color: #94a3b8; }
.log-time { color: #475569; margin-right: 8px; }
.log-level { margin-right: 8px; text-transform: uppercase; font-weight: bold; }
.log-level.info { color: #38bdf8; }
.log-level.error { color: #f43f5e; }
.log-level.warning { color: #fbbf24; }
.log-msg { color: #e2e8f0; }

.font-mono { font-family: var(--font-mono); }
.mb-32 { margin-bottom: 32px; }
.py-12 { padding-top: 12px; padding-bottom: 12px; }

@media (max-width: 1100px) {
    .admin-grid { grid-template-columns: 1fr; }
}
</style>

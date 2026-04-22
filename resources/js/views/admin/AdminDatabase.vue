<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Base de Données</div>
            <div class="hero-subtitle">Visualisez l'état du moteur de stockage et gérez les optimisations.</div>
          </div>
          <div class="hero-action">
            <div class="db-status-pill">
                <span class="status-dot online"></span>
                <span class="font-semibold text-white">PostgreSQL 16.2</span>
                <span class="opacity-60 ml-8">Uptime: 14j 02h</span>
            </div>
          </div>
        </div>
      </div>

      <div class="admin-grid">
        <!-- LEFT COLUMN: TABLES & BACKUP HISTORY (NOW PRIMARY) -->
        <div class="admin-col">
            <!-- TABLES STATE -->
            <div class="card mb-32">
                <div class="card-header card-header-sexy">
                   <div class="flex items-center gap-16">
                       <span class="card-title"><i class="fa-solid fa-table mr-8"></i> État des Tables</span>
                       <span class="badge badge-blue">7.2 MB Total</span>
                   </div>
                   <div class="ml-auto flex gap-8">
                       <button class="btn btn-ghost btn-sm" @click="refreshTables"><i class="fa-solid fa-sync"></i></button>
                       <button class="btn btn-secondary btn-sm"><i class="fa-solid fa-wand-magic-sparkles mr-4"></i> Optimisation</button>
                   </div>
                </div>
                <div class="card-body p-0">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Table</th>
                                <th>Entrées</th>
                                <th>Taille Données</th>
                                <th>Taille Index</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="t in tables" :key="t.name">
                                <td class="font-bold text-gray-800">{{ t.name }}</td>
                                <td>{{ t.rows.toLocaleString() }}</td>
                                <td>{{ t.data_size }}</td>
                                <td>{{ t.index_size }}</td>
                                <td class="text-right">
                                    <button class="btn btn-ghost btn-xs mr-4" title="Réparer"><i class="fa-solid fa-wrench"></i></button>
                                    <button class="btn btn-ghost btn-xs" title="Optimiser"><i class="fa-solid fa-broom"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BACKUP HISTORY -->
            <div class="card">
                <div class="card-header card-header-sexy bg-teal-50 border-teal-100">
                    <span class="card-title text-teal-800"><i class="fa-solid fa-clock-rotate-left mr-8"></i> Historique des Sauvegardes (.sql.gz)</span>
                </div>
                <div class="card-body p-0">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Nom du fichier</th>
                                <th>Taille</th>
                                <th>Date de création</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="b in backups" :key="b.id">
                                <td>
                                    <div class="flex items-center gap-12">
                                        <i class="fa-solid fa-file-zipper text-blue-400"></i>
                                        <span class="font-mono text-xs">{{ b.name }}</span>
                                    </div>
                                </td>
                                <td><span class="badge badge-gray">{{ b.size }}</span></td>
                                <td class="text-xs text-muted">{{ b.date }}</td>
                                <td class="text-right">
                                    <button class="btn btn-ghost btn-xs mr-4" title="Télécharger"><i class="fa-solid fa-download"></i></button>
                                    <button class="btn btn-ghost btn-xs text-red" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    <button class="btn btn-secondary btn-xs ml-8" title="Restaurer"><i class="fa-solid fa-undo mr-4"></i> Restaurer</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SETTINGS (SIDEBAR) -->
        <div class="admin-col">
            <!-- QUICK ACTIONS -->
            <div class="card mb-32">
                <div class="card-header card-header-sexy">
                    <span class="card-title"><i class="fa-solid fa-bolt mr-8"></i> Actions Rapides</span>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary w-full mb-16 py-12 flex justify-center text-center" @click="handleBackup">
                        <i class="fa-solid fa-download mr-8"></i> Backup immédiat
                    </button>
                    <div class="import-zone">
                        <label class="import-box border-dashed">
                            <i class="fa-solid fa-cloud-arrow-up mb-4"></i>
                            <span class="text-xs font-semibold">Importer un dump</span>
                            <input type="file" class="hidden">
                        </label>
                    </div>
                </div>
            </div>

            <!-- ROLLING BACKUP SETTINGS -->
            <div class="card mb-32">
                <div class="card-header card-header-sexy bg-amber-50 border-amber-100">
                    <span class="card-title text-amber-800"><i class="fa-solid fa-clock-rotate-left mr-8"></i> Sauvegardes Automatiques</span>
                </div>
                <div class="card-body">
                    <div class="flex items-center justify-between mb-16">
                        <span class="text-sm font-semibold text-gray-700">Activer le backup glissant</span>
                        <label class="switch">
                            <input type="checkbox" v-model="autoBackup.enabled">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    
                    <div :class="{ 'opacity-50 pointer-events-none': !autoBackup.enabled }">
                        <div class="form-group mb-16">
                            <label class="label">Heure d'exécution</label>
                            <input type="time" v-model="autoBackup.time" class="input-sm w-full">
                        </div>
                        <div class="form-group mb-0">
                            <label class="label">Rétention (jours)</label>
                            <div class="flex items-center gap-12">
                                <input type="number" v-model="autoBackup.retention" min="1" max="90" class="input-sm w-24">
                                <span class="text-xs text-muted">{{ autoBackup.retention }}j</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONNECTION SETTINGS -->
            <div class="card">
                <div class="card-header card-header-sexy">
                    <span class="card-title"><i class="fa-solid fa-link mr-8"></i> Paramètres de connexion</span>
                    <button class="btn btn-ghost btn-sm ml-auto" @click="toggleEdit">
                        <i class="fa-solid" :class="isEditing ? 'fa-xmark' : 'fa-pen-to-square'"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group mb-16">
                        <label class="label">Serveur / Hôte</label>
                        <input type="text" value="127.0.0.1" :disabled="!isEditing" class="input-sm w-full">
                    </div>
                    <div class="form-group mb-16">
                        <label class="label">Port</label>
                        <input type="text" value="5432" :disabled="!isEditing" class="input-sm w-full">
                    </div>
                    <div class="form-group mb-16">
                        <label class="label">Base de données</label>
                        <input type="text" value="dazo_prod" :disabled="!isEditing" class="input-sm w-full">
                    </div>
                    <div class="form-group mb-16">
                        <label class="label">Utilisateur</label>
                        <input type="text" value="dazo_admin" :disabled="!isEditing" class="input-sm w-full">
                    </div>
                    <div class="form-group">
                        <label class="label">Mot de passe</label>
                        <input type="password" value="********" :disabled="!isEditing" class="input-sm w-full">
                    </div>
                    <div v-if="isEditing" class="mt-16 text-right">
                        <button class="btn btn-primary btn-sm">Enregistrer</button>
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

const isEditing = ref(false);
const toggleEdit = () => isEditing.value = !isEditing.value;

const autoBackup = ref({
    enabled: true,
    time: '04:00',
    retention: 30
});

const backups = ref([
    { id: 1, name: 'bkp_2024-04-21_04-00-00.sql.gz', size: '1.2 MB', date: 'Aujourd\'hui, 04:00:00' },
    { id: 2, name: 'bkp_2024-04-20_04-00-00.sql.gz', size: '1.1 MB', date: 'Hier, 04:00:02' },
    { id: 3, name: 'bkp_2024-04-19_04-00-00.sql.gz', size: '1.1 MB', date: '19/04/2024, 04:00:01' },
    { id: 4, name: 'bkp_2024-04-18_04-00-00.sql.gz', size: '1.0 MB', date: '18/04/2024, 04:00:03' },
    { id: 5, name: 'bkp_manual_2024-04-17_14-30.sql.gz', size: '1.1 MB', date: '17/04/2024, 14:30:15' },
]);

const tables = ref([
    { name: 'decisions', rows: 124, data_size: '840 KB', index_size: '128 KB' },
    { name: 'users', rows: 45, data_size: '48 KB', index_size: '32 KB' },
    { name: 'decision_versions', rows: 312, data_size: '4.2 MB', index_size: '512 KB' },
    { name: 'circles', rows: 12, data_size: '16 KB', index_size: '16 KB' },
    { name: 'thread_messages', rows: 1450, data_size: '1.8 MB', index_size: '256 KB' },
    { name: 'audit_logs', rows: 8400, data_size: '12.4 MB', index_size: '1.2 MB' },
]);

const handleBackup = () => {
    alert('Génération du backup en cours...');
};

const refreshTables = () => {
    // Logic to refresh table stats
};
</script>

<style scoped>
.admin-grid { display: grid; grid-template-columns: 1fr 350px; gap: 32px; align-items: start; }
.db-status-pill { background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 50px; display: flex; align-items: center; border: 1px solid rgba(255,255,255,0.1); }
.status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 10px; }
.status-dot.online { background: #10b981; box-shadow: 0 0 8px #10b981; }

.db-table { width: 100%; border-collapse: collapse; }
.db-table th { text-align: left; padding: 12px 20px; font-size: 11px; text-transform: uppercase; color: var(--gray-500); background: var(--gray-50); border-bottom: 1px solid var(--gray-100); }
.db-table td { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); font-size: 14px; }
.db-table tr:hover { background: var(--gray-50); }

.import-zone .import-box {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    border: 2px dashed var(--gray-200); border-radius: var(--radius-lg); padding: 16px;
    cursor: pointer; transition: all 0.2s; background: var(--gray-50);
}
.import-zone .import-box:hover { border-color: var(--blue-400); background: var(--blue-50); color: var(--blue-700); }

.w-24 { width: 60px; }

/* Switch Toggle */
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: .2s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .2s; }
input:checked + .slider { background-color: #f59e0b; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

.bg-amber-50 { background-color: var(--amber-50); }
.border-amber-100 { border-color: var(--amber-100); }
.text-amber-800 { color: var(--amber-800); }
.bg-teal-50 { background-color: var(--teal-50); }
.border-teal-100 { border-color: var(--teal-100); }
.text-teal-800 { color: var(--teal-800); }

.opacity-50 { opacity: 0.5; }
.pointer-events-none { pointer-events: none; }
.font-mono { font-family: var(--font-mono); }

.hidden { display: none; }
.mb-32 { margin-bottom: 32px; }

@media (max-width: 1200px) {
    .admin-grid { grid-template-columns: 1fr; }
}
</style>

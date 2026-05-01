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
                <span class="status-dot" :class="loading ? '' : 'online'"></span>
                <span class="font-semibold text-white">{{ dbInfo.engine }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="grid-layout mt-32">
        <!-- LEFT COLUMN: TABLES & BACKUP HISTORY -->
        <div class="col-main">
            <!-- TABLES STATE -->
            <div class="premium-card mb-32">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-table"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">État des Tables</div>
                        <div class="pc-header-sub">{{ dbInfo.total_size }} occupés au total</div>
                    </div>
                    <div class="ml-auto flex gap-8" style="position: relative; z-index: 2;">
                        <button class="btn btn-white btn-icon" @click="refreshTables" :disabled="loading">
                          <i class="fa-solid fa-sync" :class="{'fa-spin': loading}"></i>
                        </button>
                    </div>
                 </div>
                <div class="pc-body">
                    <div class="table-responsive">
                      <table class="db-table">
                          <thead>
                              <tr>
                                  <th>Table</th>
                                  <th>Entrées</th>
                                  <th>Données</th>
                                  <th>Index</th>
                                  <th class="text-right">Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr v-for="t in tables" :key="t.name">
                                  <td class="font-bold text-gray-800">{{ t.name }}</td>
                                  <td>{{ t.rows.toLocaleString() }}</td>
                                  <td><span class="badge badge-gray">{{ t.data_size }}</span></td>
                                  <td><span class="text-muted text-xs">{{ t.index_size }}</span></td>
                                  <td class="text-right">
                                      <button class="btn btn-ghost btn-xs mr-4" title="Optimiser"><i class="fa-solid fa-broom"></i></button>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                </div>
            </div>

            <!-- BACKUP HISTORY -->
            <div class="premium-card">
                <div class="pc-header pc-header-teal">
                    <div class="pc-header-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Historique des Sauvegardes</div>
                        <div class="pc-header-sub">Fichiers .sql.gz stockés sur le serveur</div>
                    </div>
                </div>
                <div class="pc-body">
                    <div class="table-responsive">
                      <table class="db-table">
                          <thead>
                              <tr>
                                  <th>Fichier</th>
                                  <th>Taille</th>
                                  <th>Date</th>
                                  <th class="text-right">Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr v-for="b in backups" :key="b.id">
                                  <td>
                                      <div class="flex items-center gap-12">
                                          <i class="fa-solid fa-file-zipper text-teal-500"></i>
                                          <span class="font-mono text-xs">{{ b.name }}</span>
                                      </div>
                                  </td>
                                  <td><span class="badge badge-teal">{{ b.size }}</span></td>
                                  <td class="text-xs text-muted">{{ b.date }}</td>
                                  <td class="text-right">
                                      <button class="btn btn-secondary btn-icon mr-4" @click="downloadBackup(b.name)" title="Télécharger">
                                        <i class="fa-solid fa-download"></i>
                                      </button>
                                      <button class="btn btn-secondary btn-icon mr-4 text-amber-600" @click="handleRestore(b.name)" title="Restaurer cette sauvegarde" :disabled="loading">
                                        <i class="fa-solid fa-rotate-left"></i>
                                      </button>
                                      <button class="btn btn-secondary btn-icon text-red-500" @click="confirmDeleteBackup(b.name)" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                      </button>
                                  </td>
                              </tr>
                              <tr v-if="backups.length === 0">
                                <td colspan="4" class="text-center py-24 text-muted">Aucune sauvegarde trouvée.</td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SETTINGS -->
        <div class="col-side">
            <!-- QUICK ACTIONS -->
            <div class="premium-card mb-32">
                <div class="pc-header pc-header-indigo">
                    <div class="pc-header-icon"><i class="fa-solid fa-bolt"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Actions Rapides</div>
                        <div class="pc-header-sub">Maintenance manuelle</div>
                    </div>
                </div>
                <div class="pc-body p-24">
                    <button class="btn btn-primary btn-block py-12 mb-16" @click="handleBackup" :disabled="loading">
                        <i class="fa-solid fa-floppy-disk mr-8"></i> Backup immédiat
                    </button>
                    <div class="import-zone">
                        <label class="import-box border-dashed">
                            <i class="fa-solid fa-cloud-arrow-up mb-4"></i>
                            <span class="text-xs font-semibold">Importer SQL</span>
                            <input type="file" class="hidden">
                        </label>
                    </div>
                </div>
            </div>

            <!-- AUTOMATIC BACKUP -->
            <div class="premium-card mb-32">
                <div class="pc-header pc-header-amber">
                    <div class="pc-header-icon"><i class="fa-solid fa-calendar-check"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Sauvegardes Auto</div>
                        <div class="pc-header-sub">Tâche planifiée (Cron)</div>
                    </div>
                </div>
                <div class="pc-body p-24">
                    <div class="flex items-center justify-between mb-16">
                        <span class="text-sm font-semibold text-gray-700">Activer</span>
                        <label class="switch">
                            <input type="checkbox" v-model="autoBackup.enabled">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    
                    <div :class="{ 'opacity-50 pointer-events-none': !autoBackup.enabled }">
                        <div class="form-group mb-16">
                            <label class="label">Heure d'exécution</label>
                            <input type="time" v-model="autoBackup.time" class="input w-full">
                        </div>
                        <div class="form-group">
                            <label class="label">Rétention</label>
                            <div class="flex items-center gap-12">
                                <input type="number" v-model="autoBackup.retention" min="1" max="90" class="input" style="width: 80px;">
                                <span class="text-xs text-muted">jours</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONNECTION INFO -->
            <div class="premium-card">
                <div class="pc-header pc-header-purple">
                    <div class="pc-header-icon"><i class="fa-solid fa-link"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Connexion BDD</div>
                        <div class="pc-header-sub">Paramètres d'accès</div>
                    </div>
                </div>
                <div class="pc-body p-24">
                    <div class="form-group mb-12">
                        <label class="label">Hôte</label>
                        <div class="text-xs font-mono bg-gray-50 p-8 border-radius-sm">{{ dbInfo.connection.host || '...' }}</div>
                    </div>
                    <div class="form-group mb-12">
                        <label class="label">Port</label>
                        <div class="text-xs font-mono bg-gray-50 p-8 border-radius-sm">{{ dbInfo.connection.port || '...' }}</div>
                    </div>
                    <div class="form-group">
                        <label class="label">Nom Base</label>
                        <div class="text-xs font-mono bg-gray-50 p-8 border-radius-sm text-blue-600 font-bold">{{ dbInfo.connection.database || '...' }}</div>
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

const loading = ref(true);
const dbInfo = ref({
    engine: 'Chargement...',
    total_size: '0 B',
    connection: {}
});

const autoBackup = ref({
    enabled: true,
    time: '04:00',
    retention: 30
});

const backups = ref([]);
const tables = ref([]);

const fetchData = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/admin/tools/database');
        dbInfo.value = {
            engine: data.engine,
            total_size: data.total_size,
            connection: data.connection
        };
        tables.value = data.tables;
        backups.value = data.backups;
    } catch (e) {
        console.error("Erreur lors du chargement des stats BDD", e);
    } finally {
        loading.value = false;
    }
};

const handleBackup = async () => {
    if (!confirm('Lancer une sauvegarde manuelle maintenant ?')) return;
    loading.value = true;
    try {
        await axios.post('/api/v1/admin/tools/database/backup');
        alert('Sauvegarde terminée avec succès.');
        fetchData();
    } catch (e) {
        alert('Échec de la sauvegarde. Vérifiez les permissions pg_dump.');
    } finally {
      loading.value = false;
    }
};

const handleRestore = async (filename) => {
    if (!confirm(`ATTENTION: Vous allez écraser la base de données actuelle avec la sauvegarde "${filename}". Cette opération est irréversible. Continuer ?`)) return;
    loading.value = true;
    try {
        await axios.post('/api/v1/admin/tools/database/restore', { filename });
        alert('Restauration terminée avec succès. La page va se recharger.');
        window.location.reload();
    } catch (e) {
        alert(e.response?.data?.error || 'Échec de la restauration.');
    } finally {
        loading.value = false;
    }
};

const downloadBackup = async (filename) => {
  try {
    const { data } = await axios.get(`/api/v1/admin/tools/database/backups/${filename}/url`);
    window.location.href = data.url;
  } catch (e) {
    alert('Erreur lors de la génération du lien de téléchargement.');
  }
};

const confirmDeleteBackup = async (filename) => {
  if (!confirm('Voulez-vous vraiment supprimer définitivement cette sauvegarde ?')) return;
  try {
    await axios.delete(`/api/v1/admin/tools/database/backups/${filename}`);
    fetchData();
  } catch (e) {
    alert('Erreur lors de la suppression.');
  }
};

const refreshTables = () => fetchData();

onMounted(fetchData);
</script>

<style scoped>
.grid-layout { display: flex; flex-direction: column; gap: 24px; }
@media (min-width: 1024px) {
  .grid-layout { flex-direction: row; align-items: flex-start; }
  .col-main { flex: 2; min-width: 0; }
  .col-side { flex: 1; min-width: 320px; }
}

.db-status-pill { background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 50px; display: flex; align-items: center; border: 1px solid rgba(255,255,255,0.1); }
.status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 10px; }
.status-dot.online { background: #10b981; box-shadow: 0 0 8px #10b981; }

.table-responsive { overflow-x: auto; }
.db-table { width: 100%; border-collapse: collapse; }
.db-table th { text-align: left; padding: 12px 20px; font-size: 11px; text-transform: uppercase; color: var(--gray-500); background: var(--gray-50); border-bottom: 1px solid var(--gray-100); }
.db-table td { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
.db-table tr:hover { background: var(--gray-50); }

.import-zone .import-box {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    border: 2px dashed var(--gray-200); border-radius: var(--radius-lg); padding: 20px;
    cursor: pointer; transition: all 0.2s; background: var(--gray-50);
}
.import-zone .import-box:hover { border-color: var(--blue-400); background: var(--blue-50); color: var(--blue-700); }

/* Switch Toggle */
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: .2s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .2s; }
input:checked + .slider { background-color: #f59e0b; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

.border-radius-sm { border-radius: 4px; }
.bg-gray-50 { background-color: var(--gray-50); }
.font-mono { font-family: var(--font-mono); }
.hidden { display: none; }
.btn-white { background: white; color: var(--gray-800); }
</style>

<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div class="flex-1">
            <div class="flex items-center justify-between mb-8">
                <div class="hero-title">Sauvegardes Système</div>
                <div class="db-status-pill">
                    <span class="status-dot" :class="statusColor"></span>
                    <span class="text-[10px] font-bold text-white uppercase tracking-wider">Système Actif</span>
                </div>
            </div>
            <div class="hero-subtitle">Gérez la protection de vos données (Base de données et fichiers joints).</div>
            <div class="storage-pill mt-24">
                <i class="fa-solid fa-hard-drive mr-8 text-blue-300 text-[10px]"></i>
                <span class="text-[10px] uppercase tracking-wider opacity-70">Volume actif : </span>
                <span class="font-bold ml-4 text-[11px]">Slot {{ activeSlot?.toUpperCase() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION BDD -->
      <div class="grid-layout mt-32">
        <!-- LEFT: SETTINGS DB -->
        <div class="col-side">
            <div class="premium-card">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-calendar-check"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Planification BDD</div>
                        <div class="pc-header-sub">Paramètres auto</div>
                    </div>
                </div>
                <div class="pc-body p-20">
                    <div class="planif-grid">
                        <div class="form-group">
                            <label class="label">Statut</label>
                            <div class="input flex items-center gap-12 h-40 px-12 bg-white/50">
                                <label class="switch scale-75">
                                    <input type="checkbox" v-model="autoBackupDb.enabled">
                                    <span class="slider round slider-blue"></span>
                                </label>
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-tight">Auto-backup</span>
                            </div>
                        </div>
                        <div class="planif-fields" :class="{ 'opacity-50 pointer-events-none': !autoBackupDb.enabled }">
                            <div class="form-group">
                                <label class="label">Fréquence</label>
                                <select v-model="autoBackupDb.frequency" class="input w-full h-40">
                                    <option value="daily">Quotidien</option>
                                    <option value="weekly">Hebdomadaire</option>
                                    <option value="monthly">Mensuel</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="label">Heure</label>
                                <input type="time" v-model="autoBackupDb.time" class="input w-full h-40">
                            </div>
                            <div class="form-group">
                                <label class="label">Rétention (j)</label>
                                <input type="number" v-model="autoBackupDb.retention" min="1" max="90" class="input w-full h-40">
                            </div>
                        </div>
                        <div class="planif-action">
                             <label class="label opacity-0">Action</label>
                            <button class="btn btn-blue btn-block btn-sm h-40" @click="saveAutoBackup('database')" :disabled="loading">
                                <i class="fa-solid fa-save mr-8"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: HISTORY DB -->
        <div class="col-main">
            <div class="premium-card">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-database"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Historique BDD</div>
                        <div class="pc-header-sub">Dumps PostgreSQL compressés (.sql.gz)</div>
                    </div>
                    <div class="ml-auto flex gap-8" style="position: relative; z-index: 2;">
                        <input type="file" ref="dbUploadInput" style="display: none" @change="uploadFile($event, 'database')" accept=".sql,.gz">
                        <button class="btn btn-white btn-xs" @click="$refs.dbUploadInput.click()" :disabled="loading">
                          <i class="fa-solid fa-upload mr-4"></i> Importer
                        </button>
                        <button class="btn btn-white btn-xs" @click="handleBackup" :disabled="loading">
                          <i class="fa-solid fa-plus mr-4"></i> Backup Manuel
                        </button>
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
                              <tr v-for="b in dbBackups" :key="b.id">
                                  <td>
                                      <div class="flex items-center gap-12">
                                          <i class="fa-solid fa-file-zipper text-blue-500"></i>
                                          <span class="font-mono text-xs">{{ b.name }}</span>
                                      </div>
                                  </td>
                                  <td><span class="badge badge-blue">{{ b.size }}</span></td>
                                  <td class="text-xs text-muted">{{ b.date }}</td>
                                  <td class="text-right">
                                      <button class="btn btn-secondary btn-icon mr-4" @click="downloadBackup(b.name, 'database')" title="Télécharger">
                                        <i class="fa-solid fa-download"></i>
                                      </button>
                                      <button class="btn btn-secondary btn-icon mr-4 text-amber-600" @click="handleRestore(b.name)" title="Restaurer" :disabled="loading">
                                        <i class="fa-solid fa-rotate-left"></i>
                                      </button>
                                      <button class="btn btn-secondary btn-icon text-red-500" @click="confirmDeleteBackup(b.name, 'database')" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                      </button>
                                  </td>
                              </tr>
                              <tr v-if="dbBackups.length === 0">
                                <td colspan="4" class="text-center py-24 text-muted">Aucune sauvegarde BDD trouvée.</td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <!-- SECTION FICHIERS -->
      <div class="grid-layout mt-32">
        <!-- LEFT: SETTINGS FILES -->
        <div class="col-side">
            <div class="premium-card">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Planification Fichiers</div>
                        <div class="pc-header-sub">Paramètres auto</div>
                    </div>
                </div>
                <div class="pc-body p-20">
                    <div class="planif-grid">
                        <div class="form-group">
                            <label class="label">Statut</label>
                            <div class="input flex items-center gap-12 h-40 px-12 bg-white/50">
                                <label class="switch scale-75">
                                    <input type="checkbox" v-model="autoBackupFiles.enabled">
                                    <span class="slider round slider-blue"></span>
                                </label>
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-tight">Auto-backup</span>
                            </div>
                        </div>
                        <div class="planif-fields" :class="{ 'opacity-50 pointer-events-none': !autoBackupFiles.enabled }">
                            <div class="form-group">
                                <label class="label">Fréquence</label>
                                <select v-model="autoBackupFiles.frequency" class="input w-full h-40">
                                    <option value="daily">Quotidien</option>
                                    <option value="weekly">Hebdomadaire</option>
                                    <option value="monthly">Mensuel</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="label">Heure</label>
                                <input type="time" v-model="autoBackupFiles.time" class="input w-full h-40">
                            </div>
                            <div class="form-group">
                                <label class="label">Rétention (j)</label>
                                <input type="number" v-model="autoBackupFiles.retention" min="1" max="90" class="input w-full h-40">
                            </div>
                        </div>
                        <div class="planif-action">
                            <label class="label opacity-0">Action</label>
                            <button class="btn btn-blue btn-block btn-sm h-40" @click="saveAutoBackup('files')" :disabled="loading">
                                <i class="fa-solid fa-save mr-8"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: HISTORY FILES -->
        <div class="col-main">
            <div class="premium-card">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-paperclip"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title">Historique Fichiers</div>
                        <div class="pc-header-sub">Archives zip des pièces jointes et uploads</div>
                    </div>
                    <div class="ml-auto flex gap-8" style="position: relative; z-index: 2;">
                        <input type="file" ref="filesUploadInput" style="display: none" @change="uploadFile($event, 'files')" accept=".zip">
                        <button v-if="canRollback" class="btn btn-white btn-xs text-amber-600" @click="handleRollbackFiles" :disabled="loading">
                          <i class="fa-solid fa-undo mr-4"></i> Rollback
                        </button>
                        <button class="btn btn-white btn-xs" @click="$refs.filesUploadInput.click()" :disabled="loading">
                          <i class="fa-solid fa-upload mr-4"></i> Importer
                        </button>
                        <button class="btn btn-white btn-xs" @click="handleBackupFiles" :disabled="loading">
                          <i class="fa-solid fa-plus mr-4"></i> Backup Manuel
                        </button>
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
                              <tr v-for="b in fileBackups" :key="b.id">
                                  <td>
                                      <div class="flex items-center gap-12">
                                           <i class="fa-solid fa-file-archive text-blue-500"></i>
                                          <span class="font-mono text-xs">{{ b.name }}</span>
                                      </div>
                                  </td>
                                   <td><span class="badge badge-blue">{{ b.size }}</span></td>
                                  <td class="text-xs text-muted">{{ b.date }}</td>
                                  <td class="text-right">
                                      <button class="btn btn-secondary btn-icon mr-4" @click="downloadBackup(b.name, 'files')" title="Télécharger">
                                        <i class="fa-solid fa-download"></i>
                                      </button>
                                      <button class="btn btn-secondary btn-icon mr-4 text-amber-600" @click="handleRestoreFiles(b.name)" title="Restaurer" :disabled="loading">
                                        <i class="fa-solid fa-rotate-left"></i>
                                      </button>
                                      <button class="btn btn-secondary btn-icon text-red-500" @click="confirmDeleteBackup(b.name, 'files')" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                      </button>
                                  </td>
                              </tr>
                              <tr v-if="fileBackups.length === 0">
                                <td colspan="4" class="text-center py-24 text-muted">Aucune sauvegarde fichiers trouvée.</td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <!-- SEXY CONFIRMATION MODAL -->
      <transition name="fade">
        <div v-if="showConfirm" class="modal-overlay" @click.self="showConfirm = false">
            <div class="modal-content premium-card animate-pop-in">
                <div class="pc-header pc-header-blue">
                    <div class="pc-header-icon"><i class="fa-solid fa-circle-question"></i></div>
                    <div class="pc-header-title">{{ confirmTitle }}</div>
                </div>
                <div class="pc-body p-24">
                    <p class="text-gray-700 mb-24">{{ confirmMsg }}</p>
                    <div class="flex gap-12 justify-end">
                        <button class="btn btn-white" @click="showConfirm = false">Annuler</button>
                        <button class="btn btn-blue" @click="executeConfirm">Confirmer</button>
                    </div>
                </div>
            </div>
        </div>
      </transition>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const loading = ref(true);
const canRollback = ref(false);
const activeSlot = ref('a');

const statusColor = computed(() => {
    const db = autoBackupDb.value.enabled;
    const files = autoBackupFiles.value.enabled;
    if (db && files) return 'online'; // Green
    if (db || files) return 'warning'; // Orange
    return 'offline'; // Red
});

const showConfirm = ref(false);
const confirmTitle = ref('');
const confirmMsg = ref('');
const confirmAction = ref(null);

const askConfirmation = (title, msg, action) => {
    confirmTitle.value = title;
    confirmMsg.value = msg;
    confirmAction.value = action;
    showConfirm.value = true;
};

const executeConfirm = async () => {
    showConfirm.value = false;
    if (confirmAction.value) {
        await confirmAction.value();
    }
};

const autoBackupDb = ref({
    enabled: false,
    frequency: 'daily',
    time: '03:00',
    retention: 7
});

const autoBackupFiles = ref({
    enabled: false,
    frequency: 'daily',
    time: '03:30',
    retention: 7
});

const dbBackups = ref([]);
const fileBackups = ref([]);

const fetchData = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/admin/tools/backups');
        dbBackups.value = data.db_backups || [];
        fileBackups.value = data.file_backups || [];
        activeSlot.value = data.active_slot || 'a';
        
        if (data.auto_backup_db) {
            autoBackupDb.value = data.auto_backup_db;
        }
        if (data.auto_backup_files) {
            autoBackupFiles.value = data.auto_backup_files;
        }
    } catch (e) {
        console.error("Erreur lors du chargement des sauvegardes", e);
    } finally {
        loading.value = false;
    }
};

const uploadFile = async (event, type) => {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);

    loading.value = true;
    try {
        await axios.post('/api/v1/admin/tools/database/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        alert('Fichier importé avec succès.');
        fetchData();
    } catch (e) {
        alert(e.response?.data?.error || 'Erreur lors de l\'importation du fichier.');
    } finally {
        loading.value = false;
        event.target.value = ''; // Reset input
    }
};

const handleBackup = () => {
    askConfirmation(
        'Sauvegarde BDD',
        'Lancer une sauvegarde de la base de données maintenant ?',
        async () => {
            loading.value = true;
            try {
                await axios.post('/api/v1/admin/tools/database/backup');
                fetchData();
            } catch (e) {
                alert('Échec de la sauvegarde BDD.');
            } finally {
                loading.value = false;
            }
        }
    );
};

const handleBackupFiles = () => {
    askConfirmation(
        'Sauvegarde Fichiers',
        'Lancer une sauvegarde des fichiers maintenant ?',
        async () => {
            loading.value = true;
            try {
                await axios.post('/api/v1/admin/tools/files/backup');
                fetchData();
            } catch (e) {
                alert('Échec de la sauvegarde des fichiers. Vérifiez si "zip" est installé.');
            } finally {
                loading.value = false;
            }
        }
    );
};

const handleRestore = (filename) => {
    askConfirmation(
        'Restauration BDD',
        `ATTENTION: Restaurer la BDD avec "${filename}" ? Cela écrasera toutes les données actuelles.`,
        async () => {
            loading.value = true;
            try {
                await axios.post('/api/v1/admin/tools/database/restore', { filename });
                alert('Restauration terminée.');
            } catch (e) {
                alert('Échec de la restauration.');
            } finally {
                loading.value = false;
            }
        }
    );
};

const handleRestoreFiles = (filename) => {
    askConfirmation(
        'Restauration Fichiers',
        `ATTENTION: Restaurer les fichiers avec "${filename}" ? Cela écrasera les fichiers actuels. (Atomic Restore avec Rollback possible)`,
        async () => {
            loading.value = true;
            try {
                const { data } = await axios.post('/api/v1/admin/tools/database/restore-files', { filename });
                canRollback.value = data.can_rollback;
                alert('Restauration des fichiers terminée.');
            } catch (e) {
                alert('Échec de la restauration des fichiers.');
            } finally {
                loading.value = false;
            }
        }
    );
};

const handleRollbackFiles = () => {
    askConfirmation(
        'Rollback Fichiers',
        'Voulez-vous annuler la dernière restauration et revenir à la version précédente ?',
        async () => {
            loading.value = true;
            try {
                await axios.post('/api/v1/admin/tools/database/rollback-files');
                canRollback.value = false;
                alert('Rollback effectué avec succès.');
            } catch (e) {
                alert('Échec du rollback.');
            } finally {
                loading.value = false;
            }
        }
    );
};

const confirmDeleteBackup = (filename, type) => {
    askConfirmation(
        'Suppression',
        `Supprimer définitivement la sauvegarde "${filename}" ?`,
        async () => {
            try {
                const url = type === 'database' 
                    ? `/api/v1/admin/tools/database/backups/${filename}`
                    : `/api/v1/admin/tools/files/backups/${filename}`;
                await axios.delete(url);
                fetchData();
            } catch (e) {
                alert('Erreur lors de la suppression.');
            }
        }
    );
};

const downloadBackup = async (filename, type) => {
  try {
    const endpoint = type === 'database' 
        ? `/api/v1/admin/tools/database/backups/${filename}/url`
        : `/api/v1/admin/tools/files/backups/${filename}/url`;
        
    const { data } = await axios.get(endpoint);
    window.location.href = data.url;
  } catch (e) {
    alert('Erreur lors de la génération du lien de téléchargement.');
  }
};

const saveAutoBackup = async (type) => {
    loading.value = true;
    try {
        const config = type === 'database' ? {
            auto_backup_enabled: autoBackupDb.value.enabled,
            auto_backup_frequency: autoBackupDb.value.frequency,
            auto_backup_time: autoBackupDb.value.time,
            auto_backup_retention_days: autoBackupDb.value.retention
        } : {
            auto_backup_files_enabled: autoBackupFiles.value.enabled,
            auto_backup_files_frequency: autoBackupFiles.value.frequency,
            auto_backup_files_time: autoBackupFiles.value.time,
            auto_backup_files_retention_days: autoBackupFiles.value.retention
        };

        await axios.post('/api/v1/admin/config', { config });
        alert('Paramètres de sauvegarde mis à jour.');
    } catch (e) {
        alert('Erreur lors de l\'enregistrement.');
    } finally {
        loading.value = false;
    }
};

onMounted(fetchData);
</script>

<style scoped>
.grid-layout { display: flex; flex-direction: column; gap: 24px; }
@media (min-width: 1024px) {
  .grid-layout { flex-direction: row; align-items: flex-start; }
  .col-main { flex: 2; min-width: 0; }
  .col-side { flex: 1; min-width: 320px; }
  .hero-subtitle { color: rgba(255,255,255,0.8); font-size: 15px; }
}

.storage-pill { background: rgba(255,255,255,0.1); padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center; border: 1px solid rgba(255,255,255,0.05); color: white; }

.db-status-pill { background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 50px; display: inline-flex; align-items: center; border: 1px solid rgba(255,255,255,0.1); }
.status-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }
.status-dot.online { background: #10b981; box-shadow: 0 0 8px #10b981; }
.status-dot.warning { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
.status-dot.offline { background: #ef4444; box-shadow: 0 0 8px #ef4444; }

.scale-75 { transform: scale(0.75); transform-origin: left center; }

.table-responsive { overflow-x: auto; }
.db-table { width: 100%; border-collapse: collapse; }
.db-table th { text-align: left; padding: 12px 20px; font-size: 11px; text-transform: uppercase; color: var(--gray-500); background: var(--gray-50); border-bottom: 1px solid var(--gray-100); }
.db-table td { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
.db-table tr:hover { background: var(--gray-50); }

/* Responsive 1400px */
@media (max-width: 1400px) {
  .grid-layout { flex-direction: column !important; }
  .col-side, .col-main { width: 100% !important; flex: none !important; }
  
  .planif-grid { display: flex; align-items: flex-end; gap: 24px; flex-wrap: nowrap; }
  .planif-fields { display: flex; align-items: flex-end; gap: 16px; flex: 1; margin: 0 !important; }
  .planif-fields .form-group { margin: 0 !important; flex: 1; }
  .planif-action { margin: 0 !important; min-width: 150px; }
  .h-40 { height: 40px !important; }
}

/* Responsive 1000px */
@media (max-width: 1000px) {
  .planif-grid { flex-direction: column; align-items: stretch; gap: 16px; }
  .planif-fields { flex-direction: column; align-items: stretch; gap: 12px; }
  .planif-action { margin-top: 8px !important; }
}

/* Switch Toggle */
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: .2s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .2s; }
input:checked + .slider.slider-blue { background-color: var(--gray-300); }
input:checked + .slider-blue { background-color: var(--blue-600); }

/* Modal & Animations */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}
.modal-content {
  width: 100%;
  max-width: 500px;
  background: white;
  border-radius: var(--radius-xl);
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
.animate-pop-in {
  animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes popIn {
  0% { transform: scale(0.9); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

.font-mono { font-family: var(--font-mono); }
.btn-white { background: white; color: var(--gray-800); border: 1px solid var(--gray-200); }
.btn-white:hover { background: var(--gray-50); }
.mt-24 { margin-top: 24px !important; }
</style>

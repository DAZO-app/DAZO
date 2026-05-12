<template>
  <main class="main">
    <div class="page-body">
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Journal d'activité</div>
            <div class="hero-subtitle">Traçabilité complète des actions effectuées sur la plateforme.</div>
          </div>
        </div>
      </div>

      <div class="filters-bar mt-32 p-24 bg-white rounded-16 shadow-sm flex flex-wrap gap-16 items-end">
        <div class="form-group mb-0">
          <label class="config-label">Utilisateur</label>
          <select v-model="filters.user_id" class="select" @change="fetchLogs(1)">
            <option :value="null">Tous les utilisateurs</option>
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label class="config-label">Événement</label>
          <select v-model="filters.event_type" class="select" @change="fetchLogs(1)">
            <option :value="null">Tous les types</option>
            <option v-for="type in eventTypes" :key="type" :value="type">{{ formatEventName(type) }}</option>
          </select>
        </div>
        <div class="form-group mb-0">
          <label class="config-label">Ressource</label>
          <select v-model="filters.resource_type" class="select" @change="fetchLogs(1)">
            <option :value="null">Toutes les ressources</option>
            <option value="Decision">Décisions</option>
            <option value="User">Utilisateurs</option>
            <option value="Circle">Cercles</option>
            <option value="Config">Configuration</option>
            <option value="Backup">Backups</option>
          </select>
        </div>
        <button class="btn btn-secondary" @click="resetFilters">
          <i class="fa-solid fa-rotate-left mr-8"></i> Reset
        </button>
      </div>

      <div class="premium-card mt-32 animate-fade-in">
        <div class="pc-body">
          <div v-if="loading" class="p-48 text-center text-muted">
            <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
            <p>Chargement du journal...</p>
          </div>
          <div v-else-if="logs.length === 0" class="p-48 text-center text-muted">
            <i class="fa-solid fa-ghost fa-2x mb-16"></i>
            <p>Aucune activité enregistrée avec ces filtres.</p>
          </div>
          <div v-else class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Utilisateur</th>
                  <th>Événement</th>
                  <th>Cible</th>
                  <th>Détails</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="log in logs" :key="log.id">
                  <td class="whitespace-nowrap">{{ formatDate(log.created_at) }}</td>
                  <td>
                    <div v-if="log.user" class="flex items-center gap-8">
                       <img v-if="log.user.avatar_url" :src="log.user.avatar_url" class="avatar-xs">
                       <i v-else class="fa-solid fa-user-circle text-gray-400"></i>
                       <span>{{ log.user.name }}</span>
                    </div>
                    <span v-else class="text-muted"><i class="fa-solid fa-robot mr-4"></i> Système</span>
                  </td>
                  <td>
                    <span class="badge" :class="getEventBadgeClass(log.event_type)">
                      {{ formatEventName(log.event_type) }}
                    </span>
                  </td>
                  <td class="text-xs text-muted">
                    <div v-if="log.auditable_type">
                      <strong>{{ log.auditable_type.split('\\').pop() }}</strong>
                      <br>ID: {{ log.auditable_id.substring(0, 8) }}...
                    </div>
                    <div v-else>-</div>
                  </td>
                  <td>
                    <button class="btn btn-xs btn-outline" @click="showLogDetails(log)">
                      <i class="fa-solid fa-eye mr-4"></i> Voir
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-if="pagination.last_page > 1" class="pc-footer bg-gray-50 flex justify-center p-16">
           <div class="pagination">
              <button :disabled="pagination.current_page === 1" @click="fetchLogs(pagination.current_page - 1)" class="btn btn-xs">Précédent</button>
              <span class="px-16 text-sm">Page {{ pagination.current_page }} sur {{ pagination.last_page }}</span>
              <button :disabled="pagination.current_page === pagination.last_page" @click="fetchLogs(pagination.current_page + 1)" class="btn btn-xs">Suivant</button>
           </div>
        </div>
      </div>
    </div>

    <!-- MODAL DETAILS -->
    <div v-if="selectedLog" class="modal-overlay" @click.self="selectedLog = null">
      <div class="modal-card modal-lg animate-scale-in">
        <div class="modal-header">
           <div class="modal-title">Détails de l'action #{{ selectedLog.id }}</div>
           <button class="modal-close" @click="selectedLog = null">&times;</button>
        </div>
        <div class="modal-body p-24 overflow-auto max-h-70vh">
           <div class="grid-2 gap-16 mb-24">
             <div class="info-box">
               <label class="text-xs text-muted uppercase font-bold">Événement</label>
               <div class="font-bold">{{ formatEventName(selectedLog.event_type) }}</div>
             </div>
             <div class="info-box">
               <label class="text-xs text-muted uppercase font-bold">Date</label>
               <div>{{ formatDate(selectedLog.created_at, true) }}</div>
             </div>
             <div class="info-box">
               <label class="text-xs text-muted uppercase font-bold">IP Address</label>
               <div class="font-mono text-xs">{{ selectedLog.ip_address || 'N/A' }}</div>
             </div>
             <div class="info-box">
               <label class="text-xs text-muted uppercase font-bold">User Agent</label>
               <div class="text-xs truncate" :title="selectedLog.user_agent">{{ selectedLog.user_agent || 'N/A' }}</div>
             </div>
           </div>

           <div v-if="selectedLog.old_values || selectedLog.new_values" class="diff-container">
              <h4 class="mb-12 flex items-center gap-8"><i class="fa-solid fa-code text-teal-500"></i> Changements de données</h4>
              <div class="grid-2 gap-24">
                <div v-if="selectedLog.old_values" class="diff-box bg-red-50 border-red-soft p-12 rounded-8">
                  <div class="text-xs font-bold text-red mb-8">AVANT</div>
                  <pre class="text-xs overflow-auto">{{ formatJson(selectedLog.old_values) }}</pre>
                </div>
                <div v-if="selectedLog.new_values" class="diff-box bg-teal-50 border-teal-soft p-12 rounded-8">
                  <div class="text-xs font-bold text-teal mb-8">APRÈS</div>
                  <pre class="text-xs overflow-auto">{{ formatJson(selectedLog.new_values) }}</pre>
                </div>
              </div>
           </div>
        </div>
        <div class="modal-footer justify-end">
           <button class="btn btn-secondary" @click="selectedLog = null">Fermer</button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const logs = ref([]);
const users = ref([]);
const loading = ref(true);
const pagination = ref({});
const selectedLog = ref(null);
const filters = ref({
  user_id: null,
  event_type: null,
  resource_type: null
});

const eventTypes = [
  'decision_created', 'decision_phase_changed', 'config_updated', 
  'backup_db_generated', 'backup_files_generated', 'user_created', 'user_role_changed'
];

const fetchLogs = async (page = 1) => {
  loading.value = true;
  try {
    const params = { page, ...filters.value };
    const { data } = await axios.get('/api/v1/admin/tools/audit', { params });
    logs.value = data.data;
    pagination.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      total: data.total
    };
  } catch (e) {
    console.error("Audit logs fetch error", e);
  } finally {
    loading.value = false;
  }
};

const fetchUsers = async () => {
  try {
    const { data } = await axios.get('/api/v1/admin/users?per_page=1000');
    users.value = data.data || data;
  } catch (e) {
    console.error("Users fetch error", e);
  }
};

const resetFilters = () => {
  filters.value = { user_id: null, event_type: null, resource_type: null };
  fetchLogs(1);
};

const formatDate = (date, full = false) => {
  if (!date) return '-';
  const d = new Date(date);
  if (full) {
    return d.toLocaleString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }
  return d.toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' });
};

const formatEventName = (type) => {
  const map = {
    'decision_created': 'Création Décision',
    'decision_phase_changed': 'Changement de Phase',
    'config_updated': 'Config Mise à jour',
    'backup_db_generated': 'Backup BDD',
    'backup_files_generated': 'Backup Fichiers',
    'user_created': 'Nouvel Utilisateur',
    'user_role_changed': 'Changement de Rôle'
  };
  return map[type] || type;
};

const getEventBadgeClass = (type) => {
  if (type.includes('created')) return 'badge-teal';
  if (type.includes('changed') || type.includes('updated')) return 'badge-blue';
  if (type.includes('backup')) return 'badge-purple';
  return 'badge-gray';
};

const showLogDetails = (log) => {
  selectedLog.value = log;
};

const formatJson = (json) => {
  return JSON.stringify(json, null, 2);
};

onMounted(() => {
  fetchLogs();
  fetchUsers();
});
</script>

<style scoped>
.avatar-xs {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  object-fit: cover;
}
.diff-box pre {
  font-family: 'Fira Code', 'Courier New', Courier, monospace;
  white-space: pre-wrap;
  word-break: break-all;
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 24px;
}
.modal-card {
  background: white;
  border-radius: 20px;
  width: 100%;
  max-width: 800px;
  display: flex;
  flex-direction: column;
}
.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-title {
  font-weight: 800;
  font-size: 1.2rem;
}
.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}
.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #eee;
  display: flex;
}
.btn-xs {
  padding: 4px 8px;
  font-size: 10px;
}
.info-box {
  background: #f8fafc;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}
.pagination button {
  padding: 4px 12px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 6px;
  cursor: pointer;
}
.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>

<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Annuaire des Utilisateurs</div>
            <div class="hero-subtitle">Gérez les comptes, les rôles et les accès de l'ensemble de la plateforme.</div>
          </div>
          <div class="hero-action">
             <button class="btn btn-secondary" @click="openCreate">
               <i class="fa-solid fa-user-plus"></i> Nouvel utilisateur
             </button>
          </div>
        </div>
      </div>

      <!-- FILTER CARD -->
      <div class="premium-card mb-32">
        <div class="pc-header pc-header-indigo" style="padding: 16px 24px;">
          <div class="pc-header-icon" style="width: 32px; height: 32px; font-size: 14px;"><i class="fa-solid fa-filter"></i></div>
          <div class="pc-header-content">
            <div class="pc-header-title">Filtres & Recherche</div>
            <div class="pc-header-sub">Affinez la liste des utilisateurs selon vos critères.</div>
          </div>
        </div>
        <div class="pc-body p-20">
          <div class="filter-group main-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input v-model="filters.search" placeholder="Rechercher un nom ou un email..." class="input-inline">
          </div>
          
          <div class="filter-row">
            <div class="filter-item">
              <label>Rôle</label>
              <select v-model="filters.role" class="select-sm">
                <option value="">Tous les rôles</option>
                <option value="user">Utilisateurs</option>
                <option value="admin">Administrateurs</option>
                <option value="superadmin">Super-Admins</option>
              </select>
            </div>

            <div class="filter-item">
              <label>Cercle</label>
              <select v-model="filters.circle_id" class="select-sm">
                <option value="">Tous les cercles</option>
                <option v-for="c in availableCircles" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <div class="filter-item">
              <label>Décisions</label>
              <select v-model="filters.has_decisions" class="select-sm">
                <option value="">Toutes les activités</option>
                <option value="true">Auteurs de décisions</option>
                <option value="false">Aucune décision</option>
              </select>
            </div>

            <div class="filter-item">
              <label>Statut</label>
              <select v-model="filters.is_active" class="select-sm">
                <option value="">Tous les statuts</option>
                <option value="true">Actifs</option>
                <option value="false">Inactifs</option>
              </select>
            </div>

            <div class="filter-item">
              <label>Inscrit entre le...</label>
              <div class="date-range">
                <input type="date" v-model="filters.date_from" class="input-sm">
                <span>et le</span>
                <input type="date" v-model="filters.date_to" class="input-sm">
              </div>
            </div>

            <button class="btn btn-ghost btn-sm ml-auto" @click="resetFilters">
              <i class="fa-solid fa-rotate-left"></i> Réinitialiser
            </button>
          </div>
        </div>
      </div>

      <!-- Edit / Create Modal -->
      <div v-if="editingUser || showCreateModal" class="modal-overlay" @click.self="closeModals">
        <div class="modal-card">
          <div class="modal-header modal-header-indigo">
             <span class="modal-title">{{ showCreateModal ? 'Nouvel utilisateur' : ('Éditer ' + editingUser.name) }}</span>
             <button class="btn btn-ghost btn-icon text-white" @click="closeModals"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="form-group"><label class="label">Nom *</label><input v-model="userForm.name" class="input" required></div>
              <div class="form-group"><label class="label">Email *</label><input v-model="userForm.email" type="email" class="input" required></div>
              <div v-if="showCreateModal" class="form-group"><label class="label">Mot de passe *</label><input v-model="userForm.password" type="password" class="input" required></div>
              <div class="form-group"><label class="label">Rôle</label>
                <select v-model="userForm.role" class="select">
                  <option value="user">Utilisateur</option>
                  <option value="admin">Administrateur</option>
                  <option value="superadmin">Super Administrateur</option>
                </select>
              </div>
              <div class="form-group">
                <label class="label" style="display:flex; align-items:center; gap:8px;">
                  <input type="checkbox" v-model="userForm.is_active"> Compte actif
                </label>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-ghost" @click="closeModals">Annuler</button>
                <button type="submit" class="btn btn-primary"> Sauvegarder</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="userToDelete" class="modal-overlay" @click.self="userToDelete = null">
        <div class="modal-card" style="max-width:400px">
          <div class="modal-header pc-header-red">
            <span class="modal-title">Confirmer la suppression</span>
          </div>
          <div class="modal-body text-center">
            <div class="mb-16"><i class="fa-solid fa-triangle-exclamation text-red" style="font-size:32px;"></i></div>
            <p>Voulez-vous vraiment supprimer <strong>{{ userToDelete.name }}</strong> ?</p>
            <p class="text-xs text-muted mt-4">Si cet utilisateur possède des dépendances, il sera désactivé.</p>
            <div class="modal-footer" style="justify-content:center; border:none; margin-top:24px;">
              <button class="btn btn-secondary" @click="userToDelete = null">Annuler</button>
              <button class="btn btn-danger" @click="confirmDelete">Confirmer la suppression</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else class="user-grid">
        <div v-for="u in users" :key="u.id" class="premium-card">
          <div class="pc-header pc-header-blue" style="padding: 12px 16px;">
            <div class="pc-header-icon" style="width: 36px; height: 36px; background: white; color: var(--blue-600); font-weight: 700;">
              {{ u.name[0].toUpperCase() }}
            </div>
            <div class="pc-header-content">
              <div class="pc-header-title" style="font-size: 14px;">{{ u.name }}</div>
              <div class="pc-header-sub" style="font-size: 11px;">{{ u.email }}</div>
            </div>
            <span class="badge" :class="roleBadge(u.role)" style="margin-left: 8px; font-size: 10px;">{{ u.role }}</span>
          </div>
          
          <div class="pc-body p-24">
            <div class="user-indicators-row">
               <div class="indicator cursor-pointer" @click="showUserCircles(u)" title="Voir les cercles">
                  <i class="fa-solid fa-circle-nodes"></i>
                  <span>Cercles : <strong>{{ u.circles_count || 0 }}</strong></span>
               </div>
               <div class="indicator">
                  <i class="fa-solid fa-file-signature"></i>
                  <span>Décisions : <strong>{{ u.decisions_count || 0 }}</strong></span>
               </div>
            </div>
            <div class="indicator mt-8">
               <i class="fa-solid fa-calendar-day"></i>
               <span>Inscrit le : <strong>{{ new Date(u.created_at).toLocaleDateString() }}</strong></span>
            </div>

            <div class="user-actions-footer">
               <button class="btn btn-secondary btn-sm" @click="openEdit(u)">
                  <i class="fa-solid fa-pen"></i> Modifier
               </button>
               <button v-if="u.id !== currentUser?.id" class="btn btn-danger btn-sm" @click="userToDelete = u">
                  <i class="fa-solid fa-trash"></i> Supprimer
               </button>
               <button v-if="u.id !== currentUser?.id" class="btn btn-secondary btn-sm" @click.stop="impersonate(u)">
                  <i class="fa-solid fa-user-secret"></i> Simuler
               </button>
               <span class="badge" :class="u.is_active ? 'badge-teal' : 'badge-red'" style="margin-left:auto">{{ u.is_active ? 'Actif' : 'Inactif' }}</span>
            </div>
          </div>
        </div>
        <EmptyState v-if="users.length === 0" message="Aucun utilisateur trouvé." />
      </div>
      <!-- User Circles Detail Modal -->
      <div v-if="userForCircles" class="modal-overlay" @click.self="userForCircles = null">
        <div class="modal-card" style="max-width:400px">
          <div class="modal-header modal-header-indigo">
             <span class="modal-title">Cercles de {{ userForCircles.name }}</span>
             <button class="btn btn-ghost btn-icon text-white" @click="userForCircles = null"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body">
             <div v-if="userForCircles.circles?.length" class="flex flex-col gap-8">
                <div v-for="cm in userForCircles.circles" :key="cm.id" class="flex items-center justify-between p-8 border-bottom">
                   <div class="flex flex-col">
                      <span class="font-medium">{{ cm.circle?.name }}</span>
                      <span class="text-xs text-muted">{{ cm.circle?.description || 'Cercle de décision' }}</span>
                   </div>
                   <span class="badge badge-sm badge-blue">{{ cm.role }}</span>
                </div>
             </div>
             <div v-else class="text-center text-muted py-16">Aucun cercle.</div>
             <div class="modal-footer" style="border:none; margin:0">
                <button class="btn btn-secondary btn-block" @click="userForCircles = null">Fermer</button>
             </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import EmptyState from '../../components/EmptyState.vue';

const authStore = useAuthStore();
const router = useRouter();

const users = ref([]);
const availableCircles = ref([]);
const loading = ref(true);

const filters = ref({
  search: '',
  role: '',
  circle_id: '',
  has_decisions: '',
  is_active: '',
  date_from: '',
  date_to: ''
});

const editingUser = ref(null);
const showCreateModal = ref(false);
const userToDelete = ref(null);
const userForCircles = ref(null);
const userForm = ref({ name: '', email: '', role: 'user', is_active: true, password: '' });

const currentUser = computed(() => authStore.user);

const loadUsers = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/users', { params: filters.value });
    users.value = data.users || [];
  } catch (e) { /* */ } finally { loading.value = false; }
};

const loadCircles = async () => {
  try {
    const { data } = await axios.get('/api/v1/circles');
    availableCircles.value = data.circles || [];
  } catch (e) { /* */ }
};

const resetFilters = () => {
  filters.value = {
    search: '',
    role: '',
    circle_id: '',
    has_decisions: '',
    is_active: '',
    date_from: '',
    date_to: ''
  };
};

onMounted(() => {
  loadUsers();
  loadCircles();
});

import { watch } from 'vue';
let searchTimeout = null;
watch(filters, () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadUsers();
  }, 300);
}, { deep: true });

const roleBadge = (r) => ({ admin: 'badge-amber', superadmin: 'badge-red', user: 'badge-blue' }[r] || 'badge-gray');

const openEdit = (u) => {
  editingUser.value = u;
  userForm.value = { 
    name: u.name, 
    email: u.email, 
    role: u.role, 
    is_active: u.is_active 
  };
};

const openCreate = () => {
  showCreateModal.value = true;
  userForm.value = { name: '', email: '', role: 'user', is_active: true, password: 'password123' };
};

const closeModals = () => {
  editingUser.value = null;
  showCreateModal.value = false;
};

const saveUser = async () => {
  try {
    if (showCreateModal.value) {
      await axios.post('/api/v1/admin/users', userForm.value);
    } else {
      await axios.put(`/api/v1/admin/users/${editingUser.value.id}`, userForm.value);
    }
    closeModals();
    await loadUsers();
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de l\'enregistrement.');
  }
};

const showUserCircles = async (user) => {
  try {
    const { data } = await axios.get(`/api/v1/circles?user_id=${user.id}`); // This might need a backend filter, or user already has circles from index?
    // Actually, let's just use the circles we already have if we fetched them?
    // The current index doesn't return full circle names in the withCount.
    // I will fetch them specifically or update index. 
    // Simplified: let's fetch them now.
    const res = await axios.get(`/api/v1/admin/users/${user.id}/circles`);
    userForCircles.value = { ...user, circles: res.data.circles };
  } catch (e) {
    alert('Impossible de charger les cercles.');
  }
};

const confirmDelete = async () => {
  const u = userToDelete.value;
  try {
    await axios.delete(`/api/v1/admin/users/${u.id}`);
    userToDelete.value = null;
    await loadUsers();
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la suppression.');
  }
};

const impersonate = async (userToImpersonate) => {
  if (confirm(`Voulez-vous vraiment simuler l'utilisateur ${userToImpersonate.name} ?`)) {
    try {
      await authStore.impersonate(userToImpersonate.id);
      // Recharger la page courante (pas forcément le dashboard)
      window.location.reload();
    } catch (e) {
      alert(e.response?.data?.message || 'Erreur lors de l\'impersonation.');
    }
  }
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.user-grid { display: grid; grid-template-columns: 1fr; gap: 32px; }
@media (min-width: 768px) {
  .user-grid { grid-template-columns: 1fr 1fr; }
}

.avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
.av-blue { background: var(--blue-100); color: var(--blue-800); }

.user-indicators-row { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; border-bottom: 1px solid var(--gray-50); padding-bottom: 8px; }
.indicator { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--gray-600); }
.indicator i { width: 14px; color: var(--blue-400); }

.user-actions-footer { display: flex; align-items: center; gap: 8px; border-top: 1px solid var(--gray-100); padding-top: 12px; margin-top: 16px; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
.modal-card { background: white; border-radius: var(--radius-lg); width: 100%; max-width: 480px; box-shadow: var(--shadow-lg); overflow: hidden; animation: modalIn 0.2s ease; }
@keyframes modalIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; color: white; }
.modal-header-indigo { background: var(--blue-700); }
.modal-title { font-size: 15px; font-weight: 600; }
.modal-body { padding: 20px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; margin-top: 8px; border-top: 1px solid var(--gray-100); }

.text-red { color: var(--red-600); }

/* FILTER BAR */
.filter-bar { background: white; border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100); }
.main-search { position: relative; margin-bottom: 20px; }
.main-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gray-400); }
.input-inline { width: 100%; padding: 12px 12px 12px 42px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 14px; }
.input-inline:focus { border-color: var(--blue-500); outline: none; box-shadow: 0 0 0 3px var(--blue-50); }

.filter-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
.filter-item { display: flex; flex-direction: column; gap: 6px; }
.filter-item label { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--gray-500); letter-spacing: 0.05em; }
.select-sm, .input-sm { padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 13px; background: var(--gray-50); }
.date-range { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--gray-400); }
.ml-auto { margin-left: auto; }
</style>

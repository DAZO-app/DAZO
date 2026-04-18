<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Utilisateurs</div>
        <div class="page-subtitle">Gestion des comptes et rôles</div>
      </div>
    </div>
    <div class="page-body">

      <!-- Edit Modal -->
      <div v-if="editingUser" class="modal-overlay" @click.self="editingUser = null">
        <div class="modal-card">
          <div class="modal-header"><span class="modal-title">Éditer {{ editingUser.name }}</span><button class="btn btn-ghost btn-icon" @click="editingUser = null">✕</button></div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="form-group"><label class="label">Nom *</label><input v-model="userForm.name" class="input" required></div>
              <div class="form-group"><label class="label">Email *</label><input v-model="userForm.email" type="email" class="input" required></div>
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
              <div class="modal-footer"><button type="button" class="btn btn-ghost" @click="editingUser = null">Annuler</button><button type="submit" class="btn btn-primary">Sauvegarder</button></div>
            </form>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else class="card">
        <div class="card-body" style="padding:0;">
          <table class="admin-table">
            <thead>
              <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
              <tr v-for="u in users" :key="u.id">
                <td class="font-medium">{{ u.name }}</td>
                <td class="text-muted">{{ u.email }}</td>
                <td><span class="badge" :class="roleBadge(u.role)">{{ u.role }}</span></td>
                <td><span class="badge" :class="u.is_active ? 'badge-teal' : 'badge-red'">{{ u.is_active ? 'Actif' : 'Inactif' }}</span></td>
                <td class="text-right">
                  <button class="btn btn-ghost btn-sm" @click="openEdit(u)" title="Éditer">✏️</button>
                  <button v-if="u.id !== currentUser?.id" class="btn btn-ghost btn-sm text-red" @click="deleteUser(u)" title="Supprimer">🗑️</button>
                  <button v-if="u.id !== currentUser?.id" class="btn btn-ghost btn-sm" @click="impersonate(u)" title="Simuler cet utilisateur">
                    🎭 Simuler
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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

const authStore = useAuthStore();
const router = useRouter();

const users = ref([]);
const loading = ref(true);

const editingUser = ref(null);
const userForm = ref({ name: '', email: '', role: 'user', is_active: true });

const currentUser = computed(() => authStore.user);

const loadUsers = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/users');
    users.value = data.users || [];
  } catch (e) { /* */ } finally { loading.value = false; }
};

onMounted(loadUsers);

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

const saveUser = async () => {
  try {
    await axios.put(`/api/v1/admin/users/${editingUser.value.id}`, userForm.value);
    editingUser.value = null;
    await loadUsers();
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la mise à jour.');
  }
};

const deleteUser = async (u) => {
  if (confirm(`Voulez-vous vraiment supprimer ou désactiver l'utilisateur ${u.name} ?`)) {
    try {
      await axios.delete(`/api/v1/admin/users/${u.id}`);
      await loadUsers();
    } catch (e) {
      alert(e.response?.data?.message || 'Erreur lors de la suppression.');
    }
  }
};

const impersonate = async (userToImpersonate) => {
  if (confirm(`Voulez-vous vraiment simuler l'utilisateur ${userToImpersonate.name} ?`)) {
    try {
      await authStore.impersonate(userToImpersonate.id);
      router.push({ name: 'Dashboard' });
    } catch (e) {
      alert(e.response?.data?.message || 'Erreur lors de l\'impersonation.');
    }
  }
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.admin-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.admin-table th { text-align: left; padding: 10px 18px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-500); background: var(--gray-50); border-bottom: 1px solid var(--gray-200); }
.admin-table td { padding: 10px 18px; border-bottom: 1px solid var(--gray-100); color: var(--gray-800); }
.admin-table tr:last-child td { border-bottom: none; }
.font-medium { font-weight: 500; }
.text-muted { color: var(--gray-400); }
</style>

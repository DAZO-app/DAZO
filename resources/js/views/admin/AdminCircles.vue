<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Gestion des cercles</div>
        <div class="page-subtitle">Créer, éditer et gérer les membres</div>
      </div>
      <div class="page-actions">
        <button class="btn btn-primary btn-sm" @click="openCreateCircle">+ Nouveau cercle</button>
      </div>
    </div>

    <div class="page-body">
      <!-- Create/Edit Modal -->
      <div v-if="showCreate" class="modal-overlay" @click.self="showCreate = false">
        <div class="modal-card">
          <div class="modal-header"><span class="modal-title">{{ editingCircleData ? 'Éditer le cercle' : 'Nouveau cercle' }}</span><button class="btn btn-ghost btn-icon" @click="showCreate = false">✕</button></div>
          <div class="modal-body">
            <form @submit.prevent="saveCircle">
              <div class="form-group"><label class="label">Nom *</label><input v-model="circleForm.name" class="input" required></div>
              <div class="form-group"><label class="label">Description</label><textarea v-model="circleForm.description" class="textarea" rows="3"></textarea></div>
              <div class="form-group"><label class="label">Type</label>
                <select v-model="circleForm.type" class="select">
                  <option value="open">Ouvert</option>
                  <option value="closed">Fermé</option>
                  <option value="observer_open">Observateur ouvert</option>
                </select>
              </div>
              <div class="modal-footer"><button type="button" class="btn btn-ghost" @click="showCreate = false">Annuler</button><button type="submit" class="btn btn-primary">{{ editingCircleData ? 'Sauvegarder' : 'Créer' }}</button></div>
            </form>
          </div>
        </div>
      </div>

      <!-- Add Member Modal -->
      <div v-if="showAddMember" class="modal-overlay" @click.self="showAddMember = false">
        <div class="modal-card">
          <div class="modal-header"><span class="modal-title">Ajouter un membre à {{ editingCircle?.name }}</span><button class="btn btn-ghost btn-icon" @click="showAddMember = false">✕</button></div>
          <div class="modal-body">
            <div class="form-group"><label class="label">Email de l'utilisateur</label><input v-model="addMemberEmail" class="input" placeholder="user@dazo.test"></div>
            <div class="form-group"><label class="label">Rôle</label>
              <select v-model="addMemberRole" class="select">
                <option value="member">Membre</option>
                <option value="animator">Animateur</option>
                <option value="observer">Observateur</option>
              </select>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-ghost" @click="showAddMember = false">Annuler</button><button class="btn btn-primary" @click="addMember">Ajouter</button></div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>

      <div v-else>
        <div v-for="circle in circles" :key="circle.id" class="card mb-16">
          <div class="card-header">
            <span class="card-title">{{ circle.name }}</span>
            <span class="badge" :class="typeBadge(circle.type)">{{ circle.type }}</span>
            <div style="flex:1"></div>
            <button class="btn btn-ghost btn-sm" @click="openEditCircle(circle)">✏️ Éditer</button>
            <button class="btn btn-ghost btn-sm text-red" @click="deleteCircle(circle)">🗑️ Supprimer</button>
          </div>
          <div class="card-body">
            <p class="text-sm text-muted mb-12">{{ circle.description || 'Pas de description.' }}</p>
            <div class="text-xs font-semibold text-gray-500 mb-8">MEMBRES</div>
            <div v-for="m in circle.members" :key="m.id" class="member-row">
              <div class="member-info">
                <span class="font-medium">{{ m.user?.name }}</span>
                <span class="text-xs text-muted ml-8">{{ m.user?.email }}</span>
              </div>
              <select class="select select-sm mr-8" style="width:130px; font-size:12px;" :value="m.role" @change="updateMemberRole(circle, m, $event.target.value)">
                <option value="animator">Animateur</option>
                <option value="member">Membre</option>
                <option value="observer">Observateur</option>
              </select>
              <button class="btn btn-ghost btn-sm text-red" @click="removeMember(circle, m)" title="Retirer">✕</button>
            </div>
            <div v-if="!circle.members?.length" class="text-sm text-muted">Aucun membre</div>
          </div>
          <div class="card-footer">
            <button class="btn btn-ghost btn-sm" @click="openAddMember(circle)">+ Ajouter un membre</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const circles = ref([]);
const loading = ref(true);
const showCreate = ref(false);
const showAddMember = ref(false);
const editingCircle = ref(null);
const editingCircleData = ref(null);
const addMemberEmail = ref('');
const addMemberRole = ref('member');
const circleForm = ref({ name: '', description: '', type: 'open' });

const loadCircles = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/circles');
    const detailed = await Promise.all(
      data.circles.map(c => axios.get(`/api/v1/circles/${c.id}`).then(r => r.data.circle))
    );
    circles.value = detailed;
  } catch (e) { /* */ } finally { loading.value = false; }
};

onMounted(loadCircles);

const openCreateCircle = () => {
  editingCircleData.value = null;
  circleForm.value = { name: '', description: '', type: 'open' };
  showCreate.value = true;
};

const openEditCircle = (circle) => {
  editingCircleData.value = circle;
  circleForm.value = { name: circle.name, description: circle.description, type: circle.type };
  showCreate.value = true;
};

const saveCircle = async () => {
  try {
    if (editingCircleData.value) {
      await axios.put(`/api/v1/circles/${editingCircleData.value.id}`, circleForm.value);
    } else {
      await axios.post('/api/v1/circles', circleForm.value);
    }
    showCreate.value = false;
    await loadCircles();
  } catch (e) { alert(e.response?.data?.message || 'Erreur'); }
};

const deleteCircle = async (circle) => {
  if (!confirm(`Supprimer définitivement le cercle ${circle.name} ?`)) return;
  try {
    await axios.delete(`/api/v1/circles/${circle.id}`);
    await loadCircles();
  } catch (e) { alert(e.response?.data?.message || 'Erreur'); }
};

const openAddMember = (circle) => {
  editingCircle.value = circle;
  addMemberEmail.value = '';
  addMemberRole.value = 'member';
  showAddMember.value = true;
};

const addMember = async () => {
  alert('Fonctionnalité à venir : dans cette V1, les membres rejoignent via /api/v1/circles/{id}/join. Utilisez le système d\'invitations.');
  showAddMember.value = false;
};

const updateMemberRole = async (circle, member, newRole) => {
  try {
    await axios.put(`/api/v1/circles/${circle.id}/members/${member.user_id}`, { role: newRole });
    member.role = newRole;
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la modification du rôle.');
    // Re-render to revert visually if failed
    await loadCircles();
  }
};

const removeMember = async (circle, member) => {
  if (!confirm(`Retirer ${member.user?.name} du cercle ${circle.name} ?`)) return;
  try {
    await axios.delete(`/api/v1/circles/${circle.id}/members/${member.user_id}`);
    await loadCircles();
  } catch (e) { alert(e.response?.data?.message || 'Erreur'); }
};

const typeBadge = (t) => ({ open: 'badge-teal', closed: 'badge-red', observer_open: 'badge-blue' }[t] || 'badge-gray');
</script>

<style scoped>
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
.modal-card { background: white; border-radius: var(--radius-lg); width: 100%; max-width: 480px; box-shadow: var(--shadow-lg); overflow: hidden; animation: modalIn 0.2s ease; }
@keyframes modalIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--gray-200); }
.modal-title { font-size: 15px; font-weight: 600; }
.modal-body { padding: 20px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; }
.card-footer { padding: 10px 18px; border-top: 1px solid var(--gray-100); }
.member-row { display: flex; align-items: center; gap: 10px; padding: 6px 0; border-bottom: 1px solid var(--gray-50); }
.member-row:last-child { border-bottom: none; }
.member-info { flex: 1; font-size: 13px; }
.ml-8 { margin-left: 8px; }
.text-red { color: var(--red-600); }
.py-24 { padding: 24px 0; }
</style>

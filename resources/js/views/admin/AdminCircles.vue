<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Gestion des Cercles</div>
            <div class="hero-subtitle">Architecturez votre organisation en créant et pilotant vos cercles de décision.</div>
          </div>
          <div class="hero-action">
            <button class="btn btn-secondary" @click="openCreateCircle">
              <i class="fa-solid fa-plus"></i> Nouveau cercle
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
            <div class="pc-header-sub">Recherchez des cercles par nom ou par type de participation.</div>
          </div>
        </div>
        <div class="pc-body p-20">
          <div class="filter-group main-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input v-model="filters.search" placeholder="Rechercher un cercle par nom..." class="input-inline">
          </div>
          
          <div class="filter-row">
            <div class="filter-item">
              <label>Type de cercle</label>
              <select v-model="filters.type" class="select-sm">
                <option value="">Tous les types</option>
                <option value="open">Ouvert</option>
                <option value="closed">Fermé</option>
                <option value="observer_open">Observateur ouvert</option>
              </select>
            </div>

            <button class="btn btn-ghost btn-sm ml-auto" @click="resetFilters">
              <i class="fa-solid fa-rotate-left"></i> Réinitialiser
            </button>
          </div>
        </div>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showCreate" class="modal-overlay" @click.self="showCreate = false">
        <div class="modal-card">
          <div class="modal-header modal-header-indigo">
             <span class="modal-title">{{ editingCircleData ? 'Éditer le cercle' : 'Nouveau cercle' }}</span>
             <button class="btn btn-ghost btn-icon text-white" @click="showCreate = false"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCircle">
              <div class="form-group"><label class="label">Nom *</label><input v-model="circleForm.name" class="input" required placeholder="Ex: Bureau, Marketing, Technique..."></div>
              <div class="form-group"><label class="label">Description</label><textarea v-model="circleForm.description" class="textarea" rows="3" placeholder="Quel est l'objectif de ce cercle ?"></textarea></div>
              <div class="form-group"><label class="label">Type de participation</label>
                <select v-model="circleForm.type" class="select">
                  <option value="open">Ouvert (Tout le monde peut rejoindre)</option>
                  <option value="closed">Fermé (Sur invitation seulement)</option>
                  <option value="observer_open">Observateur ouvert (Lecture seule par défaut)</option>
                </select>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-ghost" @click="showCreate = false">Annuler</button>
                <button type="submit" class="btn btn-primary">{{ editingCircleData ? 'Sauvegarder' : 'Créer le cercle' }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Add Member Modal -->
       <div v-if="showAddMember" class="modal-overlay" @click.self="closeAddMemberModal">
         <div class="modal-card">
           <div class="modal-header modal-header-indigo">
             <span class="modal-title">Inviter : {{ editingCircle?.name }}</span>
             <button class="btn btn-ghost btn-icon text-white" @click="closeAddMemberModal"><i class="fa-solid fa-xmark"></i></button>
           </div>
          <div class="modal-body">
            <div class="form-group" style="position:relative;">
              <label class="label">Utilisateur (Nom ou Email)</label>
              <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input 
                  v-model="addMemberSearchQuery" 
                  @input="handleUserSearch"
                  class="input pl-32" 
                  placeholder="Rechercher par nom ou email..." 
                  required
                >
              </div>
              
              <!-- AJAX Results -->
              <div v-if="addMemberSearchResults.length" class="ajax-results-abs">
                <div 
                  v-for="u in addMemberSearchResults" 
                  :key="u.id" 
                  class="ajax-item"
                  @click="selectUserToInvite(u)"
                  :class="{ 'opacity-50 pointer-events-none': isUserAlreadySelected(u.id) }"
                >
                  <div class="flex items-center gap-12">
                    <div class="avatar-mini">{{ u.name.charAt(0).toUpperCase() }}</div>
                    <div class="min-w-0">
                      <div class="text-strong text-xs truncate">{{ u.name }}</div>
                      <div class="text-xs text-muted truncate">{{ u.email }}</div>
                    </div>
                  </div>
                  <i v-if="isUserAlreadySelected(u.id)" class="fa-solid fa-check text-green"></i>
                </div>
              </div>
            </div>

            <div class="form-group mt-16 pb-16 border-b border-gray-100">
              <label class="label">Ou par adresse email (séparées par ";")</label>
              <textarea 
                v-model="addMemberEmails" 
                class="textarea" 
                placeholder="nom@exemple.com; contact@autre.fr..." 
                rows="2"
              ></textarea>
            </div>

            <div class="form-group mt-16 pb-16 border-b border-gray-100">
              <label class="label">Ou importer d'un autre cercle</label>
              <select v-model="importCircleId" @change="importMembersFromCircle" class="select select-sm w-full">
                <option value="">Sélectionner un cercle pour importer...</option>
                <option v-for="c in circles.filter(c => c.id !== editingCircle?.id)" :key="c.id" :value="c.id">
                  {{ c.name }} ({{ c.members?.length || 0 }} membres)
                </option>
              </select>
            </div>

            <!-- Selected Users List -->
            <div v-if="selectedUsersToInvite.length" class="selected-users-wrap mt-16">
              <div class="text-xs font-semibold uppercase text-gray-500 mb-8">Utilisateurs sélectionnés ({{ selectedUsersToInvite.length }})</div>
              <div class="selected-users-list">
                <div v-for="u in selectedUsersToInvite" :key="u.id" class="selected-user-tag">
                   <div class="flex items-center gap-8">
                     <div class="avatar-mini" style="width:20px;height:20px;font-size:9px;">{{ u.name.charAt(0) }}</div>
                     <span class="text-xs truncate" style="max-width:120px">{{ u.name }}</span>
                   </div>
                   <i class="fa-solid fa-xmark cursor-pointer hover:text-red" @click="removeSelectedUser(u.id)"></i>
                </div>
              </div>
            </div>

            <div class="form-group mt-16">
              <label class="label">Rôle dans le cercle</label>
              <select v-model="addMemberRole" class="select">
                <option value="member">Membre (Peut voter/proposer)</option>
                <option value="animator">Animateur (Gère le cercle)</option>
                <option value="observer">Observateur (Consultation)</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-ghost" @click="closeAddMemberModal">Annuler</button>
              <button class="btn btn-primary" @click="addMember" :disabled="!selectedUsersToInvite.length && !addMemberEmails.trim()">
                {{ (selectedUsersToInvite.length + (addMemberEmails.trim() ? 1 : 0)) > 1 ? 'Ajouter / Inviter' : 'Ajouter au cercle' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Resend Invitation Modal -->
      <div v-if="showResendModal" class="modal-overlay" @click.self="closeResendModal">
        <div class="modal-card" style="max-width: 400px;">
          <div class="modal-header modal-header-sexy">
            <span class="modal-title">{{ resendSuccess ? 'Succès ! (v2)' : 'Renvoyer l\'invitation (v2)' }}</span>
            <button class="btn btn-ghost btn-icon text-white" @click="closeResendModal"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body text-center py-24">
            <template v-if="!resendSuccess">
              <div class="avatar av-orange mx-auto mb-16" style="width:64px;height:64px;font-size:24px;">
                <i class="fa-solid fa-paper-plane"></i>
              </div>
              <h3 class="text-lg font-semibold mb-8">Confirmer le renvoi ?</h3>
              <p class="text-sm text-muted mb-24">
                Un nouvel email d'invitation sera envoyé à <br>
                <strong class="text-dark">{{ resendingInviteData?.email }}</strong>
              </p>
              <div class="flex justify-center gap-12">
                <button class="btn btn-ghost" @click="showResendModal = false">Annuler</button>
                <button class="btn btn-primary" @click="confirmResendInvite" :disabled="loadingResend">
                  <i v-if="loadingResend" class="fa-solid fa-spinner fa-spin mr-8"></i>
                  {{ loadingResend ? 'Envoi...' : 'Oui, renvoyer' }}
                </button>
              </div>
            </template>
            <template v-else>
              <div class="avatar av-teal mx-auto mb-16" style="width:64px;height:64px;font-size:24px;">
                <i class="fa-solid fa-check"></i>
              </div>
              <h3 class="text-lg font-semibold mb-8">C'est envoyé !</h3>
              <p class="text-sm text-muted mb-24">
                L'invitation a été renvoyée avec succès à <br>
                <strong class="text-dark">{{ resendingInviteData?.email }}</strong>
              </p>
              <button class="btn btn-primary w-full" @click="closeResendModal">Fermer</button>
            </template>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement des cercles...</div>

      <div v-else class="premium-grid mt-24">
        <div v-for="circle in circles" :key="circle.id" class="premium-card">
          <div class="pc-header pc-header-blue" style="padding: 14px 20px;">
            <div class="pc-header-icon" style="width: 36px; height: 36px; font-size: 16px;">
              <i class="fa-solid fa-circle-nodes"></i>
            </div>
            <div class="pc-header-content">
              <div class="pc-header-title">{{ circle.name }}</div>
              <div class="pc-header-sub">{{ typeLabel(circle.type) }}</div>
            </div>
            <div class="flex flex-col items-end gap-6 ml-auto">
               <span class="badge" :class="typeBadge(circle.type)" style="font-size: 10px;">{{ circle.type }}</span>
               <div class="flex gap-4">
                  <button class="btn btn-ghost btn-icon btn-sm text-white opacity-80 hover:opacity-100" @click="openEditCircle(circle)" title="Modifier">
                    <i class="fa-solid fa-pen text-xs"></i>
                  </button>
                  <button class="btn btn-ghost btn-icon btn-sm text-white opacity-80 hover:opacity-100" @click="deleteCircle(circle)" title="Supprimer">
                    <i class="fa-solid fa-trash text-xs"></i>
                  </button>
               </div>
            </div>
          </div>
          
          <div class="pc-body p-24">
            <p class="circle-desc">{{ circle.description || 'Pas de description fournie.' }}</p>
            
            <div class="mt-16">
              <div class="flex items-center justify-between mb-8">
                 <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Membres ({{ circle.members?.length || 0 }})</div>
                 <button class="btn btn-ghost btn-sm" style="padding:2px 6px; font-size:10px" @click="openAddMember(circle)">
                    <i class="fa-solid fa-user-plus"></i> Inviter
                 </button>
              </div>
              
              <div class="member-list-compact">
                <!-- Registered Members -->
                <div v-for="m in circle.members" :key="m.id" class="member-row">
                  <div class="member-info">
                    <span class="font-medium">{{ m.user?.name }}</span>
                    <span class="text-xs text-muted ml-8 hide-tablet">{{ m.user?.email }}</span>
                  </div>
                  <div class="member-controls">
                    <select class="select-member-role" :value="m.role" @change="updateMemberRole(circle, m, $event.target.value)">
                      <option value="animator">Animateur</option>
                      <option value="member">Membre</option>
                      <option value="observer">Observateur</option>
                    </select>
                    <button class="btn btn-ghost btn-icon btn-sm text-red" @click="removeMember(circle, m)" title="Retirer"><i class="fa-solid fa-xmark"></i></button>
                  </div>
                </div>

                <!-- Pending Invitations -->
                <div v-for="inv in circle.invitations" :key="inv.id" class="member-row is-pending-invite">
                  <div class="member-info">
                    <div class="envelope-hitbox" @click.stop="resendInvite(circle, inv)" title="Renvoyer l'invitation">
                      <i class="fa-solid fa-envelope text-orange"></i>
                    </div>
                    <span class="font-medium">Invité</span>
                    <span class="text-xs text-muted ml-8">{{ inv.email }}</span>
                  </div>
                  <div class="member-controls">
                    <span class="badge badge-orange mr-8" style="font-size:10px">{{ inv.role }}</span>
                    <button class="btn btn-ghost btn-icon btn-sm text-red" @click="cancelInvite(circle, inv)" title="Annuler l'invitation"><i class="fa-solid fa-xmark"></i></button>
                  </div>
                </div>

                <div v-if="!circle.members?.length && !circle.invitations?.length" class="text-xs text-muted italic py-8 text-center">Aucun membre dans ce cercle.</div>
              </div>
            </div>
          </div>
        </div>
        <EmptyState v-if="circles.length === 0" message="Aucun cercle trouvé." />
      </div>

      <!-- PAGINATION -->
      <div v-if="pagination && pagination.last_page > 1" class="pagination-bar mt-24">
        <div class="pagination-info">
          Affichage de <b>{{ pagination.from }}</b> à <b>{{ pagination.to }}</b> sur <b>{{ pagination.total }}</b> cercles
        </div>
        <div class="pagination-controls">
          <button class="btn btn-ghost btn-xs" :disabled="pagination.current_page === 1" @click="loadCircles(pagination.current_page - 1)">
            <i class="fa-solid fa-chevron-left"></i> Précédent
          </button>
          <div class="page-numbers">
            Page {{ pagination.current_page }} / {{ pagination.last_page }}
          </div>
          <button class="btn btn-ghost btn-xs" :disabled="pagination.current_page === pagination.last_page" @click="loadCircles(pagination.current_page + 1)">
            Suivant <i class="fa-solid fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import EmptyState from '../../components/EmptyState.vue';

const circles = ref([]);
const loading = ref(true);
const showCreate = ref(false);
const showAddMember = ref(false);
const showResendModal = ref(false);
const resendSuccess = ref(false);
const resendingInviteData = ref(null);
const loadingResend = ref(false);
const editingCircle = ref(null);
const editingCircleData = ref(null);
const selectedUsersToInvite = ref([]);
const importCircleId = ref('');
const addMemberEmails = ref('');
const addMemberSearchQuery = ref('');
const addMemberSearchResults = ref([]);
const loadingUserSearch = ref(false);
const addMemberRole = ref('member');
const circleForm = ref({ name: '', description: '', type: 'open' });

const filters = ref({
  search: '',
  type: ''
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
});

const loadCircles = async (page = 1) => {
  loading.value = true;
  try {
    // Utilisation de l'API Admin pour voir TOUS les cercles
    const { data } = await axios.get('/api/v1/admin/circles', { 
      params: { ...filters.value, page } 
    });
    circles.value = data.data || [];
    pagination.value = data.meta || { current_page: 1, last_page: 1, total: circles.value.length, from: 1, to: circles.value.length };
  } catch (e) { 
    // Fallback if admin API fails
    try {
      const { data } = await axios.get('/api/v1/circles', { params: { ...filters.value, page } });
      circles.value = data.data || [];
      pagination.value = data.meta || { current_page: 1, last_page: 1, total: circles.value.length, from: 1, to: circles.value.length };
    } catch (err) { /* */ }
  } finally { loading.value = false; }
};

const resetFilters = () => {
  filters.value = { search: '', type: '' };
};

onMounted(loadCircles);

let searchTimeout = null;
watch(filters, () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadCircles();
  }, 300);
}, { deep: true });

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
      await axios.put(`/api/v1/admin/circles/${editingCircleData.value.id}`, circleForm.value);
    } else {
      await axios.post('/api/v1/admin/circles', circleForm.value);
    }
    showCreate.value = false;
    await loadCircles();
  } catch (e) { alert(e.response?.data?.message || 'Erreur lors de l\'enregistrement.'); }
};

const deleteCircle = async (circle) => {
  if (!confirm(`Supprimer définitivement le cercle ${circle.name} ?`)) return;
  try {
    await axios.delete(`/api/v1/admin/circles/${circle.id}`);
    await loadCircles();
  } catch (e) { alert(e.response?.data?.message || 'Erreur lors de la suppression.'); }
};

const openAddMember = (circle) => {
  editingCircle.value = circle;
  selectedUsersToInvite.value = [];
  importCircleId.value = '';
  addMemberEmails.value = '';
  addMemberSearchQuery.value = '';
  addMemberSearchResults.value = [];
  addMemberRole.value = 'member';
  showAddMember.value = true;
};

const closeAddMemberModal = () => {
    if (selectedUsersToInvite.value.length > 0) {
        if (!confirm(`Vous avez ${selectedUsersToInvite.value.length} utilisateur(s) sélectionné(s). Voulez-vous vraiment annuler ?`)) {
            return;
        }
    }
    showAddMember.value = false;
};

const importMembersFromCircle = () => {
    if (!importCircleId.value) return;
    
    const sourceCircle = circles.value.find(c => c.id === importCircleId.value);
    if (!sourceCircle || !sourceCircle.members) return;
    
    let addedAny = false;
    sourceCircle.members.forEach(m => {
        if (m.user && !isUserAlreadySelected(m.user.id)) {
            // Check if user is already in the target circle
            const alreadyInTarget = editingCircle.value.members?.some(tm => tm.user_id === m.user.id);
            if (!alreadyInTarget) {
                selectedUsersToInvite.value.push(m.user);
                addedAny = true;
            }
        }
    });
    
    if (!addedAny) {
        alert("Tous les membres de ce cercle sont déjà sélectionnés ou déjà membres de ce cercle.");
    }
    
    importCircleId.value = ''; // Reset select
};

const handleUserSearch = () => {
    if (addMemberSearchQuery.value.length < 2) {
        addMemberSearchResults.value = [];
        return;
    }
    
    if (searchTimeout) clearTimeout(searchTimeout);
    loadingUserSearch.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const { data } = await axios.get('/api/v1/users/search', { params: { q: addMemberSearchQuery.value } });
            addMemberSearchResults.value = data.users || [];
        } catch (e) {
            addMemberSearchResults.value = [];
        } finally {
            loadingUserSearch.value = false;
        }
    }, 300);
};

const isUserAlreadySelected = (id) => selectedUsersToInvite.value.some(u => u.id === id);

const selectUserToInvite = (user) => {
    if (isUserAlreadySelected(user.id)) return;
    selectedUsersToInvite.value.push(user);
    addMemberSearchQuery.value = '';
    addMemberSearchResults.value = [];
};

const removeSelectedUser = (id) => {
    selectedUsersToInvite.value = selectedUsersToInvite.value.filter(u => u.id !== id);
};

const addMember = async () => {
  if (!selectedUsersToInvite.value.length && !addMemberEmails.value.trim()) return;
  
  try {
    await axios.post(`/api/v1/admin/circles/${editingCircle.value.id}/members`, {
      user_ids: selectedUsersToInvite.value.map(u => u.id),
      emails: addMemberEmails.value,
      role: addMemberRole.value
    });
    showAddMember.value = false;
    selectedUsersToInvite.value = [];
    addMemberEmails.value = '';
    await loadCircles();
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de l\'ajout des membres.');
  }
};

const resendInvite = (circle, inv) => {
    editingCircle.value = circle;
    resendingInviteData.value = inv;
    resendSuccess.value = false;
    showResendModal.value = true;
};

const closeResendModal = () => {
    showResendModal.value = false;
    resendSuccess.value = false;
};

const confirmResendInvite = async () => {
    if (!resendingInviteData.value) return;
    loadingResend.value = true;
    try {
        await axios.post(`/api/v1/admin/circles/${editingCircle.value.id}/invitations/${resendingInviteData.value.id}/resend`);
        resendSuccess.value = true;
    } catch (e) {
        alert('Erreur lors du renvoi.');
    } finally {
        loadingResend.value = false;
    }
};

const cancelInvite = async (circle, inv) => {
    if (!confirm(`Annuler l'invitation de ${inv.email} ?`)) return;
    try {
        await axios.delete(`/api/v1/admin/circles/${circle.id}/invitations/${inv.id}`);
        await loadCircles();
    } catch (e) {
        alert('Erreur lors de l\'annulation.');
    }
};

const updateMemberRole = async (circle, member, newRole) => {
  try {
    await axios.put(`/api/v1/admin/circles/${circle.id}/members/${member.user_id}`, { role: newRole });
    member.role = newRole;
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la modification du rôle.');
    await loadCircles();
  }
};

const removeMember = async (circle, member) => {
  if (!confirm(`Retirer ${member.user?.name} du cercle ${circle.name} ?`)) return;
  try {
    await axios.delete(`/api/v1/admin/circles/${circle.id}/members/${member.user_id}`);
    await loadCircles();
  } catch (e) { alert(e.response?.data?.message || 'Erreur lors du retrait.'); }
};

const typeBadge = (t) => ({ open: 'badge-teal', closed: 'badge-red', observer_open: 'badge-blue' }[t] || 'badge-gray');
const typeLabel = (t) => ({ open: 'Cercle Ouvert', closed: 'Cercle Fermé', observer_open: 'Observateurs' }[t] || 'Standard');
</script>

<style scoped>
.circle-desc { font-size: 13px; color: var(--gray-600); min-height: 40px; line-height: 1.5; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
.modal-card { background: white; border-radius: var(--radius-lg); width: 100%; max-width: 480px; box-shadow: var(--shadow-lg); overflow: hidden; animation: modalIn 0.2s ease; }
@keyframes modalIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; color: white; }
.modal-header-indigo { background: var(--blue-700); }
.modal-title { font-size: 15px; font-weight: 600; }
.modal-body { padding: 24px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; border-top: 1px solid var(--gray-100); margin-top: 16px; }

.member-list-compact { max-height: 200px; overflow-y: auto; padding-right: 4px; border: 1px solid var(--gray-100); border-radius: var(--radius-md); background: var(--gray-50); padding: 4px 12px; }
.member-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--gray-100); }
.member-row:last-child { border-bottom: none; }
.member-info { flex: 1; font-size: 12px; display: flex; align-items: center; min-width: 0; }
.member-info .font-medium { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; color: var(--gray-800); }
.member-controls { display: flex; align-items: center; gap: 4px; }

.select-member-role { border: 1px solid var(--gray-200); border-radius: 4px; font-size: 11px; padding: 2px 4px; background: white; width: 100px; }

/* AJAX SEARCH STYLES */
.ajax-results-abs {
    position: absolute; left: 0; right: 0; top: 100%; z-index: 100;
    margin-top: 4px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); max-height: 180px; overflow-y: auto;
    background: white; box-shadow: var(--shadow-lg); animation: slideDown 0.2s ease;
}
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.ajax-item {
    display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; cursor: pointer; border-bottom: 1px solid var(--gray-50); transition: all 0.1s;
}
.ajax-item:last-child { border-bottom: none; }
.ajax-item:hover { background: var(--gray-50); }
.avatar-mini {
    width: 28px; height: 28px; border-radius: 50%; background: var(--blue-100); color: var(--blue-800);
    display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; flex-shrink: 0;
}
.search-input-wrap { position: relative; }
.search-input-wrap .pl-32 { padding-left: 36px; }
.search-input-wrap .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--gray-400); font-size: 14px; }
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.text-strong { font-weight: 600; }
.text-green { color: var(--teal-500); }

.selected-users-wrap { background: var(--gray-50); border: 1px solid var(--gray-100); border-radius: var(--radius-md); padding: 12px; }
.selected-users-list { display: flex; flex-wrap: wrap; gap: 8px; }
.selected-user-tag { background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-full); padding: 4px 10px; display: flex; align-items: center; gap: 8px; box-shadow: var(--shadow-sm); }
.cursor-pointer { cursor: pointer; }
.hover\:text-red:hover { color: var(--red-600); }
.text-orange { color: var(--orange-600); }
.mr-8 { margin-right: 8px; }
.mx-auto { margin-left: auto; margin-right: auto; }
.mb-16 { margin-bottom: 16px; }
.mb-24 { margin-bottom: 24px; }
.text-lg { font-size: 18px; }
.text-dark { color: var(--gray-900); }

.envelope-hitbox { padding: 8px 12px 8px 0; cursor: pointer; transition: transform 0.2s; display: flex; align-items: center; }
.envelope-hitbox:hover { transform: scale(1.2); }

.member-row.is-pending-invite { background: #fff9f2; border-bottom-color: #ffe8d1; }
.badge-orange { background: var(--orange-100); color: var(--orange-800); }

.card-actions-footer { display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid var(--gray-50); padding-top: 14px; margin-top: 16px; }

.premium-grid { display: grid; grid-template-columns: 1fr; gap: 32px; }
@media (min-width: 1024px) {
  .premium-grid { grid-template-columns: 1fr 1fr; }
}

.text-red { color: var(--red-600); }
.py-24 { padding: 24px 0; }
@media (max-width: 1024px) {
  .hide-tablet { display: none; }
}

/* FILTER BAR */
.filter-bar { background: white; border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100); }
.main-search { position: relative; margin-bottom: 20px; }
.main-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gray-400); }
.input-inline { width: 100%; padding: 12px 12px 12px 42px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 14px; background: var(--gray-50); }
.input-inline:focus { border-color: var(--blue-500); outline: none; box-shadow: 0 0 0 3px var(--blue-50); background: white; }

.filter-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
.filter-item { display: flex; flex-direction: column; gap: 6px; }
.filter-item label { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--gray-500); letter-spacing: 0.05em; }
.select-sm { padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 13px; background: var(--gray-50); }
.ml-auto { margin-left: auto; }
</style>

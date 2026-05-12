<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Gestion des Partages</div>
            <div class="hero-subtitle">Visualisez et révoquez les liens d'invitation et les partages par email.</div>
          </div>
          <div class="hero-action">
             <div class="flex gap-16">
               <div class="stat-badge">
                 <div class="stat-icon bg-white/10"><i class="fa-solid fa-link"></i></div>
                 <div class="stat-content text-white">
                   <div class="value">{{ activeLinksCount }}</div>
                   <div class="label">Liens actifs</div>
                 </div>
               </div>
               <div class="stat-badge">
                 <div class="stat-icon bg-white/10"><i class="fa-solid fa-envelope"></i></div>
                 <div class="stat-content text-white">
                   <div class="value">{{ pendingInvitesCount }}</div>
                   <div class="label">Invitations mail</div>
                 </div>
               </div>
             </div>
          </div>
        </div>
      </div>

      <div class="mt-32">
        <!-- NAVIGATION GAUCHE -->
        <div class="shares-filter-grid mb-24">
            <button @click="activeTab = 'links'" 
                    class="premium-nav-item centered" :class="{ active: activeTab === 'links' }">
              <div class="icon-wrap bg-blue-50 text-blue-600">
                <i class="fa-solid fa-link"></i>
              </div>
              <div class="nav-content">
                <div class="nav-title">Liens publics</div>
                <div class="nav-desc">{{ inviteLinks.length }} liens enregistrés</div>
              </div>
            </button>
            <button @click="activeTab = 'invites'" 
                    class="premium-nav-item centered" :class="{ active: activeTab === 'invites' }">
              <div class="icon-wrap bg-indigo-50 text-indigo-600">
                <i class="fa-solid fa-envelope"></i>
              </div>
              <div class="nav-content">
                <div class="nav-title">Invitations mail</div>
                <div class="nav-desc">{{ invitations.length }} envois groupés</div>
              </div>
            </button>
        </div>

        <!-- CONTENU CENTRAL -->
        <div class="shares-content">
          
          <!-- SECTION LIENS D'INVITATION -->
          <div v-if="activeTab === 'links'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-link"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Liens d'invitation (Cercle)</div>
                <div class="pc-header-sub">Liens publics permettant de rejoindre un cercle sans invitation nominative.</div>
              </div>
            </div>
            <div class="pc-body p-0">
              <div class="table-responsive">
                <table class="premium-table">
                  <thead>
                    <tr>
                      <th>Cercle</th>
                      <th>Créé par</th>
                      <th>Utilisations</th>
                      <th>Expiration</th>
                      <th class="text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="link in inviteLinks" :key="link.id" :class="{ 'opacity-50': isExpired(link.expires_at) }">
                      <td>
                        <div class="flex flex-col">
                          <span class="font-bold">{{ link.circle_name }}</span>
                          <span class="text-xs text-muted truncate" style="max-width: 200px">{{ link.url }}</span>
                        </div>
                      </td>
                      <td>{{ link.created_by_name || 'Système' }}</td>
                      <td>
                        <span class="badge badge-blue">{{ link.use_count }}</span>
                      </td>
                      <td>
                        <span :class="isExpired(link.expires_at) ? 'text-red font-bold' : 'text-muted'">
                          {{ formatDate(link.expires_at) }}
                        </span>
                      </td>
                      <td class="text-right">
                        <div class="flex justify-end gap-8 items-center">
                          <transition name="fade">
                            <span v-if="copySuccess" class="text-xxs text-teal-600 font-bold mr-4">Copié !</span>
                          </transition>
                          <button class="btn btn-ghost btn-icon btn-sm" @click="copyToClipboard(link.url)" title="Copier le lien">
                            <i class="fa-solid fa-copy"></i>
                          </button>
                          <button class="btn btn-ghost btn-icon btn-sm text-red" @click="confirmRevokeLink(link)" title="Révoquer">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="inviteLinks.length === 0">
                      <td colspan="5" class="text-center py-32 text-muted">Aucun lien d'invitation actif.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- SECTION INVITATIONS EMAIL -->
          <div v-if="activeTab === 'invites'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-indigo">
              <div class="pc-header-icon"><i class="fa-solid fa-envelope"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Invitations par email</div>
                <div class="pc-header-sub">Invitations nominatives envoyées par email pour rejoindre un cercle.</div>
              </div>
            </div>
            <div class="pc-body p-0">
              <div class="table-responsive">
                <table class="premium-table">
                  <thead>
                    <tr>
                      <th>Email</th>
                      <th>Cercle</th>
                      <th>Rôle</th>
                      <th>Statut</th>
                      <th class="text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="invite in invitations" :key="invite.id">
                      <td>
                        <div class="font-bold">{{ invite.email }}</div>
                        <div class="text-xs text-muted">Invité par {{ invite.invited_by_name }}</div>
                      </td>
                      <td>{{ invite.circle_name }}</td>
                      <td><span class="badge badge-sm badge-blue">{{ invite.role }}</span></td>
                      <td>
                        <span class="badge badge-sm" :class="invite.accepted_at || invite.used_by ? 'badge-teal' : 'badge-amber'">
                          {{ invite.accepted_at || invite.used_by ? 'Acceptée' : 'En attente' }}
                        </span>
                      </td>
                      <td class="text-right">
                        <button class="btn btn-ghost btn-icon btn-sm text-red" @click="confirmRevokeInvite(invite)" title="Révoquer">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="invitations.length === 0">
                      <td colspan="5" class="text-center py-32 text-muted">Aucune invitation email trouvée.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- MODALS DE CONFIRMATION -->
      <div v-if="confirmModal.show" class="modal-overlay" @click.self="confirmModal.show = false">
        <div class="modal-card" style="max-width:400px">
          <div class="modal-header pc-header-red">
            <span class="modal-title">Confirmer la révocation</span>
            <button class="btn btn-ghost btn-icon text-white" @click="confirmModal.show = false"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body text-center">
            <div class="mb-16"><i class="fa-solid fa-triangle-exclamation text-red" style="font-size:32px;"></i></div>
            <p>Voulez-vous vraiment révoquer ce partage ?</p>
            <p class="text-xs text-muted mt-8">Cette action est irréversible et l'accès sera immédiatement coupé.</p>
            <div class="modal-footer" style="justify-content:center; border:none; margin-top:24px;">
              <button class="btn btn-secondary" @click="confirmModal.show = false">Annuler</button>
              <button class="btn btn-danger" @click="executeRevoke" :disabled="revoking">
                <i v-if="revoking" class="fa-solid fa-spinner fa-spin mr-8"></i>
                Confirmer la révocation
              </button>
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

const activeTab = ref('links');
const inviteLinks = ref([]);
const invitations = ref([]);
const loading = ref(true);
const revoking = ref(false);

const confirmModal = ref({
  show: false,
  type: '', // 'link' or 'invite'
  item: null
});

const activeLinksCount = computed(() => inviteLinks.value.filter(l => !isExpired(l.expires_at)).length);
const pendingInvitesCount = computed(() => invitations.value.filter(i => !i.accepted_at).length);

const fetchData = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/shares');
    inviteLinks.value = data.invite_links || [];
    invitations.value = data.email_invitations || [];
  } catch (e) {
    console.error("Error fetching shares", e);
  } finally {
    loading.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return 'Jamais';
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const isExpired = (date) => {
  if (!date) return false;
  return new Date(date) < new Date();
};

const copySuccess = ref(false);
const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text);
  copySuccess.value = true;
  setTimeout(() => copySuccess.value = false, 2000);
};

const confirmRevokeLink = (link) => {
  confirmModal.value = { show: true, type: 'link', item: link };
};

const confirmRevokeInvite = (invite) => {
  confirmModal.value = { show: true, type: 'invite', item: invite };
};

const executeRevoke = async () => {
  revoking.value = true;
  const { type, item } = confirmModal.value;
  try {
    if (type === 'link') {
      await axios.delete(`/api/v1/admin/shares/invite-links/${item.id}`);
      inviteLinks.value = inviteLinks.value.filter(l => l.id !== item.id);
    } else {
      await axios.delete(`/api/v1/admin/shares/invitations/${item.id}`);
      invitations.value = invitations.value.filter(i => i.id !== item.id);
    }
    confirmModal.value.show = false;
  } catch (e) {
    alert("Erreur lors de la révocation.");
  } finally {
    revoking.value = false;
  }
};

onMounted(fetchData);
</script>

<style scoped>
.stat-badge {
  background: rgba(255,255,255,0.08);
  padding: 10px 18px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid rgba(255,255,255,0.12);
  transition: all 0.2s;
}
.stat-badge:hover { background: rgba(255,255,255,0.12); }
.stat-icon {
  width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
  font-size: 16px; color: white;
}
.stat-badge .label { font-size: 10px; text-transform: uppercase; color: white; opacity: 0.8; font-weight: 700; letter-spacing: 0.05em; }
.stat-badge .value { font-size: 18px; font-weight: 800; line-height: 1; color: white; }

.shares-filter-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.premium-nav-item {
  width: 100%; display: flex; align-items: center; gap: 14px; padding: 16px; border-radius: 16px;
  background: white; border: 1px solid var(--gray-200); cursor: pointer; transition: all 0.2s;
  text-align: left;
}
.premium-nav-item.centered {
  text-align: left;
  justify-content: center;
  padding: 18px 24px;
}
.premium-nav-item:hover { border-color: var(--blue-300); transform: translateY(-3px); box-shadow: var(--shadow-md); }
.premium-nav-item.active { border-color: var(--blue-600); background: var(--blue-50); box-shadow: inset 0 0 0 1px var(--blue-600); }
.premium-nav-item .icon-wrap { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.premium-nav-item.centered .icon-wrap { margin-bottom: 0; }
.premium-nav-item .nav-title { font-size: 15px; font-weight: 700; color: var(--gray-900); }
.premium-nav-item .nav-desc { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
.premium-nav-item.active .nav-title { color: var(--blue-800); }

.bg-blue-50 { background-color: var(--blue-50) !important; }
.bg-indigo-50 { background-color: #f0f0ff !important; }

.member-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  border-bottom: 1px solid var(--gray-50);
}

.invite-link-box {
  background: var(--gray-50);
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  padding: 12px;
}

.premium-table {
  width: 100%;
  border-collapse: collapse;
}
.premium-table th {
  text-align: left;
  padding: 12px 16px;
  background: var(--gray-50);
  color: var(--gray-600);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid var(--gray-200);
}
.premium-table td {
  padding: 16px;
  border-bottom: 1px solid var(--gray-100);
  font-size: 14px;
}

.text-red { color: var(--red-600); }
.text-amber { color: var(--amber-600); }
.text-teal-600 { color: var(--teal-600); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Bonjour {{ authStore.user?.name }} 👋</div>
        <div class="page-subtitle">Tableau de bord</div>
      </div>
    </div>

    <div v-if="loading" class="p-24 text-center">Chargement...</div>

    <div class="page-body" v-else>
      
      <!-- STATS BLOCK -->
      <div class="stats-row mb-16" v-if="dashboard.stats">
        <div class="stat-card clickable" @click="$router.push('/decisions?filter=all')">
          <div class="stat-value">{{ dashboard.stats.total }}</div>
          <div class="stat-label">Décisions de mes cercles</div>
        </div>
        <div class="stat-card clickable" @click="$router.push('/decisions?filter=author')">
          <div class="stat-value text-blue-600">{{ dashboard.stats.as_author }}</div>
          <div class="stat-label">Mes Propositions</div>
        </div>
        <div class="stat-card clickable" @click="$router.push('/decisions?filter=animator')">
          <div class="stat-value text-amber-600">{{ dashboard.stats.as_animator }}</div>
          <div class="stat-label">J'Anime</div>
        </div>
        <div class="stat-card clickable" @click="$router.push('/decisions?filter=active')">
          <div class="stat-value text-orange-500">{{ dashboard.stats.in_progress }}</div>
          <div class="stat-label">En Cours</div>
        </div>
        <div class="stat-card clickable" @click="$router.push('/decisions?filter=adopted')">
          <div class="stat-value text-teal-600">{{ dashboard.stats.adopted }}</div>
          <div class="stat-label">Adoptées</div>
        </div>
      </div>
      
      <!-- LIGNE 1 : MES TICKETS ACTIFS (CLARIFICATIONS / OBJECTIONS) -->
      <div class="grid-2 mb-16" v-if="hasActiveTickets">
        
        <!-- Clarifications en cours -->
        <div class="card phase-card phase-clarif" v-if="dashboard.my_clarifications?.length">
          <div class="card-header phase-header-clarif">
            <span class="phase-icon">💬</span>
            <span class="card-title">Clarifications</span>
            <span class="badge badge-amber badge-sm" style="margin-left:auto">{{ dashboard.my_clarifications.length }}</span>
          </div>
          <div class="card-body" style="padding:0">
            <div v-for="fb in dashboard.my_clarifications" :key="fb.id" class="ticket-row" @click="goToDecision(fb.version?.decision_id)">
              <div class="role-bg-mini mr-12" :class="'role-' + getMyRole(fb.version?.decision)" :title="getMyRoleLabel(fb.version?.decision)">
                  {{ getRolePicto(getMyRole(fb.version?.decision)) }}
              </div>
              <div class="ticket-info">
                <div class="text-xs font-semibold">
                  <span class="text-amber-700 mr-4">[{{ fb.version?.decision?.circle?.name }}]</span> 
                  {{ fb.version?.decision?.title }}
                </div>
                <div class="text-xs text-muted truncate mt-4">
                  <span class="font-semibold">{{ getLastMessageAuthor(fb) }}</span>
                  <span class="font-normal text-gray-400 ml-4">({{ getLastMessageDate(fb) }})</span>: 
                  "{{ getLastMessageContent(fb) }}"
                </div>
              </div>
              <div class="ticket-status">
                <span v-if="needsMyAttention(fb)" class="status-dot dot-red" title="Réponse requise !"></span>
                <span v-else class="status-dot dot-green" title="En attente de l'autre"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Objections en cours -->
        <div class="card phase-card phase-objection" v-if="dashboard.my_objections?.length">
          <div class="card-header phase-header-objection">
            <span class="phase-icon">⚠️</span>
            <span class="card-title">Objections</span>
            <span class="badge badge-red badge-sm" style="margin-left:auto">{{ dashboard.my_objections.length }}</span>
          </div>
          <div class="card-body" style="padding:0">
            <div v-for="fb in dashboard.my_objections" :key="fb.id" class="ticket-row" @click="goToDecision(fb.version?.decision_id)">
              <div class="role-bg-mini mr-12" :class="'role-' + getMyRole(fb.version?.decision)" :title="getMyRoleLabel(fb.version?.decision)">
                  {{ getRolePicto(getMyRole(fb.version?.decision)) }}
              </div>
              <div class="ticket-info">
                <div class="text-xs font-semibold">
                  <span class="text-red-700 mr-4">[{{ fb.version?.decision?.circle?.name }}]</span> 
                  {{ fb.version?.decision?.title }}
                </div>
                <div class="text-xs text-muted truncate mt-4">
                  <span class="font-semibold">{{ getLastMessageAuthor(fb) }}</span>
                  <span class="font-normal text-gray-400 ml-4">({{ getLastMessageDate(fb) }})</span>: 
                  "{{ getLastMessageContent(fb) }}"
                </div>
              </div>
              <div class="ticket-status">
                <span v-if="needsMyAttention(fb)" class="status-dot dot-red" title="Réponse requise !"></span>
                <span v-else class="status-dot dot-green" title="En attente de l'autre"></span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- LIGNE 2 : DÉCISIONS REQUISES QUELQUE PART -->
      <div class="grid-3 mb-16">
        
        <!-- Mes décisions (Auteur/Porteur) -->
        <div class="card mb-16">
          <div class="card-header" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border-bottom: 1px solid var(--blue-200);">
            <div class="role-bg-mini mr-8 role-author" title="Porteur">💡</div>
            <span class="card-title text-blue-900">Mes propositions (pilotage)</span>
          </div>
          <div class="card-body" style="padding:0">
            <div v-if="Object.keys(dashboard.my_decisions).length === 0" class="p-16 text-xs text-muted text-center">Vous ne pilotez aucune décision.</div>
            
            <div v-for="(decisions, circleName) in dashboard.my_decisions" :key="circleName">
              <div class="group-header">{{ circleName }}</div>
              <div v-for="d in decisions" :key="d.id" class="decision-mini" :class="'status-' + d.status" @click="goToDecision(d.id)">
                <div class="decision-status-strip" :class="'strip-' + d.status"></div>
                <div class="decision-title">
                  <span>{{ d.title }}</span>
                  <div class="text-xs text-muted mt-2">
                    <span class="font-semibold text-gray-700">Porteur:</span> {{ getPorteur(d) }} 
                    <span v-if="getAnimateur(d)">· <span class="font-semibold text-gray-700">Animateur:</span> {{ getAnimateur(d) }}</span>
                    <br>Version {{ d.current_version?.version_number }} · {{ getStepProgress(d.status) }}
                    <br>Créée le {{ formatDateOnly(d.created_at) }} · Dernière intervention le {{ formatDateOnly(d.updated_at) }}
                  </div>
                </div>
                <div class="badge text-xs" :class="getStatusBadgeClass(d.status)">{{ getStepLabel(d.status) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mes décisions (Animateur) -->
        <div class="card mb-16">
          <div class="card-header" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border-bottom: 1px solid var(--amber-200);">
            <div class="role-bg-mini mr-8 role-animator" title="Animateur">🎭</div>
            <span class="card-title text-amber-900">J'anime (Facilitation)</span>
          </div>
          <div class="card-body" style="padding:0">
            <div v-if="Object.keys(dashboard.my_animated || {}).length === 0" class="p-16 text-xs text-muted text-center">Vous n'animez aucune décision en cours.</div>
            
            <div v-for="(decisions, circleName) in dashboard.my_animated" :key="circleName">
              <div class="group-header">{{ circleName }}</div>
              <div v-for="d in decisions" :key="d.id" class="decision-mini" :class="'status-' + d.status" @click="goToDecision(d.id)">
                <div class="decision-status-strip" :class="'strip-' + d.status"></div>
                <div class="decision-title">
                  <span>{{ d.title }}</span>
                  <div class="text-xs text-muted mt-2">
                    <span class="font-semibold text-gray-700">Porteur:</span> {{ getPorteur(d) }} 
                    <br>Version {{ d.current_version?.version_number }} · {{ getStepProgress(d.status) }}
                    <br>Dernière intervention le {{ formatDateOnly(d.updated_at) }}
                  </div>
                </div>
                <div class="badge text-xs" :class="getStatusBadgeClass(d.status)">{{ getStepLabel(d.status) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Décisions de mes cercles -->
        <div class="card mb-16">
          <div class="card-header" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-bottom: 1px solid var(--teal-200);">
            <div class="role-bg-mini mr-8 role-participant" title="Cercle">👥</div>
            <span class="card-title text-teal-900">À surveiller (Mes Cercles)</span>
          </div>
          <div class="card-body" style="padding:0">
            <div v-if="Object.keys(dashboard.circle_decisions).length === 0" class="p-16 text-xs text-muted text-center">Rien à signaler dans vos cercles.</div>
            
            <div v-for="(decisions, circleName) in dashboard.circle_decisions" :key="circleName">
              <div class="group-header">{{ circleName }}</div>
              <div v-for="d in decisions" :key="d.id" class="decision-mini" :class="'status-' + d.status" @click="goToDecision(d.id)">
                <div class="decision-status-strip" :class="'strip-' + d.status"></div>
                <div class="decision-title">
                  <div style="display:flex; align-items:center; gap:8px;">
                    <span class="role-bg-mini" :class="'role-' + getMyRole(d)" :title="getMyRoleLabel(d)">{{ getRolePicto(getMyRole(d)) }}</span>
                    <span>{{ d.title }}</span>
                  </div>
                  <div class="text-xs text-muted mt-2">
                    <span class="font-semibold text-gray-700">Porteur:</span> {{ getPorteur(d) }} 
                    <span v-if="getAnimateur(d)">· <span class="font-semibold text-gray-700">Animateur:</span> {{ getAnimateur(d) }}</span>
                    <br>Version {{ d.current_version?.version_number }} · {{ getStepProgress(d.status) }}
                    <br>Créée le {{ formatDateOnly(d.created_at) }} · Dernière intervention le {{ formatDateOnly(d.updated_at) }}
                  </div>
                </div>
                <div class="badge text-xs" :class="getStatusBadgeClass(d.status)">{{ getStepLabel(d.status) }}</div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- LIGNE 3 : MES CERCLES RACCOURCIS -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Accès rapide: Mes cercles</span>
        </div>
        <div class="card-body" style="display:flex; gap:16px; flex-wrap:wrap; padding:12px;">
          <div v-if="circleStore.circles.length === 0" class="text-xs text-muted">Aucun cercle rejoint.</div>
          <button v-for="c in circleStore.circles" :key="c.id" class="btn btn-outline btn-sm" @click="$router.push({ name: 'CircleDetail', params: { id: c.id } })">
            ◎ {{ c.name }}
          </button>
        </div>
      </div>

    </div>


  </main>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useCircleStore } from '../stores/circle';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();
const circleStore = useCircleStore();

const loading = ref(true);
const dashboard = ref({ my_decisions: {}, my_animated: {}, circle_decisions: {}, my_clarifications: [], my_objections: [], stats: null });

const hasActiveTickets = computed(() => dashboard.value.my_clarifications?.length > 0 || dashboard.value.my_objections?.length > 0);

onMounted(async () => {
    circleStore.fetchCircles();
    await fetchDashboard();
});

const fetchDashboard = async () => {
    try {
        const { data } = await axios.get('/api/v1/dashboard');
        dashboard.value = data;
    } catch (e) {
        console.error("Dashboard error", e);
    } finally {
        loading.value = false;
    }
};

const goToDecision = (id) => {
    if(id) router.push({ name: 'DecisionDetail', params: { id } });
};

const getStepProgress = (status) => {
    switch(status) {
        case 'draft': return 'Étape 1/5';
        case 'clarification': return 'Étape 2/5';
        case 'reaction': return 'Étape 3/5';
        case 'objection': return 'Étape 4/5';
        case 'adopted': return 'Adopté (5/5)';
        case 'revision': return 'En révision';
        default: return '';
    }
};

const getStepLabel = (status) => {
    const labels = {
        draft: 'Brouillon', clarification: 'Clarification', reaction: 'Réactions',
        objection: 'Objections', adopted: 'Adopté', revision: 'Révision'
    };
    return labels[status] || status;
};

const needsMyAttention = (fb) => {
    if (!fb.messages || fb.messages.length === 0) return false;
    const lastMsg = fb.messages[fb.messages.length - 1];
    return lastMsg.author_id !== authStore.user?.id;
};

const getLastMessageAuthor = (fb) => {
    if (!fb.messages || fb.messages.length === 0) return 'Vous';
    const lastMsg = fb.messages[fb.messages.length - 1];
    if (lastMsg.author_id === authStore.user?.id) return 'Vous';
    return lastMsg.author?.name || 'Inconnu';
};

const getLastMessageContent = (fb) => {
    if (!fb.messages || fb.messages.length === 0) return fb.content;
    const lastMsg = fb.messages[fb.messages.length - 1];
    return lastMsg.content;
};

const getPorteur = (d) => {
    const p = d.participants?.find(x => x.role === 'author');
    return p?.user?.name || 'Inconnu';
};

const formatDateStr = (isoString) => {
    if(!isoString) return '';
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    }).format(new Date(isoString));
};

const formatDateOnly = (isoString) => {
    if(!isoString) return '';
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    }).format(new Date(isoString));
};

const getLastMessageDate = (fb) => {
    if (!fb.messages || fb.messages.length === 0) return formatDateStr(fb.created_at);
    const lastMsg = fb.messages[fb.messages.length - 1];
    return formatDateStr(lastMsg.created_at);
};

const getAnimateur = (d) => {
    const animators = d.participants?.filter(x => x.role === 'animator');
    if (!animators || animators.length === 0) return '';
    return animators.map(a => a.user?.name).join(', ');
};

const getStatusBadgeClass = (status) => {
    switch(status) {
        case 'draft':         return 'badge-gray';
        case 'clarification': return 'badge-amber';
        case 'reaction':      return 'badge-blue';
        case 'objection':     return 'badge-red';
        case 'adopted':       return 'badge-teal';
        case 'revision':      return 'badge-orange';
        default: return 'badge-gray';
    }
};

const getMyRole = (decision) => {
    if(!decision || !authStore.user) return 'participant';
    const p = decision.participants?.find(x => x.user_id === authStore.user.id);
    return p?.role || 'participant';
};

const getRolePicto = (role) => {
    const icons = { author: '💡', animator: '🎭', participant: '👥', observer: '👁️' };
    return icons[role] || '👥';
};

const getMyRoleLabel = (decision) => {
    const role = getMyRole(decision);
    const labels = { author: 'Porteur', animator: 'Animateur', participant: 'Participant', observer: 'Observateur' };
    return labels[role] || 'Participant';
};
</script>

<style scoped>
.grid-2 { display: grid; gap: 16px; grid-template-columns: 1fr; }
.grid-3 { display: grid; gap: 16px; grid-template-columns: 1fr; }
@media(min-width: 800px) { 
  .grid-2 { grid-template-columns: 1fr 1fr; } 
  .grid-3 { grid-template-columns: 1fr 1fr 1fr; } 
}

/* Stats Block */
.stats-row { 
  display: flex; gap: 16px; flex-wrap: wrap; 
}
.stat-card {
  flex: 1; min-width: 140px; background: white; padding: 20px 16px;
  border-radius: var(--radius-lg); border: 1px solid var(--gray-200);
  box-shadow: var(--shadow-sm); transition: all 0.2s;
  text-align: center; position: relative;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.stat-card.clickable { cursor: pointer; }
.stat-card.clickable:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--blue-200); }
.stat-value { font-size: 28px; font-weight: 800; color: var(--gray-800); font-family: var(--font-display); line-height: 1; margin-bottom: 4px; z-index: 1;}
.stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--gray-500); z-index: 1; }
.mb-16 { margin-bottom: 16px; }

/* Groupes de cercles */
.group-header {
  background: var(--gray-50); padding: 6px 12px; font-size: 11px; font-weight: 700; color: var(--gray-500); text-transform: uppercase; border-bottom: 1px solid var(--gray-100); border-top: 1px solid var(--gray-100);
}

.decision-mini {
  display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 12px 14px;
  cursor: pointer; transition: background 0.1s; border-bottom: 1px solid var(--gray-100);
}
.decision-mini:last-child { border-bottom: none; }
.decision-mini:hover { background: var(--blue-50); }
.decision-title { font-size: 13px; font-weight: 500; color: var(--gray-800); }

/* Tickets */
.ticket-row {
  display: flex; align-items: center; justify-content: space-between; padding: 12px 16px;
  border-bottom: 1px solid var(--gray-100); cursor: pointer; transition: background 0.1s;
}
.ticket-row:last-child { border-bottom: none; }
.ticket-row:hover { background: var(--gray-50); }
.ticket-info { flex: 1; overflow: hidden; }
.truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90%; }

.status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
.dot-red { background-color: var(--red-500); box-shadow: 0 0 0 4px var(--red-100); }
.dot-green { background-color: var(--teal-500); box-shadow: 0 0 0 4px var(--teal-100); }
.dot-gray { background-color: var(--gray-300); }

/* Phase card headers */
.phase-card { overflow: hidden; }
.phase-header-clarif { 
  background: linear-gradient(135deg, #fffbeb, #fef3c7);
  border-bottom: 2px solid #f59e0b;
}
.phase-header-objection { 
  background: linear-gradient(135deg, #fff1f2, #ffe4e6);
  border-bottom: 2px solid #ef4444;
}
.phase-icon { font-size: 16px; margin-right: 8px; }

/* Phase accent strips on decision cards */
.decision-mini { position: relative; padding-left: 18px; }
.decision-status-strip {
  position: absolute; left: 0; top: 0; bottom: 0;
  width: 4px; flex-shrink: 0;
}
.strip-draft        { background: var(--gray-300); }
.strip-clarification { background: var(--amber-400); }
.strip-reaction     { background: var(--blue-400); }
.strip-objection    { background: var(--red-500); }
.strip-revision     { background: var(--orange-400); }
.strip-adopted      { background: var(--teal-500); }

/* Role Mini Pictos */
.role-bg-mini {
  width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 13px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1.5px solid transparent;
  flex-shrink: 0;
}
.role-author { border-color: var(--blue-500); background: var(--blue-50); }
.role-animator { border-color: var(--amber-500); background: var(--amber-50); }
.role-participant { border-color: var(--teal-500); background: var(--teal-50); }
.role-observer { border-color: var(--gray-400); background: var(--gray-50); }

.ticket-status { display: flex; align-items: center; gap: 12px; }
</style>

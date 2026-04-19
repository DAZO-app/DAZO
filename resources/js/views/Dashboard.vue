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
        <div class="stat-card v-total clickable" @click="$router.push('/decisions?filter=all')">
          <div class="stat-icon-wrap">📊</div>
          <div class="ring-wrap">
            <svg width="72" height="72" viewBox="0 0 72 72">
              <circle class="ring-bg" cx="36" cy="36" r="28"/>
              <circle class="ring-fg" cx="36" cy="36" r="28" :stroke-dasharray="175.9" stroke-dashoffset="0" style="stroke: rgba(255,255,255,0.35);"/>
            </svg>
            <div class="ring-center">{{ dashboard.stats.total }}</div>
          </div>
          <div class="stat-label">Décisions</div>
        </div>
        <div class="stat-card v-proposals clickable" @click="$router.push('/decisions?filter=author')">
          <div class="stat-icon-wrap">📣</div>
          <div class="stat-number-simple">{{ dashboard.stats.as_author }}</div>
          <div class="stat-label">Proposées</div>
        </div>
        <div class="stat-card v-anime clickable" @click="$router.push('/decisions?filter=animator')">
          <div class="stat-icon-wrap">🎭</div>
          <div class="stat-number-simple">{{ dashboard.stats.as_animator }}</div>
          <div class="stat-label">J'Anime</div>
        </div>
        <div class="stat-card v-active clickable" @click="$router.push('/decisions?filter=active')">
          <div class="stat-icon-wrap">🔄</div>
          <div class="stat-number-simple">{{ dashboard.stats.in_progress }}</div>
          <div class="stat-label">En Cours</div>
        </div>
        <div class="stat-card v-adopted clickable" @click="$router.push('/decisions?filter=adopted')">
          <div class="stat-icon-wrap">✅</div>
          <div class="ring-wrap">
            <svg width="72" height="72" viewBox="0 0 72 72">
              <circle class="ring-bg" cx="36" cy="36" r="28"/>
              <circle class="ring-fg" cx="36" cy="36" r="28" :stroke-dasharray="175.9" :stroke-dashoffset="ringOffset(dashboard.stats.adopted, dashboard.stats.total)"/>
            </svg>
            <div class="ring-center">{{ dashboard.stats.adopted }}</div>
          </div>
          <div class="stat-label">Adoptées</div>
        </div>
      </div>
      
      <!-- LIGNE 1 : MES TICKETS ACTIFS (CLARIFICATIONS / OBJECTIONS) -->
      <div class="grid-2 mb-16">
        
        <!-- Clarifications en cours -->
        <div class="premium-card">
          <div class="pc-header pc-header-amber">
            <div class="pc-header-icon">💬</div>
            <div class="pc-header-content">
              <div class="pc-header-title">Clarifications actives</div>
              <div class="pc-header-sub">Tickets en attente de réponse</div>
            </div>
          </div>
          <div class="pc-body">
            <div v-if="!dashboard.my_clarifications?.length" class="pc-empty">
              <img src="/DAZO-picto-carre-gris.svg" class="pc-empty-img">
              <span>Aucune clarification en cours.</span>
            </div>
            <div v-for="fb in dashboard.my_clarifications" :key="fb.id" class="decision-item" @click="goToDecision(fb.version?.decision_id)">
              <div class="role-bg-mini" :class="'role-' + getMyRole(fb.version?.decision)">
                {{ getRolePicto(getMyRole(fb.version?.decision)) }}
              </div>
              <div class="decision-item-main">
                <div class="decision-title">
                  <span class="version-pill" v-if="fb.version">v{{ fb.version.version_number }}</span>
                  <span v-if="fb.version?.decision?.current_version?.attachments?.length" title="Contient des pièces jointes" style="margin-right: 4px; opacity: 0.7;">📎</span>
                  {{ fb.version?.decision?.title }}
                </div>
                <div class="decision-people">
                  <span class="text-author">{{ fb.version?.decision?.circle?.name }}</span>
                </div>
                <div class="ticket-msg" v-if="getLastMessageContent(fb)">
                  <span class="ticket-msg-author">{{ getLastMessageAuthor(fb) }}</span> : "{{ getLastMessageContent(fb) }}"
                </div>
              </div>
              <div class="decision-end-actions">
                <span :class="needsMyAttention(fb) ? 'badge badge-red' : 'badge badge-teal'">
                  {{ needsMyAttention(fb) ? 'À vous' : 'En attente' }}
                </span>
                <div class="text-xs text-muted mt-4">{{ getLastMessageDate(fb) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Objections en cours -->
        <div class="premium-card">
          <div class="pc-header pc-header-red">
            <div class="pc-header-icon">⚠️</div>
            <div class="pc-header-content">
              <div class="pc-header-title">Objections actives</div>
              <div class="pc-header-sub">Tickets en attente de réponse</div>
            </div>
          </div>
          <div class="pc-body">
            <div v-if="!dashboard.my_objections?.length" class="pc-empty">
              <img src="/DAZO-picto-carre-gris.svg" class="pc-empty-img">
              <span>Aucune objection en cours.</span>
            </div>
            <div v-for="fb in dashboard.my_objections" :key="fb.id" class="decision-item" @click="goToDecision(fb.version?.decision_id)">
              <div class="role-bg-mini" :class="'role-' + getMyRole(fb.version?.decision)">
                {{ getRolePicto(getMyRole(fb.version?.decision)) }}
              </div>
              <div class="decision-item-main">
                <div class="decision-title">
                  <span class="version-pill" v-if="fb.version">v{{ fb.version.version_number }}</span>
                  <span v-if="fb.version?.decision?.current_version?.attachments?.length" title="Contient des pièces jointes" style="margin-right: 4px; opacity: 0.7;">📎</span>
                  {{ fb.version?.decision?.title }}
                </div>
                <div class="decision-people">
                  <span class="text-author">{{ fb.version?.decision?.circle?.name }}</span>
                </div>
                <div class="ticket-msg" v-if="getLastMessageContent(fb)">
                  <span class="ticket-msg-author">{{ getLastMessageAuthor(fb) }}</span> : "{{ getLastMessageContent(fb) }}"
                </div>
              </div>
              <div class="decision-end-actions">
                <span :class="needsMyAttention(fb) ? 'badge badge-red' : 'badge badge-teal'">
                  {{ needsMyAttention(fb) ? 'À vous' : 'En attente' }}
                </span>
                <div class="text-xs text-muted mt-4">{{ getLastMessageDate(fb) }}</div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- LIGNE 2 : DÉCISIONS -->
      <div class="grid-3 mb-16">
        
        <!-- Mes propositions -->
        <div class="premium-card">
          <div class="pc-header pc-header-blue">
            <div class="pc-header-icon">📣</div>
            <div class="pc-header-content">
              <div class="pc-header-title">Mes propositions</div>
              <div class="pc-header-sub">Décisions dont je suis porteur</div>
            </div>
          </div>
          <div class="pc-body">
            <div v-if="Object.keys(dashboard.my_decisions).length === 0" class="pc-empty">
              <img src="/DAZO-picto-carre-gris.svg" class="pc-empty-img">
              <span>Vous ne pilotez aucune décision.</span>
            </div>
            <template v-for="(decisions, circleName) in dashboard.my_decisions" :key="circleName">
              <DecisionListItem
                v-for="d in decisions" :key="d.id" :decision="d"
                @click="goToDecision"
                @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
                @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
              />
            </template>
          </div>
        </div>

        <!-- J'anime -->
        <div class="premium-card">
          <div class="pc-header pc-header-amber">
            <div class="pc-header-icon">🎭</div>
            <div class="pc-header-content">
              <div class="pc-header-title">J'anime</div>
              <div class="pc-header-sub">Décisions dont je suis facilitateur</div>
            </div>
          </div>
          <div class="pc-body">
            <div v-if="Object.keys(dashboard.my_animated || {}).length === 0" class="pc-empty">
              <img src="/DAZO-picto-carre-gris.svg" class="pc-empty-img">
              <span>Vous n'animez aucune décision en cours.</span>
            </div>
            <template v-for="(decisions, circleName) in dashboard.my_animated" :key="circleName">
              <DecisionListItem
                v-for="d in decisions" :key="d.id" :decision="d"
                @click="goToDecision"
                @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
                @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
              />
            </template>
          </div>
        </div>

        <!-- Mes cercles à surveiller -->
        <div class="premium-card">
          <div class="pc-header pc-header-teal">
            <div class="pc-header-icon">👥</div>
            <div class="pc-header-content">
              <div class="pc-header-title">À surveiller</div>
              <div class="pc-header-sub">Décisions actives dans mes cercles</div>
            </div>
          </div>
          <div class="pc-body">
            <div v-if="Object.keys(dashboard.circle_decisions).length === 0" class="pc-empty">
              <img src="/DAZO-picto-carre-gris.svg" class="pc-empty-img">
              <span>Rien à signaler dans vos cercles.</span>
            </div>
            <template v-for="(decisions, circleName) in dashboard.circle_decisions" :key="circleName">
              <DecisionListItem
                v-for="d in decisions" :key="d.id" :decision="d"
                @click="goToDecision"
                @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
                @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
              />
            </template>
          </div>
        </div>

      </div>

      <!-- LIGNE 3 : MES CERCLES & CATÉGORIES -->
      <div class="grid-2">
        <div class="premium-card">
          <div class="pc-header pc-header-indigo">
            <div class="pc-header-icon">◎</div>
            <div class="pc-header-content">
              <div class="pc-header-title">Mes cercles</div>
              <div class="pc-header-sub">{{ circleStore.circles.length }} cercle(s) rejoint(s)</div>
            </div>
          </div>
          <div class="pc-body pc-chips">
            <div v-if="circleStore.circles.length === 0" class="pc-empty">
              <img src="/DAZO-picto-carre-gris.svg" class="pc-empty-img">
              <span>Aucun cercle rejoint.</span>
            </div>
            <button v-for="c in circleStore.circles" :key="c.id" class="chip chip-blue" @click="$router.push({ name: 'CircleDetail', params: { id: c.id } })">
              ◎ {{ c.name }}
            </button>
          </div>
        </div>

        <div class="premium-card">
          <div class="pc-header pc-header-purple">
            <div class="pc-header-icon">🗂️</div>
            <div class="pc-header-content">
              <div class="pc-header-title">Catégories</div>
              <div class="pc-header-sub">{{ (dashboard.categories || []).length }} catégorie(s) disponible(s)</div>
            </div>
          </div>
          <div class="pc-body pc-chips">
            <div v-if="!dashboard.categories || dashboard.categories.length === 0" class="pc-empty">
              <img src="/DAZO-picto-carre-gris.svg" class="pc-empty-img">
              <span>Aucune catégorie.</span>
            </div>
            <button v-for="c in dashboard.categories" :key="c.id" class="chip chip-purple" @click="$router.push({ name: 'DecisionList', query: { category: c.id } })">
              {{ c.name }}
            </button>
          </div>
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
import DecisionListItem from '../components/DecisionListItem.vue';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();
const circleStore = useCircleStore();

const loading = ref(true);
const dashboard = ref({ my_decisions: {}, my_animated: {}, circle_decisions: {}, my_clarifications: [], my_objections: [], stats: null, categories: [] });

const hasActiveTickets = computed(() => dashboard.value.my_clarifications?.length > 0 || dashboard.value.my_objections?.length > 0);

const ringOffset = (value, total) => {
    const pct = total > 0 ? Math.min(1, Math.max(0, value / total)) : 0;
    return 175.9 * (1 - pct);
};

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
    const icons = { author: '📣', animator: '🎭', participant: '👥', observer: '👁️' };
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
.stats-row { display: flex; gap: 14px; flex-wrap: wrap; }
.stat-card {
  flex: 1; min-width: 130px; border-radius: 14px; padding: 18px 14px 14px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.12); transition: all 0.2s;
  position: relative; overflow: hidden;
  display: flex; flex-direction: column; align-items: center; gap: 6px; cursor: pointer;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 32px rgba(0,0,0,0.18); }
.stat-card.v-total    { background: linear-gradient(135deg, #1e3a8a, #3b82f6); }
.stat-card.v-proposals { background: linear-gradient(135deg, #1d4ed8, #60a5fa); }
.stat-card.v-anime    { background: linear-gradient(135deg, #92400e, #f59e0b); }
.stat-card.v-active   { background: linear-gradient(135deg, #9a3412, #f97316); }
.stat-card.v-adopted  { background: linear-gradient(135deg, #115e59, #14b8a6); }
.stat-card::after { content: ''; position: absolute; right: -24px; bottom: -24px; width: 90px; height: 90px; border-radius: 50%; background: rgba(255,255,255,0.07); }
.stat-icon-wrap { font-size: 16px; padding: 7px; border-radius: 50%; background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; align-self: flex-end; position: relative; z-index: 1; }
.ring-wrap { position: relative; width: 72px; height: 72px; }
.ring-wrap svg { transform: rotate(-90deg); }
.ring-bg { fill: none; stroke: rgba(255,255,255,0.15); stroke-width: 7; }
.ring-fg { fill: none; stroke: rgba(255,255,255,0.85); stroke-width: 7; stroke-linecap: round; transition: stroke-dashoffset 0.6s cubic-bezier(0.4,0,0.2,1); }
.ring-center { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 900; color: white; font-family: var(--font-display); }
.stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: rgba(255,255,255,0.7); }
.mb-16 { margin-bottom: 16px; }

/* Styles DecisionListItem (dupliqués pour cohérence dashboard) */
.decision-item { padding: 14px 18px; border-bottom: 1px solid var(--gray-100); cursor: pointer; transition: background 0.1s; display: flex; align-items: flex-start; gap: 12px; }
.decision-item:last-child { border-bottom: none; }
.decision-item:hover { background: var(--gray-50); }
.decision-item-main { flex: 1; min-width: 0; }
.decision-title { font-size: 13px; font-weight: 500; color: var(--gray-900); margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
.decision-people { font-size: 12px; color: var(--gray-600); margin-bottom: 6px; font-weight: 500; }
.text-author { color: var(--gray-700); }
.version-pill { display: inline-flex; align-items: center; justify-content: center; font-family: var(--font-mono); font-size: 11px; background: var(--gray-100); color: var(--gray-600); padding: 2px 6px; border-radius: var(--radius-sm); border: 1px solid var(--gray-200); position: relative; top: -1px; }

.role-bg-mini {
  width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 18px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1.5px solid transparent;
  flex-shrink: 0; margin-top: 2px;
}
.role-author { border-color: var(--blue-500); background: var(--blue-50); }
.role-animator { border-color: var(--amber-500); background: var(--amber-50); }
.role-participant { border-color: var(--teal-500); background: var(--teal-50); }
.role-observer { border-color: var(--gray-400); background: var(--gray-50); }

.decision-end-actions { display: flex; flex-direction: column; gap: 4px; align-items: flex-end; }

/* Stats cards sans rings */
.stat-number-simple { font-size: 32px; font-weight: 900; color: white; font-family: var(--font-display); margin: 6px 0; line-height: 1; }

.ticket-msg { font-size: 12px; color: var(--gray-500); line-height: 1.4; font-style: italic; margin-top: 2px; }
.ticket-msg-author { font-weight: 600; color: var(--gray-700); }

/* Chips */
.chip {
  font-size: 12px; font-weight: 600; border-radius: 8px; padding: 6px 14px;
  border: 1.5px solid transparent; cursor: pointer; transition: all 0.15s;
  display: inline-flex; align-items: center; gap: 6px;
}
.chip:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.chip-blue   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.chip-blue:hover { background: #dbeafe; }
.chip-purple { background: #faf5ff; color: #7c3aed; border-color: #ddd6fe; }
.chip-purple:hover { background: #ede9fe; }

/* États vides premium */
.pc-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  position: relative;
  text-align: center;
  min-height: 180px;
}
.pc-empty-img {
  width: 180px;
  height: 180px;
  opacity: 0.6;
  pointer-events: none;
}
.pc-empty span {
  position: absolute;
  width: 90%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 15px;
  font-weight: 800;
  color: #000;
  line-height: 1.5;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}

.pc-chips {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
  padding: 20px;
}
</style>

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
          <div class="ring-wrap">
            <svg width="72" height="72" viewBox="0 0 72 72">
              <circle class="ring-bg" cx="36" cy="36" r="28"/>
              <circle class="ring-fg" cx="36" cy="36" r="28" :stroke-dasharray="175.9" :stroke-dashoffset="ringOffset(dashboard.stats.as_author, dashboard.stats.total)"/>
            </svg>
            <div class="ring-center">{{ dashboard.stats.as_author }}</div>
          </div>
          <div class="stat-label">Proposées</div>
        </div>
        <div class="stat-card v-anime clickable" @click="$router.push('/decisions?filter=animator')">
          <div class="stat-icon-wrap">🎭</div>
          <div class="ring-wrap">
            <svg width="72" height="72" viewBox="0 0 72 72">
              <circle class="ring-bg" cx="36" cy="36" r="28"/>
              <circle class="ring-fg" cx="36" cy="36" r="28" :stroke-dasharray="175.9" :stroke-dashoffset="ringOffset(dashboard.stats.as_animator, dashboard.stats.total)"/>
            </svg>
            <div class="ring-center">{{ dashboard.stats.as_animator }}</div>
          </div>
          <div class="stat-label">J'Anime</div>
        </div>
        <div class="stat-card v-active clickable" @click="$router.push('/decisions?filter=active')">
          <div class="stat-icon-wrap">🔄</div>
          <div class="ring-wrap">
            <svg width="72" height="72" viewBox="0 0 72 72">
              <circle class="ring-bg" cx="36" cy="36" r="28"/>
              <circle class="ring-fg" cx="36" cy="36" r="28" :stroke-dasharray="175.9" :stroke-dashoffset="ringOffset(dashboard.stats.in_progress, dashboard.stats.total)"/>
            </svg>
            <div class="ring-center">{{ dashboard.stats.in_progress }}</div>
          </div>
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
      <div class="grid-2 mb-16" v-if="hasActiveTickets">
        
        <!-- Clarifications en cours -->
        <div class="premium-card" v-if="dashboard.my_clarifications?.length">
          <div class="pc-header pc-header-amber">
            <div class="pc-header-icon">💬</div>
            <div class="pc-header-content">
              <div class="pc-header-title">Clarifications actives</div>
              <div class="pc-header-sub">{{ dashboard.my_clarifications.length }} fil(s) en cours</div>
            </div>
          </div>
          <div class="pc-body">
            <div v-for="fb in dashboard.my_clarifications" :key="fb.id" class="ticket-item" @click="goToDecision(fb.version?.decision_id)">
              <div class="ticket-role" :class="'role-' + getMyRole(fb.version?.decision)">{{ getRolePicto(getMyRole(fb.version?.decision)) }}</div>
              <div class="ticket-content">
                <div class="ticket-circle">{{ fb.version?.decision?.circle?.name }}</div>
                <div class="ticket-title">{{ fb.version?.decision?.title }}</div>
                <div class="ticket-msg" v-if="getLastMessageContent(fb)">
                  <span class="ticket-msg-author">{{ getLastMessageAuthor(fb) }}</span> : "{{ getLastMessageContent(fb) }}"
                </div>
              </div>
              <div class="ticket-indicator">
                <span v-if="needsMyAttention(fb)" class="alert-dot dot-red"></span>
                <span v-else class="alert-dot dot-green"></span>
                <span v-if="needsMyAttention(fb)" class="alert-text alert-text-red">À vous</span>
                <span v-else class="alert-text alert-text-green">En attente</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Objections en cours -->
        <div class="premium-card" v-if="dashboard.my_objections?.length">
          <div class="pc-header pc-header-red">
            <div class="pc-header-icon">⚠️</div>
            <div class="pc-header-content">
              <div class="pc-header-title">Objections actives</div>
              <div class="pc-header-sub">{{ dashboard.my_objections.length }} fil(s) en cours</div>
            </div>
          </div>
          <div class="pc-body">
            <div v-for="fb in dashboard.my_objections" :key="fb.id" class="ticket-item" @click="goToDecision(fb.version?.decision_id)">
              <div class="ticket-role" :class="'role-' + getMyRole(fb.version?.decision)">{{ getRolePicto(getMyRole(fb.version?.decision)) }}</div>
              <div class="ticket-content">
                <div class="ticket-circle">{{ fb.version?.decision?.circle?.name }}</div>
                <div class="ticket-title">{{ fb.version?.decision?.title }}</div>
                <div class="ticket-msg" v-if="getLastMessageContent(fb)">
                  <span class="ticket-msg-author">{{ getLastMessageAuthor(fb) }}</span> : "{{ getLastMessageContent(fb) }}"
                </div>
              </div>
              <div class="ticket-indicator">
                <span v-if="needsMyAttention(fb)" class="alert-dot dot-red"></span>
                <span v-else class="alert-dot dot-green"></span>
                <span v-if="needsMyAttention(fb)" class="alert-text alert-text-red">À vous</span>
                <span v-else class="alert-text alert-text-green">En attente</span>
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
            <div v-for="(decisions, circleName) in dashboard.my_decisions" :key="circleName">
              <div class="group-header">{{ circleName }}</div>
              <DecisionListItem
                v-for="d in decisions" :key="d.id" :decision="d"
                @click="goToDecision"
                @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
                @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
              />
            </div>
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
            <div v-for="(decisions, circleName) in dashboard.my_animated" :key="circleName">
              <div class="group-header">{{ circleName }}</div>
              <DecisionListItem
                v-for="d in decisions" :key="d.id" :decision="d"
                @click="goToDecision"
                @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
                @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
              />
            </div>
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
            <div v-for="(decisions, circleName) in dashboard.circle_decisions" :key="circleName">
              <div class="group-header">{{ circleName }}</div>
              <DecisionListItem
                v-for="d in decisions" :key="d.id" :decision="d"
                @click="goToDecision"
                @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
                @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
              />
            </div>
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

/* ===== PREMIUM CARDS ===== */
.premium-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
  border: 1px solid rgba(255,255,255,0.8);
  display: flex; flex-direction: column;
  margin-bottom: 0;
}

/* Card Header */
.pc-header {
  display: flex; align-items: center; gap: 14px; padding: 16px 20px;
  position: relative; overflow: hidden;
}
.pc-header::after {
  content: ''; position: absolute;
  right: -30px; top: -30px; width: 100px; height: 100px;
  border-radius: 50%; background: rgba(255,255,255,0.08);
}
.pc-header-icon {
  font-size: 22px; width: 46px; height: 46px; border-radius: 12px;
  background: rgba(255,255,255,0.2); border: 1.5px solid rgba(255,255,255,0.35);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: relative; z-index: 1;
}
.pc-header-content { position: relative; z-index: 1; }
.pc-header-title { font-size: 14px; font-weight: 800; color: white; line-height: 1.2; }
.pc-header-sub { font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 2px; }

.pc-header-blue    { background: linear-gradient(135deg, #1e40af, #3b82f6); }
.pc-header-amber   { background: linear-gradient(135deg, #92400e, #f59e0b); }
.pc-header-red     { background: linear-gradient(135deg, #991b1b, #ef4444); }
.pc-header-teal    { background: linear-gradient(135deg, #115e59, #14b8a6); }
.pc-header-indigo  { background: linear-gradient(135deg, #312e81, #6366f1); }
.pc-header-purple  { background: linear-gradient(135deg, #581c87, #a855f7); }

/* Card Body */
.pc-body { flex: 1; }
.pc-body.pc-chips { padding: 16px 20px; display: flex; flex-wrap: wrap; gap: 10px; align-content: flex-start; min-height: 80px; }

/* Empty State */
.pc-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 28px 16px; gap: 10px; }
.pc-empty-img { height: 44px; opacity: 0.18; }
.pc-empty span { font-size: 12px; color: #94a3b8; text-align: center; }

/* Group separator */
.group-header { background: #f8fafc; padding: 6px 16px; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }

/* Ticket items */
.ticket-item {
  display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px;
  border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background 0.1s;
}
.ticket-item:last-child { border-bottom: none; }
.ticket-item:hover { background: #f8fafc; }
.ticket-role {
  width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
  font-size: 18px; flex-shrink: 0; border: 1.5px solid transparent;
}
.ticket-role.role-author    { background: #eff6ff; border-color: #bfdbfe; }
.ticket-role.role-animator  { background: #fffbeb; border-color: #fde68a; }
.ticket-role.role-participant { background: #f0fdfa; border-color: #99f6e4; }
.ticket-role.role-observer  { background: #f9fafb; border-color: #e5e7eb; }
.ticket-content { flex: 1; overflow: hidden; }
.ticket-circle { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px; }
.ticket-title { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ticket-msg { font-size: 11px; color: #64748b; font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ticket-msg-author { font-weight: 600; font-style: normal; color: #475569; }
.ticket-indicator { display: flex; flex-direction: column; align-items: center; gap: 4px; padding-top: 2px; flex-shrink: 0; }
.alert-dot { width: 10px; height: 10px; border-radius: 50%; }
.dot-red   { background: #ef4444; box-shadow: 0 0 0 3px #fee2e2; }
.dot-green { background: #14b8a6; box-shadow: 0 0 0 3px #ccfbf1; }
.alert-text { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
.alert-text-red   { color: #ef4444; }
.alert-text-green { color: #14b8a6; }

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
</style>

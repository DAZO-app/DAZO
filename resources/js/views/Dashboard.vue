<template>
  <main class="main">

    <div v-if="loading" class="p-24 text-center">Chargement...</div>

    <div class="page-body" v-else>
      <div class="hero-card">
        <div class="hero-flex">
          <div class="hero-main-identity">
            <img v-if="configStore.hasCustomLogo" :src="configStore.customLogoUrl" alt="Logo" class="hero-custom-logo" />
            <div>
              <div class="hero-title">{{ configStore.appName !== 'DAZO' ? configStore.appName : 'Tableau de bord' }}</div>
              <div class="hero-user-line">Bonjour {{ authStore.user?.name }} <i class="fa-solid fa-face-smile"></i></div>
              <div class="hero-subtitle">{{ new Date().toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</div>
            </div>
          </div>
          <div class="hero-action">
             <button class="btn btn-secondary" @click="$router.push('/decisions/create')">
               <i class="fa-solid fa-plus"></i> Nouvelle décision
             </button>
          </div>
        </div>
      </div>

      
      <!-- MODULAR DASHBOARD CONTENT -->      <!-- MODULAR DASHBOARD CONTENT -->
      <div class="dashboard-rows" style="display: flex; flex-direction: column; gap: 16px;">
        <div v-for="(row, rowIdx) in dashboardRows" :key="rowIdx" class="dashboard-grid">
          <div v-for="widget in row" :key="widget.id" :class="getWidgetClass(widget.width)">
          
            <!-- STATS BLOCK -->
            <div v-if="widget.id === 'stats'" class="widget-stats">
              <div class="stats-row" v-if="dashboard.stats">
                <div class="stat-card v-total clickable" @click="$router.push('/decisions')">
                  <div class="stat-icon-part">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-chart-pie"></i></div>
                  </div>
                  <div class="stat-info-part">
                    <div class="ring-wrap">
                      <svg width="72" height="72" viewBox="0 0 72 72">
                        <circle class="ring-bg" cx="36" cy="36" r="28"/>
                        <circle class="ring-fg" cx="36" cy="36" r="28" :stroke-dasharray="175.9" stroke-dashoffset="0" style="stroke: rgba(255,255,255,0.35);"/>
                      </svg>
                      <div class="ring-center">{{ dashboard.stats.total }}</div>
                    </div>
                    <div class="stat-label">Décisions</div>
                  </div>
                </div>

                <div class="stat-card v-proposals clickable" @click="$router.push({ path: '/decisions', query: { role: 'author' } })">
                  <div class="stat-icon-part">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-bullhorn"></i></div>
                  </div>
                  <div class="stat-info-part">
                    <div class="stat-number-simple">{{ dashboard.stats.as_author }}</div>
                    <div class="stat-label">Proposées</div>
                  </div>
                </div>

                <div class="stat-card v-anime clickable" @click="$router.push({ path: '/decisions', query: { role: 'animator' } })">
                  <div class="stat-icon-part">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-user-tie"></i></div>
                  </div>
                  <div class="stat-info-part">
                    <div class="stat-number-simple">{{ dashboard.stats.as_animator }}</div>
                    <div class="stat-label">J'Anime</div>
                  </div>
                </div>

                <div class="stat-card v-active clickable" @click="$router.push({ path: '/decisions', query: { state: 'active' } })">
                  <div class="stat-icon-part">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-spinner fa-spin"></i></div>
                  </div>
                  <div class="stat-info-part">
                    <div class="stat-number-simple">{{ dashboard.stats.in_progress }}</div>
                    <div class="stat-label">En Cours</div>
                  </div>
                </div>

                <div class="stat-card v-adopted clickable" @click="$router.push({ path: '/decisions', query: { state: 'adopted' } })">
                  <div class="stat-icon-part">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-check"></i></div>
                  </div>
                  <div class="stat-info-part">
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
              </div>
            </div>

            <!-- URGENCY WIDGET -->
            <div v-if="widget.id === 'urgencies'">
              <div class="premium-card border-red h-full">
                <div class="pc-header pc-header-red">
                  <div class="pc-header-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Urgences & Échéances</div>
                    <div class="pc-header-sub">{{ urgentDecisions.length }} décision(s) nécessite(nt) votre attention immédiate</div>
                  </div>
                </div>
                <div class="pc-body" :class="{ 'grid-3-no-gap': widget.width === 'full' || widget.width === '1/1' }">
                  <EmptyState v-if="urgentDecisions.length === 0" message="Aucune urgence à signaler." />
                  <DecisionListItem
                      v-for="d in urgentDecisions" :key="d.id" :decision="d"
                      @click="goToDecision"
                      @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
                      @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
                  />
                </div>
              </div>
            </div>

            <!-- CLARIFICATIONS WIDGET -->
            <div v-if="widget.id === 'clarifications'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-amber">
                  <div class="pc-header-icon"><i class="fa-solid fa-comments"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Clarifications actives</div>
                    <div class="pc-header-sub">Tickets en attente de réponse</div>
                  </div>
                </div>
                <div class="pc-body">
                  <EmptyState v-if="!dashboard.my_clarifications?.length" message="Aucune clarification." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <div v-for="fb in dashboard.my_clarifications" :key="fb.id"
                        class="decision-item" @click="goToDecision(fb.version?.decision_id)">
                    <div :class="'role-bg-mini role-' + getMyRole(fb.version?.decision)">
                      <i :class="getRoleIcon(getMyRole(fb.version?.decision))"></i>
                    </div>
                    <div class="decision-item-main">
                      <div class="decision-title">
                        <span class="version-pill">v{{ fb.version?.version_number || 1 }}</span>
                        {{ fb.version?.decision?.title }}
                      </div>
                      <div class="decision-people">
                        <span class="text-author">{{ fb.version?.decision?.circle?.name }}</span>
                      </div>
                      <div class="ticket-msg" v-if="getLastMessageContent(fb)">
                        <span class="ticket-msg-author">{{ getLastMessageAuthor(fb) }}</span>: "{{ getLastMessageContent(fb) }}"
                      </div>
                    </div>
                    <div class="decision-end-actions">
                      <span :class="needsMyAttention(fb) ? 'badge badge-red' : 'badge badge-teal'">
                        {{ needsMyAttention(fb) ? 'À vous' : 'En attente' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SUGGESTIONS WIDGET -->
            <div v-if="widget.id === 'suggestions'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-blue" style="background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                  <div class="pc-header-icon"><i class="fa-solid fa-lightbulb"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Suggestions actives</div>
                    <div class="pc-header-sub">Tickets en attente de réponse</div>
                  </div>
                </div>
                <div class="pc-body">
                  <EmptyState v-if="!dashboard.my_suggestions?.length" message="Aucune suggestion." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <div v-for="fb in dashboard.my_suggestions" :key="fb.id"
                        class="decision-item" @click="goToDecision(fb.version?.decision_id)">
                    <div :class="'role-bg-mini role-' + getMyRole(fb.version?.decision)">
                      <i :class="getRoleIcon(getMyRole(fb.version?.decision))"></i>
                    </div>
                    <div class="decision-item-main">
                      <div class="decision-title">
                        <span class="version-pill">v{{ fb.version?.version_number || 1 }}</span>
                        {{ fb.version?.decision?.title }}
                      </div>
                      <div class="decision-people">
                        <span class="text-author">{{ fb.version?.decision?.circle?.name }}</span>
                      </div>
                      <div class="ticket-msg" v-if="getLastMessageContent(fb)">
                        <span class="ticket-msg-author">{{ getLastMessageAuthor(fb) }}</span>: "{{ getLastMessageContent(fb) }}"
                      </div>
                    </div>
                    <div class="decision-end-actions">
                      <span :class="needsMyAttention(fb) ? 'badge badge-red' : 'badge badge-teal'">
                        {{ needsMyAttention(fb) ? 'À vous' : 'En attente' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- OBJECTIONS WIDGET -->
            <div v-if="widget.id === 'objections'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-red">
                  <div class="pc-header-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Objections actives</div>
                    <div class="pc-header-sub">Tickets en attente de réponse</div>
                  </div>
                </div>
                <div class="pc-body">
                  <EmptyState v-if="!dashboard.my_objections?.length" message="Aucune objection." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <div v-for="fb in dashboard.my_objections" :key="fb.id"
                        class="decision-item" @click="goToDecision(fb.version?.decision_id)">
                    <div :class="'role-bg-mini role-' + getMyRole(fb.version?.decision)">
                      <i :class="getRoleIcon(getMyRole(fb.version?.decision))"></i>
                    </div>
                    <div class="decision-item-main">
                      <div class="decision-title">
                        <span class="version-pill">v{{ fb.version?.version_number || 1 }}</span>
                        {{ fb.version?.decision?.title }}
                      </div>
                      <div class="decision-people">
                        <span class="text-author">{{ fb.version?.decision?.circle?.name }}</span>
                      </div>
                      <div class="ticket-msg" v-if="getLastMessageContent(fb)">
                        <span class="ticket-msg-author">{{ getLastMessageAuthor(fb) }}</span>: "{{ getLastMessageContent(fb) }}"
                      </div>
                    </div>
                    <div class="decision-end-actions">
                      <span :class="needsMyAttention(fb) ? 'badge badge-red' : 'badge badge-teal'">
                        {{ needsMyAttention(fb) ? 'À vous' : 'En attente' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PROPOSALS WIDGET -->
            <div v-if="widget.id === 'my_proposals'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-blue">
                  <div class="pc-header-icon"><i class="fa-solid fa-bullhorn"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Mes propositions</div>
                  </div>
                </div>
                <div class="pc-body">
                  <EmptyState v-if="Object.keys(dashboard.my_decisions).length === 0" message="Aucune proposition." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <template v-for="(decisions, circleName) in dashboard.my_decisions" :key="circleName">
                    <DecisionListItem v-for="d in decisions" :key="d.id" :decision="d" @click="goToDecision" />
                  </template>
                </div>
              </div>
            </div>

            <!-- ANIMATED WIDGET -->
            <div v-if="widget.id === 'my_animated'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-amber">
                  <div class="pc-header-icon"><i class="fa-solid fa-user-tie"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Mes animations</div>
                  </div>
                </div>
                <div class="pc-body">
                  <EmptyState v-if="Object.keys(dashboard.my_animated || {}).length === 0" message="Aucune animation." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <template v-for="(decisions, circleName) in dashboard.my_animated" :key="circleName">
                    <DecisionListItem v-for="d in decisions" :key="d.id" :decision="d" @click="goToDecision" />
                  </template>
                </div>
              </div>
            </div>

            <!-- WATCH WIDGET -->
            <div v-if="widget.id === 'circles_watch'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-blue">
                  <div class="pc-header-icon"><i class="fa-solid fa-user-group"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">À surveiller</div>
                  </div>
                </div>
                <div class="pc-body">
                  <EmptyState v-if="Object.keys(dashboard.circle_decisions).length === 0" message="Rien à signaler." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <template v-for="(decisions, circleName) in dashboard.circle_decisions" :key="circleName">
                    <DecisionListItem v-for="d in decisions" :key="d.id" :decision="d" @click="goToDecision" />
                  </template>
                </div>
              </div>
            </div>

            <!-- MY CIRCLES WIDGET -->
            <div v-if="widget.id === 'my_circles'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-blue">
                  <div class="pc-header-icon"><i class="fa-solid fa-circle-nodes"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Mes cercles</div>
                    <div class="pc-header-sub">{{ circleStore.circles.length }} cercle(s) rejoint(s)</div>
                  </div>
                </div>
                <div class="pc-body pc-chips">
                  <EmptyState v-if="circleStore.circles.length === 0" message="Aucun cercle rejoint." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <button v-for="c in circleStore.circles" :key="c.id" class="chip chip-blue" @click="$router.push({ name: 'CircleDetail', params: { id: c.id } })">
                    <i class="fa-solid fa-circle-nodes" style="margin-right: 4px;"></i> {{ c.name }}
                  </button>
                </div>
              </div>
            </div>

            <!-- CATEGORIES WIDGET -->
            <div v-if="widget.id === 'categories'">
              <div class="premium-card h-full">
                <div class="pc-header pc-header-blue">
                  <div class="pc-header-icon"><i class="fa-solid fa-folder-tree"></i></div>
                  <div class="pc-header-content">
                    <div class="pc-header-title">Catégories</div>
                    <div class="pc-header-sub">{{ (dashboard.categories || []).length }} catégorie(s) disponible(s)</div>
                  </div>
                </div>
                <div class="pc-body pc-chips">
                  <EmptyState v-if="(dashboard.categories || []).length === 0" message="Aucune catégorie." :small="widget.width !== 'full' && widget.width !== '1/1'" />
                  <button v-for="cat in (dashboard.categories || [])" :key="cat.id" class="chip chip-blue" @click="$router.push({ name: 'DecisionList', query: { category: cat.id } })">
                    <i :class="cat.icon || 'fa-solid fa-tag'" :style="{ color: cat.color_hex }" style="margin-right: 6px;"></i>
                    {{ cat.name }}
                  </button>
                </div>
              </div>
            </div>

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
import { useConfigStore } from '../stores/config';
import DecisionListItem from '../components/DecisionListItem.vue';
import EmptyState from '../components/EmptyState.vue';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();
const circleStore = useCircleStore();
const configStore = useConfigStore();

const loading = ref(true);
const dashboard = ref({ my_decisions: {}, my_animated: {}, circle_decisions: {}, my_clarifications: [], my_objections: [], stats: null, categories: [] });

const enabledWidgets = computed(() => {
  let widgets = authStore.user?.dashboard_widgets;
  
  // Si c'est une chaîne (JSON), on parse
  if (typeof widgets === 'string') {
    try {
      widgets = JSON.parse(widgets);
    } catch (e) {
      widgets = [];
    }
  }

  // Si c'est vide ou pas un tableau, on met les défauts
  if (!Array.isArray(widgets) || widgets.length === 0) {
    widgets = [
      { id: 'stats', enabled: true, width: 'full' },
      { id: 'urgencies', enabled: true, width: 'full' },
      { id: 'tickets', enabled: true, width: 'full' },
      { id: 'my_proposals', enabled: true, width: '1/3' },
      { id: 'my_animated', enabled: true, width: '1/3' },
      { id: 'circles_watch', enabled: true, width: '1/3' },
      { id: 'my_circles', enabled: true, width: '1/2' },
      { id: 'categories', enabled: true, width: '1/2' }
    ];
  }
  return widgets.filter(w => w.enabled);
});

const dashboardRows = computed(() => {
  const rows = [];
  let currentRow = [];
  let currentWidth = 0;

  const widthValues = {
    'quarter': 0.25,
    'third': 0.3334,
    'half': 0.5,
    'full': 1.0,
    '1/4': 0.25,
    '1/3': 0.3334,
    '1/2': 0.5,
    '1/1': 1.0
  };

  enabledWidgets.value.forEach(widget => {
    const w = widthValues[widget.width] || 1.0;
    if (currentWidth + w > 1.05 && currentRow.length > 0) {
      rows.push(currentRow);
      currentRow = [];
      currentWidth = 0;
    }
    currentRow.push(widget);
    currentWidth += w;
  });

  if (currentRow.length > 0) {
    rows.push(currentRow);
  }
  return rows;
});

const getWidgetClass = (width) => {
  switch (width) {
    case 'quarter':
    case '1/4': return 'widget-w-1-4';
    case 'third':
    case '1/3': return 'widget-w-1-3';
    case 'half':
    case '1/2': return 'widget-w-1-2';
    case 'full':
    case '1/1': return 'widget-w-full';
    default: return 'widget-w-1-3';
  }
};

const urgentDecisions = computed(() => {
  const all = [...Object.values(dashboard.value.my_decisions).flat(), ...Object.values(dashboard.value.my_animated).flat(), ...Object.values(dashboard.value.circle_decisions).flat()];
  // Deduplicate by ID
  const unique = Array.from(new Map(all.map(d => [d.id, d])).values());
  
  return unique.filter(d => {
    if (!d.current_deadline) return false;
    const deadline = new Date(d.current_deadline);
    const now = new Date();
    return deadline.getTime() - now.getTime() < 24 * 60 * 60 * 1000;
  });
});

const hasActiveTickets = computed(() => dashboard.value.my_clarifications?.length > 0 || dashboard.value.my_objections?.length > 0);

const ringOffset = (value, total) => {
    const pct = total > 0 ? Math.min(1, Math.max(0, value / total)) : 0;
    return 175.9 * (1 - pct);
};

onMounted(async () => {
    configStore.fetchConfig();
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

const getRoleIcon = (role) => {
    const icons = { 
        author: 'fa-solid fa-bullhorn', 
        animator: 'fa-solid fa-user-tie', 
        participant: 'fa-solid fa-user-group', 
        observer: 'fa-solid fa-eye' 
    };
    return icons[role] || 'fa-solid fa-user-group';
};

const getMyRoleLabel = (decision) => {
    const role = getMyRole(decision);
    const labels = { author: 'Porteur', animator: 'Animateur', participant: 'Participant', observer: 'Observateur' };
    return labels[role] || 'Participant';
};
</script>

<style scoped>
/* Modular Grid */
.dashboard-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  width: 100%;
  justify-content: center;
}
.widget-w-1-4 { width: 100%; }
.widget-w-1-3 { width: 100%; }
.widget-w-1-2 { width: 100%; }
.widget-w-full { width: 100%; }

@media(min-width: 900px) {
  .widget-w-1-4 { width: calc(25% - 12px); }
  .widget-w-1-3 { width: calc(33.33% - 11px); }
  .widget-w-1-2 { width: calc(50% - 8px); }
  .widget-w-full { width: 100%; }
}

.grid-2-internal {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}
@media(min-width: 700px) {
  .grid-2-internal { grid-template-columns: 1fr 1fr; }
}

.widget-stats {
  margin-bottom: 0;
}

.hero-main-identity {
  display: flex;
  align-items: center;
  gap: 20px;
}

.hero-custom-logo {
  height: 85px;
  width: auto;
  max-width: 180px;
  object-fit: contain;
}

.hero-user-line {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.9);
  margin-top: 4px;
  font-weight: 500;
}

.border-red { border: 2px solid var(--red-500) !important; }

/* Stats Block */
.stats-row { display: flex; gap: 14px; flex-wrap: wrap; }
.stat-card {
  flex: 1; min-width: 200px; border-radius: 14px; padding: 20px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.12); transition: all 0.2s;
  position: relative; overflow: hidden;
  display: flex; flex-direction: row; align-items: center; cursor: pointer;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 32px rgba(0,0,0,0.18); }
.stat-card.v-total    { background: linear-gradient(135deg, #1e3a8a, #3b82f6); }
.stat-card.v-proposals { background: linear-gradient(135deg, #1d4ed8, #60a5fa); }
.stat-card.v-anime    { background: linear-gradient(135deg, #92400e, #f59e0b); }
.stat-card.v-active   { background: linear-gradient(135deg, #9a3412, #f97316); }
.stat-card.v-adopted  { background: linear-gradient(135deg, #115e59, #14b8a6); }
.stat-card::after { content: ''; position: absolute; right: -24px; bottom: -24px; width: 90px; height: 90px; border-radius: 50%; background: rgba(255,255,255,0.07); }

.stat-icon-part { flex: 1; display: flex; justify-content: center; align-items: center; }
.stat-info-part { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; }

.stat-icon-wrap { 
    font-size: 32px; width: 72px; height: 72px; border-radius: 50%; 
    background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3); 
    display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; 
}

.ring-wrap { position: relative; width: 72px; height: 72px; }
.ring-wrap svg { transform: rotate(-90deg); }
.ring-bg { fill: none; stroke: rgba(255,255,255,0.15); stroke-width: 7; }
.ring-fg { fill: none; stroke: rgba(255,255,255,0.85); stroke-width: 7; stroke-linecap: round; transition: stroke-dashoffset 0.6s cubic-bezier(0.4,0,0.2,1); }
.ring-center { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 900; color: white; font-family: var(--font-display); }
.stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: rgba(255,255,255,0.8); }

@media (max-width: 800px) {
    .stat-card { flex-direction: column; min-width: 150px; padding-top: 100px; }
    .stat-icon-part { position: absolute; top: 16px; left: 16px; }
    .stat-info-part { width: 100%; justify-content: center; }
}
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
  width: 100%;
}


.pc-chips {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
  padding: 20px;
}
</style>

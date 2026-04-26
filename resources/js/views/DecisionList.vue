<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">{{ pageTitle }}</div>
            <div class="hero-subtitle">{{ pageSubtitle }}</div>
          </div>
          <div class="hero-action">
            <button class="btn btn-secondary" @click="$router.push({ name: 'DecisionCreate' })">
              <i class="fa-solid fa-plus"></i> Nouvelle décision
            </button>
          </div>
        </div>
      </div>

      <!-- FILTER BAR -->
      <div class="filter-bar">
        <div class="filter-group main-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input v-model="filters.search" placeholder="Rechercher une décision par titre ou auteur..." class="input-inline">
        </div>
        
        <div class="filter-row">
          <div class="filter-item">
            <label>État</label>
            <select v-model="filters.state" class="select-sm">
              <option value="all">Tous états</option>
              <option value="draft">Brouillon</option>
              <option value="active">En cours (Tous)</option>
              <option value="clarification">En clarification</option>
              <option value="reaction">En réaction</option>
              <option value="objection">En objection</option>
              <option value="revision">En révision</option>
              <option value="adopted">Adoptées</option>
              <option value="abandoned">Abandonnées</option>
            </select>
          </div>

          <div class="filter-item">
            <label>Mon rôle</label>
            <select v-model="filters.my_role" class="select-sm">
              <option value="all">Tous mes rôles</option>
              <option value="author">Mes propositions</option>
              <option value="animator">J'anime</option>
              <option value="participant">Je participe</option>
              <option value="observer">J'observe</option>
            </select>
          </div>

          <div class="filter-item">
            <label>Cercle</label>
            <select v-model="filters.circle" class="select-sm">
              <option value="all">Tous cercles</option>
              <option v-for="c in uniqueCircles" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <div class="filter-item">
            <label>Thématique</label>
            <select v-model="filters.category" class="select-sm">
              <option value="all">Toutes</option>
              <option v-for="c in allCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <div class="filter-item">
            <label>Tri</label>
            <select v-model="filters.sort" class="select-sm">
              <option value="created_desc">Création (Récents)</option>
              <option value="created_asc">Création (Anciens)</option>
              <option value="updated_desc">Mise à jour (Récents)</option>
              <option value="updated_asc">Mise à jour (Anciens)</option>
              <option value="alpha_asc">A-Z</option>
              <option value="alpha_desc">Z-A</option>
            </select>
          </div>

          <button class="btn btn-ghost btn-sm ml-auto" @click="resetFilters">
            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
          </button>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement des décisions...</p>
      </div>
      
      <div v-else-if="error" class="alert alert-error">{{ error }}</div>
      
      <div v-else class="decision-list-container">
        <EmptyState v-if="filteredAndSorted.length === 0" message="Aucune décision ne correspond à vos critères." />
        
        <div v-else>
          <div class="decision-grid">
            <div v-for="desc in paginatedDecisions" :key="desc.id" class="premium-card decision-card">
              <DecisionListItem
                :decision="desc"
                @click="goToDetail"
                @filter-circle="filters.circle = $event"
                @filter-category="filters.category = $event"
                @toggle-favorite="toggleFavorite"
                @open-notifications="openNotifModal"
              />
            </div>
          </div>

          <!-- PAGINATION -->
          <div class="pagination-bar mt-24">
            <div class="pagination-info">
              Affichage de <b>{{ startRange }}</b> à <b>{{ endRange }}</b> sur <b>{{ filteredAndSorted.length }}</b> décisions
            </div>
            <div class="pagination-controls">
              <div class="per-page-selector mr-16">
                <label>Par page :</label>
                <select v-model="pagination.perPage" class="select-xs">
                  <option :value="10">10</option>
                  <option :value="20">20</option>
                  <option :value="50">50</option>
                  <option :value="100">100</option>
                </select>
              </div>
              <button class="btn btn-ghost btn-xs" :disabled="pagination.page === 1" @click="pagination.page--">
                <i class="fa-solid fa-chevron-left"></i> Précédent
              </button>
              <div class="page-numbers">
                Page {{ pagination.page }} / {{ totalPages }}
              </div>
              <button class="btn btn-ghost btn-xs" :disabled="pagination.page === totalPages" @click="pagination.page++">
                Suivant <i class="fa-solid fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODALES -->
    <NotificationLevelModal 
      v-if="notifModal.show"
      :currentLevel="notifModal.decision?.my_settings?.notification_level || 'all'"
      @close="notifModal.show = false"
      @select="handleNotifUpdated"
    />
  </main>
</template>

<script setup>
import { onMounted, computed, ref, watch, reactive } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { useDecisionStore } from '../stores/decision';
import { useAuthStore } from '../stores/auth';
import DecisionListItem from '../components/DecisionListItem.vue';
import EmptyState from '../components/EmptyState.vue';
import NotificationLevelModal from '../components/NotificationLevelModal.vue';

const store = useDecisionStore();
const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const filters = ref({
  search: '',
  state: 'all',
  my_role: 'all',
  action: 'all',
  circle: 'all',
  category: 'all',
  author: 'all',
  sort: 'created_desc'
});

const pagination = reactive({
  page: 1,
  perPage: 10
});

const isFavoritesMode = computed(() => route.name === 'DecisionFavorites');

const pageTitle = computed(() => {
  if (isFavoritesMode.value) return 'Mes Favoris';
  if (route.query.view_label) return route.query.view_label;
  return 'Décisions';
});

const pageSubtitle = computed(() => {
  if (isFavoritesMode.value) return 'Retrouvez ici toutes les décisions que vous avez marquées d\'une étoile.';
  if (route.query.view_label) return 'Vue personnalisée';
  return 'Suivez et participez aux processus de décision dans vos différents cercles.';
});

const resetFilters = () => {
  filters.value = { search: '', state: 'all', my_role: 'all', action: 'all', circle: 'all', category: 'all', author: 'all', sort: 'created_desc' };
  pagination.page = 1;
};

const decisions = computed(() => store.decisions);
const loading = computed(() => store.loading);
const error = computed(() => store.error);

const uniqueCircles = computed(() => {
  const map = new Map();
  decisions.value.forEach(d => { if (d.circle) map.set(d.circle.id, d.circle); });
  return Array.from(map.values());
});

const allCategories = ref([]);

const filteredAndSorted = computed(() => {
  let result = decisions.value.filter(d => {
    // Mode Favoris
    if (isFavoritesMode.value && !d.my_settings?.is_favorite) return false;

    // Search filter
    if (filters.value.search) {
      const s = filters.value.search.toLowerCase();
      const titleMatch = d.title?.toLowerCase().includes(s);
      const authorMatch = (getParticipantName(d, 'author') || '').toLowerCase().includes(s);
      const animatorMatch = (getParticipantName(d, 'animator') || '').toLowerCase().includes(s);
      if (!titleMatch && !authorMatch && !animatorMatch) return false;
    }

    if (filters.value.state !== 'all') {
      const s = filters.value.state;
      if (s === 'active' && !['clarification','reaction','objection'].includes(d.status)) return false;
      if (s === 'adopted' && !['adopted','adopted_override'].includes(d.status)) return false;
      if (s === 'abandoned' && !['abandoned', 'deserted', 'lapsed'].includes(d.status)) return false;
      if (['draft', 'clarification', 'reaction', 'objection', 'revision'].includes(s) && d.status !== s) return false;
    }
    if (filters.value.circle !== 'all' && d.circle_id !== filters.value.circle) return false;
    if (filters.value.category !== 'all' && !(d.categories || []).some(c => c.id === filters.value.category)) return false;
    if (filters.value.author !== 'all') {
      const authorId = d.participants?.find(p => p.role === 'author')?.user_id;
      if (authorId !== filters.value.author) return false;
    }
    if (filters.value.my_role !== 'all') {
      if (getMyRole(d) !== filters.value.my_role) return false;
    }
    if (filters.value.action === 'pending') {
      if (!d.user_status?.needs_action) return false;
    }
    return true;
  });

  // SORTING
  const s = filters.value.sort;
  result.sort((a, b) => {
    if (s === 'created_desc') return new Date(b.created_at) - new Date(a.created_at);
    if (s === 'created_asc') return new Date(a.created_at) - new Date(b.created_at);
    if (s === 'updated_desc') return new Date(b.updated_at) - new Date(a.updated_at);
    if (s === 'updated_asc') return new Date(a.updated_at) - new Date(b.updated_at);
    if (s === 'alpha_asc') return a.title.localeCompare(b.title);
    if (s === 'alpha_desc') return b.title.localeCompare(a.title);
    return 0;
  });

  return result;
});

const paginatedDecisions = computed(() => {
  const start = (pagination.page - 1) * pagination.perPage;
  return filteredAndSorted.value.slice(start, start + pagination.perPage);
});

const totalPages = computed(() => Math.ceil(filteredAndSorted.value.length / pagination.perPage) || 1);
const startRange = computed(() => filteredAndSorted.value.length === 0 ? 0 : (pagination.page - 1) * pagination.perPage + 1);
const endRange = computed(() => Math.min(pagination.page * pagination.perPage, filteredAndSorted.value.length));

const applyQueryFilters = (query) => {
    resetFilters();
    if (query.state) filters.value.state = query.state;
    if (query.role) filters.value.my_role = query.role;
    if (query.action) filters.value.action = query.action;
    if (query.category) filters.value.category = query.category;
};

onMounted(async () => {
    store.fetchDecisions();
    try {
        const { data } = await axios.get('/api/v1/categories');
        allCategories.value = data.categories || [];
    } catch (e) {}

    applyQueryFilters(route.query);
});

watch(() => route.query, (newQuery) => {
    applyQueryFilters(newQuery);
}, { deep: true });

watch([filters, () => pagination.perPage], () => {
  pagination.page = 1;
}, { deep: true });

const goToDetail = (id) => router.push({ name: 'DecisionDetail', params: { id } });

const getMyRole = (decision) => {
    if (!authStore.user || !decision.participants) return 'participant';
    const myRoles = decision.participants.filter(p => p.user_id === authStore.user.id).map(p => p.role);
    if (myRoles.includes('author')) return 'author';
    if (myRoles.includes('animator')) return 'animator';
    if (myRoles.includes('participant')) return 'participant';
    if (myRoles.includes('observer')) return 'observer';
    return 'participant';
};

const getParticipantName = (decision, role) => {
    if (!decision.participants) return null;
    const p = decision.participants.find(p => p.role === role);
    return p?.user?.name || null;
};

// Actions rapides
const toggleFavorite = async (decision) => {
  try {
    const { data } = await axios.post(`/api/v1/decisions/${decision.id}/favorite`);
    // Update local state in store
    const idx = store.decisions.findIndex(d => d.id === decision.id);
    if (idx !== -1) {
      if (!store.decisions[idx].my_settings) store.decisions[idx].my_settings = {};
      store.decisions[idx].my_settings.is_favorite = data.is_favorite;
    }
  } catch (e) {
    console.error("Toggle favorite error", e);
  }
};

const notifModal = reactive({ show: false, decision: null });
const openNotifModal = (decision) => {
  notifModal.decision = decision;
  notifModal.show = true;
};

const handleNotifUpdated = async (level) => {
  try {
    await axios.put(`/api/v1/decisions/${notifModal.decision.id}/notifications`, { notification_level: level });
    const idx = store.decisions.findIndex(d => d.id === notifModal.decision.id);
    if (idx !== -1) {
      if (!store.decisions[idx].my_settings) store.decisions[idx].my_settings = {};
      store.decisions[idx].my_settings.notification_level = level;
    }
    notifModal.show = false;
  } catch (e) {
    console.error('Error updating notifications', e);
    alert('Erreur lors de la mise à jour des notifications.');
  }
};
</script>

<style scoped>
.py-24 { padding: 48px 0; }
.mb-16 { margin-bottom: 16px; }
.mr-16 { margin-right: 16px; }

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
.select-xs { padding: 4px 8px; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-size: 11px; background: white; }
.ml-auto { margin-left: auto; }

/* LISTING */
.decision-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
.decision-card { border: 1px solid var(--gray-100); transition: all 0.2s; overflow: hidden; }
.decision-card:hover { transform: translateY(-2px); border-color: var(--blue-200); box-shadow: var(--shadow-md); }

/* PAGINATION */
.pagination-bar { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; background: white; border-radius: var(--radius-lg); border: 1px solid var(--gray-100); }
.pagination-info { font-size: 13px; color: var(--gray-600); }
.pagination-controls { display: flex; align-items: center; gap: 8px; }
.page-numbers { font-size: 13px; font-weight: 600; color: var(--gray-700); padding: 0 12px; }
.per-page-selector { display: flex; align-items: center; gap: 8px; font-size: 11px; color: var(--gray-500); }

:deep(.decision-item) { border-bottom: none !important; }
</style>

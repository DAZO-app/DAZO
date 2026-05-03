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
        <EmptyState v-if="decisionsList.length === 0" message="Aucune décision ne correspond à vos critères." />
        
        <div v-else>
          <div class="decision-grid">
            <div v-for="desc in decisionsList" :key="desc.id" class="premium-card decision-card">
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
          <div v-if="pagination && pagination.last_page > 1" class="pagination-bar mt-24">
            <div class="pagination-info">
              Affichage de <b>{{ pagination.from }}</b> à <b>{{ pagination.to }}</b> sur <b>{{ pagination.total }}</b> décisions
            </div>
            <div class="pagination-controls">
              <div class="per-page-selector mr-16">
                <label>Par page :</label>
                <select v-model="pagination.per_page" @change="loadPage(1)" class="select-xs">
                  <option :value="10">10</option>
                  <option :value="20">20</option>
                  <option :value="50">50</option>
                  <option :value="100">100</option>
                </select>
              </div>
              <button class="btn btn-ghost btn-xs" :disabled="pagination.current_page === 1" @click="loadPage(pagination.current_page - 1)">
                <i class="fa-solid fa-chevron-left"></i> Précédent
              </button>
              <div class="page-numbers">
                Page {{ pagination.current_page }} / {{ pagination.last_page }}
              </div>
              <button class="btn btn-ghost btn-xs" :disabled="pagination.current_page === pagination.last_page" @click="loadPage(pagination.current_page + 1)">
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
import { useCircleStore } from '../stores/circle';
import DecisionListItem from '../components/DecisionListItem.vue';
import EmptyState from '../components/EmptyState.vue';
import NotificationLevelModal from '../components/NotificationLevelModal.vue';

const decisionStore = useDecisionStore();
const circleStore = useCircleStore();
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
  loadPage(1);
};

const decisions = computed(() => decisionStore.decisions);
const paginationFromStore = computed(() => decisionStore.pagination);
const loading = computed(() => decisionStore.loading);
const error = computed(() => decisionStore.error);

// Sync internal pagination reactive with store for UI bindings
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0
});

watch(paginationFromStore, (newVal) => {
    if (newVal) {
        Object.assign(pagination, newVal);
    }
}, { immediate: true, deep: true });

const decisionsList = computed(() => decisions.value);

const uniqueCircles = computed(() => {
  const map = new Map();
  decisions.value.forEach(d => { if (d.circle) map.set(d.circle.id, d.circle); });
  return Array.from(map.values());
});

const allCategories = ref([]);

const applyQueryFilters = (query) => {
    if (query.state) filters.value.state = query.state;
    if (query.role) filters.value.my_role = query.role;
    if (query.action) filters.value.action = query.action;
    if (query.category) filters.value.category = query.category;
};

const loadPage = async (page = 1) => {
    const params = {
        ...filters.value,
        page,
        per_page: pagination.per_page,
        favorites_only: isFavoritesMode.value
    };
    await decisionStore.fetchDecisions(params);
};

onMounted(async () => {
    applyQueryFilters(route.query);
    await loadPage(1);
    circleStore.fetchCircles();
    try {
        const { data } = await axios.get('/api/v1/categories');
        allCategories.value = data.data || data.categories || [];
    } catch (e) {}
});

watch(() => route.query, (newQuery) => {
    applyQueryFilters(newQuery);
    loadPage(1);
}, { deep: true });

let searchTimeout = null;
watch(filters, () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadPage(1);
    }, 400);
}, { deep: true });

watch(isFavoritesMode, () => {
    loadPage(1);
});

const goToDetail = (id) => router.push({ name: 'DecisionDetail', params: { id } });

// Actions rapides
const toggleFavorite = async (decision) => {
  try {
    const { data } = await axios.post(`/api/v1/decisions/${decision.id}/favorite`);
    // Update local state in store
    const idx = decisionStore.decisions.findIndex(d => d.id === decision.id);
    if (idx !== -1) {
      if (!decisionStore.decisions[idx].my_settings) decisionStore.decisions[idx].my_settings = {};
      decisionStore.decisions[idx].my_settings.is_favorite = data.is_favorite;
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
    const idx = decisionStore.decisions.findIndex(d => d.id === notifModal.decision.id);
    if (idx !== -1) {
      if (!decisionStore.decisions[idx].my_settings) decisionStore.decisions[idx].my_settings = {};
      decisionStore.decisions[idx].my_settings.notification_level = level;
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

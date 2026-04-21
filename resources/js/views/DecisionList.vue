<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Décisions</div>
            <div class="hero-subtitle">Suivez et participez aux processus de décision dans vos différents cercles.</div>
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

          <button class="btn btn-ghost btn-sm ml-auto" @click="resetFilters">
            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
          </button>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement des décisions...</div>
      
      <div v-else-if="error" class="alert alert-error">{{ error }}</div>
      
      <div v-else class="decision-list-container">
        <EmptyState v-if="filtered.length === 0" message="Aucune décision ne correspond à vos critères." />
        
        <div v-else class="decision-grid">
          <div v-for="desc in filtered" :key="desc.id" class="premium-card decision-card">
            <DecisionListItem
              :decision="desc"
              @click="goToDetail"
              @filter-circle="filters.circle = $event"
              @filter-category="filters.category = $event"
            />
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, computed, ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { useDecisionStore } from '../stores/decision';
import { useAuthStore } from '../stores/auth';
import DecisionListItem from '../components/DecisionListItem.vue';
import EmptyState from '../components/EmptyState.vue';

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
  author: 'all'
});

const isAllActive = computed(() => {
  return filters.value.search === '' &&
         filters.value.state === 'all' && 
         filters.value.my_role === 'all' && 
         filters.value.action === 'all' &&
         filters.value.circle === 'all' && 
         filters.value.category === 'all' && 
         filters.value.author === 'all';
});

const resetFilters = () => {
  filters.value = { search: '', state: 'all', my_role: 'all', action: 'all', circle: 'all', category: 'all', author: 'all' };
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

const filtered = computed(() => {
  return decisions.value.filter(d => {
    // Search filter
    if (filters.value.search) {
      const s = filters.value.search.toLowerCase();
      const titleMatch = d.title.toLowerCase().includes(s);
      const authorMatch = getParticipantName(d, 'author')?.toLowerCase().includes(s);
      const circleMatch = d.circle?.name?.toLowerCase().includes(s);
      if (!titleMatch && !authorMatch && !circleMatch) return false;
    }

    if (filters.value.state !== 'all') {
      const s = filters.value.state;
      if (s === 'active' && !['clarification','reaction','objection'].includes(d.status)) return false;
      if (s === 'adopted' && !['adopted','adopted_override'].includes(d.status)) return false;
      if (s === 'abandoned' && !['abandoned', 'deserted', 'lapsed'].includes(d.status)) return false;
      if (['draft', 'clarification', 'reaction', 'objection', 'revision'].includes(s) && d.status !== s) return false;
    }
    if (filters.value.circle !== 'all' && d.circle_id !== filters.value.circle) return false;
    if (filters.value.category !== 'all' && d.category_id !== filters.value.category) return false;
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
});

const applyQueryFilters = (query) => {
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
</script>

<style scoped>
.py-24 { padding: 48px 0; }
.py-48 { padding: 48px 0; }
.mb-16 { margin-bottom: 16px; }

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

/* LISTING */
.decision-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
.decision-card { border: 1px solid var(--gray-100); transition: all 0.2s; }
.decision-card:hover { transform: translateY(-2px); border-color: var(--blue-200); box-shadow: var(--shadow-md); }

:deep(.decision-item) { border-bottom: none !important; }
</style>

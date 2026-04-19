<template>
  <main class="main">
    <div class="page-header justify-between">
      <div>
        <div class="page-title">Décisions</div>
        <div class="page-subtitle">Suivi des propositions dans vos cercles</div>
      </div>
      <div class="page-actions">
        <button class="btn btn-primary btn-sm" @click="$router.push({ name: 'DecisionCreate' })">+ Nouvelle décision</button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
      <button class="filter-chip" :class="{ active: isAllActive }" @click="resetFilters">Toutes</button>
      
      <select v-model="filters.state" class="filter-chip select-chip">
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

      <select v-model="filters.my_role" class="filter-chip select-chip">
        <option value="all">Tous mes rôles</option>
        <option value="author">Mes propositions</option>
        <option value="animator">J'anime</option>
        <option value="participant">Je participe</option>
        <option value="observer">J'observe</option>
      </select>

      <select v-model="filters.circle" class="filter-chip select-chip">
        <option value="all">Tous cercles</option>
        <option v-for="c in uniqueCircles" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>

      <select v-model="filters.category" class="filter-chip select-chip">
        <option value="all">Toutes catégories</option>
        <option v-for="c in uniqueCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>

      <select v-model="filters.author" class="filter-chip select-chip">
        <option value="all">Tous auteurs</option>
        <option v-for="a in uniqueAuthors" :key="a.id" :value="a.id">{{ a.name }}</option>
      </select>
    </div>

    <div class="page-body">
      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      
      <div v-else-if="error" class="alert alert-error">{{ error }}</div>
      
      <div v-else class="card">
        <div v-if="filtered.length === 0" class="card-body text-center text-muted">
          Aucune décision trouvée.
        </div>
        
        <DecisionListItem
          v-else
          v-for="desc in filtered"
          :key="desc.id"
          :decision="desc"
          @click="goToDetail"
          @filter-circle="filters.circle = $event"
          @filter-category="filters.category = $event"
        />
      </div>
    </div>


  </main>
</template>

<script setup>
import { onMounted, computed, ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useDecisionStore } from '../stores/decision';
import { useAuthStore } from '../stores/auth';
import DecisionListItem from '../components/DecisionListItem.vue';

const store = useDecisionStore();
const router = useRouter();
const route = useRoute();
const filters = ref({
  state: 'all',
  my_role: 'all',
  circle: 'all',
  category: 'all',
  author: 'all'
});

const isAllActive = computed(() => {
  return filters.value.state === 'all' && 
         filters.value.my_role === 'all' && 
         filters.value.circle === 'all' && 
         filters.value.category === 'all' && 
         filters.value.author === 'all';
});

const resetFilters = () => {
  filters.value = { state: 'all', my_role: 'all', circle: 'all', category: 'all', author: 'all' };
};

const uniqueCircles = computed(() => {
  const map = new Map();
  decisions.value.forEach(d => { if (d.circle) map.set(d.circle.id, d.circle); });
  return Array.from(map.values());
});

const uniqueCategories = computed(() => {
  const map = new Map();
  decisions.value.forEach(d => { if (d.category) map.set(d.category.id, d.category); });
  return Array.from(map.values());
});

const uniqueAuthors = computed(() => {
  const map = new Map();
  decisions.value.forEach(d => {
    const authorP = d.participants?.find(p => p.role === 'author');
    if (authorP?.user) map.set(authorP.user.id, authorP.user);
  });
  return Array.from(map.values());
});

const decisions = computed(() => store.decisions);
const loading = computed(() => store.loading);
const error = computed(() => store.error);

const filtered = computed(() => {
  return decisions.value.filter(d => {
    if (filters.value.state !== 'all') {
      const s = filters.value.state;
      if (s === 'active' && !['clarification','reaction','objection','revision'].includes(d.status)) return false;
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
    return true;
  });
});

onMounted(() => {
    store.fetchDecisions();
    if (route.query.filter) {
        filters.value.state = route.query.filter;
    }
});

watch(() => route.query.filter, (newFilter) => {
    if (newFilter) {
        filters.value.state = newFilter;
    }
});

const goToDetail = (id) => router.push({ name: 'DecisionDetail', params: { id } });

const getMyRole = (decision) => {
    if (!authStore.user || !decision.participants) return 'participant';
    const p = decision.participants.find(p => p.user_id === authStore.user.id);
    return p ? p.role : 'participant';
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.filter-bar { display: flex; align-items: center; gap: 8px; padding: 10px 18px; background: white; border-bottom: 1px solid var(--gray-200); flex-wrap: wrap; }
.filter-chip { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; border-radius: var(--radius-md); font-size: 13px; font-weight: 500; border: 1px solid var(--gray-300); background: white; color: var(--gray-700); cursor: pointer; transition: all 0.12s; }
.filter-chip:hover { border-color: var(--blue-400); background: var(--gray-50); }
.filter-chip.active { background: var(--blue-50); color: var(--blue-700); border-color: var(--blue-300); }

.select-chip { appearance: auto; padding-right: 8px; cursor: pointer; }
.select-chip:focus { outline: none; border-color: var(--blue-500); box-shadow: 0 0 0 2px var(--blue-100); }
</style>

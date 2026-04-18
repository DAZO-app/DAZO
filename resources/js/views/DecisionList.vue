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
      <button v-for="f in filters" :key="f.key" class="filter-chip" :class="{ active: activeFilter === f.key }" @click="activeFilter = f.key">{{ f.label }}</button>
    </div>

    <div class="page-body">
      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      
      <div v-else-if="error" class="alert alert-error">{{ error }}</div>
      
      <div v-else class="card">
        <div v-if="filtered.length === 0" class="card-body text-center text-muted">
          Aucune décision trouvée.
        </div>
        
        <div v-else v-for="desc in filtered" :key="desc.id" class="decision-item" @click="goToDetail(desc.id)">
          <div class="role-bg-mini" :class="'role-' + getMyRole(desc)" :title="getMyRole(desc)">
            {{ getRolePicto(getMyRole(desc)) }}
          </div>
          <div class="decision-item-main">
            <div class="decision-title">{{ desc.title }}</div>
            <div class="decision-meta">
              <span>Cercle : {{ desc.circle?.name || 'Général' }}</span>
              <span>· Créée le : {{ formatDateOnly(desc.created_at) }}</span>
              <span>· Dernière modif : {{ formatDateOnly(desc.updated_at) }}</span>
            </div>
            <div class="decision-tags">
              <span class="badge" :class="statusClass(desc.status)">{{ desc.status?.toUpperCase() }}</span>
              <span class="version-pill" v-if="desc.current_version">v{{ desc.current_version.version_number }}</span>
            </div>
          </div>
          <button class="btn btn-ghost btn-sm">Ouvrir</button>
        </div>
      </div>
    </div>


  </main>
</template>

<script setup>
import { onMounted, computed, ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useDecisionStore } from '../stores/decision';
import { useAuthStore } from '../stores/auth';

const store = useDecisionStore();
const router = useRouter();
const route = useRoute();
const activeFilter = ref('all');

const filters = [
  { key: 'all', label: 'Toutes' },
  { key: 'draft', label: 'Brouillon' },
  { key: 'active', label: 'En cours' },
  { key: 'clarification', label: 'Clarification' },
  { key: 'reaction', label: 'Réaction' },
  { key: 'objection', label: 'Objection' },
  { key: 'author', label: 'Mes propositions' },
  { key: 'animator', label: 'J\'anime' },
  { key: 'adopted', label: 'Adoptées' },
  { key: 'abandoned', label: 'Abandonnées' },
];

const decisions = computed(() => store.decisions);
const loading = computed(() => store.loading);
const error = computed(() => store.error);

const filtered = computed(() => {
  if (activeFilter.value === 'all') return decisions.value;
  if (activeFilter.value === 'active') return decisions.value.filter(d => ['clarification','reaction','objection','revision'].includes(d.status));
  if (activeFilter.value === 'adopted') return decisions.value.filter(d => ['adopted','adopted_override'].includes(d.status));
  if (activeFilter.value === 'abandoned') return decisions.value.filter(d => ['abandoned', 'deserted', 'lapsed'].includes(d.status));
  if (activeFilter.value === 'author') return decisions.value.filter(d => getMyRole(d) === 'author');
  if (activeFilter.value === 'animator') return decisions.value.filter(d => getMyRole(d) === 'animator');
  return decisions.value.filter(d => d.status === activeFilter.value);
});

onMounted(() => {
    store.fetchDecisions();
    if (route.query.filter) {
        activeFilter.value = route.query.filter;
    }
});

watch(() => route.query.filter, (newFilter) => {
    if (newFilter) {
        activeFilter.value = newFilter;
    }
});

const goToDetail = (id) => router.push({ name: 'DecisionDetail', params: { id } });

const statusClass = (status) => {
  const map = { draft: 'badge-gray', clarification: 'badge-blue', reaction: 'badge-blue', objection: 'badge-amber', revision: 'badge-amber', adopted: 'badge-teal', adopted_override: 'badge-teal', deserted: 'badge-gray', lapsed: 'badge-red' };
  return map[status] || 'badge-gray';
};

const formatDateOnly = (isoString) => {
    if(!isoString) return '';
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    }).format(new Date(isoString));
};

const authStore = useAuthStore();
const getMyRole = (decision) => {
    if (!authStore.user || !decision.participants) return 'participant';
    const p = decision.participants.find(p => p.user_id === authStore.user.id);
    return p ? p.role : 'participant';
};

const getRolePicto = (role) => {
    const map = { author: '💡', animator: '🎭', participant: '👥', observer: '👁️' };
    return map[role] || '👥';
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.decision-item { padding: 14px 18px; border-bottom: 1px solid var(--gray-100); cursor: pointer; transition: background 0.1s; display: flex; align-items: flex-start; gap: 12px; }
.decision-item:last-child { border-bottom: none; }
.decision-item:hover { background: var(--gray-50); }
.decision-item-main { flex: 1; min-width: 0; }
.decision-title { font-size: 13px; font-weight: 500; color: var(--gray-900); margin-bottom: 4px; }
.decision-meta { font-size: 11px; color: var(--gray-400); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.decision-tags { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 5px; }
.filter-bar { display: flex; align-items: center; gap: 8px; padding: 10px 18px; background: white; border-bottom: 1px solid var(--gray-200); flex-wrap: wrap; }
.filter-chip { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: var(--radius-full); font-size: 12px; font-weight: 500; border: 1px solid var(--gray-300); background: white; color: var(--gray-600); cursor: pointer; transition: all 0.12s; }
.filter-chip:hover { border-color: var(--blue-400); color: var(--blue-700); }
.filter-chip.active { background: var(--blue-800); color: white; border-color: var(--blue-800); }
.version-pill { display: inline-flex; align-items: center; gap: 4px; font-family: var(--font-mono); font-size: 11px; background: var(--gray-100); color: var(--gray-600); padding: 2px 8px; border-radius: var(--radius-sm); border: 1px solid var(--gray-200); }

.role-bg-mini {
  width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 13px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1.5px solid transparent;
  flex-shrink: 0;
}
.role-author { border-color: var(--blue-500); background: var(--blue-50); }
.role-animator { border-color: var(--amber-500); background: var(--amber-50); }
.role-participant { border-color: var(--teal-500); background: var(--teal-50); }
.role-observer { border-color: var(--gray-400); background: var(--gray-50); }
</style>

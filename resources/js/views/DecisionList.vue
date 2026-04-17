<template>
  <main class="main">
    <div class="page-header justify-between">
      <div>
        <div class="page-title">Décisions</div>
        <div class="page-subtitle">Suivi des propositions dans vos cercles</div>
      </div>
      <div class="page-actions">
        <button class="btn btn-primary btn-sm">+ Nouvelle décision</button>
      </div>
    </div>

    <!-- Filter Bar Proxy -->
    <div class="filter-bar">
      <button class="filter-chip active">Toutes</button>
      <button class="filter-chip">En Révision</button>
      <button class="filter-chip">Objection</button>
      <button class="filter-chip">Adoptées</button>
    </div>

    <div class="page-body">
      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      
      <div v-else-if="error" class="alert alert-error">
        {{ error }}
      </div>
      
      <div v-else class="card">
        <div v-if="decisions.length === 0" class="card-body text-center text-muted">
          Aucune décision trouvée.
        </div>
        
        <div v-else v-for="desc in decisions" :key="desc.id" class="decision-item" @click="goToDetail(desc.id)">
          <div class="decision-item-main">
            <div class="decision-title">{{ desc.title }}</div>
            <div class="decision-meta">
              <span>Cercle : {{ desc.circle?.name || 'Général' }}</span>
              <span v-if="desc.deadline">·</span>
              <span v-if="desc.deadline" style="color:var(--amber-600);font-weight:500">Exp. {{ desc.deadline }}</span>
            </div>
            <div class="decision-tags">
              <span class="badge" :class="statusClass(desc.status)">{{ desc.status.toUpperCase() }}</span>
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
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useDecisionStore } from '../stores/decision';

const store = useDecisionStore();
const router = useRouter();

const decisions = computed(() => store.decisions);
const loading = computed(() => store.loading);
const error = computed(() => store.error);

onMounted(() => {
    store.fetchDecisions();
});

const goToDetail = (id) => {
    router.push({ name: 'DecisionDetail', params: { id } });
};

const statusClass = (status) => {
    const map = {
        'draft': 'badge-gray',
        'clarification': 'badge-blue',
        'reaction': 'badge-blue',
        'objection': 'badge-amber',
        'revision': 'badge-amber',
        'adopted': 'badge-teal',
        'adopted_override': 'badge-teal',
        'deserted': 'badge-gray',
        'lapsed': 'badge-red',
    };
    return map[status] || 'badge-gray';
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.decision-item {
  padding: 14px 18px;
  border-bottom: 1px solid var(--gray-100);
  cursor: pointer;
  transition: background 0.1s;
  display: flex; align-items: flex-start; gap: 12px;
}
.decision-item:last-child { border-bottom: none; }
.decision-item:hover { background: var(--gray-50); }
.decision-item-main { flex: 1; min-width: 0; }
.decision-title { font-size: 13px; font-weight: 500; color: var(--gray-900); margin-bottom: 4px; }
.decision-meta { font-size: 11px; color: var(--gray-400); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.decision-tags { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 5px; }

.filter-bar {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 18px; background: white; border-bottom: 1px solid var(--gray-200);
  flex-wrap: wrap;
}
.filter-chip {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 4px 10px; border-radius: var(--radius-full);
  font-size: 12px; font-weight: 500; border: 1px solid var(--gray-300);
  background: white; color: var(--gray-600); cursor: pointer; transition: all 0.12s;
}
.filter-chip:hover { border-color: var(--blue-400); color: var(--blue-700); }
.filter-chip.active { background: var(--blue-800); color: white; border-color: var(--blue-800); }
.version-pill {
  display: inline-flex; align-items: center; gap: 4px;
  font-family: var(--font-mono); font-size: 11px;
  background: var(--gray-100); color: var(--gray-600);
  padding: 2px 8px; border-radius: var(--radius-sm); border: 1px solid var(--gray-200);
}
</style>

<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">{{ pageTitle }}</div>
        <div class="page-subtitle">Décisions en attente de votre participation</div>
      </div>
    </div>

    <div v-if="loading" class="p-24 text-center text-muted">Chargement...</div>

    <div class="page-body" v-else>
      <div v-if="items.length === 0" class="card">
        <div class="card-body text-center" style="padding: 48px;">
          <div style="font-size: 40px; margin-bottom: 12px;">✅</div>
          <div class="text-base font-semibold text-gray-700">Ici tout va bien !</div>
          <div class="text-sm text-muted mt-4">Aucune action requise de votre part pour le moment.</div>
        </div>
      </div>

      <div v-else class="pending-list">
        <DecisionListItem
          v-for="item in items"
          :key="item.id"
          :decision="item"
          @click="$router.push({ name: 'DecisionDetail', params: { id: item.id } })"
          @filter-circle="$router.push({ name: 'DecisionList', query: { circle: $event } })"
          @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
        />
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { usePendingStore } from '../stores/pending';
import DecisionListItem from '../components/DecisionListItem.vue';
import axios from 'axios';

const props = defineProps({
  type: { type: String, required: true } // 'clarifications' | 'reactions' | 'objections'
});

const route = useRoute();
const authStore = useAuthStore();
const pendingStore = usePendingStore();

const loading = ref(true);
const items = ref([]);

const pageTitle = computed(() => {
  switch (props.type) {
    case 'clarifications': return 'Clarifications en attente';
    case 'reactions':      return 'Réactions en attente';
    case 'objections':     return 'Objections & Suggestions en attente';
    default: return 'En attente';
  }
});

const typeLabel = computed(() => {
  switch (props.type) {
    case 'clarifications': return 'Clarification';
    case 'reactions':      return 'Réaction';
    case 'objections':     return 'Objection';
    default: return '';
  }
});

const phaseStatus = computed(() => {
  switch (props.type) {
    case 'clarifications': return 'clarification';
    case 'reactions':      return 'reaction';
    case 'objections':     return 'objection';
    default: return '';
  }
});

const fetchItems = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get(`/api/v1/pending-items`, { params: { phase: phaseStatus.value } });
    items.value = data.decisions || [];
  } catch (e) {
    items.value = [];
  } finally {
    loading.value = false;
  }
};

const getRolePicto = (role) => {
  const map = { author: '📣', animator: '🎭', participant: '👥', observer: '👁️' };
  return map[role] || '👥';
};

onMounted(() => fetchItems());
watch(() => props.type, () => fetchItems());
</script>

<style scoped>
.p-24 { padding: 24px; }

.pending-list { display: flex; flex-direction: column; gap: 0; background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-md); overflow: hidden; }

.decision-item { padding: 14px 18px; border-bottom: 1px solid var(--gray-100); cursor: pointer; transition: background 0.1s; display: flex; align-items: flex-start; gap: 12px; }
.decision-item:last-child { border-bottom: none; }
.decision-item:hover { background: var(--gray-50); }
.decision-item-main { flex: 1; min-width: 0; }
.decision-title { font-size: 13px; font-weight: 500; color: var(--gray-900); margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
.decision-people { font-size: 12px; color: var(--gray-600); margin-bottom: 6px; font-weight: 500; }
.text-author { color: var(--gray-700); }
.text-animator { color: var(--gray-500); }
.decision-tags { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px; }

.version-pill { display: inline-flex; align-items: center; justify-content: center; font-family: var(--font-mono); font-size: 11px; background: var(--gray-100); color: var(--gray-600); padding: 2px 6px; border-radius: var(--radius-sm); border: 1px solid var(--gray-200); position: relative; top: -1px; }

.role-bg-mini {
  width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 22px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1.5px solid transparent;
  flex-shrink: 0; margin-top: 4px; border-color: var(--gray-200);
}
.role-author { border-color: var(--blue-500); background: var(--blue-50); }
.role-animator { border-color: var(--amber-500); background: var(--amber-50); }
.role-participant { border-color: var(--teal-500); background: var(--teal-50); }
.role-observer { border-color: var(--gray-400); background: var(--gray-50); }

.decision-end-actions {
  display: flex; flex-direction: column; gap: 6px; align-items: flex-end; height: 100%; min-height: 50px;
}
.action-badge-btn {
  font-size: 11px; font-weight: 500; cursor: pointer; border-radius: var(--radius-sm); padding: 4px 10px; border: 1px solid transparent; transition: all 0.15s; white-space: nowrap;
}
.circle-btn { color: var(--blue-700); background: var(--blue-50); border-color: var(--blue-200); }
.circle-btn:hover { background: var(--blue-100); }

.status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink:0; }
.dot-red { background: var(--red-500); box-shadow: 0 0 0 3px var(--red-100); }
.dot-green { background: var(--teal-500); box-shadow: 0 0 0 3px var(--teal-100); }
.ml-8 { margin-left: 8px; }
</style>

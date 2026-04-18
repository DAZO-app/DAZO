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
        <div
          v-for="item in items"
          :key="item.id"
          class="pending-row"
          @click="$router.push({ name: 'DecisionDetail', params: { id: item.decision_id } })"
        >
          <!-- Indicateur couleur -->
          <div class="pending-bar" :class="props.type === 'clarifications' ? 'bar-amber' : props.type === 'reactions' ? 'bar-blue' : 'bar-red'"></div>

          <!-- Pictogramme de rôle -->
          <div class="pending-role">
            <div class="role-bg-mini" :class="'role-' + item.my_role" :title="item.my_role">
              {{ getRolePicto(item.my_role) }}
            </div>
          </div>

          <div class="pending-content">
            <div class="pending-circle">◎ {{ item.circle_name }}</div>
            <div class="pending-title">{{ item.decision_title }}</div>
            <div class="pending-meta">
              <span class="text-xs text-muted">Version {{ item.version_number }}</span>
              <span class="pill pill-gray ml-8">{{ typeLabel }}</span>
            </div>
            <div v-if="item.last_message" class="pending-last-msg">
              <span class="font-semibold">{{ item.last_message_author }}:</span>
              "{{ item.last_message }}"
            </div>
          </div>

          <div class="pending-action">
            <span v-if="item.needs_reply" class="status-dot dot-red" title="Réponse requise"></span>
            <span v-else class="status-dot dot-amber" title="Participation requise"></span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted ml-8"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { usePendingStore } from '../stores/pending';
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
    items.value = data.items || [];
  } catch (e) {
    items.value = [];
  } finally {
    loading.value = false;
  }
};

const getRolePicto = (role) => {
  const map = { author: '💡', animator: '🎭', participant: '👥', observer: '👁️' };
  return map[role] || '👥';
};

onMounted(() => fetchItems());
watch(() => props.type, () => fetchItems());
</script>

<style scoped>
.p-24 { padding: 24px; }

.pending-list { display: flex; flex-direction: column; gap: 0; }

.pending-row {
  display: flex; align-items: stretch; gap: 0;
  background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-md);
  margin-bottom: 10px; cursor: pointer; overflow: hidden; transition: box-shadow 0.15s;
}
.pending-row:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.08); }

.pending-bar { width: 4px; flex-shrink: 0; }
.bar-amber { background: var(--amber-400); }
.bar-blue { background: var(--blue-500); }
.bar-red { background: var(--red-500); }

.pending-content { flex: 1; padding: 14px 16px; }
.pending-circle { font-size: 11px; color: var(--blue-600); font-weight: 600; margin-bottom: 4px; }
.pending-title { font-size: 14px; font-weight: 600; color: var(--gray-900); margin-bottom: 6px; }
.pending-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; margin-bottom: 6px; }
.pending-last-msg { font-size: 12px; color: var(--gray-600); font-style: italic; }

.pending-role { display: flex; align-items: center; padding-left: 14px; }
.role-bg-mini {
  width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 13px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1.5px solid transparent;
  flex-shrink: 0;
}
.role-author { border-color: var(--blue-500); background: var(--blue-50); }
.role-animator { border-color: var(--amber-500); background: var(--amber-50); }
.role-participant { border-color: var(--teal-500); background: var(--teal-50); }
.role-observer { border-color: var(--gray-400); background: var(--gray-50); }

.pending-action {
  display: flex; align-items: center; padding: 0 16px;
  color: var(--gray-400);
}

.pill { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 600; }
.pill-gray { background: var(--gray-100); color: var(--gray-600); }
.ml-8 { margin-left: 8px; }
.mt-4 { margin-top: 4px; }

.status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink:0; }
.dot-red { background: var(--red-500); box-shadow: 0 0 0 3px var(--red-100); }
.dot-amber { background: var(--amber-400); box-shadow: 0 0 0 3px var(--amber-100); }
</style>

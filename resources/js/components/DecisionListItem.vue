<template>
  <div class="decision-item" @click="emit('click', decision.id)">
    <div class="role-bg-mini" :class="'role-' + getMyRole(decision)" :title="getMyRole(decision)">
      {{ getRolePicto(getMyRole(decision)) }}
    </div>
    <div class="decision-item-main">
      <div class="decision-title">
        <span class="version-pill" v-if="decision.current_version" style="margin-right: 6px">v{{ decision.current_version.version_number }}</span>
        {{ decision.title }}
      </div>
      <div class="decision-people">
        <span class="text-author">Auteur : {{ getParticipantName(decision, 'author') || 'Inconnu' }}</span>
        <span class="text-animator" v-if="getParticipantName(decision, 'animator')"> · Animateur : {{ getParticipantName(decision, 'animator') }}</span>
        <span class="text-animator" v-else> · Animateur : Non assigné</span>
      </div>
      <div class="decision-meta">
        <span>Créée le : {{ formatDateOnly(decision.created_at) }}</span>
        <span>· Dernière modif : {{ formatDateOnly(decision.updated_at) }}</span>
      </div>
      <div class="decision-meta" style="margin-top: 2px" v-if="decision.participation_stats && ['clarification', 'reaction', 'objection'].includes(decision.status)">
        <span>Progression : {{ decision.participation_stats.participated }}/{{ decision.participation_stats.eligible }} intervention(s) sur cette phase</span>
      </div>
      <div class="decision-tags">
        <span class="badge" :class="statusClass(decision.status)">{{ decision.status?.toUpperCase() }}</span>
      </div>
    </div>
    <div class="decision-end-actions">
      <button class="action-badge-btn circle-btn" @click.stop="emit('filter-circle', decision.circle_id)">{{ decision.circle?.name || 'Général' }}</button>
      <button v-if="decision.category" class="action-badge-btn category-btn" @click.stop="emit('filter-category', decision.category_id)">{{ decision.category.name }}</button>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';

const props = defineProps({
  decision: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['click', 'filter-circle', 'filter-category']);

const authStore = useAuthStore();

const getMyRole = (decision) => {
    if (!authStore.user || !decision.participants) return 'participant';
    const p = decision.participants.find(p => p.user_id === authStore.user.id);
    return p ? p.role : 'participant';
};

const getRolePicto = (role) => {
    const map = { author: '📣', animator: '🎭', participant: '👥', observer: '👁️' };
    return map[role] || '👥';
};

const getParticipantName = (decision, role) => {
    if (!decision.participants) return null;
    const p = decision.participants.find(p => p.role === role);
    return p?.user?.name || null;
};

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
</script>

<style scoped>
.decision-item { padding: 14px 18px; border-bottom: 1px solid var(--gray-100); cursor: pointer; transition: background 0.1s; display: flex; align-items: flex-start; gap: 12px; }
.decision-item:last-child { border-bottom: none; }
.decision-item:hover { background: var(--gray-50); }
.decision-item-main { flex: 1; min-width: 0; }
.decision-title { font-size: 13px; font-weight: 500; color: var(--gray-900); margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
.decision-people { font-size: 12px; color: var(--gray-600); margin-bottom: 6px; font-weight: 500; }
.text-author { color: var(--gray-700); }
.text-animator { color: var(--gray-500); }

.decision-meta { font-size: 11px; color: var(--gray-400); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.decision-tags { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px; }

.version-pill { display: inline-flex; align-items: center; justify-content: center; font-family: var(--font-mono); font-size: 11px; background: var(--gray-100); color: var(--gray-600); padding: 2px 6px; border-radius: var(--radius-sm); border: 1px solid var(--gray-200); position: relative; top: -1px; }

.role-bg-mini {
  width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 22px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1.5px solid transparent;
  flex-shrink: 0;
  margin-top: 4px;
}
.role-author { border-color: var(--blue-500); background: var(--blue-50); }
.role-animator { border-color: var(--amber-500); background: var(--amber-50); }
.role-participant { border-color: var(--teal-500); background: var(--teal-50); }
.role-observer { border-color: var(--gray-400); background: var(--gray-50); }

.decision-end-actions {
  display: flex; flex-direction: column; gap: 6px; align-items: flex-end;
}
.action-badge-btn {
  font-size: 11px; font-weight: 500; cursor: pointer; border-radius: var(--radius-sm); padding: 4px 10px; border: 1px solid transparent; transition: all 0.15s; white-space: nowrap;
}
.circle-btn { color: var(--blue-700); background: var(--blue-50); border-color: var(--blue-200); }
.circle-btn:hover { background: var(--blue-100); }
.category-btn { color: var(--purple-700); background: var(--purple-50); border-color: var(--purple-200); }
.category-btn:hover { background: var(--purple-100); }
</style>

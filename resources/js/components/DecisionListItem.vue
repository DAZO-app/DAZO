<template>
  <div class="decision-item" @click="emit('click', decision.id)">
    <div class="role-bg-mini" :class="'role-' + getMyRole(decision)" :title="getMyRole(decision)">
      <i :class="getRoleIcon(getMyRole(decision))"></i>
      <!-- Voyant d'état -->
      <span v-if="decision.user_status?.needs_action" class="alert-dot dot-red"></span>
      <span v-else-if="['clarification', 'reaction', 'objection'].includes(decision.status)" class="alert-dot dot-green"></span>
    </div>
    <div class="decision-item-main">
      <div class="decision-title">
        <span class="version-pill" v-if="decision.current_version" style="margin-right: 6px">v{{ decision.current_version.version_number }}</span>
        <span v-if="decision.current_version?.attachments?.length > 0" title="Contient des pièces jointes" style="margin-right: 4px; opacity: 0.7;"><i class="fa-solid fa-paperclip"></i></span>
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
        <span v-if="decision.share_count > 0" title="Nombre de partages publics">
          · <i class="fa-solid fa-share-nodes ml-2"></i> {{ decision.share_count }}
        </span>
      </div>
      <div class="decision-meta" style="margin-top: 2px" v-if="decision.participation_stats && ['clarification', 'reaction', 'objection'].includes(decision.status)">
        <span>Progression : {{ decision.participation_stats.participated }}/{{ decision.participation_stats.eligible }} intervention(s)</span>
      </div>
      <div class="decision-deadline" v-if="decision.current_deadline && !['adopted', 'deserted', 'lapsed'].includes(decision.status)">
        <i class="fa-solid fa-clock"></i> 
        <span :class="{ 'text-red': isUrgent }">{{ deadlineLabel }}</span>
      </div>
      <div class="decision-tags-row">
        <div class="decision-actions-mini">
          <button @click.stop="emit('toggle-favorite', decision)" class="mini-btn" :class="{ active: decision.my_settings?.is_favorite }" title="Favori">
            <i :class="decision.my_settings?.is_favorite ? 'fa-solid fa-star text-amber-500' : 'fa-regular fa-star'"></i>
          </button>
          <button @click.stop="emit('open-notifications', decision)" class="mini-btn" :class="{ active: decision.my_settings?.notification_level && decision.my_settings.notification_level !== 'none' }" title="Notifications">
            <i :class="decision.my_settings?.notification_level && decision.my_settings.notification_level !== 'none' ? 'fa-solid fa-bell text-blue-500' : 'fa-regular fa-bell'"></i>
          </button>
        </div>
        <div class="decision-tags">
          <span class="badge" :class="statusClass(decision.status)">{{ translateStatus(decision.status) }}</span>
        </div>
      </div>
    </div>
    <div class="decision-end-actions">
      <button class="action-badge-btn circle-btn" @click.stop="emit('filter-circle', decision.circle_id)">{{ decision.circle?.name || 'Général' }}</button>
      <template v-if="decision.categories && decision.categories.length > 0">
        <button 
          v-for="cat in decision.categories" 
          :key="cat.id" 
          class="action-badge-btn category-btn" 
          @click.stop="emit('filter-category', cat.id)"
          :style="{ color: cat.color_hex, background: cat.color_hex + '10', borderColor: cat.color_hex + '30' }"
        >
          {{ cat.name }}
        </button>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
  decision: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['click', 'filter-circle', 'filter-category', 'toggle-favorite', 'open-notifications']);

const authStore = useAuthStore();

const getMyRole = (decision) => {
    if (!authStore.user || !decision.participants) return 'participant';
    const myRoles = decision.participants.filter(p => p.user_id === authStore.user.id).map(p => p.role);
    if (myRoles.includes('author')) return 'author';
    if (myRoles.includes('animator')) return 'animator';
    if (myRoles.includes('participant')) return 'participant';
    if (myRoles.includes('observer')) return 'observer';
    return 'participant';
};

const getRoleIcon = (role) => {
    const map = { 
        author: 'fa-solid fa-bullhorn', 
        animator: 'fa-solid fa-user-tie', 
        participant: 'fa-solid fa-user-group', 
        observer: 'fa-solid fa-eye' 
    };
    return map[role] || 'fa-solid fa-user-group';
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

const translateStatus = (status) => {
  const map = {
    draft: 'BROUILLON',
    clarification: 'CLARIFICATION',
    reaction: 'RÉACTION',
    objection: 'OBJECTION',
    revision: 'RÉVISION',
    adopted: 'ADOPTÉE',
    adopted_override: 'ADOPTÉE (FORCE)',
    deserted: 'ABANDONNÉE',
    lapsed: 'EXPIRÉE'
  };
  return map[status] || status?.toUpperCase();
};

const formatDateOnly = (isoString) => {
    if(!isoString) return '';
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    }).format(new Date(isoString));
};

const isUrgent = computed(() => {
    if (!props.decision.current_deadline) return false;
    const deadline = new Date(props.decision.current_deadline);
    const now = new Date();
    return deadline.getTime() - now.getTime() < 24 * 60 * 60 * 1000;
});

const deadlineLabel = computed(() => {
    if (!props.decision.current_deadline) return '';
    const deadline = new Date(props.decision.current_deadline);
    const now = new Date();
    const diff = deadline.getTime() - now.getTime();

    if (diff < 0) return 'Échéance dépassée';
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    if (hours < 1) return 'Moins d\'une heure !';
    if (hours < 24) return `Finit dans ${hours}h`;
    
    const days = Math.floor(hours / 24);
    return `Expire dans ${days}j`;
});
</script>

<style scoped>
.decision-item { padding: 16px 20px; cursor: pointer; transition: background 0.1s; display: flex; align-items: flex-start; gap: 14px; }
.decision-item:hover { background: rgba(0,0,0,0.01); }
.decision-item-main { flex: 1; min-width: 0; }
.decision-title { font-size: 13px; font-weight: 500; color: var(--gray-900); margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
.decision-people { font-size: 12px; color: var(--gray-600); margin-bottom: 6px; font-weight: 500; }
.text-author { color: var(--gray-700); }
.text-animator { color: var(--gray-500); }

.decision-meta { font-size: 11px; color: var(--gray-400); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.decision-deadline { font-size: 11px; font-weight: 700; color: var(--gray-500); margin-top: 4px; display: flex; align-items: center; gap: 6px; }
.decision-tags-row { display: flex; align-items: center; gap: 12px; margin-top: 8px; }
.decision-tags { display: flex; gap: 4px; flex-wrap: wrap; }

.decision-actions-mini { display: flex; gap: 4px; align-items: center; }
.mini-btn { 
  background: none; border: none; padding: 4px; cursor: pointer; color: var(--gray-400); font-size: 14px; border-radius: 4px; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center;
}
.mini-btn:hover { background: var(--gray-100); color: var(--gray-600); }
.mini-btn.active { color: var(--gray-700); }
.text-amber-500 { color: #f59e0b; }
.text-blue-500 { color: #3b82f6; }

.version-pill { display: inline-flex; align-items: center; justify-content: center; font-family: var(--font-mono); font-size: 11px; background: var(--gray-100); color: var(--gray-600); padding: 2px 6px; border-radius: var(--radius-sm); border: 1px solid var(--gray-200); position: relative; top: -1px; }

.role-bg-mini {
  position: relative;
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

/* Voyant d'état */
.alert-dot {
  position: absolute; bottom: -2px; right: -2px;
  width: 12px; height: 12px; border-radius: 50%;
  border: 2px solid white;
}
.dot-red   { background: #ef4444; box-shadow: 0 0 0 2px #fee2e2; }
.dot-green { background: #14b8a6; box-shadow: 0 0 0 2px #ccfbf1; }

.text-red { color: #ef4444 !important; }
</style>

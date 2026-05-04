<template>
  <div class="hero-card" v-if="decision">
    <!-- LIGNE 1 : Titre + Phase à droite -->
    <div class="flex justify-between items-start mb-8">
      <div class="hero-title">{{ decision.title }}</div>
      <span class="badge" :class="statusClass" style="font-size: 14px; padding: 6px 16px;">{{ statusLabel }}</span>
    </div>

    <!-- LIGNE 2 : Personnes (Porteur/Animateur) -->
    <div class="hero-subtitle mb-12 text-sm flex items-center">
      <span class="flex items-center">
        <i class="fa-solid fa-bullhorn mr-8"></i>Porteur: <strong>{{ authorName }}</strong>
        <AnimatorSelector :decision="decision" :canEdit="isAuthorOrAnimator" @updated="$emit('refresh')" style="margin-left: 8px;" />
      </span>
      <span class="opacity-30 mx-20">|</span>
      <span class="flex items-center">
        <i class="fa-solid fa-user-tie mr-8"></i>Animateur: <strong>{{ animatorName }}</strong>
      </span>
    </div>

    <!-- LIGNE 3 : Trombonne/Cadenna/Dates -->
    <div class="hero-dates flex items-center gap-12 text-xs mb-12 opacity-90">
      <div class="flex items-center gap-8 text-sm">
        <i class="fa-solid" :class="decision.visibility === 'public' ? 'fa-lock-open text-blue-500' : 'fa-lock text-amber-600'" :title="decision.visibility === 'public' ? 'Décision Publique' : 'Décision Privée'"></i>
        <i v-if="hasAttachments" class="fa-solid fa-paperclip opacity-70" title="Pièces jointes"></i>
      </div>

      <span class="opacity-30 mx-4">|</span>

      <span><i class="fa-solid fa-calendar-plus mr-8"></i>Créée le {{ formatDateOnly(decision.created_at) }}</span>
      <span><i class="fa-solid fa-clock mr-8"></i>Actu. le {{ formatDateOnly(decision.updated_at) }}</span>
      <span v-if="decision.current_deadline && !['adopted', 'abandoned'].includes(currentStatus)" :class="{ 'text-red font-bold': isUrgent }">
        <i class="fa-solid fa-hourglass-half mr-8"></i>{{ deadlineLabel }}
      </span>
      <span v-if="decision.share_count > 0"><i class="fa-solid fa-share-nodes mr-8"></i>{{ decision.share_count }} partage(s)</span>
    </div>

    <!-- LIGNE 4 : Méta (Cercle, Version, Catégories) -->
    <div class="flex items-center gap-12 mb-16">
      <div class="btn-setting text-xs px-8 font-bold" style="width:auto; padding-left: 8px; padding-right: 8px; border-color: transparent;">{{ decision.circle?.name || 'Général' }}</div>
      <div class="btn-setting text-xs font-mono font-bold" style="width:auto; padding-left: 8px; padding-right: 8px; border-color: transparent;">v{{ currentVersion?.version_number || 1 }}</div>
      
      <div class="flex gap-8 text-xs font-bold" v-if="decision.categories && decision.categories.length > 0">
        <span 
          v-for="cat in decision.categories" 
          :key="cat.id"
          class="badge text-white"
          :style="{ borderColor: cat.color_hex, background: 'transparent', border: '1.5px solid', padding: '4px 10px', borderRadius: '99px' }"
        >
          #{{ cat.name }}
        </span>
      </div>
    </div>

    <!-- LIGNE 5 : Actions (Boutons carrés) + Navigation -->
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-8">
        <button class="btn-setting" :class="{ 'is-fav': isFavorite }" @click="$emit('toggle-favorite')" :title="isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris'">
          <i :class="isFavorite ? 'fa-solid fa-star' : 'fa-regular fa-star'"></i>
        </button>
        <button class="btn-setting" @click="$emit('show-notif-levels')" title="Préférences de notification">
          <i :class="notifLevelIcon"></i>
        </button>
        <button v-if="canOpenMeetingMode" class="btn-setting" @click="$emit('open-meeting-mode')" title="Mode réunion">
          <i class="fa-solid fa-display"></i>
        </button>
        <button v-if="isAuthorOrAnimator" class="btn-setting" @click="$emit('show-reminder')" title="Relancer les participants en attente">
          <i class="fa-solid fa-envelope"></i>
        </button>
        <button class="btn-setting" @click="$emit('print-decision')" title="Imprimer / Export PDF">
          <i class="fa-solid fa-print"></i>
        </button>
        
        <div class="w-16" style="width: 16px;"></div> <!-- Espace -->
      </div>
      
      <!-- Navigation (Précédent / Suivant) -->
      <div class="header-nav">
        <button class="btn btn-white btn-icon" :disabled="!previousDecisionId" @click="$emit('go-to-decision', previousDecisionId)">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class="btn btn-white btn-icon" :disabled="!nextDecisionId" @click="$emit('go-to-decision', nextDecisionId)">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import AnimatorSelector from '../AnimatorSelector.vue';
import { useDecisionStore } from '../../stores/decision';

const props = defineProps({
  decision: { type: Object, required: true },
  currentVersion: { type: Object, default: null },
  currentStatus: { type: String, default: '' },
  previousDecisionId: { type: [String, Number], default: null },
  nextDecisionId: { type: [String, Number], default: null },
  isRevision: { type: Boolean, default: false },
  isAuthorOrAnimator: { type: Boolean, default: false },
  savingDraft: { type: Boolean, default: false },
  publishing: { type: Boolean, default: false },
});

defineEmits([
  'save-revision', 'publish-revision', 'go-to-decision', 
  'toggle-favorite', 'show-notif-levels', 'open-meeting-mode', 
  'show-reminder', 'print-decision', 'refresh'
]);

const decisionStore = useDecisionStore();

const isFavorite = computed(() => decisionStore.mySettings?.is_favorite || false);
const notifLevelIcon = computed(() => {
  const level = decisionStore.mySettings?.notification_level;
  if (level === 'all') return 'fa-solid fa-bell';
  if (level === 'none') return 'fa-regular fa-bell-slash';
  return 'fa-regular fa-bell';
});

const canOpenMeetingMode = computed(() => ['clarification', 'reaction', 'objection', 'revision'].includes(props.currentStatus));

const authorName = computed(() => {
  const a = props.decision?.participants?.find(p => p.role?.value === 'author' || p.role === 'author');
  return a?.user?.name || '—';
});

const animatorName = computed(() => {
  const a = props.decision?.participants?.find(p => p.role?.value === 'animator' || p.role === 'animator');
  return a?.user?.name || 'Non assigné';
});

const hasAttachments = computed(() => {
  return props.currentVersion?.attachments?.length > 0 || props.decision?.revision_attachments?.length > 0;
});

const statusLabels = {
  draft: 'Brouillon',
  clarification: 'Phase de Clarification',
  reaction: 'Phase de Réaction',
  objection: 'Phase d\'Objection',
  revision: 'En Révision',
  adopted: 'Décision Adoptée',
  adopted_override: 'Adoptée (forçage)',
  abandoned: 'Décision Abandonnée'
};

const statusClasses = {
  draft: 'badge-gray',
  clarification: 'badge-blue',
  reaction: 'badge-blue',
  objection: 'badge-amber',
  revision: 'badge-amber',
  adopted: 'badge-teal',
  adopted_override: 'badge-teal',
  abandoned: 'badge-gray'
};

const statusLabel = computed(() => statusLabels[props.currentStatus] || props.currentStatus);
const statusClass = computed(() => statusClasses[props.currentStatus] || 'badge-gray');

const deadlineLabel = computed(() => {
  if (!props.decision?.current_deadline) return '';
  const diff = new Date(props.decision.current_deadline) - new Date();
  if (diff < 0) return 'Délai dépassé';
  const hours = Math.floor(diff / (1000 * 60 * 60));
  if (hours < 24) return `Fin dans ${hours}h`;
  return `Fin dans ${Math.floor(hours / 24)}j`;
});

const isUrgent = computed(() => {
  if (!props.decision?.current_deadline) return false;
  const diff = new Date(props.decision.current_deadline) - new Date();
  return diff > 0 && diff < 24 * 60 * 60 * 1000;
});

const formatDateOnly = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
</script>

<style scoped>
.header-nav {
  display: flex;
  gap: 8px;
}

.btn-setting {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 14px;
}

.btn-setting:hover {
  background: rgba(255, 255, 255, 0.25);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-1px);
}

.btn-setting.is-fav {
  background: #f59e0b;
  border-color: #fbbf24;
  color: white;
  text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.hero-footer-meta {
  color: rgba(255, 255, 255, 0.9);
}

.hero-footer-meta span {
  display: flex;
  align-items: center;
}

.category-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.02em;
  background: rgba(255, 255, 255, 0.18);
  border: 1.5px solid rgba(255, 255, 255, 0.45);
  color: white;
  backdrop-filter: blur(4px);
  box-shadow: 0 1px 4px rgba(0,0,0,0.15);
  transition: background 0.15s;
}

.category-hero-badge::before {
  content: '';
  display: inline-block;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--cat-color, white);
  flex-shrink: 0;
  margin-right: 1px;
}

.category-hero-badge:hover {
  background: rgba(255, 255, 255, 0.28);
}
</style>

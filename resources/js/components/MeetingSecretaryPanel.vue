<template>
  <div 
    class="premium-card secretary-panel-classic"
    :class="{ 'is-collapsed': isCollapsed }"
    :style="panelStyle"
  >
    <!-- Header (Zone de drag) -->
    <div 
      class="pc-header pc-header-indigo cursor-grab active:cursor-grabbing" 
      @mousedown="startDrag"
    >
      <div class="pc-header-icon"><i class="fa-solid fa-clipboard-user"></i></div>
      <div class="pc-header-content" v-if="!isCollapsed">
        <div class="pc-header-title">Secrétariat</div>
        <div class="pc-header-sub">Gérer la réunion</div>
      </div>
      <div class="ml-auto flex items-center">
        <button class="btn btn-sm" style="background:transparent; color:white; border:none;" @click.stop="toggleCollapse">
          <i class="fa-solid" :class="isCollapsed ? 'fa-expand' : 'fa-compress'"></i>
        </button>
      </div>
    </div>

    <!-- Contenu -->
    <div class="card-body" v-show="!isCollapsed" style="padding: 16px; max-height: 70vh; overflow-y: auto;">
      
      <!-- Infos Phase -->
      <div class="mb-24 border-b border-gray-100 pb-16">
        <div class="flex justify-between items-center mb-8">
          <span class="text-xs uppercase tracking-wider text-gray-500 font-bold">Phase en cours</span>
          <span class="badge badge-sm" :class="phaseBadgeClass">{{ translateStatus(decision.status) }}</span>
        </div>
        
        <!-- Changement de phase rapide -->
        <div class="flex gap-8 mt-12" v-if="['clarification', 'reaction'].includes(decision.status)">
          <button class="btn btn-sm btn-primary w-full" @click="nextPhase" :disabled="loading">
            <i class="fa-solid fa-forward-step mr-8"></i> Passer à l'étape suivante
          </button>
        </div>
      </div>

      <!-- Tour de table (Actions Rapides) -->
      <div class="mb-24 border-b border-gray-100 pb-16" v-if="['clarification', 'reaction', 'objection'].includes(decision.status)">
        <h4 class="text-sm font-bold text-gray-800 mb-12">Tour de table (restants)</h4>
        
        <div v-if="remainingParticipants.length === 0" class="text-xs text-gray-500 italic">
          Tous les participants ont interagi.
        </div>
        <div v-else class="flex flex-col gap-8">
          <div v-for="p in remainingParticipants" :key="p.user.id" class="flex justify-between items-center p-8 bg-gray-50 rounded-md border border-gray-100">
            <div class="flex flex-col">
              <span class="text-sm font-bold text-gray-800">{{ p.user.name }}</span>
              <span class="text-xs text-indigo-500 font-medium">{{ translateRole(p.role) }}</span>
            </div>
            <div class="flex gap-4">
              <button 
                class="btn btn-xs btn-outline" 
                title="Valider / Rien à signaler"
                @click="sendQuickSignal(p.user.id)"
                :disabled="loading"
              >
                <i class="fa-solid fa-check text-emerald-500"></i>
              </button>
              <button 
                class="btn btn-xs btn-outline" 
                title="Saisir un retour pour cette personne"
                @click="prepareFeedbackFor(p.user.id)"
                :disabled="loading"
              >
                <i class="fa-solid fa-comment-dots text-indigo-500"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulaire de réponse / saisie -->
      <div>
        <div class="flex justify-between items-center mb-12">
          <h4 class="text-sm font-bold text-gray-800">
            <span v-if="replyToFeedback">Répondre à {{ replyToFeedback.author?.name }}</span>
            <span v-else>Saisir un retour</span>
          </h4>
        </div>

        <div v-if="replyToFeedback" class="text-xs text-gray-500 italic border-l-2 border-indigo-200 pl-8 mb-12">
          "{{ replyToFeedback.content.substring(0, 50) }}..."
        </div>
        
        <div class="form-group mb-12">
          <select v-model="actingAsUserId" class="form-control form-control-sm">
            <option :value="authStore.user.id">Moi-même ({{ authStore.user.name }})</option>
            <option v-for="p in allowedActingParticipants" :key="'act-'+p.user.id" :value="p.user.id">
              Au nom de : {{ p.user.name }} ({{ translateRole(p.role) }})
            </option>
          </select>
        </div>

        <div class="form-group mb-12">
          <textarea 
            v-model="feedbackContent" 
            class="form-control form-control-sm" 
            rows="5" 
            placeholder="Saisir la remarque ou réponse..."
          ></textarea>
        </div>

        <div class="flex gap-8">
          <template v-if="replyToFeedback">
            <button class="btn btn-sm btn-outline flex-1" @click="$emit('cancel-reply')">
              Annuler
            </button>
            <button class="btn btn-sm btn-primary flex-1" @click="submitReply" :disabled="loading || !feedbackContent">
              <i class="fa-solid fa-paper-plane mr-4"></i> Publier
            </button>
          </template>
          <template v-else>
            <button class="btn btn-sm btn-primary flex-1" @click="submitFeedback(getFeedbackTypeForPhase())" :disabled="loading || !feedbackContent">
              <i class="fa-solid fa-paper-plane mr-4"></i> Publier la réaction
            </button>
          </template>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
  decision: Object,
  currentVersion: Object,
  participants: Array,
  replyToFeedback: Object, 
  feedbacks: { type: Array, default: () => [] }, // Added to filter
  consents: { type: Array, default: () => [] }
});

const emit = defineEmits(['refresh-data', 'phase-change', 'cancel-reply', 'action-logged']);

const authStore = useAuthStore();

const isCollapsed = ref(false);
const loading = ref(false);

const actingAsUserId = ref(authStore.user.id);
const feedbackContent = ref('');

// Expand panel if reply targets changes
watch(() => props.replyToFeedback, (val) => {
  if (val) {
    isCollapsed.value = false;
  }
});

const getSignalTypeForPhase = () => {
  const status = props.decision.status;
  if (status === 'clarification') return 'no_questions';
  if (status === 'reaction') return 'no_reaction';
  if (status === 'objection') return 'no_objection';
  return 'abstention';
};

const getFeedbackTypeForPhase = () => {
  const status = props.decision.status;
  if (status === 'clarification') return 'Clarification';
  if (status === 'reaction') return 'Reaction';
  if (status === 'objection') return 'Objection';
  return 'Reaction';
};

// Compute remaining participants
const remainingParticipants = computed(() => {
  const allParts = props.participants.filter(p => p.role !== 'excluded' && p.role !== 'author' && p.role !== 'animator');
  
  // Phase logic
  let types = [];
  if (props.decision.status === 'clarification') types = ['clarification'];
  else if (props.decision.status === 'reaction') types = ['reaction'];
  else if (props.decision.status === 'objection') types = ['objection'];
  else return allParts;

  // Has feedback in phase
  const userHasFeedback = new Set();
  props.feedbacks.forEach(fb => {
    const t = fb.type?.value || fb.type;
    if (types.includes(t)) {
      userHasFeedback.add(fb.author_id);
    }
    // Also include those who joined
    if (fb.joins && types.includes(t)) {
      fb.joins.forEach(j => userHasFeedback.add(j.user_id));
    }
  });

  // Has consent in phase
  const consents = props.consents || [];
  const expectedSignal = getSignalTypeForPhase();
  consents.forEach(c => {
    const sig = typeof c.signal === 'object' && c.signal !== null ? c.signal.value : c.signal;
    if (sig === expectedSignal || (expectedSignal === 'no_objection' && sig === 'abstention')) {
      userHasFeedback.add(c.user_id);
    }
  });

  return allParts.filter(p => !userHasFeedback.has(p.user.id));
});

const allowedActingParticipants = computed(() => {
  let list = [];
  if (props.replyToFeedback) {
    list = props.participants.filter(p => {
      return p.role === 'animator' || 
             p.role === 'author' || 
             p.user.id === props.replyToFeedback.author_id;
    });
  } else {
    // For new feedbacks, anyone who hasn't submitted a feedback yet can be selected.
    // So we rebuild the list without filtering out 'author' and 'animator' like remainingParticipants does.
    const userHasFeedback = new Set();
    const types = props.decision.status === 'clarification' ? ['clarification'] : 
                 (props.decision.status === 'reaction' ? ['reaction'] : 
                 (props.decision.status === 'objection' ? ['objection'] : []));
    
    props.feedbacks.forEach(fb => {
      const t = fb.type?.value || fb.type;
      if (types.includes(t)) userHasFeedback.add(fb.author_id);
    });

    list = props.participants.filter(p => !userHasFeedback.has(p.user.id));
  }
  return list.filter(p => p.user.id !== authStore.user.id && p.role !== 'excluded');
});

// --- Draggable Logic ---
const position = ref({ x: window.innerWidth - 380, y: 100 });
const isDragging = ref(false);
const dragOffset = ref({ x: 0, y: 0 });

const panelStyle = computed(() => {
  return {
    left: `${position.value.x}px`,
    top: `${position.value.y}px`
  };
});

const startDrag = (e) => {
  if (e.target.closest('button')) return;
  isDragging.value = true;
  dragOffset.value = {
    x: e.clientX - position.value.x,
    y: e.clientY - position.value.y
  };
  document.addEventListener('mousemove', onDrag);
  document.addEventListener('mouseup', stopDrag);
};

const onDrag = (e) => {
  if (!isDragging.value) return;
  let newX = e.clientX - dragOffset.value.x;
  let newY = e.clientY - dragOffset.value.y;
  
  newX = Math.max(0, Math.min(newX, window.innerWidth - (isCollapsed.value ? 250 : 350)));
  newY = Math.max(0, Math.min(newY, window.innerHeight - 60));

  position.value = { x: newX, y: newY };
};

const stopDrag = () => {
  isDragging.value = false;
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDrag);
};

onUnmounted(() => {
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDrag);
});

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value;
};

// --- Actions ---

const phaseBadgeClass = computed(() => {
  if (props.decision.status === 'clarification') return 'badge-indigo';
  if (props.decision.status === 'reaction') return 'badge-blue';
  if (props.decision.status === 'objection') return 'badge-amber';
  return 'badge-gray';
});

const translateStatus = (status) => {
  const map = {
    'draft': 'Brouillon',
    'clarification': 'Clarification',
    'reaction': 'Réaction',
    'objection': 'Objection',
    'adopted': 'Adoptée',
    'rejected': 'Rejetée',
    'abandoned': 'Abandonnée',
  };
  return map[status] || status;
};

const translateRole = (role) => {
  const map = {
    'author': 'Porteur',
    'animator': 'Animateur',
    'participant': 'Participant',
    'observer': 'Observateur',
    'excluded': 'Exclu'
  };
  return map[role] || role;
};

const sendQuickSignal = async (userId) => {
  if (loading.value) return;
  loading.value = true;
  
  try {
    const res = await axios.post(`/api/v1/decisions/${props.decision.id}/versions/${props.currentVersion.id}/consents`, {
      type: getSignalTypeForPhase(),
      acting_as_user_id: userId
    });
    emit('action-logged', { type: 'consent', id: res.data.consent.id });
    emit('refresh-data');
  } catch (err) {
    alert(err.response?.data?.message || "Erreur lors de l'enregistrement.");
  } finally {
    loading.value = false;
  }
};

const submitFeedback = async (type) => {
  if (!feedbackContent.value || loading.value) return;
  loading.value = true;

  try {
    const res = await axios.post(`/api/v1/decisions/${props.decision.id}/feedback`, {
      type: type,
      content: feedbackContent.value,
      acting_as_user_id: actingAsUserId.value
    });
    
    emit('action-logged', { type: 'feedback', id: res.data.feedback.id });
    feedbackContent.value = '';
    emit('refresh-data');
  } catch (err) {
    alert(err.response?.data?.message || "Erreur lors de la publication.");
  } finally {
    loading.value = false;
  }
};

const submitReply = async () => {
  if (!feedbackContent.value || loading.value || !props.replyToFeedback) return;
  loading.value = true;

  try {
    const res = await axios.post(`/api/v1/feedback/${props.replyToFeedback.id}/messages`, {
      content: feedbackContent.value,
      acting_as_user_id: actingAsUserId.value
    });
    
    emit('action-logged', { type: 'message', id: res.data.feedback_message.id });
    feedbackContent.value = '';
    emit('refresh-data');
    emit('cancel-reply');
  } catch (err) {
    alert(err.response?.data?.message || "Erreur lors de la réponse.");
  } finally {
    loading.value = false;
  }
};

const nextPhase = async () => {
  if (!confirm("Voulez-vous clôturer cette phase et passer à la suivante ?")) return;
  loading.value = true;
  
  try {
    let action = 'start_reactions';
    if (props.decision.status === 'reaction') action = 'start_objections';
    
    await axios.post(`/api/v1/decisions/${props.decision.id}/transition`, { action });
    emit('phase-change');
  } catch (err) {
    alert(err.response?.data?.message || "Erreur.");
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.secretary-panel-classic {
  position: absolute;
  width: 350px;
  z-index: 10000;
  overflow: hidden;
  transition: width 0.3s ease, height 0.3s ease;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  margin-bottom: 0;
}

.secretary-panel-classic.is-collapsed {
  width: 250px;
}

.card-body::-webkit-scrollbar {
  width: 6px;
}
.card-body::-webkit-scrollbar-thumb {
  background: var(--gray-300);
  border-radius: 3px;
}
</style>

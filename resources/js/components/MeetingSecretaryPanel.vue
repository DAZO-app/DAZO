<template>
  <div 
    class="premium-card secretary-panel-classic"
    :class="{ 'is-collapsed': isCollapsed, 'is-docked': isDocked }"
    :style="panelStyle"
  >
    <!-- Header (Zone de drag) -->
    <div 
      class="secretary-header-role cursor-move active:cursor-grabbing" 
      @mousedown="startDrag"
    >
      <div class="role-item-mini">
        <div class="role-icon-mini secretariat">
          <i class="fa-solid fa-pen-nib"></i>
        </div>
        <div class="role-info-mini" v-if="!isCollapsed">
          <span class="role-label-mini">Secrétariat</span>
          <span class="role-name-mini">{{ authStore.user?.name || 'Gérer la réunion' }}</span>
        </div>
      </div>
      <div class="ml-auto flex items-center">
        <button class="btn btn-sm" style="background:transparent; color:var(--gray-400); border:none;" @click.stop="toggleCollapse">
          <i class="fa-solid" :class="isCollapsed ? 'fa-expand' : 'fa-compress'"></i>
        </button>
      </div>
    </div>

    <!-- Contenu -->
    <div class="card-body" v-show="!isCollapsed" style="padding: 16px; max-height: 70vh; overflow-y: auto;">
      
      <!-- Sélecteur de version -->
      <div v-if="allVersions.length > 1" class="mb-24 border-b border-gray-100 pb-16">
        <div class="flex justify-between items-center mb-8">
          <span class="text-xs uppercase tracking-wider text-gray-500 font-bold">Version affichée</span>
          <span v-if="isReadOnly" class="badge badge-sm badge-amber">Historique (Lecture seule)</span>
        </div>
        <select 
          :value="currentVersion.id" 
          @change="$emit('version-change', $event.target.value)"
          class="sexy-select"
        >
          <option v-for="v in sortedVersions" :key="v.id" :value="v.id">
            Version {{ v.version_number }} {{ v.id === decision.current_version_id ? '(Actuelle)' : '' }}
          </option>
        </select>
      </div>

      <!-- Infos Phase -->
      <div class="mb-24 border-b border-gray-100 pb-16">
        <div class="flex justify-between items-center mb-8">
          <span class="text-xs uppercase tracking-wider text-gray-500 font-bold">Phase en cours</span>
          <span class="badge badge-sm" :class="phaseBadgeClass">{{ translateStatus(decision.status) }}</span>
        </div>
        
        <!-- Changement de phase rapide -->
        <div class="flex gap-8 mt-12 items-stretch" v-if="!isReadOnly">
          <button 
            v-if="['clarification', 'reaction', 'objection'].includes(decision.status?.value || decision.status)"
            class="btn btn-sm btn-primary flex-1" 
            @click="nextPhase" 
            :disabled="loading"
          >
            <i class="fa-solid fa-forward-step mr-8"></i> 
            {{ (decision.status?.value || decision.status) === 'objection' ? 'Adopter la décision' : 'Passer à l\'étape suivante' }}
          </button>

          <!-- Actions spécifiques Phase Révision -->
          <template v-if="(decision.status?.value || decision.status) === 'revision'">
             <div class="flex flex-col gap-8 w-full">
                <button class="btn btn-sm btn-secondary w-full" @click="handleQuickAction('suspend')" :disabled="loading">
                  <i class="fa-solid fa-clock-rotate-left mr-8"></i> Réviser ultérieurement
                </button>
                <button class="btn btn-sm btn-indigo w-full" @click="publishRevision('clarification')" :disabled="loading">
                  <i class="fa-solid fa-recycle mr-8"></i> Cycle complet
                </button>
                <button class="btn btn-sm btn-emerald w-full" @click="publishRevision('objection')" :disabled="loading">
                  <i class="fa-solid fa-paper-plane mr-8"></i> Direct objections
                </button>
             </div>
          </template>

          <button 
            class="btn btn-sm btn-secondary" 
            @click.stop="showQuickActions = !showQuickActions" 
            title="Actions rapides"
            :class="{ 'btn-indigo': showQuickActions }"
            :disabled="loading"
          >
            <i class="fa-solid fa-gear"></i>
          </button>
        </div>
      </div>

      <!-- Actions Rapides (Remplace le tour de table si actif) -->
      <div v-if="showQuickActions" class="mb-24 border-b border-gray-100 pb-16 animate-slide-in">
        <div class="flex justify-between items-center mb-12">
          <h4 class="text-sm font-bold text-gray-800">Actions rapides</h4>
          <button class="btn btn-xs btn-ghost text-gray-400" @click="showQuickActions = false">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        
        <div class="flex flex-col gap-8">
           <!-- 1. Mettre en pause / Reprendre -->
           <button class="btn btn-sm btn-outline-gray justify-start h-auto py-12" @click="confirmAction('suspend')" v-if="(decision.status?.value || decision.status) !== 'suspended'">
              <i class="fa-solid fa-pause mr-12 text-amber-500 w-16"></i>
              <div class="flex flex-col items-start">
                <span class="font-bold">Mettre en pause</span>
                <span class="text-[10px] text-gray-500">Suspendre le processus temporairement</span>
              </div>
           </button>
           
           <button class="btn btn-sm btn-outline-gray justify-start h-auto py-12" @click="confirmAction('resume')" v-if="(decision.status?.value || decision.status) === 'suspended'">
              <i class="fa-solid fa-play mr-12 text-emerald-500 w-16"></i>
              <div class="flex flex-col items-start">
                <span class="font-bold">Reprendre</span>
                <span class="text-[10px] text-gray-500">Relancer la phase actuelle</span>
              </div>
           </button>

           <!-- 2. Réviser en direct -->
           <button class="btn btn-sm btn-outline-gray justify-start h-auto py-12" @click="confirmAction('revision-direct')" v-if="!['revision', 'adopted', 'abandoned', 'suspended'].includes(decision.status?.value || decision.status)">
              <i class="fa-solid fa-pen-to-square mr-12 text-emerald-500 w-16"></i>
              <div class="flex flex-col items-start">
                <span class="font-bold">Réviser en direct</span>
                <span class="text-[10px] text-gray-500">Créer et éditer la révision en direct</span>
              </div>
           </button>

           <!-- 3. Réviser ultérieurement -->
           <button class="btn btn-sm btn-outline-gray justify-start h-auto py-12" @click="confirmAction('revision-later')" v-if="!['revision', 'adopted', 'abandoned', 'suspended'].includes(decision.status?.value || decision.status)">
              <i class="fa-solid fa-file-signature mr-12 text-indigo-500 w-16"></i>
              <div class="flex flex-col items-start">
                <span class="font-bold">Réviser ultérieurement</span>
                <span class="text-[10px] text-gray-500">Rédiger la révision ultérieurement</span>
              </div>
           </button>

           <!-- 4. Abandonner -->
           <button class="btn btn-sm btn-outline-gray justify-start h-auto py-12 border-red-100 hover:bg-red-50" @click="confirmAction('abandon')">
              <i class="fa-solid fa-trash-can mr-12 text-red-500 w-16"></i>
              <div class="flex flex-col items-start">
                <span class="font-bold text-red-600">Abandonner définitivement</span>
                <span class="text-[10px] text-gray-500">Clore ce dossier sans suite</span>
              </div>
           </button>

           <!-- 5. Annuler dernière action -->
           <button class="btn btn-sm btn-outline-gray justify-start h-auto py-12 border-gray-200 hover:bg-gray-50 transition-opacity" 
                   :disabled="!canUndo" 
                   :class="{'opacity-40 cursor-not-allowed': !canUndo}" 
                   @click="canUndo ? confirmAction('undo') : null">
              <i class="fa-solid fa-rotate-left mr-12 text-gray-500 w-16" :class="{'text-amber-600': canUndo}"></i>
              <div class="flex flex-col items-start">
                <span class="font-bold" :class="canUndo ? 'text-amber-600' : 'text-gray-400'">Annuler dernière action</span>
                <span class="text-[10px] text-gray-500">Revenir sur la dernière validation</span>
              </div>
           </button>
        </div>
      </div>

      <!-- Tour de table (Actions Rapides) - Masqué si Quick Actions ou Formulaire ouvert -->
      <div v-if="!isReadOnly && !showForm && !showQuickActions && ['clarification', 'reaction', 'objection'].includes(decision.status)" class="mb-24 border-b border-gray-100 pb-16">
        <h4 class="text-sm font-bold text-gray-800 mb-12">Tour de table (restants)</h4>
        
        <div v-if="remainingParticipants.length === 0" class="text-xs text-gray-500 italic">
          Tous les participants ont interagi.
        </div>
        <div v-else class="flex flex-col gap-8 participant-list-scrollable">
          <div v-for="p in remainingParticipants" :key="p.user.id" class="flex flex-col gap-8 p-12 bg-white rounded-lg border border-gray-100 shadow-sm">
            <div class="flex items-center gap-12">
              <div class="w-32 h-32 rounded-full flex items-center justify-center text-white text-xs font-bold"
                :class="getRoleColor(p.role)">
                <i :class="getRoleIcon(p.role)"></i>
              </div>
              <div class="flex flex-col">
                <span class="text-sm font-bold text-gray-800">{{ p.user.name }}</span>
                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">{{ translateRole(p.role) }}</span>
              </div>
            </div>
            <div class="flex gap-4 w-full">
              <button 
                class="btn btn-xs btn-outline flex-1 flex flex-col sm:flex-row items-center justify-center gap-4 py-8 h-auto" 
                title="Valider / Rien à signaler"
                @click="sendQuickSignal(p.user.id)"
                :disabled="loading || isReadOnly"
              >
                <i class="fa-solid fa-check text-emerald-500"></i>
                <span class="text-[9px]" v-if="!isDocked">Clair</span>
              </button>
              <button 
                class="btn btn-xs btn-outline flex-1 flex flex-col sm:flex-row items-center justify-center gap-4 py-8 h-auto" 
                title="Saisir un retour pour cette personne"
                @click="prepareFeedbackFor(p.user.id)"
                :disabled="loading || isReadOnly"
              >
                <i class="fa-solid fa-comment-dots text-indigo-500"></i>
                <span class="text-[9px]" v-if="!isDocked">Parole</span>
              </button>
              <button 
                class="btn btn-xs btn-outline flex-1 flex flex-col sm:flex-row items-center justify-center gap-4 py-8 h-auto" 
                title="Abandonner / Ne se prononce pas"
                @click="sendQuickSignal(p.user.id, 'abstention')"
                :disabled="loading || isReadOnly"
              >
                <i class="fa-solid fa-xmark text-red-500"></i>
                <span class="text-[9px]" v-if="!isDocked">Passe</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulaire de réponse / saisie -->
      <div v-if="showForm" class="form-container-meeting">
        <div class="flex justify-between items-center mb-16 pb-12 border-b border-gray-100">
          <h4 class="text-sm font-bold text-gray-800">
            <span v-if="replyToFeedback">Répondre à {{ replyToFeedback.author?.name }}</span>
            <span v-else>Saisir un retour</span>
          </h4>
          <button class="btn btn-xs btn-ghost text-gray-400" @click="cancelFeedback">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>


        
        <div class="form-group mb-16">
          <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4 block">Au nom de</label>
          <select v-model="actingAsUserId" class="sexy-select">
            <option v-for="p in finalActingParticipants" :key="'act-'+p.user_id" :value="p.user_id">
              {{ p.user?.name }} ({{ translateRole(p.role) }})
            </option>
          </select>
        </div>

        <div class="form-group mb-16" v-if="!replyToFeedback && (decision.status?.value || decision.status) === 'objection'">
          <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-8 block">Type de retour</label>
          <div class="flex flex-col gap-8">
            <label class="flex items-center gap-8 cursor-pointer bg-amber-50 border border-amber-200 p-8 rounded-md">
              <input type="radio" v-model="selectedFeedbackType" value="objection" class="text-amber-600 focus:ring-amber-500">
              <span class="text-sm font-medium text-amber-800">Objection bloquante</span>
            </label>
            <label class="flex items-center gap-8 cursor-pointer bg-teal-50 border border-teal-200 p-8 rounded-md">
              <input type="radio" v-model="selectedFeedbackType" value="suggestion" class="text-teal-600 focus:ring-teal-500">
              <span class="text-sm font-medium text-teal-800">Suggestion / Mineure</span>
            </label>
          </div>
        </div>

        <div class="form-group mb-24">
          <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4 block">Commentaire / Réponse</label>
          <textarea 
            ref="textareaRef"
            v-model="feedbackContent" 
            class="sexy-textarea" 
            rows="6" 
            placeholder="Écrivez ici ce que l'utilisateur souhaite exprimer..."
          ></textarea>
        </div>

        <div class="flex flex-col gap-12">
          <!-- Boutons de résolution (Pleine largeur, Vert) -->
          <div class="flex flex-col gap-8" v-if="replyToFeedback && ['clarification', 'objection'].includes(replyToFeedback.type?.value || replyToFeedback.type)">
              <button 
                v-if="(replyToFeedback.type?.value || replyToFeedback.type) === 'clarification'"
                class="btn btn-sm btn-emerald w-full" 
                @click="resolveFeedback('treated')"
                :disabled="loading || isReadOnly"
              >
                  <i class="fa-solid fa-check mr-8"></i> Marquer comme "C'est clair"
              </button>
              <button 
                v-if="(replyToFeedback.type?.value || replyToFeedback.type) === 'objection'"
                class="btn btn-sm btn-emerald w-full" 
                @click="resolveFeedback('withdrawn')"
                :disabled="loading || isReadOnly"
              >
                  <i class="fa-solid fa-check mr-8"></i> Lever l'objection
              </button>
          </div>

          <!-- Annuler / Publier -->
          <div class="flex justify-between items-center w-full mt-12">
            <button class="btn btn-sm btn-outline" @click="cancelFeedback">
              <i class="fa-solid fa-rotate-left mr-8"></i> Annuler
            </button>
            <button 
              class="btn btn-sm btn-primary" 
              @click="replyToFeedback ? submitReply() : submitFeedback(getFeedbackTypeForPhase())" 
              :disabled="loading || !feedbackContent"
            >
              <i class="fa-solid fa-paper-plane mr-8"></i> Publier
            </button>
          </div>
        </div> <!-- Fin de flex-col (170) -->
      </div> <!-- Fin de showForm (116) -->

    </div> <!-- Fin de card-body (25) -->
  </div> <!-- Fin de panel (2) -->

  <!-- Custom Alert/Confirm Modal for Fullscreen -->
  <div v-if="showConfirmModal" class="modal-overlay" style="z-index: 2147483647;" @click.self="closeCustomModal(false)">
    <div class="modal-container rounded-lg shadow-xl overflow-hidden" style="max-width: 400px; width: 100%; background: white; animation: modalIn 0.2s ease;">
      <div class="modal-header flex items-center justify-between p-16 border-b border-gray-200">
        <h3 class="modal-title m-0 text-base font-bold text-gray-900">{{ modalConfig.title }}</h3>
        <button class="modal-close bg-transparent border-none text-gray-400 hover:text-gray-600 cursor-pointer text-lg p-4" @click="closeCustomModal(false)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body p-20 text-gray-700 text-sm leading-relaxed">
        {{ modalConfig.message }}
      </div>
      <div class="modal-footer flex justify-end gap-12 p-16 bg-gray-50 border-t border-gray-200">
        <button v-if="!modalConfig.isAlert" class="btn btn-gray" @click="closeCustomModal(false)">Annuler</button>
        <button class="btn btn-primary" @click="closeCustomModal(true)">{{ modalConfig.isAlert ? 'OK' : 'Confirmer' }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

// Modal state
const showConfirmModal = ref(false);
const modalConfig = ref({
  title: 'Confirmation',
  message: '',
  isAlert: false,
  resolve: null
});

const customConfirm = (message, title = 'Confirmation') => {
  showConfirmModal.value = true;
  return new Promise(resolve => {
    modalConfig.value = { title, message, isAlert: false, resolve };
  });
};

const customAlert = (message, title = 'Information') => {
  showConfirmModal.value = true;
  return new Promise(resolve => {
    modalConfig.value = { title, message, isAlert: true, resolve };
  });
};

const closeCustomModal = (result) => {
  showConfirmModal.value = false;
  if (modalConfig.value.resolve) {
    modalConfig.value.resolve(result);
  }
};

const props = defineProps({
  decision: Object,
  currentVersion: Object,
  participants: Array,
  replyToFeedback: Object, 
  feedbacks: { type: Array, default: () => [] },
  consents: { type: Array, default: () => [] },
  allVersions: { type: Array, default: () => [] },
  isDocked: { type: Boolean, default: false },
  replyTrigger: { type: Number, default: 0 },
  canUndo: { type: Boolean, default: false }
});

const emit = defineEmits(['refresh-data', 'phase-change', 'cancel-reply', 'action-logged', 'version-change', 'dock', 'undock', 'drag-start', 'drag-end', 'direct-edit', 'publish-revision', 'undo-last-action']);

const authStore = useAuthStore();

const isCollapsed = ref(false);
const loading = ref(false);
const showForm = ref(false);
const showQuickActions = ref(false);

const isReadOnly = computed(() => {
  if (!props.decision || !props.currentVersion) return false;
  // Comparaison rigoureuse des IDs
  const decisionCurrentId = props.decision.current_version?.id || props.decision.current_version_id;
  return String(props.currentVersion.id) !== String(decisionCurrentId);
});

const sortedVersions = computed(() => {
  return [...props.allVersions].sort((a, b) => b.version_number - a.version_number);
});

const actingAsUserId = ref(authStore.user.id);
const feedbackContent = ref('');
const textareaRef = ref(null);
const selectedFeedbackType = ref('objection');

// Watches moved down to resolve initialization issues



watch(() => props.replyTrigger, () => {
  if (props.replyToFeedback) {
    showForm.value = true;
    isCollapsed.value = false;
  }
});

const cancelFeedback = () => {
  showForm.value = false;
  feedbackContent.value = '';
  selectedFeedbackType.value = 'objection';
  emit('cancel-reply');
};

const getSignalTypeForPhase = () => {
  const status = props.decision.status?.value || props.decision.status;
  if (status === 'clarification') return 'no_questions';
  if (status === 'reaction') return 'no_reaction';
  if (status === 'objection') return 'no_objection';
  return 'abstention';
};

const getFeedbackTypeForPhase = () => {
  const status = props.decision.status?.value || props.decision.status;
  if (status === 'clarification') return 'clarification';
  if (status === 'reaction') return 'reaction';
  if (status === 'objection') return selectedFeedbackType.value;
  return 'reaction';
};

const getTransitionAction = () => {
  let status = props.decision.status?.value || props.decision.status;
  if (typeof status === 'object') status = status.value;
  status = String(status).toLowerCase();
  
  if (status === 'clarification') return 'reaction';
  if (status === 'reaction') return 'objection';
  if (status === 'objection') return 'adopted';
  return null;
};

const participatedUserIds = computed(() => {
  const ids = new Set();
  
  // Feedbacks
  const feedbacks = props.feedbacks || [];
  feedbacks.forEach(fb => {
    const status = props.decision.status?.value || props.decision.status;
    const t = fb.type?.value || fb.type;
    if (t === status || (status === 'objection' && t === 'suggestion')) {
      ids.add(fb.author_id);
    }
    // Joins count as participation in objection phase
    if (fb.joins && status === 'objection') {
      fb.joins.forEach(j => ids.add(j.user_id));
    }
  });

  // Consents
  const consents = props.consents || [];
  const expectedSignal = getSignalTypeForPhase();
  consents.forEach(c => {
    const sig = typeof c.signal === 'object' && c.signal !== null ? c.signal.value : c.signal;
    if (sig === expectedSignal || (expectedSignal === 'no_objection' && sig === 'abstention')) {
      ids.add(c.user_id);
    }
  });

  return ids;
});

const allEligibleUsers = computed(() => {
  if (!props.decision?.circle?.members) return [];
  
  // Map roles from participants list (snapshot)
  const participantRoles = {};
  props.participants.forEach(p => {
    participantRoles[p.user_id] = p.role?.value || p.role;
  });

  return props.decision.circle.members
    .filter(m => {
      const circleRole = m.role?.value || m.role;
      if (circleRole === 'observer') return false;
      
      const decisionRole = participantRoles[m.user_id];
      // On exclut ceux qui ont un rôle spécial bloquant la participation directe
      if (['excluded', 'author', 'animator'].includes(decisionRole)) return false;
      
      return true;
    })
    .map(m => ({
      user_id: m.user_id,
      user: m.user,
      role: participantRoles[m.user_id] || 'participant'
    }));
});

// Compute remaining participants
const remainingParticipants = computed(() => {
  return allEligibleUsers.value.filter(p => !participatedUserIds.value.has(p.user_id));
});

// Compute participants eligible to act (for "Au nom de")
const finalActingParticipants = computed(() => {
  let list = [];
  if (props.replyToFeedback) {
    // Restriction demandée : Animateur, Porteur, ou auteur du feedback original
    list = props.participants.filter(p => {
      const r = p.role?.value || p.role;
      return r === 'animator' || 
             r === 'author' || 
             String(p.user_id) === String(props.replyToFeedback.author_id);
    });
  } else {
    // Nouveau feedback : participants n'ayant pas encore interagi
    list = [...allEligibleUsers.value.filter(p => !participatedUserIds.value.has(p.user_id))];
    
    // Ajouter l'utilisateur courant s'il n'est pas déjà dans la liste et qu'il peut encore participer
    if (!list.some(p => String(p.user_id) === String(authStore.user.id))) {
      const myParticipant = props.participants.find(p => String(p.user_id) === String(authStore.user.id));
      if (myParticipant && !participatedUserIds.value.has(authStore.user.id)) {
        list.unshift(myParticipant);
      }
    }
  }
  return list;
});

// Expand panel if reply targets changes
watch(() => props.replyToFeedback, (val) => {
  console.log("SecretaryPanel: replyToFeedback changed", val?.id);
  if (val) {
    showForm.value = true;
    isCollapsed.value = false;
    
    if (props.isDocked) {
      emit('dock'); 
    }

    // Auto-select animator or author when replying
    const participants = props.participants || [];
    const animator = participants.find(p => {
      const r = p.role?.value || p.role;
      return r === 'animator';
    });
    const author = participants.find(p => {
      const r = p.role?.value || p.role;
      return r === 'author';
    });
    const feedbackAuthor = participants.find(p => String(p.user_id) === String(val.author_id));

    nextTick(() => {
      if (animator) {
        actingAsUserId.value = animator.user_id;
      } else if (author) {
        actingAsUserId.value = author.user_id;
      } else if (feedbackAuthor) {
        actingAsUserId.value = feedbackAuthor.user_id;
      }
    });
  }
}, { immediate: true });

// Auto-select first available if current selection becomes invalid
watch(finalActingParticipants, (newList) => {
  if (newList.length > 0) {
    const stillValid = newList.find(p => String(p.user_id) === String(actingAsUserId.value));
    if (!stillValid) {
      // Prioritize animator in the new list if possible
      const animator = newList.find(p => (p.role?.value || p.role) === 'animator');
      actingAsUserId.value = animator ? animator.user_id : newList[0].user_id;
    }
  }
}, { immediate: true });

// --- Draggable Logic ---
const position = ref({ x: window.innerWidth - 380, y: 100 });
const isDragging = ref(false);
const dragOffset = ref({ x: 0, y: 0 });

const panelStyle = computed(() => {
  if (props.isDocked) return {}; // Pas de position absolue quand docké
  return {
    left: `${position.value.x}px`,
    top: `${position.value.y}px`
  };
});

const startDrag = (e) => {
  if (e.target.closest('button')) return;
  
  if (props.isDocked) {
    emit('undock');
    // On repositionne approximativement au curseur pour une transition fluide
    position.value = {
      x: e.clientX - 100,
      y: e.clientY - 20
    };
  }

  isDragging.value = true;
  emit('drag-start');
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

const stopDrag = (e) => {
  isDragging.value = false;
  emit('drag-end');
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDrag);
  
  // Si on lâche le panneau sur la partie gauche de l'écran, on le dock
  if (!props.isDocked && e.clientX < 280) {
    emit('dock');
  }
};

onUnmounted(() => {
  document.removeEventListener('mousemove', onDrag);
  document.removeEventListener('mouseup', stopDrag);
});

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value;
};

const prepareFeedbackFor = (userId) => {
  emit('cancel-reply'); // Important: sortir du mode réponse
  actingAsUserId.value = userId;
  feedbackContent.value = '';
  showForm.value = true;
  isCollapsed.value = false;
  setTimeout(() => {
    textareaRef.value?.focus();
  }, 100);
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

const sendQuickSignal = async (userId, signalType = null) => {
  if (loading.value) return;
  loading.value = true;
  
  try {
    const res = await axios.post(`/api/v1/decisions/${props.decision.id}/versions/${props.currentVersion.id}/consent`, {
      type: signalType || getSignalTypeForPhase(),
      acting_as_user_id: userId,
      notify: false
    });
    emit('action-logged', { type: 'consent', id: res.data.consent.id });
    emit('refresh-data');
  } catch (err) {
    customAlert(err.response?.data?.message || "Erreur lors de l'enregistrement.");
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
      acting_as_user_id: actingAsUserId.value,
      notify: false
    });
    
    emit('action-logged', { type: 'feedback', id: res.data.feedback.id });
    feedbackContent.value = '';
    showForm.value = false;
    emit('refresh-data');
  } catch (err) {
    customAlert(err.response?.data?.message || "Erreur lors de la publication.");
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
    showForm.value = false;
    emit('refresh-data');
    emit('cancel-reply');
  } catch (err) {
    customAlert(err.response?.data?.message || "Erreur lors de la réponse.");
  } finally {
    loading.value = false;
  }
};

const resolveFeedback = async (status) => {
  if (!props.replyToFeedback || loading.value) return;
  loading.value = true;
  try {
    await axios.put(`/api/v1/decisions/${props.decision.id}/feedback/${props.replyToFeedback.id}/status`, {
      status: status,
      notify: false
    });
    emit('refresh-data');
    cancelFeedback();
  } catch (err) {
    console.error('Feedback Delete Error:', err);
    customAlert(err.response?.data?.message || "Erreur lors de la mise à jour.");
  } finally {
    loading.value = false;
  }
};

const publishRevision = (targetPhase) => {
  emit('publish-revision', targetPhase);
};

const transitionError = ref(null);

const nextPhase = async () => {
  transitionError.value = null;
  const action = getTransitionAction();
  
  console.log('Meeting Mode - Attempting transition from:', props.decision.status, 'to:', action);

  if (!action) {
    transitionError.value = "Impossible de déterminer l'étape suivante.";
    return;
  }

  loading.value = true;
  try {
    const prevStatus = props.decision.status?.value || props.decision.status;
    await axios.post(`/api/v1/decisions/${props.decision.id}/transition`, { 
      to: action,
      notify: false,
      is_meeting: true
    });
    emit('refresh-data');
    emit('action-logged', { type: 'phase-transition', previousStatus: prevStatus, actionLabel: `next-phase-${action}` });
  } catch (err) {
    console.error('Transition Error:', err);
    transitionError.value = err.response?.data?.message || "Erreur lors du changement de phase.";
  } finally {
    loading.value = false;
  }
};

const confirmAction = async (type) => {
  const confirmMessages = {
    'suspend': 'Voulez-vous mettre cette décision en pause ?',
    'resume': 'Voulez-vous reprendre le processus ?',
    'revision-direct': 'Créer une révision et ouvrir l\'éditeur en direct ?',
    'revision-later': 'Passer en mode révision (brouillon) ? La révision pourra être rédigée ultérieurement.',
    'abandon': 'Êtes-vous sûr de vouloir abandonner définitivement cette décision ?',
    'undo': 'Êtes-vous sûr de vouloir annuler la dernière action effectuée ?'
  };
  
  const msg = confirmMessages[type] || 'Confirmer cette action ?';
  const confirmed = await customConfirm(msg);
  if (!confirmed) return;
  
  if (type === 'undo') {
    showQuickActions.value = false;
    emit('undo-last-action');
    return;
  }
  
  handleQuickAction(type);
};

const handleQuickAction = async (type) => {
  showQuickActions.value = false;
  loading.value = true;
  transitionError.value = null;

  try {
    let endpoint = `/api/v1/decisions/${props.decision.id}/transition`;
    let data = {};

    if (type === 'suspend') {
      data = { to: 'suspended' };
    } else if (type === 'resume') {
      data = { to: props.decision.status_before_suspension || 'clarification' };
    } else if (type === 'revision-direct' || type === 'revision-later') {
      data = { to: 'revision' };
    } else if (type === 'abandon') {
      endpoint = `/api/v1/decisions/${props.decision.id}/abandon`;
    }

    await axios.post(endpoint, data);
    
    // Log action with previous status for potential rollback
    const prevStatus = props.decision.status?.value || props.decision.status;
    emit('action-logged', { type: 'phase-transition', previousStatus: prevStatus, actionLabel: type });
    emit('refresh-data');
    
    // If revision-direct, open the editor after a short delay for data refresh
    if (type === 'revision-direct') {
      setTimeout(() => {
        emit('direct-edit');
      }, 500);
    }
  } catch (err) {
    console.error('Quick Action Error:', err);
    customAlert(err.response?.data?.message || "Erreur lors de l'action.");
  } finally {
    loading.value = false;
  }
};

const getRoleIcon = (role) => {
  const r = role?.value || role;
  if (r === 'author') return 'fa-solid fa-bullhorn';
  if (r === 'animator') return 'fa-solid fa-user-tie';
  return 'fa-solid fa-user';
};

const getRoleColor = (role) => {
  const r = role?.value || role;
  if (r === 'author') return 'bg-blue-500';
  if (r === 'animator') return 'bg-amber-500';
  return 'bg-gray-400';
};
</script>

<style scoped>

.secretary-header-role {
  padding: 12px 16px;
  background: white;
  border-bottom: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  gap: 12px;
  transition: background 0.2s;
  cursor: move !important;
}

.secretary-header-role:active {
  cursor: grabbing !important;
}

.secretary-header-role:hover {
  background: var(--gray-50);
}

.role-item-mini {
  display: flex;
  align-items: center;
  gap: 12px;
}

.role-icon-mini {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.role-icon-mini.secretariat {
  background: #ecfdf5;
  border: 1px solid #10b981;
  color: #059669;
}

.role-info-mini {
  display: flex;
  flex-direction: column;
}

.role-label-mini {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--gray-500);
  font-weight: 700;
  line-height: 1.2;
}

.role-name-mini {
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-900);
  line-height: 1.2;
}

.secretary-panel-classic {
  position: fixed;
  width: 350px;
  z-index: 9990;
  overflow: hidden;
  transition: width 0.3s ease, height 0.3s ease;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  margin-bottom: 0;
}

.secretary-panel-classic.is-docked {
  position: relative !important;
  left: auto !important;
  top: auto !important;
  width: 100% !important;
  box-shadow: none !important;
  border-radius: 0;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-lg);
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

.participant-list-scrollable {
  max-height: 280px;
  overflow-y: auto;
  padding-right: 4px;
}

.participant-list-scrollable::-webkit-scrollbar {
  width: 6px;
}
.participant-list-scrollable::-webkit-scrollbar-thumb {
  background: var(--indigo-200);
  border-radius: 3px;
}

.sexy-select {
  width: 100%;
  padding: 10px 12px;
  border-radius: var(--radius-md);
  border: 1px solid var(--gray-200);
  background-color: var(--gray-50);
  font-size: 13px;
  color: var(--gray-800);
  font-weight: 500;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 16px;
  transition: all 0.2s;
}
.sexy-select:focus {
  outline: none;
  border-color: var(--indigo-400);
  background-color: white;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.sexy-textarea {
  width: 100%;
  padding: 12px;
  border-radius: var(--radius-md);
  border: 1px solid var(--gray-200);
  background-color: white;
  font-size: 13px;
  color: var(--gray-800);
  line-height: 1.5;
  resize: vertical;
  transition: all 0.2s;
}
.sexy-textarea:focus {
  outline: none;
  border-color: var(--indigo-400);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-container-meeting {
  animation: slide-in 0.3s ease-out;
}

@keyframes slide-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}

.btn-outline-gray {
  background: white;
  border: 1px solid var(--gray-100);
  color: var(--gray-700);
  transition: all 0.2s;
}

.btn-outline-gray:hover {
  border-color: var(--indigo-200);
  background: var(--gray-50);
  color: var(--indigo-700);
}
</style>

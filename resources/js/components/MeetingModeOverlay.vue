<template>
  <div class="meeting-mode-overlay" ref="overlayRef">
    <!-- Header épuré -->
    <div class="meeting-header grad-indigo text-white">
      <div class="meeting-logo-container">
        <div class="logo-circle">
          <img :src="configStore.defaultLogoUrl" alt="DAZO Logo">
        </div>
      </div>
      
      <div v-if="configStore.hasCustomLogo" class="meeting-custom-logo-container">
        <img :src="configStore.customLogoUrl" alt="Custom Logo" class="meeting-custom-logo" />
      </div>

      <div class="meeting-title">
        <h1 class="text-3xl font-bold text-white mb-4">{{ decision.title }}</h1>
        <div class="meeting-breadcrumb">
          <span class="mb-step" :class="{ active: currentStatus === 'clarification', completed: isPast('clarification') }">Clarification</span>
          <i class="fa-solid fa-chevron-right mb-sep"></i>
          <span class="mb-step" :class="{ active: currentStatus === 'reaction', completed: isPast('reaction') }">Réaction</span>
          <i class="fa-solid fa-chevron-right mb-sep"></i>
          <span class="mb-step" :class="{ active: currentStatus === 'objection', completed: isPast('objection') }">Objection</span>
          <i class="fa-solid fa-chevron-right mb-sep"></i>
          <span class="mb-step" :class="{ active: currentStatus === 'adopted', completed: isPast('adopted') }">Adoptée</span>
        </div>
      </div>
      
      <div class="ml-auto flex items-center gap-16">
        <!-- Tag Version -->
        <div class="version-tag">
          Version {{ currentVersion.version_number }}
        </div>

        <!-- Bouton Undo -->
        <button 
          v-if="isAnimator && actionHistory.length > 0"
          class="btn-undo" 
          @click="undoLastAction" 
          title="Annuler la dernière action (Ctrl+Z)"
        >
          <i class="fa-solid fa-rotate-left mr-8"></i> Annuler
        </button>

        <!-- Test Célébration (Admin/Secrétaire seulement) -->
        <button 
          v-if="isAnimator"
          class="btn-celebrate-test" 
          @click.stop="triggerCelebration" 
          title="Tester l'effet Wow"
        >
          <i class="fa-solid fa-wand-magic-sparkles"></i>
        </button>

        <!-- Fermer -->
        <button class="btn-close-meeting" @click="closeMeetingMode" title="Quitter le mode réunion (Echap)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

    <!-- Overlay de Célébration -->
    <div v-if="showCelebration" class="celebration-overlay" @click="showCelebration = false">
      <!-- Canvas dédié aux confettis pour être sûr qu'ils soient au premier plan sur tout l'écran -->
      <canvas id="celebration-canvas" style="position:fixed; top:0; left:0; width:100vw; height:100vh; pointer-events:none; z-index:2147483647;"></canvas>

      <div class="celebration-content">
        <div class="celebration-logo">
          <img src="/DAZO-logo-carre-blanc.svg" alt="DAZO Logo">
        </div>
        <h2 class="celebration-title">ZÉRO OBJECTION</h2>
        <p class="celebration-text">Félicitations à toutes et à tous,<br>pour cette décision adoptée !</p>
        <div class="mt-32">
          <button class="btn btn-white btn-lg" @click.stop="showCelebration = false">Continuer</button>
        </div>
      </div>
    </div>

    <div class="meeting-main-layout">
      <!-- Bloc Gauche : Porteur & Animateur -->
      <div class="meeting-left-sidebar" :class="{ 'is-collapsed': isSidebarCollapsed }">
        
        <button class="btn-toggle-sidebar" @click="isSidebarCollapsed = !isSidebarCollapsed">
           <i class="fa-solid" :class="isSidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
        </button>

        <div class="sidebar-content" v-show="!isSidebarCollapsed">
          <div class="roles-block">
            <div class="role-item">
              <div class="role-icon porteur">
                <i class="fa-solid fa-bullhorn"></i>
              </div>
              <div class="role-info">
                <span class="role-label">Porteur</span>
                <span class="role-name">{{ authorParticipant?.user?.name || 'Non défini' }}</span>
              </div>
            </div>

            <div class="role-item">
              <div class="role-icon animateur">
                <i class="fa-solid fa-user-tie"></i>
              </div>
              <div class="role-info">
                <span class="role-label">Animateur</span>
                <span class="role-name">{{ animatorParticipant?.user?.name || 'Non défini' }}</span>
              </div>
            </div>
          </div>

          <!-- Zone de dock pour le panneau de secrétariat -->
          <div id="secretary-dock-target" class="mt-32">
            <div v-if="isDraggingPanel && !isPanelDocked" class="dock-placeholder">
              <i class="fa-solid fa-download mb-8 text-2xl"></i>
              <span>Glissez ici pour ancrer le secrétariat</span>
            </div>
          </div>
        </div>
      </div>

      <div class="meeting-content-wrapper">
        <div class="meeting-body-flex">
          <!-- Nouvelle Barre Latérale : Contenu détaillé & PJ -->
          <div class="meeting-doc-sidebar" :class="{ 'is-collapsed': isDocSidebarCollapsed }">
            <button class="btn-toggle-sidebar" @click="isDocSidebarCollapsed = !isDocSidebarCollapsed">
              <i class="fa-solid" :class="isDocSidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
            </button>
            
            <div class="sidebar-content" v-show="!isDocSidebarCollapsed">
              <!-- Prose en affichage fluide (Mode Flat) -->
              <div class="meeting-prose-container flex-1">
                <div class="meeting-prose prose-sm" v-html="displayContent"></div>
              </div>

              <!-- Pièces jointes -->
              <div v-if="attachments && attachments.length > 0" class="meeting-attachments-panel mt-24 pt-24 border-t border-gray-200">
                <h3 class="text-xs font-bold mb-12 text-gray-400 uppercase tracking-wider">Pièces jointes</h3>
                <div class="flex flex-col gap-8">
                  <button 
                    v-for="(att, idx) in attachments" 
                    :key="att.id"
                    class="attachment-row"
                    @click.stop.prevent="openFloatingAttachment(att)"
                  >
                    <i class="fa-solid fa-paperclip mr-12 text-indigo-400"></i>
                    <span class="font-medium truncate">{{ att.filename }}</span>
                    <i class="fa-solid fa-chevron-right ml-auto text-[10px] opacity-30"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Zone Principale : Feedbacks & Onglets -->
          <div class="meeting-feedbacks-area flex-1">
            <div class="meeting-feedbacks">
              <div class="tabs-container mb-24">
                <div class="tabs">
                  <button class="tab" :class="{active: activeTab === 'clarifications'}" @click="activeTab = 'clarifications'">
                    <i class="fa-solid fa-question-circle mr-8 opacity-70"></i> Clarifications
                  </button>
                  <button class="tab" :class="{active: activeTab === 'reactions'}" @click="activeTab = 'reactions'">
                    <i class="fa-solid fa-face-smile mr-8 opacity-70"></i> Réactions
                  </button>
                  <button class="tab" :class="{active: activeTab === 'objections'}" @click="activeTab = 'objections'">
                    <i class="fa-solid fa-shield-halved mr-8 opacity-70"></i> Objections & Suggestions
                  </button>
                </div>
              </div>

              <div v-if="loadingFeedbacks" class="text-gray-500 text-center py-48">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-12"></i>
                <p>Chargement des échanges...</p>
              </div>
              
              <div v-else class="feedbacks-list">
                <!-- Liste des personnes ayant validé -->
                <div v-if="consentedParticipants.length > 0" class="mf-consents-summary mb-24">
                  <div class="flex items-center gap-8 mb-12">
                    <i class="fa-solid fa-check-circle text-emerald-500"></i>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                      {{ getPositiveSignalLabel() }} ({{ consentedParticipants.length }})
                    </span>
                  </div>
                  <div class="flex flex-wrap gap-8">
                    <div v-for="c in consentedParticipants" :key="c.id" class="badge badge-sm bg-emerald-50 text-emerald-700 border-emerald-200" :title="c.user?.name">
                      {{ c.user?.name }}
                    </div>
                  </div>
                </div>

                <div v-if="filteredFeedbacks.length === 0 && consentedParticipants.length === 0" class="text-gray-500 italic text-center py-24">
                  Aucun échange dans cette catégorie.
                </div>

                <!-- Boucle sur les feedbacks -->
                <template v-if="activeTab !== 'reactions'">
                  <div v-for="fb in filteredFeedbacks" :key="fb.id" class="mf-card" :class="{'fb-closed': isClosed(fb)}">
                    <div class="mf-header" @click.stop="toggleCollapse(fb.id)" style="cursor: pointer;">
                <div class="flex flex-col">
                  <div class="flex items-center gap-8">
                    <span :class="typeBadge(fb.type)" class="badge badge-xs uppercase px-6 font-bold text-[9px]">
                      {{ translateType(fb.type) }}
                    </span>
                    <span class="font-bold text-gray-800">{{ fb.author?.name }}</span>
                  </div>
                  <!-- Liste des soutiens directement dans le titre -->
                  <div v-if="fb.joins && fb.joins.length > 0" class="text-[10px] text-gray-500 italic font-normal ml-4">
                    Soutenu par ({{ fb.joins.length }}) : {{ fb.joins.map(j => j.user?.name).join(', ') }}
                  </div>
                </div>
                
                <div class="ml-auto flex items-center gap-12">
                  <!-- Bouton Soutenir / Rejoindre -->
                  <button 
                    v-if="isAnimator && !isClosed(fb) && ['objection', 'suggestion'].includes(fb.type?.value || fb.type)"
                    class="btn btn-xs btn-outline-indigo"
                    @click.stop="showJoinPickerFor = (showJoinPickerFor === fb.id ? null : fb.id)"
                    :title="'Ajouter un soutien à cette ' + (fb.type?.value || fb.type)"
                  >
                    <i class="fa-solid fa-users mr-4"></i> Rejoindre
                  </button>

                  <!-- Bouton Répondre pour le secrétaire (Masqué si la phase est passée) -->
                  <button 
                    v-if="isAnimator && !isClosed(fb) && canReply(fb)" 
                    class="btn btn-xs btn-secondary"
                    @click.stop="prepareReply(fb)"
                  >
                    <i class="fa-solid fa-reply mr-4"></i> Répondre
                  </button>

                  <!-- Bouton Toggle -->
                  <button 
                    class="btn btn-xs btn-ghost" 
                    @click.stop="toggleCollapse(fb.id)"
                    :title="isCollapsed(fb.id) ? 'Déplier' : 'Replier'"
                  >
                    <i class="fa-solid" :class="isCollapsed(fb.id) ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                  </button>
                </div>
              </div>

              <!-- Sélecteur pour Rejoindre -->
              <div v-if="showJoinPickerFor === fb.id" class="px-16 pb-12">
                <div class="bg-indigo-50 p-12 rounded-md border border-indigo-100 shadow-sm">
                   <div class="flex justify-between items-center mb-12">
                     <p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider">Qui rejoint cette {{ translateType(fb.type) }} ?</p>
                     <button @click.stop="showJoinPickerFor = null" class="text-indigo-400 hover:text-indigo-600 p-4" title="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                     </button>
                   </div>
                   <div class="flex flex-wrap gap-8">
                      <button 
                        v-for="p in getJoinableParticipants(fb)" 
                        :key="p.user_id"
                        class="btn btn-xs bg-white border-indigo-200 text-indigo-700 hover:bg-indigo-500 hover:text-white px-12 py-6 rounded shadow-sm"
                        @click.stop="joinFeedback(fb, p.user_id)"
                      >
                        {{ p.user?.name }}
                      </button>
                      <span v-if="getJoinableParticipants(fb).length === 0" class="text-xs text-gray-400 italic">Aucun participant éligible.</span>
                   </div>
                </div>
              </div>
              
              <div v-if="!isCollapsed(fb.id)" class="mf-body">
                <!-- Liste des messages (thread) -->
                <div v-for="msg in allMessages(fb)" :key="msg.id" class="mf-msg" :class="getRoleStyle(msg.author_id)">
                  <div class="mf-msg-author">
                    {{ msg.author?.name }} 
                    <span v-if="getRoleBadge(msg.author_id)" class="role-badge">{{ getRoleBadge(msg.author_id) }}</span>
                    <span class="text-xs text-gray-400 font-normal ml-8">{{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                  </div>
                  <div class="mf-msg-content">{{ msg.content }}</div>
                </div>
              </div>
            </div>
          </template>

          <!-- Bloc spécifique pour les Réactions -->
          <template v-else>
            <div v-for="fb in filteredFeedbacks" :key="fb.id" class="mf-card" :class="{'fb-closed': isClosed(fb)}">
              <div class="mf-header" @click.stop="toggleCollapse(fb.id)" style="cursor: pointer;">
                <div class="flex items-center gap-8">
                  <span :class="typeBadge(fb.type)" class="badge badge-xs uppercase px-6 font-bold text-[9px]">
                    {{ translateType(fb.type) }}
                  </span>
                  <span class="font-bold text-gray-800">{{ fb.author?.name }}</span>
                </div>
                
                <div class="ml-auto flex items-center gap-12">
                  <button 
                    class="btn btn-xs btn-ghost" 
                    @click.stop="toggleCollapse(fb.id)"
                    :title="isCollapsed(fb.id) ? 'Déplier' : 'Replier'"
                  >
                    <i class="fa-solid" :class="isCollapsed(fb.id) ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                  </button>
                </div>
              </div>
              
              <div v-if="!isCollapsed(fb.id)" class="mf-body">
                <div class="text-gray-700 whitespace-pre-wrap">{{ fb.content }}</div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

    <!-- Panneau du Secrétaire (Flottant ou Docké) -->
    <Teleport v-if="isMounted && isAnimator" to="#secretary-dock-target" :disabled="!isPanelDocked">
      <MeetingSecretaryPanel 
        :decision="decision"
        :current-version="currentVersion"
        :participants="participants"
        :feedbacks="feedbacks"
        :consents="consents"
        :all-versions="allVersions"
        :reply-to-feedback="replyTarget"
        :reply-trigger="replyTrigger"
        :is-docked="isPanelDocked"
        @dock="handleDock"
        @undock="isPanelDocked = false"
        @phase-change="$emit('phase-change', $event)"
        @refresh-data="refreshAll"
        @cancel-reply="replyTarget = null"
        @action-logged="logAction"
        @version-change="$emit('version-change', $event)"
        @drag-start="isDraggingPanel = true"
        @drag-end="isDraggingPanel = false"
      />
    </Teleport>

    <!-- Pièces jointes flottantes (Doit rester DANS l'overlay pour être visible en mode plein écran HTML5) -->
    <FloatingWindow
      v-for="fw in activeFloatingAttachments"
      :key="fw.id"
      :id="fw.id"
      :title="fw.title"
      :url="fw.url"
      :z-index="fw.zIndex"
      :initial-x="fw.x"
      :initial-y="fw.y"
      @focus="bringToFront"
      @close="closeFloatingAttachment"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue';
import axios from 'axios';
import MeetingSecretaryPanel from './MeetingSecretaryPanel.vue';
import FloatingWindow from './FloatingWindow.vue';
import { useConfigStore } from '../stores/config';

const configStore = useConfigStore();

// On essaie l'import local, mais on prévoira un fallback
// Suppression de l'import local qui semble poser problème avec le build Vite
// On utilisera un chargement dynamique via CDN

const props = defineProps({
  decision: { type: Object, required: true },
  currentVersion: { type: Object, required: true },
  attachments: { type: Array, default: () => [] },
  isAnimator: { type: Boolean, default: false },
  participants: { type: Array, default: () => [] },
  allVersions: { type: Array, default: () => [] }
});

const emit = defineEmits(['close', 'open-attachment', 'phase-change', 'refresh-data', 'version-change']);
const overlayRef = ref(null);
const isMounted = ref(false);

onMounted(() => {
  isMounted.value = true;
  console.log("DAZO MeetingModeOverlay v2.2 Mounted");
});

const feedbacks = ref([]);
const consents = ref([]);
const loadingFeedbacks = ref(false);
const activeTab = ref('clarifications');

// Suivre le changement de phase pour mettre à jour l'onglet actif automatiquement
watch(() => props.decision.status?.value || props.decision.status, (newStatus) => {
  if (newStatus === 'clarification') activeTab.value = 'clarifications';
  if (newStatus === 'reaction') activeTab.value = 'reactions';
  if (newStatus === 'objection') activeTab.value = 'objections';
}, { immediate: true });

const replyTarget = ref(null);
const replyTrigger = ref(0);
const collapsedFeedbacks = ref({}); // { fbId: true/false }
const showCelebration = ref(false);
const showJoinPickerFor = ref(null);
const isDocSidebarCollapsed = ref(false);

const isSidebarCollapsed = ref(false);
const isPanelDocked = ref(true);
const isDraggingPanel = ref(false);

// Floating Attachments State
const activeFloatingAttachments = ref([]);
const highestZIndex = ref(1000000); // Au dessus du overlay de la réunion

const openFloatingAttachment = (att) => {
  console.log('openFloatingAttachment called for:', att);
  const targetId = att.id || Math.random().toString(36).substr(2, 9);
  
  // Vérifier si déjà ouverte
  const existingIndex = activeFloatingAttachments.value.findIndex(fw => fw.id === targetId);
  if (existingIndex !== -1) {
    // Ramener au premier plan et centrer visuellement (ou laisser la position)
    bringToFront(targetId);
    
    // Centrer la fenêtre
    const vW = window.innerWidth;
    const vH = window.innerHeight;
    const w = 600;
    const h = 450;
    activeFloatingAttachments.value[existingIndex].x = Math.max(0, (vW - w) / 2);
    activeFloatingAttachments.value[existingIndex].y = Math.max(0, (vH - h) / 2);
    return;
  }

  const vW = window.innerWidth;
  const vH = window.innerHeight;
  const w = 600;
  const h = 450;
  
  // Effet de cascade
  const offset = activeFloatingAttachments.value.length * 30;
  let initialX = Math.max(0, (vW - w) / 2) + offset;
  let initialY = Math.max(0, (vH - h) / 2) + offset;

  highestZIndex.value += 1;

  const resolvedUrl = att.s3_path ? `/storage/${att.s3_path}` : (att.url || '');
  console.log('Resolved URL for attachment:', resolvedUrl);

  activeFloatingAttachments.value.push({
    id: targetId,
    title: att.filename || att.name || 'Document',
    url: resolvedUrl,
    x: initialX,
    y: initialY,
    zIndex: highestZIndex.value
  });
};

const bringToFront = (id) => {
  const index = activeFloatingAttachments.value.findIndex(fw => fw.id === id);
  if (index !== -1) {
    highestZIndex.value += 1;
    activeFloatingAttachments.value[index].zIndex = highestZIndex.value;
  }
};

const closeFloatingAttachment = (id) => {
  activeFloatingAttachments.value = activeFloatingAttachments.value.filter(fw => fw.id !== id);
};

const handleDock = () => {
  isPanelDocked.value = true;
  isSidebarCollapsed.value = false;
};

const prepareReply = (fb) => {
  console.log("prepareReply called for feedback:", fb.id);
  isSidebarCollapsed.value = false;
  showJoinPickerFor.value = null; // Fermer le sélecteur de soutien si ouvert
  
  if (replyTarget.value?.id === fb.id) {
    replyTarget.value = null;
    nextTick(() => {
      replyTarget.value = fb;
      replyTrigger.value++;
      console.log("replyTarget forced reset and set to:", fb.id);
    });
  } else {
    replyTarget.value = fb;
    replyTrigger.value++;
    console.log("replyTarget set to:", fb.id);
  }
};

const getJoinableParticipants = (fb) => {
  const currentPhase = props.decision.status?.value || props.decision.status;
  
  // Identifier TOUS ceux qui ont déjà participé à cette phase
  const participatedIds = new Set();
  
  // 1. Ceux qui ont fait un feedback direct (objection ou suggestion)
  feedbacks.value.forEach(f => {
    const fType = f.type?.value || f.type;
    const isFeedbackInCurrentPhase = 
      (fType === currentPhase) || 
      (currentPhase === 'objection' && fType === 'suggestion');

    if (isFeedbackInCurrentPhase) {
      participatedIds.add(String(f.author_id));
    }
    // 2. Ceux qui ont REJOINT un feedback de la phase actuelle
    if (f.joins && isFeedbackInCurrentPhase) {
      f.joins.forEach(j => participatedIds.add(String(j.user_id)));
    }
  });

  // 3. Ceux qui ont donné un signal "OK" (consent) dans la phase ACTUELLE
  consents.value.forEach(c => {
    const signalPhase = c.phase?.value || c.phase;
    if (signalPhase === currentPhase) {
      participatedIds.add(String(c.user_id));
    }
  });

  return props.participants.filter(p => {
    const userIdStr = String(p.user_id);
    
    // Pas l'auteur de ce feedback spécifique
    if (userIdStr === String(fb.author_id)) return false;
    
    // Ni le porteur, ni l'animateur (rôles d'animation)
    const role = p.role?.value || p.role;
    if (role === 'author' || role === 'animator') return false;

    // Pas déjà participé (Tour de table restant uniquement)
    if (participatedIds.has(userIdStr)) return false;

    return true;
  });
};

const joinFeedback = async (fb, userId) => {
  try {
    await axios.post(`/api/v1/feedback/${fb.id}/join`, { user_id: userId });
    refreshAll();
  } catch (err) {
    console.error("Erreur joining feedback", err);
    alert("Impossible d'ajouter ce soutien.");
  }
};

const currentStatus = computed(() => props.decision.status?.value || props.decision.status);

const isPast = (phase) => {
  const order = ['draft', 'clarification', 'reaction', 'objection', 'adopted'];
  const currentIndex = order.indexOf(currentStatus.value);
  const phaseIndex = order.indexOf(phase);
  return phaseIndex < currentIndex;
};

const triggerCelebration = () => {
  console.log('Triggering celebration...');
  showCelebration.value = true;
  
  const startConfetti = () => {
    const canvas = document.getElementById('celebration-canvas');
    if (!canvas) {
      console.error('Celebration canvas NOT FOUND');
      return;
    }
    
    // On force les dimensions réelles du canvas pour couvrir tout l'écran
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const myConfetti = window.confetti.create(canvas, { 
      resize: true,
      useWorker: true 
    });

    myConfetti({
      particleCount: 150,
      spread: 70,
      origin: { y: 0.6 }
    });

    const duration = 5 * 1000;
    const animationEnd = Date.now() + duration;
    const defaults = { startVelocity: 30, spread: 360, ticks: 60 };

    const interval = setInterval(function() {
      const timeLeft = animationEnd - Date.now();
      if (timeLeft <= 0) return clearInterval(interval);

      const particleCount = 50 * (timeLeft / duration);
      myConfetti({ ...defaults, particleCount, origin: { x: Math.random() * (0.3 - 0.1) + 0.1, y: Math.random() - 0.2 } });
      myConfetti({ ...defaults, particleCount, origin: { x: Math.random() * (0.9 - 0.7) + 0.7, y: Math.random() - 0.2 } });
    }, 250);
  };

  // On attend que la bibliothèque soit là et que le DOM soit à jour (v-if)
  nextTick(() => {
    if (window.confetti) {
      startConfetti();
    } else {
      let attempts = 0;
      const checkInterval = setInterval(() => {
        attempts++;
        if (window.confetti) {
          clearInterval(checkInterval);
          startConfetti();
        } else if (attempts > 20) {
          clearInterval(checkInterval);
          console.error('Confetti library never loaded');
        }
      }, 100);
    }
  });
};

// Auto-trigger de la célébration quand on passe en phase "Adoptée"
watch(currentStatus, (newStatus, oldStatus) => {
  if (newStatus === 'adopted' && oldStatus !== 'adopted') {
    // On pourrait vérifier ici s'il y a réellement eu 0 objections, 
    // mais si le bouton "Adopter" a été pressé, c'est que le processus est un succès.
    triggerCelebration();
  }
});

// Re-charger les données si la version change (via le sélecteur du secrétaire)
watch(() => props.currentVersion.id, () => {
  fetchFeedbacks();
  replyTarget.value = null;
});

// Historique d'annulation (Undo)
const actionHistory = ref([]); // Array of { type: 'consent'|'feedback'|'message', id: 123 }

const logAction = (action) => {
  actionHistory.value.push(action);
};

const undoLastAction = async () => {
  if (actionHistory.value.length === 0) return;
  const lastAction = actionHistory.value[actionHistory.value.length - 1];
  
  if (!confirm(`Êtes-vous sûr de vouloir annuler la dernière action effectuée en live ?`)) {
    return;
  }

  try {
    let url = '';
    if (lastAction.type === 'consent') url = `/api/v1/consents/${lastAction.id}`;
    if (lastAction.type === 'feedback') url = `/api/v1/feedback/${lastAction.id}`;
    if (lastAction.type === 'message') url = `/api/v1/feedback/messages/${lastAction.id}`;

    await axios.delete(url);
    
    // Succès
    actionHistory.value.pop();
    refreshAll();
  } catch (err) {
    alert(err.response?.data?.message || "Erreur lors de l'annulation.");
  }
};

const handleKeyDown = (e) => {
  if (e.ctrlKey && e.key === 'z') {
    e.preventDefault();
    undoLastAction();
  }
};

const displayContent = computed(() => {
  return props.currentVersion?.content || '<p class="text-muted">Aucun contenu.</p>';
});

const filteredFeedbacks = computed(() => {
  return feedbacks.value.filter(fb => {
    const t = fb.type?.value || fb.type;
    if (activeTab.value === 'clarifications') return t === 'clarification';
    if (activeTab.value === 'reactions') return t === 'reaction';
    if (activeTab.value === 'objections') return t === 'objection' || t === 'suggestion';
    return false;
  });
});

const authorParticipant = computed(() => props.participants.find(p => p.role?.value === 'author' || p.role === 'author'));
const animatorParticipant = computed(() => props.participants.find(p => p.role?.value === 'animator' || p.role === 'animator'));

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

const statusBadgeClass = computed(() => {
  const map = {
    'draft': 'badge-gray',
    'clarification': 'badge-indigo',
    'reaction': 'badge-blue',
    'objection': 'badge-amber',
    'adopted': 'badge-emerald',
    'rejected': 'badge-red',
    'abandoned': 'badge-gray',
  };
  return map[props.decision.status] || 'badge-gray';
});

const typeBadge = (type) => {
  const t = type?.value || type;
  if (t === 'clarification') return 'badge-indigo';
  if (t === 'reaction') return 'badge-blue';
  if (t === 'objection') return 'badge-amber';
  if (t === 'suggestion') return 'badge-teal';
  return 'badge-gray';
};

const getSignalTypeForPhase = () => {
  const active = activeTab.value;
  if (active === 'clarifications') return 'no_questions';
  if (active === 'reactions') return 'no_reaction';
  if (active === 'objections') return 'no_objection';
  return 'abstention';
};

const getPositiveSignalLabel = () => {
  if (activeTab.value === 'clarifications') return "Ont dit : C'est clair";
  if (activeTab.value === 'reactions') return "Ont dit : RAS";
  if (activeTab.value === 'objections') return "N'ont pas d'objection";
  return "Ont validé";
};

const consentedParticipants = computed(() => {
  const targetSignal = getSignalTypeForPhase();
  const active = activeTab.value;
  const currentPhase = active === 'clarifications' ? 'clarification' : (active === 'reactions' ? 'reaction' : 'objection');
  
  return (consents.value || []).filter(c => {
    const s = c.signal?.value || c.signal;
    const p = c.phase?.value || c.phase;

    // Filtre par signal
    const matchesSignal = s === targetSignal || (targetSignal === 'no_objection' && s === 'abstention');
    if (!matchesSignal) return false;
    
    // Si la phase est renseignée, elle doit correspondre à l'onglet actif
    if (p && p !== currentPhase) return false;

    // Exclure ceux qui ont un feedback dans cette phase
    const hasFeedback = feedbacks.value.some(fb => {
        const type = fb.type?.value || fb.type;
        const fbPhase = (type === 'suggestion' ? 'objection' : type);
        return fb.author_id === c.user_id && fbPhase === currentPhase;
    });
    
    return !hasFeedback;
  });
});

const getRoleStyle = (authorId) => {
  const p = props.participants.find(p => String(p.user_id) === String(authorId));
  const role = p ? (p.role?.value || p.role) : 'participant';
  if (role === 'author') return 'role-author';
  if (role === 'animator') return 'role-animator';
  return 'role-participant';
};

const getRoleBadge = (authorId) => {
  const p = props.participants.find(p => String(p.user_id) === String(authorId));
  if (!p) return '';
  const role = p.role?.value || p.role;
  if (role === 'author') return 'Porteur';
  if (role === 'animator') return 'Animateur';
  return '';
};

const translateType = (type) => {
  const t = type?.value || type;
  if (t === 'objection') return 'Objection';
  if (t === 'suggestion') return 'Suggestion';
  if (t === 'clarification') return 'Clarification';
  if (t === 'reaction') return 'Réaction';
  return t;
};

const toggleCollapse = (id) => {
  collapsedFeedbacks.value = {
    ...collapsedFeedbacks.value,
    [id]: !collapsedFeedbacks.value[id]
  };
};
const isCollapsed = (id) => !!collapsedFeedbacks.value[id];

const getVal = (v) => v?.value || v || '';
const isClosed = (fb) => {
  const s = fb.status?.value || fb.status;
  return ['withdrawn', 'rejected', 'acknowledged'].includes(s);
};

const allMessages = (fb) => {
  const msgs = [];
  msgs.push({
    id: 'fb-'+fb.id,
    author_id: fb.author_id,
    author: fb.author,
    content: fb.content,
    created_at: fb.created_at
  });
  if (fb.messages) {
    msgs.push(...fb.messages);
  }
  return msgs;
};

const canReply = (fb) => {
  const status = props.decision.status?.value || props.decision.status;
  const type = fb.type?.value || fb.type;
  // On ne peut répondre que si on est dans la phase correspondante
  if (type === 'clarification') return status === 'clarification';
  if (['objection', 'suggestion'].includes(type)) return status === 'objection';
  return false;
};

const fetchFeedbacks = async () => {
  loadingFeedbacks.value = true;
  try {
    const { data } = await axios.get(`/api/v1/decisions/${props.decision.id}/feedback?version_id=${props.currentVersion.id}`);
    feedbacks.value = data.feedbacks || [];
    consents.value = data.consents || [];
  } catch (err) {
    console.error("Erreur de chargement des échanges", err);
  } finally {
    loadingFeedbacks.value = false;
  }
};

const refreshAll = () => {
  fetchFeedbacks();
  emit('refresh-data');
};

// Fullscreen API Logic
const enterFullscreen = () => {
  const elem = overlayRef.value;
  if (elem && elem.requestFullscreen) {
    elem.requestFullscreen().catch(err => {
      console.warn(`Erreur tentative plein écran: ${err.message}`);
    });
  }
};

const exitFullscreen = () => {
  if (document.fullscreenElement && document.exitFullscreen) {
    document.exitFullscreen().catch(err => console.warn(err));
  }
};

const handleFullscreenChange = () => {
  if (!document.fullscreenElement) {
    emit('close');
  }
};

const closeMeetingMode = () => {
  exitFullscreen();
  emit('close');
};

onMounted(() => {
  document.body.style.overflow = 'hidden';
  enterFullscreen();
  document.addEventListener('fullscreenchange', handleFullscreenChange);
  window.addEventListener('keydown', handleKeyDown);

  // Chargement dynamique des confettis
  if (!window.confetti) {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js';
    script.async = true;
    document.head.appendChild(script);
  }
  
  // Initialisation de l'onglet actif selon la phase de la décision
  const status = props.decision.status?.value || props.decision.status;
  if (status === 'clarification') activeTab.value = 'clarifications';
  else if (status === 'reaction') activeTab.value = 'reactions';
  else if (status === 'objection') activeTab.value = 'objections';
  else if (status === 'adopted' || status === 'rejected') activeTab.value = 'objections';

  fetchFeedbacks();
});

onUnmounted(() => {
  document.body.style.overflow = '';
  document.removeEventListener('fullscreenchange', handleFullscreenChange);
  window.removeEventListener('keydown', handleKeyDown);
  exitFullscreen();
});
</script>

<style scoped>
.meeting-custom-logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 24px;
}
.meeting-custom-logo {
  height: 50px;
  width: auto;
  object-fit: contain;
  background: rgba(255, 255, 255, 0.1);
  padding: 6px;
  border-radius: var(--radius-sm);
}
.meeting-mode-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: #ffffff;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  overflow: hidden; /* IMPORTANT: doit être hidden pour que les scrolls internes fonctionnent */
}

/* Scrollbar */
.meeting-mode-overlay::-webkit-scrollbar { width: 10px; }
.meeting-mode-overlay::-webkit-scrollbar-track { background: transparent; }
.meeting-mode-overlay::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 5px; }

.meeting-header {
  display: flex;
  align-items: center;
  padding: 24px 64px;
  padding-left: 100px; /* Moins de padding car le logo est décalé vers l'extérieur */
  background: white;
  border-bottom: 1px solid var(--gray-200);
  position: sticky;
  top: 0;
  z-index: 1000;
  overflow: visible;
}

.meeting-header.text-white {
  background-color: transparent; /* let the gradient show */
  border-bottom: none;
  overflow: visible;
}

.meeting-header.text-white .meeting-title h1 {
  color: white;
}

.meeting-header.text-white .btn-close-meeting {
  color: rgba(255, 255, 255, 0.7);
}

.meeting-header.text-white .btn-close-meeting:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.meeting-logo-container {
  position: relative;
  width: 100px;
  height: 48px;
  flex-shrink: 0;
}

.logo-circle {
  position: absolute;
  top: 10px;
  left: -40px;
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #0f172a, #1e293b);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 30px rgba(0,0,0,0.3);
  border: 4px solid white;
  z-index: 1100;
  transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.logo-circle:hover {
  transform: scale(1.05) rotate(5deg);
}

.logo-circle img {
  width: 50px;
  height: 50px;
}

.meeting-title {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 4px;
  flex: 1;
}

.meeting-header h1 {
  font-size: 20px;
  font-weight: 800;
  margin: 0;
  color: white;
  letter-spacing: -0.02em;
  line-height: 1.1;
}

.meeting-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
}

.mb-step {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: rgba(255, 255, 255, 0.5);
  transition: all 0.3s;
}

.mb-step.active {
  color: white;
  text-shadow: 0 0 10px rgba(255,255,255,0.5);
}

.mb-step.completed {
  color: rgba(255, 255, 255, 0.9);
}

.mb-sep {
  font-size: 8px;
  color: rgba(255, 255, 255, 0.3);
}

.version-tag {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(4px);
  color: white;
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 700;
  border: 1px solid rgba(255, 255, 255, 0.25);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-undo {
  background: var(--amber-50);
  border: 1px solid var(--amber-200);
  color: var(--amber-700);
  padding: 8px 16px;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-undo:hover {
  background: var(--amber-100);
}

.btn-close-meeting {
  background: transparent;
  border: none;
  color: var(--gray-500);
  font-size: 28px;
  cursor: pointer;
  transition: all 0.2s;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close-meeting:hover {
  background: var(--gray-100);
  color: var(--gray-900);
}

.btn-celebrate-test {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.25);
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  backdrop-filter: blur(4px);
}

.btn-celebrate-test:hover {
  background: var(--amber-500);
  border-color: var(--amber-400);
  transform: rotate(15deg) scale(1.1);
  box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
}

/* Célébration Overlay */
.celebration-overlay {
  position: fixed;
  inset: 0;
  z-index: 11000;
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  animation: fadeIn 0.5s ease-out;
}

.celebration-content {
  text-align: center;
  max-width: 900px;
  padding: 48px;
  color: white;
}

.celebration-logo {
  width: 180px;
  height: 180px;
  margin: 0 auto 48px;
  animation: logoPop 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.celebration-logo img {
  width: 100%;
  height: 100%;
}

.celebration-title {
  font-size: 64px;
  font-weight: 950;
  letter-spacing: 0.05em;
  margin-bottom: 24px;
  white-space: normal;
  line-height: 1.1;
  background: linear-gradient(to bottom, #fff, #ccc);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: logoPop 0.6s ease-out 0.2s both;
}

.celebration-text {
  font-size: 20px;
  color: rgba(255,255,255,0.8);
  animation: slideUp 0.6s ease-out 0.4s both;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes logoPop {
  0% { transform: scale(0) rotate(-45deg); opacity: 0; }
  70% { transform: scale(1.1) rotate(5deg); }
  100% { transform: scale(1) rotate(0); opacity: 1; }
}
@keyframes slideUp {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.meeting-content-wrapper {
  flex: 1;
  min-height: 0; /* Permet au flex enfant de rétrécir et scroller */
  display: flex;
  overflow: hidden;
  width: 100%;
}

.meeting-main-layout {
  display: flex;
  flex: 1;
  min-height: 0; /* Permet au flex enfant de rétrécir et scroller */
  width: 100%;
  overflow: hidden;
  background: var(--gray-50);
}

.meeting-left-sidebar, .meeting-doc-sidebar {
  width: 260px;
  min-width: 260px;
  max-width: 260px;
  flex-shrink: 0;
  padding: 48px 24px;
  border-right: 1px solid var(--gray-200);
  background: white;
  display: flex;
  flex-direction: column;
  gap: 32px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  height: 100%;
  padding: 0; /* On déplace le padding dans sidebar-content pour le scroll */
}

.meeting-left-sidebar {
  z-index: 100; /* Priorité maximale */
}

.meeting-doc-sidebar {
  z-index: 90;
  width: 32%;
  min-width: 380px;
  max-width: 500px;
  padding: 0; /* On déplace le padding dans sidebar-content */
  background: white;
  border-right: 1px solid var(--gray-200);
}

.meeting-doc-sidebar .btn-toggle-sidebar {
  top: 64px;
  z-index: 1000; /* Assure la visibilité au-dessus des contenus */
}

.meeting-left-sidebar.is-collapsed, .meeting-doc-sidebar.is-collapsed {
  width: 8px; /* Laisse dépasser un petit liseré */
  min-width: 8px;
  max-width: 8px;
  padding: 48px 0;
  border-right: 1px solid var(--gray-100);
  background: var(--gray-50);
}

.meeting-feedbacks-area {
  flex: 1;
  padding: 48px;
  overflow-y: auto;
  background: white;
  width: 100%;
}

.meeting-left-sidebar.is-collapsed .btn-toggle-sidebar, 
.meeting-doc-sidebar.is-collapsed .btn-toggle-sidebar {
  right: -24px; /* Ajusté pour le liseré de 8px */
  background: var(--indigo-600);
  color: white;
  border-color: var(--indigo-500);
  box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
}

.btn-toggle-sidebar {
  position: absolute;
  top: 16px;
  right: -16px;
  width: 32px;
  height: 32px;
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1000;
  color: var(--gray-500);
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  transition: all 0.2s;
}

.btn-toggle-sidebar:hover {
  color: var(--indigo-600);
  border-color: var(--indigo-300);
  transform: scale(1.1);
}

.sidebar-content {
  display: flex;
  flex-direction: column;
  min-width: 212px;
  opacity: 1;
  transition: opacity 0.2s;
  padding: 32px;
}

.meeting-left-sidebar .sidebar-content {
  padding: 48px 24px;
}

/* Scroll via position absolute — garanti sans dépendance aux utilitaires flex */
.meeting-doc-sidebar .sidebar-content {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow-y: auto;
  padding: 32px 20px 32px 32px;
}

.meeting-doc-sidebar .sidebar-content::-webkit-scrollbar {
  width: 6px;
}

.meeting-doc-sidebar .sidebar-content::-webkit-scrollbar-track {
  background: transparent;
}

.meeting-doc-sidebar .sidebar-content::-webkit-scrollbar-thumb {
  background: var(--gray-200);
  border-radius: 10px;
}

.meeting-doc-sidebar .sidebar-content::-webkit-scrollbar-thumb:hover {
  background: var(--gray-300);
}

.is-collapsed .sidebar-content {
  opacity: 0;
  pointer-events: none;
}

.meeting-body-flex {
  display: flex;
  flex: 1;
  min-height: 0; /* Chaîne flex: doit rétrécir pour que les enfants scrollent */
  width: 100%;
  overflow: hidden;
}

.meeting-prose-container {
  background: transparent;
  padding: 0;
  box-shadow: none;
  border: none;
}

.attachment-row {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 12px 16px;
  background: white;
  border: 1px solid var(--gray-100);
  border-radius: var(--radius-lg);
  color: var(--gray-700);
  font-size: 13px;
  transition: all 0.2s;
}

.attachment-row:hover {
  border-color: var(--indigo-200);
  background: var(--indigo-50);
  color: var(--indigo-700);
  transform: translateX(4px);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: var(--gray-200);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: var(--gray-300);
}

@keyframes pulse-bg {
  0% { background-color: rgba(99, 102, 241, 0.02); }
  50% { background-color: rgba(99, 102, 241, 0.1); }
  100% { background-color: rgba(99, 102, 241, 0.02); }
}

.dock-placeholder {
  height: 180px;
  border: 2px dashed var(--indigo-300);
  border-radius: var(--radius-lg);
  animation: pulse-bg 1.5s infinite ease-in-out;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: var(--indigo-500);
  font-weight: 600;
  font-size: 12px;
  text-align: center;
  padding: 16px;
}

.roles-block {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.role-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px;
  border-radius: 12px;
  background: var(--gray-50);
  border: 1px solid var(--gray-100);
}

.role-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.role-icon.porteur {
  background: var(--blue-50);
  border: 1px solid var(--blue-200);
  color: var(--blue-600);
}

.role-icon.animateur {
  background: var(--amber-50);
  border: 1px solid var(--amber-100);
  color: var(--amber-600);
}

.role-info {
  display: flex;
  flex-direction: column;
}

.role-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--gray-500);
  font-weight: 700;
}

.role-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-900);
}

.meeting-prose {
  font-size: 20px;
  line-height: 1.8;
  color: var(--gray-800);
}

.meeting-prose :deep(h1) { font-size: 2.5em; margin-bottom: 0.8em; }
.meeting-prose :deep(h2) { font-size: 2em; margin-top: 1.5em; margin-bottom: 0.8em; border-bottom: 1px solid var(--gray-200); padding-bottom: 8px; }
.meeting-prose :deep(h3) { font-size: 1.5em; margin-top: 1.2em; margin-bottom: 0.6em; }
.meeting-prose :deep(p) { margin-bottom: 1.2em; }
.meeting-prose :deep(ul), .meeting-prose :deep(ol) { margin-bottom: 1.2em; padding-left: 2em; }
.meeting-prose :deep(li) { margin-bottom: 0.5em; }

.attachment-chip {
  background: white;
  border: 1px solid var(--gray-200);
  padding: 12px 20px;
  border-radius: 50px;
  font-size: 16px;
  color: var(--gray-700);
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.attachment-chip:hover {
  border-color: var(--indigo-300);
  background: var(--indigo-50);
  color: var(--indigo-700);
}

/* Tabs Stylisées (Segmented Control style) */
.tabs-container {
  display: flex;
  border-bottom: 2px solid var(--gray-200);
}
.tabs {
  display: flex;
  gap: 8px;
}
.tabs .tab {
  padding: 12px 24px;
  background: transparent;
  border: 1px solid transparent;
  border-bottom: none;
  border-radius: 8px 8px 0 0;
  font-size: 16px;
  font-weight: 600;
  color: var(--gray-500);
  cursor: pointer;
  margin-bottom: -2px;
  transition: all 0.2s;
}
.tabs .tab:hover {
  color: var(--gray-800);
  background: var(--gray-50);
}
.tabs .tab.active {
  color: var(--indigo-600);
  background: white;
  border-color: var(--gray-200);
  border-bottom-color: white;
}

/* Feedbacks CSS */
.mf-card {
  border: 1px solid var(--gray-200);
  border-radius: 16px;
  margin-bottom: 20px;
  background: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  transition: all 0.2s ease;
}

.mf-card.fb-closed {
  opacity: 0.6;
}

.mf-header {
  padding: 16px;
  background: var(--gray-50);
  border-bottom: 1px solid var(--gray-200);
  display: flex;
  align-items: center;
  width: 100%;
}

.mf-body {
  padding: 16px;
}

.mf-msg {
  margin-bottom: 16px;
  padding: 16px;
  border-radius: 8px;
  border-width: 1px;
  border-style: solid;
}
.mf-msg.role-author {
  background-color: #eff6ff; /* Blue-50 */
  border-color: #3b82f6; /* Blue-500 */
}
.mf-msg.role-animator {
  background-color: #fffbeb; /* Amber-50 */
  border-color: #f59e0b; /* Amber-500 */
}
.mf-msg.role-participant {
  background-color: #ecfdf5; /* Green-50 */
  border-color: #10b981; /* Green-500 */
}
.mf-msg:last-child {
  margin-bottom: 0;
}

/* Custom Gradients */
.grad-indigo {
  background-image: linear-gradient(to right, var(--blue-700), var(--blue-900));
}
.grad-blue {
  background-image: linear-gradient(to right, var(--blue-600), var(--blue-800));
}
.grad-amber {
  background-image: linear-gradient(to right, #d97706, #92400e);
}
.grad-teal {
  background-image: linear-gradient(to right, var(--teal-500), var(--teal-600));
}
.grad-red {
  background-image: linear-gradient(to right, var(--red-600), #991b1b);
}
.grad-gray {
  background-image: linear-gradient(to right, var(--gray-500), var(--gray-700));
}

.mf-msg-author {
  font-weight: 600;
  color: var(--gray-800);
  margin-bottom: 8px;
  display: flex;
  align-items: center;
}

.role-badge {
  font-size: 10px;
  text-transform: uppercase;
  font-weight: 700;
  color: var(--gray-500);
  margin-left: 8px;
  border: 1px solid var(--gray-200);
  padding: 2px 6px;
  border-radius: 4px;
  background: white;
}

.mf-msg-content {
  color: var(--gray-700);
  white-space: pre-wrap;
  font-size: 15px;
}
.mf-consents-summary {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 32px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.feedbacks-list {
  flex: 1;
  overflow-y: auto;
  padding: 16px 0;
}
</style>


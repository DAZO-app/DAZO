<template>
  <div class="meeting-mode-overlay" ref="overlayRef">
    <!-- Header épuré -->
    <div class="meeting-header" :class="headerGradientClass">
      <div class="meeting-logo">
        <img src="/DAZO-logo-carre-blanc.svg" alt="DAZO Logo" class="h-12 w-12">
      </div>
      <div class="meeting-title">
        <h1>{{ decision.title }}</h1>
        <span class="badge" :class="statusBadgeClass">{{ translateStatus(decision.status) }}</span>
      </div>
      
      <div class="ml-auto flex items-center gap-16">
        <!-- Bouton Undo -->
        <button 
          v-if="isAnimator && actionHistory.length > 0"
          class="btn-undo" 
          @click="undoLastAction" 
          title="Annuler la dernière action (Ctrl+Z)"
        >
          <i class="fa-solid fa-rotate-left mr-8"></i> Annuler
        </button>

        <!-- Fermer -->
        <button class="btn-close-meeting" @click="closeMeetingMode" title="Quitter le mode réunion (Echap)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

    <div class="meeting-content-wrapper">
      <!-- Prose en affichage fluide -->
      <div class="meeting-prose" v-html="displayContent"></div>

      <!-- Pièces jointes (Plus d'espace avant les échanges) -->
      <div v-if="attachments && attachments.length > 0" class="meeting-attachments" style="margin-top: 64px; margin-bottom: 64px;">
        <h3 class="text-lg font-bold mb-16 text-gray-700">Pièces jointes</h3>
        <div class="flex flex-wrap gap-12">
          <button 
            v-for="(att, idx) in attachments" 
            :key="att.id"
            class="attachment-chip"
            @click="$emit('open-attachment', idx)"
          >
            <i class="fa-solid fa-paperclip mr-8 text-indigo-500"></i>
            <span class="font-medium">{{ att.filename }}</span>
          </button>
        </div>
      </div>

      <!-- Échanges (Feedbacks) avec Onglets stylisés -->
      <div class="meeting-feedbacks">
        <div class="tabs-container mb-24">
          <div class="tabs">
            <button class="tab" :class="{active: activeTab === 'clarifications'}" @click="activeTab = 'clarifications'">Clarifications</button>
            <button class="tab" :class="{active: activeTab === 'reactions'}" @click="activeTab = 'reactions'">Réactions</button>
            <button class="tab" :class="{active: activeTab === 'objections'}" @click="activeTab = 'objections'">Objections & Suggestions</button>
          </div>
        </div>

        <div v-if="loadingFeedbacks" class="text-gray-500">Chargement des échanges...</div>
        
        <div v-else class="feedbacks-list">
          <div v-if="filteredFeedbacks.length === 0" class="text-gray-500 italic">Aucun échange dans cette catégorie.</div>
          
          <!-- Boucle sur les feedbacks -->
          <div v-for="fb in filteredFeedbacks" :key="fb.id" class="mf-card" :class="{'fb-closed': isClosed(fb)}">
            <div class="mf-header">
              <span class="font-bold text-gray-800">{{ fb.author?.name }}</span>
              
              <!-- Bouton Répondre pour le secrétaire -->
              <button 
                v-if="isAnimator && !isClosed(fb)" 
                class="btn btn-xs btn-secondary"
                style="margin-left: auto;"
                @click="replyTarget = fb"
              >
                <i class="fa-solid fa-reply mr-4"></i> Répondre
              </button>
            </div>
            
            <div class="mf-body">
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
        </div>
      </div>
    </div>

    <!-- Panneau du Secrétaire (Flottant) -->
    <MeetingSecretaryPanel 
      v-if="isAnimator"
      :decision="decision"
      :current-version="currentVersion"
      :participants="participants"
      :feedbacks="feedbacks"
      :consents="consents"
      :reply-to-feedback="replyTarget"
      @phase-change="$emit('phase-change', $event)"
      @refresh-data="refreshAll"
      @cancel-reply="replyTarget = null"
      @action-logged="logAction"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';
import MeetingSecretaryPanel from './MeetingSecretaryPanel.vue';

const props = defineProps({
  decision: { type: Object, required: true },
  currentVersion: { type: Object, required: true },
  attachments: { type: Array, default: () => [] },
  isAnimator: { type: Boolean, default: false },
  participants: { type: Array, default: () => [] }
});

const emit = defineEmits(['close', 'open-attachment', 'phase-change', 'refresh-data']);
const overlayRef = ref(null);

const feedbacks = ref([]);
const consents = ref([]);
const loadingFeedbacks = ref(false);
const activeTab = ref('clarifications'); // 'clarifications', 'reactions', 'objections'
const replyTarget = ref(null);

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
    if (activeTab.value === 'objections') return ['objection', 'suggestion'].includes(t);
    return false;
  });
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

const headerGradientClass = computed(() => {
  const map = {
    'draft': 'grad-gray text-white',
    'clarification': 'grad-indigo text-white',
    'reaction': 'grad-blue text-white',
    'objection': 'grad-amber text-white',
    'adopted': 'grad-teal text-white',
    'rejected': 'grad-red text-white',
    'abandoned': 'grad-gray text-white',
  };
  return map[props.decision.status] || 'grad-gray text-white';
});

const typeBadge = (type) => {
  const t = type?.value || type;
  if (t === 'clarification') return 'badge-indigo';
  if (t === 'reaction') return 'badge-blue';
  if (t === 'objection') return 'badge-amber';
  if (t === 'suggestion') return 'badge-teal';
  return 'badge-gray';
};

const getRoleStyle = (authorId) => {
  const p = props.participants.find(p => p.user.id === authorId);
  const role = p ? p.role : 'participant';
  if (role === 'author') return 'role-author';
  if (role === 'animator') return 'role-animator';
  return 'role-participant';
};

const getRoleBadge = (authorId) => {
  const p = props.participants.find(p => p.user.id === authorId);
  if (!p) return '';
  if (p.role === 'author') return 'Porteur';
  if (p.role === 'animator') return 'Animateur';
  return '';
};

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
  
  // Set default tab based on decision status
  if (props.decision.status === 'Réaction') activeTab.value = 'reactions';
  if (props.decision.status === 'Objection') activeTab.value = 'objections';
  
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
  overflow-y: auto;
}

/* Scrollbar */
.meeting-mode-overlay::-webkit-scrollbar { width: 10px; }
.meeting-mode-overlay::-webkit-scrollbar-track { background: transparent; }
.meeting-mode-overlay::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 5px; }

.meeting-header {
  display: flex;
  align-items: center;
  padding: 16px 48px;
  background: white;
  border-bottom: 1px solid var(--gray-200);
  position: sticky;
  top: 0;
  z-index: 10;
}

.meeting-header.text-white {
  background-color: transparent; /* let the gradient show */
  border-bottom: none;
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

.meeting-logo img {
  height: 48px;
  width: 48px;
  margin-right: 32px;
}

.meeting-title {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
}

.meeting-title h1 {
  font-size: 24px;
  font-weight: 700;
  color: var(--gray-900);
  margin: 0;
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

.meeting-content-wrapper {
  flex: 1;
  padding: 48px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
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
  border-radius: 12px;
  margin-bottom: 16px;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  overflow: hidden;
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
  background-color: var(--amber-50);
  border-color: var(--amber-600);
}
.mf-msg.role-animator {
  background-color: var(--blue-50);
  border-color: var(--blue-600);
}
.mf-msg.role-participant {
  background-color: var(--gray-50);
  border-color: var(--gray-300);
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
</style>


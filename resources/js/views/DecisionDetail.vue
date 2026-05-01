<template>
  <main class="main" v-if="loading">
    <div class="p-24 text-center">Chargement...</div>
  </main>

  <main class="main" v-else-if="error">
    <div class="p-24 text-center text-red-500">
      <div style="font-size: 24px; margin-bottom: 8px;"><i class="fa-solid fa-face-frown"></i></div>
      <div>{{ error }}</div>
      <button class="btn btn-secondary mt-16" @click="$router.push('/decisions')">Retour aux décisions</button>
    </div>
  </main>

  <main class="main" v-else-if="decision">
    <ReminderModal 
      v-if="showReminderModal" 
      :decision="decision" 
      @close="showReminderModal = false" 
    />
    
    <MeetingModeOverlay
      v-if="showMeetingMode"
      :decision="decision"
      :current-version="displayedVersion"
      :attachments="displayAttachments"
      :is-animator="true"
      :participants="decision.participants"
      :all-versions="allVersions"
      @close="handleMeetingModeClose"
      @open-attachment="openAttachment"
      @phase-change="refreshDecision"
      @refresh-data="refreshDecision"
      @version-change="handleMeetingVersionChange"
    />

    <div class="page-body">
      <DecisionHero 
        :decision="decision"
        :current-version="currentVersion"
        :current-status="currentStatus"
        :previous-decision-id="previousDecisionId"
        :next-decision-id="nextDecisionId"
        :is-revision="isRevision"
        :saving-draft="savingDraft"
        :publishing="publishing"
        @save-revision="saveRevision"
        @publish-revision="publishRevision"
        @go-to-decision="goToDecision"
        @toggle-favorite="toggleFavorite"
        @show-notif-levels="showNotifLevels = true"
        @open-meeting-mode="showMeetingMode = true"
        @show-reminder="showReminderModal = true"
        @print-decision="printDecision"
        @refresh="refreshDecision"
      />

      <DecisionPhaseActions 
        :current-status="currentStatus"
        :is-author-or-animator="isAuthorOrAnimator"
        :step-actions="stepActions"
        :disabled="transitioning || publishing || savingDraft"
      />
      <div class="grid-layout">
        <div class="col-main">
          <DecisionEditForm
            v-if="isDraft || (isRevision && isAuthorOrAnimator)"
            :is-draft="isDraft"
            :is-revision="isRevision"
            :is-author-or-animator="isAuthorOrAnimator"
            :form="draftForm"
            :current-version="currentVersion"
            :excludable-members="excludableMembers"
            :saving-draft="savingDraft"
            :deleting-draft="deletingDraft"
            :publishing="publishing"
            :reused-attachment-ids="reusedAttachmentIds"
            :revision-attachments="revisionAttachments"
            @save-draft="saveDraft"
            @delete-draft="deleteDraft"
            @refresh="refreshDecision"
            @toggle-all-attachments="toggleAllPreviousAttachments"
            @toggle-attachment="toggleAttachmentReuse"
            @upload-revision-file="handleRevisionFileUpload"
            @remove-revision-file="handleRevisionFileRemove"
            @save-revision="saveRevision"
            @publish-revision="publishRevision"
          />

          <DecisionContentView
            :is-draft="isDraft"
            :is-revision="isRevision"
            :is-author-or-animator="isAuthorOrAnimator"
            :viewing-version-id="viewingVersionId"
            :historical-version-number="historicalVersionData?.version_number"
            :current-version-number="currentVersion?.version_number"
            :current-version-id="currentVersion?.id"
            :display-content="displayContent"
            :current-content="currentVersion?.content"
            :display-attachments="displayAttachments"
          />

          <div v-if="showParticipationCard" class="premium-card mb-16 border-2" :class="hasAlreadyParticipated ? 'border-teal-500' : 'border-red-500'">
            <div class="pc-header" :class="hasAlreadyParticipated ? 'pc-header-teal' : 'pc-header-red'">
              <div class="pc-header-icon"><i class="fa-solid fa-comments"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">{{ participationCardTitle }}</div>
                <div class="pc-header-sub">{{ hasAlreadyParticipated ? 'Participation enregistrée' : 'Action requise de votre part' }}</div>
              </div>
            </div>

            <div class="card-body">
              <div v-if="hasAlreadyParticipated" class="consent-done-block">
                <div class="consent-done-icon"><i :class="consentIcon(myConsent?.signal)"></i></div>
                <div class="consent-done-label">{{ consentLabel(myConsent?.signal) }}</div>
                <p class="text-xs text-muted mt-8">Votre participation est déjà enregistrée pour cette phase.</p>
              </div>

              <template v-else>
                <div class="grid-2 gap-12">
                  <button class="btn btn-secondary" @click="openReactionModal" style="padding: 12px 8px; font-size: 13px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                    <span class="text-xl"><i class="fa-solid fa-comments"></i></span>
                    <span>{{ modalActionLabelShort }}</span>
                  </button>

                  <div v-if="decision.status === 'clarification'">
                    <button class="vote-btn vote-ok" @click="submitConsent('no_questions')" style="width: 100%; height: 100%;">
                      <span class="vote-icon"><i class="fa-solid fa-circle-check"></i></span>
                      C'est clair
                    </button>
                  </div>

                  <div v-if="decision.status === 'reaction'">
                    <button class="vote-btn vote-ok" @click="submitConsent('no_reaction')" style="width: 100%; height: 100%;">
                      <span class="vote-icon"><i class="fa-solid fa-thumbs-up"></i></span>
                      RAS
                    </button>
                  </div>

                  <div v-if="decision.status === 'objection'" class="grid-1 gap-8">
                    <button class="vote-btn vote-ok" @click="submitConsent('no_objection')" style="width: 100%;">
                      <span class="vote-icon"><i class="fa-solid fa-thumbs-up"></i></span>
                      Sans objection
                    </button>
                    <button class="vote-btn vote-abs" @click="submitConsent('abstention')" style="width: 100%;">
                      <span class="vote-icon"><i class="fa-solid fa-eye"></i></span>
                      Abstention
                    </button>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <div
            v-if="isFeedbackEngineVisible"
            class="mb-16"
          >
            <FeedbackEngine 
                 :key="feedbackKey"
                 :decision="viewingVersionId ? historicalVersionDecision : decision" 
                 :historical-data="viewingVersionId ? historicalVersionData : null"
                 @refresh="refreshDecision" 
            />
          </div>
        </div>

        <div class="col-side">
          <!-- Cartes de Rôle / Participation -->
          <div v-if="myRole === 'author' || isAuthor" class="premium-card mb-16">
            <div class="pc-header pc-header-blue" style="padding: 12px;">
              <div class="pc-header-icon" style="font-size: 1.2rem;"><i class="fa-solid fa-bullhorn"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title" style="font-size: 14px;">Porteur</div>
                <div class="pc-header-sub" style="font-size: 12px;">Vous pilotez cette décision.</div>
              </div>
              <button v-if="isAuthorOrAnimator" class="btn btn-icon btn-sm" style="color: white; background: rgba(255,255,255,0.15); border: none; position: relative; z-index: 10;" title="Actions rapides" @click="showActionsModal = true">
                <i class="fa-solid fa-gear"></i>
              </button>
            </div>
          </div>
          <div v-else-if="myRole === 'animator' || isAnimator" class="premium-card mb-16">
            <div class="pc-header pc-header-amber" style="padding: 12px;">
              <div class="pc-header-icon" style="font-size: 1.2rem;"><i class="fa-solid fa-user-tie"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title" style="font-size: 14px;">Animateur</div>
                <div class="pc-header-sub" style="font-size: 12px;">Vous facilitez ce processus.</div>
              </div>
              <button v-if="isAuthorOrAnimator" class="btn btn-icon btn-sm" style="color: white; background: rgba(255,255,255,0.15); border: none; position: relative; z-index: 10;" title="Actions rapides" @click="showActionsModal = true">
                <i class="fa-solid fa-gear"></i>
              </button>
            </div>
          </div>
          <div v-else-if="myRole === 'participant'" class="premium-card mb-16">
            <div class="pc-header pc-header-teal" style="padding: 12px;">
              <div class="pc-header-icon" style="font-size: 1.2rem;"><i class="fa-solid fa-user-group"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title" style="font-size: 14px;">Participant</div>
                <div class="pc-header-sub" style="font-size: 12px;">
                  {{ hasAlreadyParticipated ? 'Participation validée pour cette phase.' : 'Vous participez à cette décision.' }}
                </div>
              </div>
            </div>
          </div>
          <div v-else-if="hasAlreadyParticipated" class="premium-card mb-16">
            <div class="pc-header pc-header-teal" style="padding: 12px;">
              <div class="pc-header-icon" style="font-size: 1.2rem;"><i class="fa-solid fa-check"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title" style="font-size: 14px;">Participation validée</div>
                <div class="pc-header-sub" style="font-size: 12px;">Vous avez agi pour cette phase.</div>
              </div>
            </div>
          </div>

          <!-- Paramètres de la décision -->
          <div v-if="isDraft && isAuthorOrAnimator" class="premium-card mb-16">
            <div class="pc-header pc-header-indigo">
              <div class="pc-header-icon"><i class="fa-solid fa-sliders"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Paramètres de la décision</div>
                <div class="pc-header-sub">Configuration du processus</div>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="label">Cercle *</label>
                <select v-model="draftForm.circle_id" class="select" required>
                  <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>

              <div class="form-group">
                <label class="label">Catégories</label>
                <div class="categories-selector mt-8">
                   <div class="category-chips">
                      <div 
                        v-for="cat in categories" 
                        :key="cat.id"
                        class="category-chip"
                        :class="{ active: draftForm.category_ids.includes(cat.id) }"
                        @click="toggleCategory(cat.id)"
                        :style="draftForm.category_ids.includes(cat.id) ? { borderColor: cat.color_hex, background: cat.color_hex + '15', color: cat.color_hex } : {}"
                      >
                         <i :class="cat.icon || 'fa-solid fa-tag'" class="mr-6"></i>
                         {{ cat.name }}
                      </div>
                   </div>
                </div>
              </div>

              <div class="form-group">
                <label class="label">Animateur désigné</label>
                <select v-model="draftForm.animator_id" class="select">
                  <option value="">Auteur (par défaut)</option>
                  <option
                    v-for="member in selectableMembers"
                    :key="member.user_id"
                    :value="member.user_id"
                  >
                    {{ member.user?.name }} ({{ member.role }})
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Navigation entre versions -->
          <div v-if="allVersions.length > 1" class="premium-card mb-16">
            <div class="pc-header pc-header-indigo" style="padding: 12px;">
              <div class="pc-header-icon" style="font-size: 1.2rem;"><i class="fa-solid fa-clock-rotate-left"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title" style="font-size: 14px;">Versions précédentes</div>
                <div class="pc-header-sub" style="font-size: 12px;">Consulter l'historique</div>
              </div>
            </div>
            <div class="card-body" style="padding: 12px;">
              <div class="form-group mb-8">
                <select v-model="selectedVersionNavId" class="select select-sm" @change="handleVersionChange">
                  <option v-for="v in allVersions" :key="v.id" :value="v.id">
                    Version {{ v.version_number }} ({{ formatDateOnly(v.created_at) }})
                  </option>
                </select>
              </div>
              <button 
                v-if="viewingVersionId" 
                class="btn btn-secondary btn-sm w-full" 
                @click="resetToCurrentVersion"
              >
                <i class="fa-solid fa-arrow-left"></i> Retour à la version en cours
              </button>
            </div>
          </div>

          <ParticipantPhasePanel
            v-if="!isDraft"
            :decision="viewingVersionId ? historicalVersionDecision : decision"
            :phase-participation-map="viewingVersionId ? historicalPhaseParticipationMap : phaseParticipationMap"
          />
        </div>
      </div>
    </div>

    <div v-if="showReactionModal" class="modal-overlay" @click.self="showReactionModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <span class="modal-title">{{ modalTitle }}</span>
          <button class="btn btn-ghost btn-icon" @click="showReactionModal = false"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
          <p class="text-sm text-muted mb-16">{{ modalDescription }}</p>

          <div v-if="decision.status === 'objection'" class="form-group">
            <label class="label">Type de retour</label>
            <select v-model="reactionType" class="select">
              <option value="objection">Objection bloquante</option>
              <option value="suggestion">Suggestion</option>
            </select>
          </div>

          <div class="form-group">
            <label class="label">Votre message</label>
            <textarea v-model="reactionText" class="textarea" rows="6" placeholder="Écrivez ici..."></textarea>
          </div>

          <div class="modal-footer">
            <button class="btn btn-ghost" @click="showReactionModal = false">Annuler</button>
            <button class="btn btn-primary" :disabled="submittingReaction || !reactionText.trim()" @click="submitReaction">
              {{ submittingReaction ? 'Envoi…' : 'Envoyer' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL ACTIONS RAPIDES -->
    <div v-if="showActionsModal" class="modal-overlay" @click.self="showActionsModal = false" style="z-index: 1000;">
      <div class="modal-card" style="max-width: 400px;">
        <div class="modal-header">
          <span class="modal-title">Actions rapides</span>
          <button class="btn btn-ghost btn-icon" @click="showActionsModal = false"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body text-center">
          <p class="text-sm text-muted mb-24">Gestion manuelle du cycle de vie</p>
          
          <div class="flex flex-col gap-12">
            <!-- REPRENDRE (SI SUSPENDUE) -->
            <button v-if="currentStatus === 'suspended'" 
                    class="btn btn-primary btn-block p-16" 
                    style="height: 54px; font-size: 15px;"
                    @click="handleManualAction('resume')" 
                    :disabled="performingAction">
              <i class="fa-solid fa-play mr-10"></i> Reprendre le cycle
            </button>

            <!-- PAUSE (SI ACTIVE) -->
            <button v-if="['clarification', 'reaction', 'objection', 'revision'].includes(currentStatus)" 
                    class="btn btn-amber btn-block p-16" 
                    style="height: 54px; font-size: 15px;"
                    @click="handleManualAction('suspend')" 
                    :disabled="performingAction">
              <i class="fa-solid fa-pause mr-10"></i> Mettre en pause
            </button>

            <!-- NOUVELLE RÉVISION -->
            <button v-if="['clarification', 'reaction', 'objection'].includes(currentStatus)" 
                    class="btn btn-blue btn-block p-16" 
                    style="height: 54px; font-size: 15px;"
                    @click="handleManualAction('revision')" 
                    :disabled="performingAction">
              <i class="fa-solid fa-pen-nib mr-10"></i> Créer une révision
            </button>

            <!-- ABANDONNER -->
            <button v-if="!['abandoned', 'adopted'].includes(currentStatus)" 
                    class="btn btn-danger btn-block p-16" 
                    style="height: 54px; font-size: 15px;"
                    @click="handleManualAction('abandon')" 
                    :disabled="performingAction">
              <i class="fa-solid fa-ban mr-10"></i> Abandonner définitivement
            </button>
          </div>
        </div>
      </div>
    </div>

    <NotificationPromptModal 
      :visible="showNotificationPrompt" 
      :targetStatus="pendingPublishType === 'revision' ? targetRevisionStatus : 'clarification'"
      @confirm="handlePublishConfirm" 
      @cancel="showNotificationPrompt = false" 
    />

    <RevisionPathModal 
      v-if="showRevisionPathModal" 
      @close="showRevisionPathModal = false" 
      @confirm="handlePathChoice"
    />

    <NotificationLevelModal
      v-if="showNotifLevels"
      :currentLevel="currentNotifLevel"
      @close="showNotifLevels = false"
      @select="setNotifLevel"
    />
    <!-- Modal impression / PDF -->
    <DecisionPrintModal
      v-if="showPrintModal"
      :decision="decision"
      :current-version="currentVersion"
      @close="showPrintModal = false"
    />
  </main>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AnimatorSelector from '../components/AnimatorSelector.vue';
import DecisionHero from '../components/decision/DecisionHero.vue';
import DecisionPhaseActions from '../components/decision/DecisionPhaseActions.vue';
import DecisionEditForm from '../components/decision/DecisionEditForm.vue';
import DecisionContentView from '../components/decision/DecisionContentView.vue';
import ReminderModal from '../components/ReminderModal.vue';
import AttachmentPanel from '../components/AttachmentPanel.vue';
import FeedbackEngine from '../components/FeedbackEngine.vue';
import ParticipantPhasePanel from '../components/ParticipantPhasePanel.vue';
import RichTextEditor from '../components/RichTextEditor.vue';
import NotificationPromptModal from '../components/NotificationPromptModal.vue';
import RevisionPathModal from '../components/RevisionPathModal.vue';
import NotificationLevelModal from '../components/NotificationLevelModal.vue';
import MeetingModeOverlay from '../components/MeetingModeOverlay.vue';
import DecisionPrintModal from '../components/DecisionPrintModal.vue';
import { useAuthStore } from '../stores/auth';
import { useDecisionStore } from '../stores/decision';
import { usePendingStore } from '../stores/pending';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const decisionStore = useDecisionStore();
const pendingStore = usePendingStore();

const currentDecisionIndex = computed(() => {
  if (!decision.value) return -1;
  return decisionStore.decisions.findIndex((item) => item.id === decision.value.id);
});

const previousDecisionId = computed(() => {
  if (currentDecisionIndex.value <= 0) return null;
  return decisionStore.decisions[currentDecisionIndex.value - 1]?.id || null;
});

const nextDecisionId = computed(() => {
  if (currentDecisionIndex.value === -1) return null;
  return decisionStore.decisions[currentDecisionIndex.value + 1]?.id || null;
});

const decision = computed(() => decisionStore.currentDecision);
const myConsent = computed(() => decisionStore.myConsent);
const phaseParticipationMap = computed(() => decisionStore.phaseParticipationMap || {});
const loading = computed(() => decisionStore.loading);
const error = computed(() => decisionStore.error);

const currentVersion = computed(() => decision.value?.current_version || null);

const displayedVersion = computed(() => {
  if (viewingVersionId.value && historicalVersionData.value) {
    return historicalVersionData.value;
  }
  return currentVersion.value;
});

const displayContent = computed(() => {
  if (viewingVersionId.value && historicalVersionData.value) {
    return historicalVersionData.value.content;
  }
  return currentVersion.value?.content;
});

const displayAttachments = computed(() => {
  if (viewingVersionId.value && historicalVersionData.value) {
    return historicalVersionData.value.attachments || [];
  }
  return currentVersion.value?.attachments || [];
});

const historicalVersionDecision = computed(() => {
  if (!viewingVersionId.value || !historicalVersionData.value) return null;
  // On retourne un objet décision-like pour les composants enfants
  return {
    ...decision.value,
    participation_stats: historicalVersionData.value.participation_stats || decision.value.participation_stats
  };
});

const historicalPhaseParticipationMap = computed(() => {
  if (!viewingVersionId.value || !historicalVersionData.value) return phaseParticipationMap.value;
  
  // Si l'API nous a déjà fourni la map calculée, on l'utilise directement
  if (historicalVersionData.value.phase_participation_map) {
    return historicalVersionData.value.phase_participation_map;
  }

  // Fallback (ancien code)
  const map = { clarification: {}, reaction: {}, objection: {} };
  const v = historicalVersionData.value;
  
  if (v.feedbacks) {
    v.feedbacks.forEach(f => {
      const type = f.type?.value || f.type;
      if (type === 'clarification') map.clarification[f.author_id] = true;
      if (type === 'reaction') map.reaction[f.author_id] = true;
      if (['objection', 'suggestion'].includes(type)) map.objection[f.author_id] = true;
    });
  }
  
  if (v.consents) {
    v.consents.forEach(c => {
      const signal = c.signal?.value || c.signal;
      const phase = c.phase?.value || c.phase;
      if (phase && map[phase]) {
        map[phase][c.user_id] = signal;
      } else {
        // Fallback si la phase n'est pas renseignée
        if (signal === 'no_questions') map.clarification[c.user_id] = true;
        if (signal === 'no_reaction') map.reaction[c.user_id] = true;
        if (signal === 'no_objection') map.objection[c.user_id] = true;
      }
    });
  }
  
  return map;
});
const savingDraft = ref(false);
const deletingDraft = ref(false);
const publishing = ref(false);
const transitioning = ref(false);
const submittingReaction = ref(false);
const showReactionModal = ref(false);
const showActionsModal = ref(false);
const performingAction = ref(false);
const showNotificationPrompt = ref(false);
const showRevisionPathModal = ref(false);
const showNotifLevels = ref(false);
const showReminderModal = ref(false);
const targetRevisionStatus = ref('clarification');
const pendingPublishType = ref(null);
const revisionAttachments = ref([]);

// Navigation Historique
const allVersions = ref([]);
const circles = ref([]);
const categories = ref([]);
const viewingVersionId = ref(null);
const historicalVersionData = ref(null);
const selectedVersionNavId = ref(null);
const loadingHistorical = ref(false);

const draftForm = ref({
  title: '',
  content: '',
  animator_id: '',
  circle_id: '',
  category_ids: [],
  model_id: '',
  revision_attachment_ids: [],
});

const toggleCategory = (id) => {
  const index = draftForm.value.category_ids.indexOf(id);
  if (index === -1) {
    draftForm.value.category_ids.push(id);
  } else {
    draftForm.value.category_ids.splice(index, 1);
  }
};

const reactionText = ref('');
const reactionType = ref('objection');

const roleMeta = {
  author: { label: 'Porteur', icon: 'fa-solid fa-bullhorn', className: 'role-author' },
  animator: { label: 'Animateur', icon: 'fa-solid fa-user-tie', className: 'role-animator' },
  participant: { label: 'Participant', icon: 'fa-solid fa-user-group', className: 'role-participant' },
  excluded: { label: 'Exclu', icon: 'fa-solid fa-ban', className: 'role-excluded' },
  observer: { label: 'Observateur', icon: 'fa-solid fa-eye', className: 'role-observer' },
};

const currentCircleMember = computed(() => {
  if (!decision.value || !authStore.user) return null;
  return decision.value.circle?.members?.find((member) => member.user_id === authStore.user.id) || null;
});

const explicitParticipant = computed(() => {
  if (!decision.value || !authStore.user) return null;
  return decision.value.participants?.find((participant) => participant.user_id === authStore.user.id) || null;
});

const myRole = computed(() => {
  let r = explicitParticipant.value?.role || currentCircleMember.value?.role || 'participant';
  
  // Handle potential enum object { value: '...', label: '...' }
  if (r && typeof r === 'object' && r.value) {
    r = r.value;
  }
  
  // Normalize 'member' (from circle) to 'participant' (for display)
  return r === 'member' ? 'participant' : r;
});

const myRoleInfo = computed(() => roleMeta[myRole.value] || roleMeta.participant);

const isDraft = computed(() => {
  const s = decision.value?.status;
  const val = (typeof s === 'object' && s !== null) ? s.value : s;
  return val === 'draft';
});
const isRevision = computed(() => {
  const s = currentStatus.value;
  return s === 'revision';
});

const isFavorite = computed(() => decisionStore.mySettings?.is_favorite || false);
const currentNotifLevel = computed(() => decisionStore.mySettings?.notification_level || 'all');

const notifLevels = [
  { value: 'all', label: 'Toutes les mises à jour', desc: 'Recevoir un mail pour chaque modification ou commentaire.', icon: 'fa-solid fa-bell' },
  { value: 'relevant', label: 'Mises à jour me concernant', desc: 'Seulement pour vos participations ou si vous êtes porteur.', icon: 'fa-solid fa-bell-concierge' },
  { value: 'phase_change', label: 'Changements de phase', desc: 'Seulement quand la décision progresse.', icon: 'fa-solid fa-bolt' },
  { value: 'none', label: 'Aucune', desc: 'Désactiver les notifications par email.', icon: 'fa-regular fa-bell-slash' },
];

const notifLevelIcon = computed(() => {
  return notifLevels.find(l => l.value === currentNotifLevel.value)?.icon || 'fa-solid fa-bell';
});

const toggleFavorite = async () => {
  try {
    const { data } = await axios.post(`/api/v1/decisions/${decision.value.id}/favorite`);
    if (!decisionStore.mySettings) {
      decisionStore.mySettings = { user_id: authStore.user.id, decision_id: decision.value.id };
    }
    decisionStore.mySettings.is_favorite = data.is_favorite;
  } catch (err) {
    console.error('Favorite toggle failed', err);
  }
};

const showMeetingMode = ref(false);
const showPrintModal = ref(false);

const feedbackKey = ref(0);

const handleMeetingModeClose = () => {
  showMeetingMode.value = false;
  // We reload the page to ensure all components, stats and feedback engines are 100% synchronized
  window.location.reload();
};

const openAttachment = (idx) => {
  const attachment = displayAttachments.value[idx];
  if (attachment) {
    const url = attachment.id ? `/api/v1/attachments/${attachment.id}/download` : (attachment.url || null);
    if (url) window.open(url, '_blank');
  }
};

const printDecision = () => {
  showPrintModal.value = true;
};

const setNotifLevel = async (level) => {
  try {
    const { data } = await axios.put(`/api/v1/decisions/${decision.value.id}/notifications`, { level });
    if (!decisionStore.mySettings) {
      decisionStore.mySettings = { user_id: authStore.user.id, decision_id: decision.value.id };
    }
    decisionStore.mySettings.notification_level = data.notification_level;
    showNotifLevels.value = false;
  } catch (err) {
    console.error('Notif level update failed', err);
  }
};

const isAuthor = computed(() => {
  return decision.value?.participants?.some(p => {
    const r = (typeof p.role === 'object' && p.role !== null) ? p.role.value : p.role;
    return p.user_id === authStore.user?.id && r === 'author';
  });
});
const isAnimator = computed(() => {
  return decision.value?.participants?.some(p => {
    const r = (typeof p.role === 'object' && p.role !== null) ? p.role.value : p.role;
    return p.user_id === authStore.user?.id && r === 'animator';
  });
});

const isCircleMember = computed(() => {
  const member = decision.value?.circle?.members?.find(m => m.user_id === authStore.user?.id);
  return member && member.role?.value !== 'observer' && member.role !== 'observer';
});

// Droits de gestion (vue classique, boutons de changement de phase sidebar)
const isAuthorOrAnimator = computed(() => isAuthor.value || isAnimator.value || authStore.user?.is_global_animator);

// Droits d'ouvrir le mode meeting (élargis aux membres du cercle)
const canOpenMeetingMode = computed(() => isAuthorOrAnimator.value || isCircleMember.value);

const draftCircleMembers = ref([]);
const loadingCircleMembers = ref(false);

const selectableMembers = computed(() => {
  const list = (isDraft.value && draftForm.value.circle_id) ? draftCircleMembers.value : (decision.value?.circle?.members || []);
  return list.filter((member) => member.role !== 'observer' && member.user_id !== authorParticipant.value?.user_id);
});

const authorParticipant = computed(() => decision.value?.participants?.find((participant) => participant.role === 'author') || null);

const authorName = computed(() => authorParticipant.value?.user?.name || 'Inconnu');

const excludableMembers = computed(() => {
  const list = (isDraft.value && draftForm.value.circle_id) ? draftCircleMembers.value : (decision.value?.circle?.members || []);
  return list.filter((member) => {
    if (member.role === 'observer') return false;
    if (member.user_id === authorParticipant.value?.user_id) return false;
    if (member.user_id === draftForm.value.animator_id) return false;
    return true;
  });
});

const hasAlreadyParticipated = computed(() => {
  const userId = authStore.user?.id;
  const status = currentStatus.value;

  // Source 1 : user_status calculé côté serveur (le plus fiable)
  if (decision.value?.user_status) {
    const needs = decision.value.user_status.needs_action;
    // Pour les porteurs/animateurs, needs_action concerne les réponses à donner, pas la participation
    if (!['author', 'animator'].includes(myRole.value)) {
      return !needs;
    }
  }

  // Source 2 : phaseParticipationMap du serveur (mis à jour à chaque refreshDecision)
  if (userId && status && phaseParticipationMap.value) {
    const phaseMap = phaseParticipationMap.value[status] || {};
    if (phaseMap[userId] === true) return true;
  }

  // Source 3 : has_participated du store (fallback)
  return Boolean(decisionStore.hasParticipated || myConsent.value?.has_participated);
});

const showParticipationCard = computed(() => {
  if (!decision.value) return false;
  if (!['clarification', 'reaction', 'objection'].includes(decision.value.status)) return false;
  if (['author', 'animator', 'excluded', 'observer'].includes(myRole.value)) return false;
  return true;
});

const participationCardTitle = computed(() => {
  if (decision.value?.status === 'clarification') return hasAlreadyParticipated.value ? 'Votre clarification' : 'Votre clarification';
  if (decision.value?.status === 'reaction') return hasAlreadyParticipated.value ? 'Votre réaction' : 'Votre réaction';
  return hasAlreadyParticipated.value ? 'Votre position' : 'Votre position';
});

const modalActionLabel = computed(() => {
  if (decision.value?.status === 'clarification') return 'Poser une question / demander une clarification';
  if (decision.value?.status === 'reaction') return 'Donner votre avis / réaction';
  return 'Soumettre une objection ou une suggestion';
});

const hasActionStatus = computed(() => !!decision.value?.user_status);

const shouldShowAttachments = computed(() => {
  return !isDraft.value && displayAttachments.value.length > 0;
});

const isFeedbackEngineVisible = computed(() => {
  if (!decision.value) return false;
  const statusValues = ['clarification', 'reaction', 'objection', 'revision', 'adopted', 'adopted_override'];
  const currentStatusValue = (typeof decision.value.status === 'object' && decision.value.status !== null) 
    ? decision.value.status.value 
    : decision.value.status;
  return statusValues.includes(currentStatusValue);
});
const currentStatus = computed(() => {
  const s = decision.value?.status;
  return (typeof s === 'object' && s !== null) ? s.value : s;
});

const modalActionLabelShort = computed(() => {
  const s = currentStatus.value;
  if (s === 'clarification') return 'Clarifier';
  if (s === 'reaction') return 'Réagir';
  return 'Objecter';
});

const modalTitle = computed(() => {
  if (decision.value?.status === 'clarification') return 'Demander une clarification';
  if (decision.value?.status === 'reaction') return 'Partager votre réaction';
  return 'Soumettre un retour formel';
});

const modalDescription = computed(() => {
  if (decision.value?.status === 'clarification') {
    return 'Posez votre question ou demandez une précision avant de passer à la phase suivante.';
  }
  if (decision.value?.status === 'reaction') {
    return 'Exprimez votre ressenti, une réserve, ou un point d’attention sur cette proposition.';
  }
  return 'Décrivez une objection bloquante ou proposez une amélioration concrète pour faire avancer la décision.';
});

const statusLabel = computed(() => {
  const map = {
    draft: 'BROUILLON',
    clarification: 'CLARIFICATION',
    reaction: 'RÉACTION',
    objection: 'OBJECTION',
    revision: 'RÉVISION',
    adopted: 'ADOPTÉE',
    adopted_override: 'ADOPTÉE (FORCE)',
    deserted: 'ABANDONNÉE',
    abandoned: 'ABANDONNÉE',
    suspended: 'SUSPENDUE',
    lapsed: 'EXPIRÉE'
  };
  return map[currentStatus.value] || currentStatus.value?.toUpperCase();
});

const isUrgent = computed(() => {
    if (!decision.value?.current_deadline) return false;
    const deadline = new Date(decision.value.current_deadline);
    return (deadline.getTime() - new Date().getTime()) < 24 * 60 * 60 * 1000;
});

const deadlineLabel = computed(() => {
    if (!decision.value?.current_deadline) return '';
    const deadline = new Date(decision.value.current_deadline);
    const now = new Date();
    const diff = deadline.getTime() - now.getTime();

    if (diff < 0) return 'Échéance dépassée';
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    if (hours < 1) return 'Finit dans moins d\'une heure !';
    if (hours < 24) return `Finit dans ${hours}h`;
    
    const days = Math.floor(hours / 24);
    return `Échéance phase : ${days}j (${new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(deadline)})`;
});

const statusClass = computed(() => {
  const classes = {
    draft: 'badge-gray',
    clarification: 'badge-blue',
    reaction: 'badge-blue',
    objection: 'badge-amber',
    revision: 'badge-amber',
    adopted: 'badge-teal',
    adopted_override: 'badge-teal',
    abandoned: 'badge-red',
    suspended: 'badge-amber',
    lapsed: 'badge-red',
    deserted: 'badge-gray',
  };

  return classes[currentStatus.value] || 'badge-gray';
});

const stepActions = computed(() => {
  if (!decision.value || !isAuthorOrAnimator.value) return [];

  const actions = [];

  if (currentStatus.value === 'draft') {
    actions.push({
      key: 'publish',
      label: publishing.value ? 'Publication…' : 'Publier la décision',
      primary: true,
      run: publishDecision,
    });
  }

  if (currentStatus.value === 'clarification') {
    actions.push({
      key: 'to-reaction',
      label: 'Passer aux réactions',
      primary: true,
      run: () => transitionStatus('reaction'),
    });
  }

  if (currentStatus.value === 'reaction') {
    actions.push({
      key: 'to-objection',
      label: 'Passer aux objections',
      primary: true,
      run: () => transitionStatus('objection'),
    });
  }

  if (currentStatus.value === 'objection') {
    actions.push({
      key: 'to-revision',
      label: 'Nouvelle version',
      primary: false,
      run: () => transitionStatus('revision'),
    });
    actions.push({
      key: 'to-adopted',
      label: 'Passer à l’adoption',
      primary: true,
      run: () => transitionStatus('adopted'),
    });
  }

  return actions;
});

const updateDraftForm = () => {
  const value = decision.value;
  if (!value) return;

  const status = (typeof value.status === 'object' && value.status !== null) ? value.status.value : value.status;

  draftForm.value = {
    title: value.title || '',
    content: status === 'revision' ? (value.revision_content || value.current_version?.content || '') : (value.current_version?.content || ''),
    animator_id: value.participants?.find((p) => p.role === 'animator')?.user_id || '',
    circle_id: value.circle_id || '',
    category_ids: value.categories ? value.categories.map(c => c.id) : [],
    model_id: value.model_id || '',
    excluded_members: (value.participants || [])
      .filter((p) => p.role === 'excluded')
      .map((p) => p.user_id),
  };

  // Hydrate revision attachments if any
  if (status === 'revision') {
    // Priority 1: Use full attachment objects from the API (includes draft new uploads)
    if (value.revision_attachments) {
      revisionAttachments.value = [...value.revision_attachments];
    } 
    // Priority 2: Fallback to filtering current version attachments (backward compatibility/safety)
    else if (value.revision_attachment_ids && value.current_version?.attachments) {
      revisionAttachments.value = value.current_version.attachments.filter(a => value.revision_attachment_ids.includes(a.id));
    } else {
      revisionAttachments.value = [];
    }
  } else {
    revisionAttachments.value = [];
  }
};

watch(decision, () => {
  updateDraftForm();
}, { immediate: true });

watch(() => route.params.id, async (id) => {
  if (id) {
    resetToCurrentVersion();
    await decisionStore.fetchDecisionById(id);
    if (!decisionStore.decisions.length) {
      await decisionStore.fetchDecisions();
    }
    updateDraftForm();
    await fetchAllVersions();
  }
});



const formatDateOnly = (value) => {
  if (!value) return '';
  return new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(new Date(value));
};

const saveDraft = async () => {
  if (!decision.value) return false;

  savingDraft.value = true;

  try {
    await axios.put(`/api/v1/decisions/${decision.value.id}`, draftForm.value);
    await refreshDecision();
    return true;
  } catch (error) {
    window.alert(error.response?.data?.message || 'Impossible d’enregistrer le brouillon.');
    return false;
  } finally {
    savingDraft.value = false;
  }
};

const deleteDraft = async () => {
  if (!decision.value) return;
  if (!window.confirm('Supprimer définitivement ce brouillon ?')) return;

  deletingDraft.value = true;

  try {
    await axios.delete(`/api/v1/decisions/${decision.value.id}`);
    router.push({ name: 'DecisionList' });
  } catch (error) {
    window.alert(error.response?.data?.message || 'Suppression impossible pour le moment.');
  } finally {
    deletingDraft.value = false;
  }
};

const publishDecision = async () => {
  if (!decision.value) return;
  pendingPublishType.value = 'draft';
  showNotificationPrompt.value = true;
};

const executePublishDecision = async (notify = false) => {
  publishing.value = true;
  try {
    const saved = await saveDraft();
    if (!saved) return;
    
    await axios.post(`/api/v1/decisions/${decision.value.id}/transition`, { 
      to: 'clarification',
      notify: notify 
    });
    await refreshDecision();
  } catch (error) {
    window.alert(error.response?.data?.message || 'Erreur lors de la publication.');
  } finally {
    publishing.value = false;
  }
};

const handleRevisionFileUpload = (attachment) => {
  revisionAttachments.value.push(attachment);
};

const handleRevisionFileRemove = (attachmentId) => {
  revisionAttachments.value = revisionAttachments.value.filter(a => a.id !== attachmentId);
};

const isAttachmentReused = (attachmentId) => {
  return revisionAttachments.value.some(a => a.id === attachmentId);
};

const toggleAttachmentReuse = (attachment) => {
  if (isAttachmentReused(attachment.id)) {
    handleRevisionFileRemove(attachment.id);
  } else {
    revisionAttachments.value.push(attachment);
  }
};

const toggleAllPreviousAttachments = () => {
  if (!currentVersion.value?.attachments) return;
  
  const allReused = currentVersion.value.attachments.every(a => isAttachmentReused(a.id));
  
  if (allReused) {
    // Deselect all that belong to the current version
    const previousIds = currentVersion.value.attachments.map(a => a.id);
    revisionAttachments.value = revisionAttachments.value.filter(a => !previousIds.includes(a.id));
  } else {
    // Select all from current version (don't duplicate)
    currentVersion.value.attachments.forEach(a => {
      if (!isAttachmentReused(a.id)) {
        revisionAttachments.value.push(a);
      }
    });
  }
};

const saveRevision = async () => {
  if (!decision.value) return;
  savingDraft.value = true;
  try {
    const payload = {
      ...draftForm.value,
      revision_attachment_ids: revisionAttachments.value.map(a => a.id),
    };
    await axios.put(`/api/v1/decisions/${decision.value.id}`, payload);
    await refreshDecision();
  } catch (error) {
    window.alert(error.response?.data?.message || 'Impossible d’enregistrer la révision.');
  } finally {
    savingDraft.value = false;
  }
};

const publishRevision = async () => {
  if (!decision.value) return;
  pendingPublishType.value = 'revision';
  showRevisionPathModal.value = true;
};

const handlePathChoice = (status) => {
  targetRevisionStatus.value = status;
  showRevisionPathModal.value = false;
  showNotificationPrompt.value = true;
};

const executePublishRevision = async (notify = false) => {
  publishing.value = true;
  try {
    // D'abord on sauve le brouillon actuel
    const payload = {
      ...draftForm.value,
      revision_attachment_ids: revisionAttachments.value.map(a => a.id),
      notify: notify,
    };
    await axios.put(`/api/v1/decisions/${decision.value.id}`, payload);

    // Puis on publie une nouvelle version
    await axios.post(`/api/v1/decisions/${decision.value.id}/versions`, {
      content: draftForm.value.content,
      attachment_ids: revisionAttachments.value.map(a => a.id),
      notify: notify,
      status: targetRevisionStatus.value,
    });
    
    await refreshDecision();
    resetToCurrentVersion();
    pendingStore.fetch();
  } catch (error) {
    window.alert(error.response?.data?.message || 'Erreur lors de la publication de la révision.');
  } finally {
    publishing.value = false;
  }
};

const handlePublishConfirm = (notify) => {
  showNotificationPrompt.value = false;
  if (pendingPublishType.value === 'draft') {
    executePublishDecision(notify);
  } else if (pendingPublishType.value === 'revision') {
    executePublishRevision(notify);
  }
};

const fetchAllVersions = async () => {
  if (!decision.value) return;
  try {
    const { data } = await axios.get(`/api/v1/decisions/${decision.value.id}/versions`);
    allVersions.value = data.versions || [];
    if (!selectedVersionNavId.value && decision.value.current_version?.id) {
       selectedVersionNavId.value = decision.value.current_version.id;
    }
  } catch (e) { console.error(e); }
};

const handleVersionChange = async () => {
  if (!selectedVersionNavId.value) return;
  
  if (selectedVersionNavId.value === decision.value.current_version?.id) {
    resetToCurrentVersion();
    return;
  }
  
  loadingHistorical.value = true;
  viewingVersionId.value = selectedVersionNavId.value;
  
  try {
    const { data } = await axios.get(`/api/v1/decisions/${decision.value.id}/versions/${selectedVersionNavId.value}`);
    historicalVersionData.value = {
      ...data.version,
      participation_stats: data.participation_stats,
      phase_participation_map: data.phase_participation_map
    };
  } catch (e) {
    window.alert("Erreur lors du chargement de la version historique.");
    resetToCurrentVersion();
  } finally {
    loadingHistorical.value = false;
  }
};

const resetToCurrentVersion = () => {
  viewingVersionId.value = null;
  historicalVersionData.value = null;
  if (decision.value) {
    selectedVersionNavId.value = decision.value.current_version?.id;
  }
};

const transitionStatus = async (to) => {
  if (!decision.value) return;

  transitioning.value = true;

  try {
    await axios.post(`/api/v1/decisions/${decision.value.id}/transition`, { to });
    await refreshDecision();
  } catch (error) {
    window.alert(error.response?.data?.message || `Transition impossible vers ${to}.`);
  } finally {
    transitioning.value = false;
  }
};

const refreshDecision = async () => {
  if (!decision.value) return;
  await decisionStore.fetchDecisionById(decision.value.id);
  if (!decisionStore.decisions.length) {
    await decisionStore.fetchDecisions();
  }
  updateDraftForm();
  await fetchAllVersions();
};

const handleMeetingVersionChange = async (versionId) => {
  selectedVersionNavId.value = versionId;
  await handleVersionChange();
};

const goToDecision = (id) => {
  if (!id || id === decision.value?.id) return;
  router.push({ name: 'DecisionDetail', params: { id } });
};

const openReactionModal = () => {
  reactionText.value = '';
  reactionType.value = decision.value?.status === 'objection' ? 'objection' : 'reaction';
  showReactionModal.value = true;
};

const submitConsent = async (type) => {
  if (!decision.value || !currentVersion.value) return;

  try {
    await axios.post(`/api/v1/decisions/${decision.value.id}/versions/${currentVersion.value.id}/consent`, { type });
    await refreshDecision();
    pendingStore.fetch();
  } catch (error) {
    window.alert(error.response?.data?.message || 'Impossible d’enregistrer votre participation.');
  }
};

const submitReaction = async () => {
  if (!decision.value || !currentVersion.value || !reactionText.value.trim()) return;

  submittingReaction.value = true;

  try {
    let feedbackType = 'reaction';

    if (decision.value.status === 'clarification') {
      feedbackType = 'clarification';
    } else if (decision.value.status === 'objection') {
      feedbackType = reactionType.value;
    }

    await axios.post(`/api/v1/decisions/${decision.value.id}/feedback`, {
      type: feedbackType,
      content: reactionText.value.trim(),
    });

    if (decision.value.status === 'reaction') {
      await axios.post(`/api/v1/decisions/${decision.value.id}/versions/${currentVersion.value.id}/consent`, {
        type: 'no_reaction',
      });
    }

    showReactionModal.value = false;
    reactionText.value = '';
    await refreshDecision();
    pendingStore.fetch();
  } catch (error) {
    window.alert(error.response?.data?.message || 'Impossible d’enregistrer ce retour.');
  } finally {
    submittingReaction.value = false;
  }
};

const consentLabel = (signal) => {
  const normalized = signal?.value || signal;
  const labels = {
    no_questions: 'Clarification enregistrée',
    no_reaction: 'Réaction enregistrée',
    no_objection: 'Sans objection',
    abstention: 'Abstention',
  };

  return labels[normalized] || 'Participation enregistrée';
};

const consentIcon = (signal) => {
  const normalized = signal?.value || signal;
  const icons = {
    no_questions: 'fa-solid fa-circle-check',
    no_reaction: 'fa-solid fa-thumbs-up',
    no_objection: 'fa-solid fa-check',
    abstention: 'fa-solid fa-eye',
  };

  return icons[normalized] || 'fa-solid fa-check';
};

// Removed redundant watch since it's already handled above with async/await

watch(() => draftForm.value.circle_id, async (newCircleId) => {
  if (isDraft.value && newCircleId) {
    loadingCircleMembers.value = true;
    try {
      const { data } = await axios.get(`/api/v1/circles/${newCircleId}/members`);
      draftCircleMembers.value = data.members || [];
    } catch (e) {
      draftCircleMembers.value = [];
    } finally {
      loadingCircleMembers.value = false;
    }
  }
}, { immediate: true });

onMounted(async () => {
  if (route.params.id) {
    await decisionStore.fetchDecisionById(route.params.id);
    updateDraftForm();
    await fetchAllVersions();
  }

  try {
    const [circleRes, catRes] = await Promise.all([
      axios.get('/api/v1/circles'),
      axios.get('/api/v1/categories'),
    ]);
    circles.value = circleRes.data.circles || [];
    categories.value = catRes.data.categories || [];
  } catch (e) { /* silent */ }
});

const handleManualAction = async (type) => {
  if (type === 'abandon' && !confirm('Êtes-vous sûr de vouloir abandonner cette décision ?')) return;
  
  performingAction.value = true;
  try {
    let endpoint = `/api/v1/decisions/${decision.value.id}/transition`;
    let data = {};

    if (type === 'suspend') {
      data = { to: 'suspended' };
    } else if (type === 'resume') {
      data = { to: decision.value.status_before_suspension || 'clarification' };
    } else if (type === 'revision') {
      data = { to: 'revision' };
    } else if (type === 'abandon') {
      endpoint = `/api/v1/decisions/${decision.value.id}/abandon`;
    } else {
      // Cas générique pour les transitions (reaction, objection, adopted)
      data = { to: type };
    }

    const { data: responseData } = await axios.post(endpoint, data);
    decisionStore.setCurrentDecision(responseData.decision);
    showActionsModal.value = false;
    
    // Pas d'alerte en mode meeting pour éviter de casser le fullscreen
    if (!showMeetingMode.value) {
      alert('Action effectuée avec succès.');
    }
  } catch (e) {
    console.error(e);
    alert('Erreur lors de l\'exécution de l\'action.');
  } finally {
    performingAction.value = false;
  }
};
</script>

<style scoped>
.p-24 { padding: 24px; }

.page-header {
  display: flex;
  align-items: center;
  gap: 24px;
}

.header-text-block {
  flex: 1;
  min-width: 0;
}

.header-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 6px;
  font-size: 11px;
  color: var(--gray-500);
}

.page-role-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.header-nav {
  display: flex;
  gap: 8px;
}

.role-circle {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.role-author { border: 2px solid var(--blue-200); background: var(--blue-50); }
.role-animator { border: 2px solid var(--amber-100); background: var(--amber-50); }
.role-participant { border: 2px solid var(--teal-100); background: var(--teal-50); }
.role-excluded { border: 2px solid var(--red-100); background: var(--red-50); }
.role-observer { border: 2px solid var(--gray-200); background: var(--gray-100); }

.steps-bar {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-lg);
  padding: 12px 20px;
  margin-bottom: 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.steps {
  display: flex;
  align-items: center;
  gap: 0;
  flex-wrap: wrap;
}

.step {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 500;
}

.step-num {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 600;
  flex-shrink: 0;
}

.step-done .step-num { background: var(--teal-600); color: white; }
.step-active .step-num { background: var(--blue-800); color: white; }
.step-pending .step-num { background: var(--gray-200); color: var(--gray-500); }
.step-done .step-label { color: var(--teal-600); }
.step-active .step-label { color: var(--blue-800); font-weight: 600; }
.step-pending .step-label { color: var(--gray-400); }

.step-sep {
  width: 24px;
  height: 1px;
  background: var(--gray-200);
  margin: 0 4px;
  flex-shrink: 0;
}

.status-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: flex-end;
}

.grid-layout {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

@media (min-width: 960px) {
  .grid-layout {
    flex-direction: row;
    align-items: flex-start;
  }

  .col-main { flex: 2; min-width: 0; }
  .col-side { flex: 1; min-width: 320px; }
}

.draft-header {
  background: linear-gradient(135deg, #fff8ed, #fff3d6);
}

.form-row {
  display: flex;
  gap: 16px;
}

.form-col {
  flex: 1;
}

.checkbox-panel {
  border: 1px solid var(--gray-200);
  background: var(--gray-50);
  border-radius: var(--radius-md);
  padding: 12px;
  display: grid;
  gap: 8px;
  max-height: 200px;
  overflow-y: auto;
}

.checkbox-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--gray-700);
}

.draft-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.decision-prose :deep(p),
.decision-prose :deep(ul),
.decision-prose :deep(ol),
.decision-prose :deep(blockquote),
.decision-prose :deep(pre),
.decision-prose :deep(h1),
.decision-prose :deep(h2),
.decision-prose :deep(h3) {
  margin-bottom: 12px;
}

.decision-prose :deep(ul),
.decision-prose :deep(ol) {
  padding-left: 20px;
}

.decision-prose :deep(a) {
  color: var(--blue-700);
}

.phase-header-done {
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  border-bottom: 2px solid var(--teal-500);
}

.phase-header-pending {
  background: linear-gradient(135deg, #fffbeb, #fef3c7);
  border-bottom: 2px solid var(--amber-600);
}

.consent-done-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  text-align: center;
  padding: 16px 0;
}

.consent-done-icon {
  font-size: 32px;
}

.consent-done-label {
  font-size: 14px;
  font-weight: 700;
  color: var(--teal-600);
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.divider {
  display: flex;
  align-items: center;
  text-align: center;
  color: var(--gray-400);
  font-size: 11px;
  font-weight: 700;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid var(--gray-100);
}

.divider::before { margin-right: 12px; }
.divider::after { margin-left: 12px; }

.vote-btn {
  flex: 1;
  padding: 10px;
  border-radius: var(--radius-md);
  font-family: var(--font-sans);
  font-size: 12px;
  font-weight: 600;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.12s;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.vote-ok {
  background: var(--teal-50);
  color: var(--teal-600);
  border-color: var(--teal-100);
}

.vote-ok:hover {
  background: var(--teal-600);
  color: white;
}

.vote-abs {
  background: var(--gray-100);
  color: var(--gray-600);
  border-color: var(--gray-200);
}

.vote-abs:hover {
  background: var(--gray-500);
  color: white;
}

.vote-icon {
  font-size: 18px;
  line-height: 1;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  z-index: 1000;
}

.modal-card {
  width: 100%;
  max-width: 640px;
  background: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--gray-200);
}

.modal-title {
  font-size: 15px;
  font-weight: 600;
  color: var(--gray-900);
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 16px;
}

@media (max-width: 768px) {
  .page-header {
    align-items: flex-start;
  }

  .steps-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .status-actions {
    justify-content: stretch;
  }

  .status-actions .btn {
    flex: 1;
  }

  .draft-actions,
  .form-row,
  .grid-2 {
    grid-template-columns: 1fr;
    flex-direction: column;
  }

  .hidden-mobile-text {
    display: none;
  }
}

/* Styles additionnels pour la sélection des pièces jointes */
.bg-gray-50 { background-color: var(--gray-50); }
.p-12 { padding: 12px; }
.border-radius-sm { border-radius: var(--radius-sm); }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.flex-wrap { flex-wrap: wrap; }
.gap-8 { gap: 8px; }
.gap-12 { gap: 12px; }
.cursor-pointer { cursor: pointer; }
.checkbox-sm {
  width: 16px;
  height: 16px;
  cursor: pointer;
}
.btn-link {
  background: none;
  border: none;
  color: var(--indigo-600);
  padding: 0;
  font-weight: 500;
  text-decoration: underline;
  cursor: pointer;
}
.btn-link:hover {
  color: var(--indigo-700);
}

/* Settings Styles */
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
  background: #f59e0b; /* Amber 500 */
  border-color: #fbbf24; /* Amber 400 */
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
</style>

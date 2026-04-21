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
    <div class="hero-card">
      <div class="hero-flex">
        <div class="flex items-center gap-20">
          <div class="page-role-indicator" v-if="myRoleInfo">
            <div class="role-circle" :class="myRoleInfo.className" :title="myRoleInfo.label">
              <i :class="myRoleInfo.icon"></i>
            </div>
          </div>
          <div>
            <div class="hero-title">{{ decision.title }}</div>
            <div class="hero-subtitle">
              {{ decision.circle?.name }} · Version {{ currentVersion?.version_number || 1 }} ·
              Porteur: <strong>{{ authorName }}</strong>
            </div>
          </div>
        </div>
        <div class="hero-action flex items-center gap-12">
          <span class="badge" :class="statusClass" style="font-size: 14px; padding: 6px 16px;">{{ statusLabel }}</span>
          
          <div class="header-nav">
             <template v-if="isRevision && isAuthorOrAnimator">
               <button class="btn btn-secondary" :disabled="savingDraft || publishing" @click="saveRevision">
                 {{ savingDraft ? 'Enregistrer' : 'Enregistrer' }}
               </button>
               <button class="btn btn-primary" :disabled="savingDraft || publishing" @click="publishRevision">
                 {{ publishing ? 'Publier...' : 'Publier' }}
               </button>
             </template>
             <template v-else>
               <button class="btn btn-white btn-icon" :disabled="!previousDecisionId" @click="goToDecision(previousDecisionId)">
                 <i class="fa-solid fa-chevron-left"></i>
               </button>
               <button class="btn btn-white btn-icon" :disabled="!nextDecisionId" @click="goToDecision(nextDecisionId)">
                 <i class="fa-solid fa-chevron-right"></i>
               </button>
             </template>
          </div>
        </div>
      </div>
      <div class="hero-footer-meta mt-16 flex items-center gap-16 text-xs" style="opacity: 0.8;">
        <span><i class="fa-solid fa-calendar-plus mr-4"></i> Créée le {{ formatDateOnly(decision.created_at) }}</span>
        <span><i class="fa-solid fa-clock mr-4"></i> Actue le {{ formatDateOnly(decision.updated_at) }}</span>
        <AnimatorSelector :decision="decision" :canEdit="isAuthorOrAnimator" @updated="refreshDecision" style="margin-left: auto;" />
      </div>
    </div>

    <div class="steps-bar">
      <div class="steps">
        <div class="step" :class="getStepClass('clarification')">
          <div class="step-num">1</div>
          <span class="step-label hidden-mobile-text">Clarification</span>
        </div>
        <div class="step-sep"></div>
        <div class="step" :class="getStepClass('reaction')">
          <div class="step-num">2</div>
          <span class="step-label hidden-mobile-text">Réaction</span>
        </div>
        <div class="step-sep"></div>
        <div class="step" :class="getStepClass('objection')">
          <div class="step-num">3</div>
          <span class="step-label hidden-mobile-text">Objection</span>
        </div>
        <div class="step-sep"></div>
        <div class="step" :class="getStepClass('adopted')">
          <div class="step-num"><i class="fa-solid fa-check"></i></div>
          <span class="step-label hidden-mobile-text">Adopté</span>
        </div>
      </div>

      <div class="status-actions" v-if="isAuthorOrAnimator">
        <button
          v-for="action in stepActions"
          :key="action.key"
          class="btn btn-sm"
          :class="action.primary ? 'btn-primary' : 'btn-secondary'"
          :disabled="transitioning || publishing || savingDraft"
          @click="action.run"
        >
          {{ action.label }}
        </button>
      </div>
    </div>

    <div class="page-body">
      <div class="grid-layout">
        <div class="col-main">
          <div v-if="isDraft" class="premium-card mb-16">
            <div class="pc-header pc-header-amber">
              <div class="pc-header-icon"><i class="fa-solid fa-file-pen"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mode brouillon</div>
                <div class="pc-header-sub">Édition active</div>
              </div>
            </div>

            <div class="card-body">
              <form @submit.prevent="saveDraft">
                <div class="form-group">
                  <label class="label">Titre</label>
                  <input v-model="draftForm.title" class="input" required>
                </div>

                <div class="form-group">
                  <label class="label">Contenu de la proposition</label>
                  <RichTextEditor
                    v-model="draftForm.content"
                    placeholder="Rédigez la décision avec mise en forme, liens et listes…"
                  />
                </div>

                <div class="form-row">
                  <div class="form-group form-col">
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

                <div class="form-group">
                  <label class="label">Exclure des membres de ce processus</label>
                  <div class="checkbox-panel">
                    <label
                      v-for="member in excludableMembers"
                      :key="member.user_id"
                      class="checkbox-row"
                    >
                      <input v-model="draftForm.excluded_members" type="checkbox" :value="member.user_id">
                      <span>{{ member.user?.name }}</span>
                    </label>
                  </div>
                </div>

                <div class="mb-16">
                  <AttachmentPanel
                    :attachments="currentVersion?.attachments || []"
                    :editable="true"
                    :version-id="currentVersion?.id || ''"
                    @changed="refreshDecision"
                  />
                </div>

                <div class="draft-actions">
                  <button type="button" class="btn btn-danger" :disabled="deletingDraft" @click="deleteDraft">
                    {{ deletingDraft ? 'Suppression…' : 'Supprimer le brouillon' }}
                  </button>
                  <button type="submit" class="btn btn-primary" :disabled="savingDraft">
                    {{ savingDraft ? 'Enregistrement…' : 'Enregistrer le brouillon' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div v-if="isRevision && isAuthorOrAnimator" class="premium-card mb-16">
            <div class="pc-header pc-header-amber">
              <div class="pc-header-icon"><i class="fa-solid fa-pen-to-square"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Révision de la proposition</div>
                <div class="pc-header-sub">Préparez la nouvelle version suite aux retours</div>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="label">Contenu de la nouvelle proposition</label>
                <RichTextEditor
                  v-model="draftForm.content"
                  placeholder="Appliquez les modifications nécessaires ici..."
                />
              </div>

              <!-- Sélection des pièces jointes précédentes -->
              <div v-if="currentVersion?.attachments?.length > 0" class="mb-16 p-12 bg-gray-50 border-radius-sm">
                <div class="flex items-center justify-between mb-8">
                  <label class="label mb-0" style="font-size: 13px;">Conserver des pièces jointes de la version précédente</label>
                  <button type="button" class="btn btn-link btn-sm" @click="toggleAllPreviousAttachments" style="font-size: 12px;">
                    {{ (currentVersion?.attachments || []).every(a => isAttachmentReused(a.id)) ? 'Tout décocher' : 'Tout cocher' }}
                  </button>
                </div>
                <div class="flex flex-wrap gap-12">
                  <div v-for="att in currentVersion.attachments" :key="att.id" class="flex items-center gap-8">
                    <input 
                      type="checkbox" 
                      :id="'prev-att-' + att.id"
                      :checked="isAttachmentReused(att.id)"
                      @change="toggleAttachmentReuse(att)"
                      class="checkbox-sm"
                    >
                    <label :for="'prev-att-' + att.id" class="text-sm cursor-pointer" style="margin-bottom: 0;">{{ att.filename }}</label>
                  </div>
                </div>
              </div>

              <div class="mb-16">
                <AttachmentPanel
                  :attachments="revisionAttachments"
                  :editable="true"
                  version-id=""
                  @uploaded="handleRevisionFileUpload"
                  @removed="handleRevisionFileRemove"
                />
              </div>

              <div class="draft-actions">
                <button class="btn btn-secondary" :disabled="savingDraft" @click="saveRevision">
                  {{ savingDraft ? 'Enregistrement...' : 'Enregistrer le brouillon' }}
                </button>
                <button class="btn btn-primary" :disabled="publishing || savingDraft" @click="publishRevision">
                  {{ publishing ? 'Publication...' : 'Publier cette version' }}
                </button>
              </div>
            </div>
          </div>

          <div v-if="!isRevision && !isDraft" class="premium-card mb-16">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i :class="viewingVersionId ? 'fa-solid fa-clock-rotate-left' : 'fa-solid fa-file-lines'"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">{{ viewingVersionId ? 'Version ' + historicalVersionData.version_number : 'Contenu de la décision' }}</div>
                <div class="pc-header-sub">{{ viewingVersionId ? 'Version historique' : 'Version actuelle de la proposition' }}</div>
              </div>
            </div>
            <div class="card-body">
              <div v-if="displayContent" class="decision-prose" v-html="displayContent"></div>
              <div v-else class="text-muted text-sm">Aucun contenu disponible pour cette version.</div>
            </div>
          </div>

          <div class="mb-16">
            <AttachmentPanel
              :attachments="displayAttachments"
              :editable="false"
              :version-id="viewingVersionId || currentVersion?.id || ''"
            />
          </div>
          
          <div v-if="isRevision && !isDraft" class="premium-card mb-16">
            <div class="pc-header pc-header-blue" style="opacity: 0.9;">
              <div class="pc-header-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">{{ isAuthorOrAnimator ? 'Proposition à réviser' : 'Proposition actuelle' }}</div>
                <div class="pc-header-sub">Version {{ currentVersion?.version_number }} en cours de révision</div>
              </div>
            </div>
            <div class="card-body" style="background: var(--gray-50); border-radius: 0 0 var(--radius-lg) var(--radius-lg);">
              <div v-if="currentVersion?.content" class="decision-prose" v-html="currentVersion.content"></div>
              <div v-else class="text-muted text-sm">Aucun contenu disponible pour cette version.</div>
            </div>
          </div>

          <div v-if="showParticipationCard && !hasAlreadyParticipated" class="premium-card mb-16">
            <div class="pc-header" :class="hasAlreadyParticipated ? 'pc-header-teal' : 'pc-header-amber'">
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
                 :decision="viewingVersionId ? historicalVersionDecision : decision" 
                 :historical-data="viewingVersionId ? historicalVersionData : null"
                 @refresh="refreshDecision" 
            />
          </div>
        </div>

        <div class="col-side">
          <!-- Cartes de Rôle / Participation -->
          <div v-if="myRole === 'author'" class="premium-card mb-16">
            <div class="pc-header pc-header-blue" style="padding: 12px;">
              <div class="pc-header-icon" style="font-size: 1.2rem;"><i class="fa-solid fa-bullhorn"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title" style="font-size: 14px;">Porteur</div>
                <div class="pc-header-sub" style="font-size: 12px;">Vous pilotez cette décision.</div>
              </div>
            </div>
          </div>
          <div v-else-if="myRole === 'animator'" class="premium-card mb-16">
            <div class="pc-header pc-header-amber" style="padding: 12px;">
              <div class="pc-header-icon" style="font-size: 1.2rem;"><i class="fa-solid fa-user-tie"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title" style="font-size: 14px;">Animateur</div>
                <div class="pc-header-sub" style="font-size: 12px;">Vous facilitez ce processus.</div>
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

    <NotificationPromptModal 
      :visible="showNotificationPrompt" 
      @confirm="handlePublishConfirm" 
      @cancel="showNotificationPrompt = false" 
    />
  </main>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AnimatorSelector from '../components/AnimatorSelector.vue';
import AttachmentPanel from '../components/AttachmentPanel.vue';
import FeedbackEngine from '../components/FeedbackEngine.vue';
import ParticipantPhasePanel from '../components/ParticipantPhasePanel.vue';
import RichTextEditor from '../components/RichTextEditor.vue';
import NotificationPromptModal from '../components/NotificationPromptModal.vue';
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
      if (signal === 'no_questions') map.clarification[c.user_id] = true;
      if (signal === 'no_reaction') map.reaction[c.user_id] = true;
      if (['no_objection', 'abstention'].includes(signal)) map.objection[c.user_id] = true;
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
const showNotificationPrompt = ref(false);
const pendingPublishType = ref(null);
const revisionAttachments = ref([]);

// Navigation Historique
const allVersions = ref([]);
const viewingVersionId = ref(null);
const historicalVersionData = ref(null);
const selectedVersionNavId = ref(null);
const loadingHistorical = ref(false);

const draftForm = ref({
  title: '',
  content: '',
  animator_id: '',
  excluded_members: [],
});

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
  return explicitParticipant.value?.role || currentCircleMember.value?.role || 'participant';
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

const isAuthorOrAnimator = computed(() => ['author', 'animator'].includes(myRole.value));

const selectableMembers = computed(() => (
  decision.value?.circle?.members || []
).filter((member) => member.role !== 'observer' && member.user_id !== authorParticipant.value?.user_id));

const authorParticipant = computed(() => decision.value?.participants?.find((participant) => participant.role === 'author') || null);

const authorName = computed(() => authorParticipant.value?.user?.name || 'Inconnu');

const excludableMembers = computed(() => (
  decision.value?.circle?.members || []
).filter((member) => {
  if (member.role === 'observer') return false;
  if (member.user_id === authorParticipant.value?.user_id) return false;
  if (member.user_id === draftForm.value.animator_id) return false;
  return true;
}));

const hasAlreadyParticipated = computed(() => {
  if (decision.value?.user_status) {
    return !decision.value.user_status.needs_action;
  }
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
  const labels = {
    draft: 'Brouillon',
    clarification: 'Clarification',
    reaction: 'Réaction',
    objection: 'Objection',
    revision: 'Révision',
    adopted: 'Adoptée',
    adopted_override: 'Adoptée avec override',
    abandoned: 'Abandonnée',
    lapsed: 'Échue',
    deserted: 'Désertée',
  };

  return labels[currentStatus.value] || 'Inconnue';
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

const getStepClass = (step) => {
  const status = decision.value?.status;
  const order = ['draft', 'clarification', 'reaction', 'objection', 'adopted'];
  const currentIndex = order.indexOf(status);
  const stepIndex = order.indexOf(step);

  if (status === step) return 'step-active';
  if (currentIndex > stepIndex && stepIndex !== -1) return 'step-done';
  return 'step-pending';
};

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
      participation_stats: data.participation_stats
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
  await fetchAllVersions();
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

onMounted(async () => {
  if (route.params.id) {
    await decisionStore.fetchDecisionById(route.params.id);
    updateDraftForm();
    await fetchAllVersions();
  }
});
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
  margin: 0 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

@media (min-width: 768px) {
  .steps-bar {
    margin: 0 28px;
  }
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
</style>

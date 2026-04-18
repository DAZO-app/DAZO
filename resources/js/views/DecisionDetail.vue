<template>
  <main class="main" v-if="loading">
    <div class="p-24 text-center">Chargement...</div>
  </main>

  <main class="main" v-else-if="error">
    <div class="p-24 text-center text-red-500">
      <div style="font-size: 24px; margin-bottom: 8px;">😕</div>
      <div>{{ error }}</div>
      <button class="btn btn-secondary mt-16" @click="$router.push('/decisions')">Retour aux décisions</button>
    </div>
  </main>

  <main class="main" v-else-if="decision">
    <div class="page-header">
      <div class="page-role-indicator" v-if="myRoleInfo">
        <div class="role-circle" :class="myRoleInfo.className" :title="myRoleInfo.label">
          {{ myRoleInfo.icon }}
        </div>
        <div class="text-xs font-semibold text-gray-500 mt-4">{{ myRoleInfo.label }}</div>
      </div>

      <div class="header-text-block">
        <div class="page-title">{{ decision.title }}</div>
        <div class="page-subtitle">
          Cercle: {{ decision.circle?.name }} · Version: {{ currentVersion?.version_number || 1 }}
        </div>
        <div class="header-meta">
          <span><strong>Porteur:</strong> {{ authorName }}</span>
          <span>Créée le {{ formatDateOnly(decision.created_at) }}</span>
          <span>Dernière activité le {{ formatDateOnly(decision.updated_at) }}</span>
        </div>
        <AnimatorSelector :decision="decision" :canEdit="isAuthorOrAnimator" @updated="refreshDecision" />
      </div>

      <div class="page-actions">
        <span class="badge" :class="statusClass">{{ statusLabel }}</span>
        <div class="header-nav">
          <button class="btn btn-secondary btn-icon" :disabled="!previousDecisionId" @click="goToDecision(previousDecisionId)">
            &lt;
          </button>
          <button class="btn btn-secondary btn-icon" :disabled="!nextDecisionId" @click="goToDecision(nextDecisionId)">
            &gt;
          </button>
        </div>
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
          <div class="step-num">✓</div>
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
          <div v-if="isDraft" class="card mb-16">
            <div class="card-header draft-header">
              <span class="card-title">Mode brouillon</span>
              <span class="badge badge-amber">Édition active</span>
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

          <div v-else class="card mb-16">
            <div class="card-header">
              <span class="card-title">Contenu de la décision</span>
            </div>
            <div class="card-body">
              <div v-if="currentVersion?.content" class="decision-prose" v-html="currentVersion.content"></div>
              <div v-else class="text-muted text-sm">Aucun contenu disponible pour cette version.</div>
            </div>
          </div>

          <div v-if="!isDraft" class="mb-16">
            <AttachmentPanel
              :attachments="currentVersion?.attachments || []"
              :editable="false"
              :version-id="currentVersion?.id || ''"
            />
          </div>

          <div
            v-if="['clarification', 'reaction'].includes(decision.status)"
            class="mb-16"
          >
            <DecisionThread :decision="decision" />
          </div>

          <div
            v-if="['clarification', 'objection', 'revision', 'adopted', 'adopted_override'].includes(decision.status)"
            class="mb-16"
          >
            <FeedbackEngine :decision="decision" @refresh="refreshDecision" />
          </div>
        </div>

        <div class="col-side">
          <div v-if="showParticipationCard" class="card mb-16">
            <div class="card-header" :class="hasAlreadyParticipated ? 'phase-header-done' : 'phase-header-pending'">
              <span class="status-dot" :class="hasAlreadyParticipated ? 'dot-teal' : 'dot-amber'"></span>
              <span class="card-title">{{ participationCardTitle }}</span>
            </div>

            <div class="card-body">
              <div v-if="hasAlreadyParticipated" class="consent-done-block">
                <div class="consent-done-icon">{{ consentIcon(myConsent?.signal) }}</div>
                <div class="consent-done-label">{{ consentLabel(myConsent?.signal) }}</div>
                <p class="text-xs text-muted mt-8">Votre participation est déjà enregistrée pour cette phase.</p>
              </div>

              <template v-else>
                <button class="btn btn-secondary btn-block mb-12" @click="openReactionModal">
                  <span class="text-lg mr-8">💬</span>
                  {{ modalActionLabel }}
                </button>

                <div class="divider mb-16">OU</div>

                <div class="grid-2">
                  <button
                    v-if="decision.status === 'clarification'"
                    class="vote-btn vote-ok"
                    @click="submitConsent('no_questions')"
                  >
                    <span class="vote-icon">👌</span>
                    C'est clair
                  </button>

                  <button
                    v-if="decision.status === 'reaction'"
                    class="vote-btn vote-ok"
                    @click="submitConsent('no_reaction')"
                  >
                    <span class="vote-icon">👍</span>
                    RAS / Consensus
                  </button>

                  <button
                    v-if="decision.status === 'objection'"
                    class="vote-btn vote-ok"
                    @click="submitConsent('no_objection')"
                  >
                    <span class="vote-icon">👍</span>
                    Sans objection
                  </button>

                  <button
                    v-if="decision.status === 'objection'"
                    class="vote-btn vote-abs"
                    @click="submitConsent('abstention')"
                  >
                    <span class="vote-icon">👀</span>
                    Abstention
                  </button>
                </div>
              </template>
            </div>
          </div>

          <ParticipantPhasePanel
            :decision="decision"
            :phase-participation-map="phaseParticipationMap"
          />
        </div>
      </div>
    </div>

    <div v-if="showReactionModal" class="modal-overlay" @click.self="showReactionModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <span class="modal-title">{{ modalTitle }}</span>
          <button class="btn btn-ghost btn-icon" @click="showReactionModal = false">✕</button>
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
  </main>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AnimatorSelector from '../components/AnimatorSelector.vue';
import AttachmentPanel from '../components/AttachmentPanel.vue';
import DecisionThread from '../components/DecisionThread.vue';
import FeedbackEngine from '../components/FeedbackEngine.vue';
import ParticipantPhasePanel from '../components/ParticipantPhasePanel.vue';
import RichTextEditor from '../components/RichTextEditor.vue';
import { useAuthStore } from '../stores/auth';
import { useDecisionStore } from '../stores/decision';
import { usePendingStore } from '../stores/pending';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const decisionStore = useDecisionStore();
const pendingStore = usePendingStore();

const loading = computed(() => decisionStore.loading);
const error = computed(() => decisionStore.error);
const decision = computed(() => decisionStore.currentDecision);
const myConsent = computed(() => decisionStore.myConsent);
const phaseParticipationMap = computed(() => decisionStore.phaseParticipationMap || {});

const currentVersion = computed(() => decision.value?.current_version || null);
const savingDraft = ref(false);
const deletingDraft = ref(false);
const publishing = ref(false);
const transitioning = ref(false);
const submittingReaction = ref(false);
const showReactionModal = ref(false);

const draftForm = ref({
  title: '',
  content: '',
  animator_id: '',
  excluded_members: [],
});

const reactionText = ref('');
const reactionType = ref('objection');

const roleMeta = {
  author: { label: 'Porteur', icon: '💡', className: 'role-author' },
  animator: { label: 'Animateur', icon: '🎭', className: 'role-animator' },
  participant: { label: 'Participant', icon: '👥', className: 'role-participant' },
  excluded: { label: 'Exclu', icon: '🚫', className: 'role-excluded' },
  observer: { label: 'Observateur', icon: '👁', className: 'role-observer' },
};

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

const isDraft = computed(() => decision.value?.status === 'draft');

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

  return labels[decision.value?.status] || 'Inconnue';
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

  return classes[decision.value?.status] || 'badge-gray';
});

const stepActions = computed(() => {
  if (!decision.value || !isAuthorOrAnimator.value) return [];

  const actions = [];

  if (decision.value.status === 'draft') {
    actions.push({
      key: 'publish',
      label: publishing.value ? 'Publication…' : 'Publier la décision',
      primary: true,
      run: publishDecision,
    });
  }

  if (decision.value.status === 'clarification') {
    actions.push({
      key: 'to-reaction',
      label: 'Passer aux réactions',
      primary: true,
      run: () => transitionStatus('reaction'),
    });
  }

  if (decision.value.status === 'reaction') {
    actions.push({
      key: 'to-objection',
      label: 'Passer aux objections',
      primary: true,
      run: () => transitionStatus('objection'),
    });
  }

  if (decision.value.status === 'objection') {
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

watch(decision, (value) => {
  if (!value) return;

  draftForm.value = {
    title: value.title || '',
    content: value.current_version?.content || '',
    animator_id: value.participants?.find((participant) => participant.role === 'animator')?.user_id || '',
    excluded_members: (value.participants || [])
      .filter((participant) => participant.role === 'excluded')
      .map((participant) => participant.user_id),
  };
}, { immediate: true });

watch(() => route.params.id, (id) => {
  if (id) {
    decisionStore.fetchDecisionById(id);
    if (!decisionStore.decisions.length) {
      decisionStore.fetchDecisions();
    }
  }
}, { immediate: true });

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

  publishing.value = true;

  try {
    const saved = await saveDraft();
    if (!saved) {
      return;
    }
    await axios.post(`/api/v1/decisions/${decision.value.id}/transition`, { to: 'clarification' });
    await refreshDecision();
  } catch (error) {
    window.alert(error.response?.data?.message || 'Erreur lors de la publication.');
  } finally {
    publishing.value = false;
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
    no_questions: '👌',
    no_reaction: '👍',
    no_objection: '✅',
    abstention: '👀',
  };

  return icons[normalized] || '✅';
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
</style>

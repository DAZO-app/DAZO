<template>
  <div class="premium-card">
    <div class="pc-header" :class="headerClass">
      <div class="pc-header-icon"><i :class="headerIcon"></i></div>
      <div class="pc-header-content">
        <div class="pc-header-title">Échanges</div>
        <div class="pc-header-sub">{{ feedbacks.length }} message(s)</div>
      </div>
    </div>
    
    <div class="card-body" style="padding:0">
      <div v-if="feedbacks.length === 0 && consents.length === 0" class="text-sm text-center text-muted" style="padding:24px;">
        Aucun échange ou validation enregistré sur cette version.
      </div>
      
      <div v-else class="p-16">
        <!-- Validations simples : Abstention (Global car s'applique à tout le processus) -->
        <div v-if="consentsBySignal.abstention.length > 0" class="phase-group mb-24">
          <div class="feedback-card" style="border-left: 3px solid var(--gray-400)">
            <div class="feedback-card-header">
              <span class="badge badge-gray">ABSTENTION</span>
              <span style="font-size:12px; color:var(--gray-600)">{{ consentsBySignal.abstention.length }} personne(s)</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">S'abstient</span>
            </div>
            <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
               <span class="text-muted">Se sont abstenus pour cette version :</span> {{ consentsBySignal.abstention.map(c => c.user?.name).join(', ') }}
            </div>
          </div>
        </div>

        <!-- Section Clarifications -->
        <div v-if="groupedFeedbacks.clarifications.length > 0 || consentsBySignal.no_questions.length > 0 || acknowledgedUsers.length > 0 || (currentStatus === 'objection' && !isHistorical)" class="phase-group mb-24">
          <div class="phase-header mb-12" style="display: flex; justify-content: space-between; align-items: center;">
             <span class="phase-badge" :class="groupNeedsIntervention('clarifications') ? 'badge-red' : 'badge-amber'">Clarifications</span>
             <button v-if="groupedFeedbacks.clarifications.length > 0" class="btn btn-xs btn-outline" style="padding: 2px 8px; font-size: 10px;" @click="toggleAll('clarifications')">
               <i :class="areAllExpanded('clarifications') ? 'fa-solid fa-compress' : 'fa-solid fa-expand'"></i> {{ areAllExpanded('clarifications') ? 'Tout réduire' : 'Tout ouvrir' }}
             </button>
          </div>

          <div v-if="groupedFeedbacks.clarifications.length === 0 && consentsBySignal.no_questions.length === 0 && currentStatus === 'objection'" class="alert-skip mb-12">
            <i class="fa-solid fa-forward mr-8"></i>
            Cette phase a été considérée comme traitée lors de la publication de la révision.
          </div>
          
          <!-- Validations simples -->
          <div v-if="consentsBySignal.no_questions.length > 0 || acknowledgedUsers.length > 0" class="feedback-card feedback-ok">
            <div class="feedback-card-header">
              <span class="badge badge-teal">C'EST CLAIR</span>
              <span style="font-size:12px; color:var(--gray-600)">{{ consentsBySignal.no_questions.length + acknowledgedUsers.length }} personne(s)</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">Validé</span>
            </div>
            <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
               <div v-if="consentsBySignal.no_questions.length > 0">
                 <span class="text-muted">Ont validé immédiatement :</span> {{ consentsBySignal.no_questions.map(c => c.user?.name).join(', ') }}
               </div>
               <div v-if="acknowledgedUsers.length > 0" :class="{'mt-8 pt-8 border-t': consentsBySignal.no_questions.length > 0}">
                 <span class="text-muted">Validé après clarification :</span> {{ acknowledgedUsers.join(', ') }}
               </div>
            </div>
          </div>

          <div v-for="fb in groupedFeedbacks.clarifications" :key="fb.id" class="feedback-card" :class="{'fb-closed': isClosed(fb)}">
            <div class="feedback-card-header" style="cursor:pointer;" @click="toggleExpand(fb.id)">
              <span v-if="isMyTurn(fb)" class="pulse-dot" title="À votre tour de répondre"></span>
              <span class="badge" :class="typeBadge(fb.type)">{{ getVal(fb.type).toUpperCase() }}</span>
              <span style="font-size:13px; font-weight:600">{{ fb.author?.name }}</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">{{ statusLabel(fb.status) }}</span>
              <span class="text-xs text-muted ml-8"><i :class="isExpanded(fb.id) ? 'fa-solid fa-caret-down' : 'fa-solid fa-caret-right'"></i></span>
            </div>
            <div v-if="isExpanded(fb.id)" class="feedback-card-body">
              <div class="thread-section">
                <!-- Messages combinés (Ticket initial + Réponses) -->
                <div v-for="msg in allMessages(fb)" :key="msg.id" class="msg-row" :class="isMe(msg.author_id) ? 'msg-me' : 'msg-other'">
                  <div class="msg-avatar">
                    <i :class="getRoleIcon(msg.author_id)" :title="isMe(msg.author_id) ? 'Moi' : getRoleLabel(msg.author_id)"></i>
                  </div>
                  <div class="msg-bubble-container">
                    <div class="msg-bubble" :style="getMsgStyle(msg.author_id)">
                      <div class="msg-author-name" v-if="!isMe(msg.author_id)">{{ msg.author?.name }}</div>
                      <div class="msg-text">{{ msg.content }}</div>
                      <div class="msg-meta">{{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <form v-if="!isHistorical && !isClosed(fb) && canPostMessage(fb) && isPhaseActiveFor(fb)" @submit.prevent="submitMessage(fb)" class="msg-form mt-12">
                <input type="text" v-model="newMessages[fb.id]" class="input input-sm" placeholder="Votre réponse..." required>
                <button type="submit" class="btn btn-primary btn-sm" :disabled="sendingMsg">
                  <i class="fa-solid fa-paper-plane"></i>
                </button>
              </form>
              <div v-if="!isHistorical && !isClosed(fb)" class="mt-16 pt-16 border-t" style="display:flex; justify-content:space-between; align-items:center;">
                <button v-if="getVal(fb.type) !== 'clarification'" class="join-btn" @click="joinFeedback(fb.id)" :class="{ joined: isJoined(fb) }">
                  <i :class="isJoined(fb) ? 'fa-solid fa-check' : 'fa-solid fa-hand-fist'"></i> {{ isJoined(fb) ? 'Soutenu' : 'Rejoindre l\'objection' }} ({{ fb.joins?.length || 0 }})
                </button>
                <div v-if="isMe(fb.author_id)">
                   <button v-if="getVal(fb.type) === 'clarification'" class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'acknowledged')"><i class="fa-solid fa-check"></i> C'est clair</button>
                   <button v-else-if="getVal(fb.type) === 'objection'" class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'withdrawn')"><i class="fa-solid fa-check"></i> Plus d'objection</button>
                </div>
              </div>
            </div>
          </div>
        </div>

          <!-- Section Réactions -->
          <div v-if="groupedFeedbacks.reactions.length > 0 || consentsBySignal.no_reaction.length > 0 || (currentStatus === 'objection' && !isHistorical)" class="phase-group mb-24">
            <div class="phase-header mb-12">
               <span class="phase-badge" :class="groupNeedsIntervention('reactions') ? 'badge-red' : 'badge-blue'">Réactions</span>
            </div>

            <div v-if="groupedFeedbacks.reactions.length === 0 && consentsBySignal.no_reaction.length === 0 && currentStatus === 'objection'" class="alert-skip mb-12">
              <i class="fa-solid fa-forward mr-8"></i>
              Cette phase a été considérée comme traitée lors de la publication de la révision.
            </div>

            <!-- Validations simples -->
            <div v-if="consentsBySignal.no_reaction.length > 0" class="feedback-card feedback-ok">
              <div class="feedback-card-header">
                <span class="badge badge-teal">RAS</span>
                <span style="font-size:12px; color:var(--gray-600)">{{ consentsBySignal.no_reaction.length }} personne(s)</span>
                <span class="badge badge-gray text-xs" style="margin-left:auto">Validé</span>
              </div>
              <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
                 <span class="text-muted">Ont validé :</span> {{ consentsBySignal.no_reaction.map(c => c.user?.name).join(', ') }}
              </div>
            </div>

            <!-- Affichage direct des réactions -->
            <div v-if="groupedFeedbacks.reactions.length > 0" class="mt-12 p-8" style="background: var(--gray-50); border-radius: var(--radius-md); border: 1px solid var(--gray-100);">
              <div v-for="fb in groupedFeedbacks.reactions" :key="fb.id" class="mb-8" style="font-size: 13px; line-height: 1.4;">
                <span style="font-weight: 700; color: var(--gray-900);">{{ fb.author?.name }}</span>
                <span class="ml-8" style="color: var(--gray-700);">{{ fb.content }}</span>
              </div>
            </div>
          </div>

        <!-- Section Objections -->
        <div v-if="groupedFeedbacks.objections.length > 0 || consentsBySignal.no_objection.length > 0" class="phase-group">
          <div class="phase-header mb-12" style="display: flex; justify-content: space-between; align-items: center;">
             <span class="phase-badge badge-red">Objections & Suggestions</span>
             <button v-if="groupedFeedbacks.objections.length > 0" class="btn btn-xs btn-outline" style="padding: 2px 8px; font-size: 10px;" @click="toggleAll('objections')">
               <i :class="areAllExpanded('objections') ? 'fa-solid fa-compress' : 'fa-solid fa-expand'"></i> {{ areAllExpanded('objections') ? 'Tout réduire' : 'Tout ouvrir' }}
             </button>
          </div>

          <!-- Validations simples : Pas d'objection -->
          <div v-if="consentsBySignal.no_objection.length > 0 || withdrawnUsers.length > 0" class="feedback-card feedback-ok">
            <div class="feedback-card-header">
              <span class="badge badge-teal">PAS D'OBJECTION</span>
              <span style="font-size:12px; color:var(--gray-600)">{{ consentsBySignal.no_objection.length + withdrawnUsers.length }} personne(s)</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">Validé</span>
            </div>
            <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
               <div v-if="consentsBySignal.no_objection.length > 0">
                 <span class="text-muted">Pas d'objection immédiate :</span> {{ consentsBySignal.no_objection.map(c => c.user?.name).join(', ') }}
               </div>
               <div v-if="withdrawnUsers.length > 0" :class="{'mt-8 pt-8 border-t': consentsBySignal.no_objection.length > 0}">
                 <span class="text-muted">Plus d'objection :</span> {{ withdrawnUsers.join(', ') }}
               </div>
            </div>
          </div>

          <div v-for="fb in groupedFeedbacks.objections" :key="fb.id" class="feedback-card" :class="{'fb-closed': isClosed(fb)}">
            <div class="feedback-card-header" style="cursor:pointer;" @click="toggleExpand(fb.id)">
              <span v-if="isMyTurn(fb)" class="pulse-dot" title="À votre tour de répondre"></span>
              <span class="badge" :class="typeBadge(fb.type)">{{ getVal(fb.type).toUpperCase() }}</span>
              <span style="font-size:13px; font-weight:600">{{ fb.author?.name }}</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">{{ statusLabel(fb.status) }}</span>
              <span class="text-xs text-muted ml-8"><i :class="isExpanded(fb.id) ? 'fa-solid fa-caret-down' : 'fa-solid fa-caret-right'"></i></span>
            </div>
            <div v-if="isExpanded(fb.id)" class="feedback-card-body">
              <div class="thread-section">
                <div v-for="msg in allMessages(fb)" :key="msg.id" class="msg-row" :class="isMe(msg.author_id) ? 'msg-me' : 'msg-other'">
                  <div class="msg-avatar">
                    <i :class="getRoleIcon(msg.author_id)" :title="isMe(msg.author_id) ? 'Moi' : getRoleLabel(msg.author_id)"></i>
                  </div>
                  <div class="msg-bubble-container">
                    <div class="msg-bubble" :style="getMsgStyle(msg.author_id)">
                      <div class="msg-author-name" v-if="!isMe(msg.author_id)">{{ msg.author?.name }}</div>
                      <div class="msg-text">{{ msg.content }}</div>
                      <div class="msg-meta">{{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <form v-if="!isHistorical && !isClosed(fb) && canPostMessage(fb) && isPhaseActiveFor(fb)" @submit.prevent="submitMessage(fb)" class="msg-form mt-12">
                <input type="text" v-model="newMessages[fb.id]" class="input input-sm" placeholder="Votre réponse..." required>
                <button type="submit" class="btn btn-primary btn-sm" :disabled="sendingMsg">
                   <i class="fa-solid fa-paper-plane"></i>
                </button>
              </form>
              <div v-if="!isHistorical && !isClosed(fb)" class="mt-16 pt-16 border-t" style="display:flex; justify-content:space-between; align-items:center;">
                <button class="join-btn" @click="joinFeedback(fb.id)" :class="{ joined: isJoined(fb) }">
                  <i :class="isJoined(fb) ? 'fa-solid fa-check' : 'fa-solid fa-hand-fist'"></i> {{ isJoined(fb) ? 'Soutenu' : 'Rejoindre l\'objection' }} ({{ fb.joins?.length || 0 }})
                </button>
                <div v-if="isMe(fb.author_id)">
                   <button class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'withdrawn')"><i class="fa-solid fa-check"></i> Plus d'objection</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, onMounted, computed, watch, reactive } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const props = defineProps({
    decision: Object,
    historicalData: {
        type: Object,
        default: null
    }
});
const emits = defineEmits(['refresh']);

const authStore = useAuthStore();
const feedbacks = ref([]);
const consents = ref([]);
const newMessages = ref({});
const getVal = (t) => (typeof t === 'object' && t !== null) ? t.value : t;
const expandedStates = reactive({});
const loadingBtn = ref(false);
const sendingMsg = ref(false);

const currentStatus = computed(() => {
    if (!props.decision) return 'unknown';
    const s = props.decision.status;
    return (typeof s === 'object' && s !== null) ? s.value : s;
});

const isDecisionAuthor = computed(() => {
    if (!authStore.user || !props.decision?.author_id) return false;
    return String(props.decision.author_id) === String(authStore.user.id);
});
const isDecisionAnimator = computed(() => {
    if (!authStore.user || !props.decision?.animator_id) return false;
    return String(props.decision.animator_id) === String(authStore.user.id);
});
const isHistorical = computed(() => !!props.historicalData);
const currentVersionId = computed(() => {
    if (props.historicalData) return props.historicalData.id;
    return props.decision?.current_version?.id;
});

const isClarificationPhase = computed(() => currentStatus.value === 'clarification');
const isReactionPhase = computed(() => currentStatus.value === 'reaction');
const isObjectionPhase = computed(() => currentStatus.value === 'objection');

const groupedFeedbacks = computed(() => {
    const fbs = feedbacks.value || [];
    return {
        clarifications: fbs.filter(fb => getVal(fb.type) === 'clarification'),
        reactions: fbs.filter(fb => getVal(fb.type) === 'reaction'),
        objections: fbs.filter(fb => ['objection', 'suggestion'].includes(getVal(fb.type)))
    };
});

const consentsBySignal = computed(() => {
    const list = consents.value || [];
    const map = {
        no_questions: [],
        no_reaction: [],
        no_objection: [],
        abstention: []
    };
    list.forEach(c => {
        const s = getVal(c.signal);
        const p = getVal(c.phase);
        
        // On ne garde que les consentements dont la phase correspond au signal
        // pour éviter que des vieux signaux (sans phase) n'apparaissent partout.
        if (s === 'no_questions' && p !== 'clarification') return;
        if (s === 'no_reaction' && p !== 'reaction') return;
        if (s === 'no_objection' && p !== 'objection') return;
        
        // Sécurité supplémentaire : si l'utilisateur a déjà un feedback dans cette phase,
        // on ne l'affiche pas dans le bloc RAS (même si le backend n'a pas encore fini le ménage)
        const hasFeedbackInPhase = feedbacks.value.some(fb => {
            const fbType = getVal(fb.type);
            const fbPhase = (fbType === 'suggestion' ? 'objection' : fbType);
            return fb.author_id === c.user_id && fbPhase === p;
        });
        if (hasFeedbackInPhase) return;

        if (map[s]) map[s].push(c);
    });
    return map;
});

const acknowledgedUsers = computed(() => {
    return feedbacks.value
        .filter(fb => getVal(fb.type) === 'clarification' && getVal(fb.status) === 'acknowledged')
        .map(fb => fb.author?.name)
        .filter(Boolean);
});

const withdrawnUsers = computed(() => {
    return feedbacks.value
        .filter(fb => ['objection', 'suggestion'].includes(getVal(fb.type)) && getVal(fb.status) === 'withdrawn')
        .map(fb => fb.author?.name)
        .filter(Boolean);
});

const groupNeedsIntervention = (groupName) => {
    const group = groupedFeedbacks.value[groupName];
    if (!group) return false;
    return group.some(fb => isMyTurn(fb));
};

const needsIntervention = computed(() => {
    return groupNeedsIntervention('clarifications') || 
           groupNeedsIntervention('reactions') || 
           groupNeedsIntervention('objections');
});

const headerClass = computed(() => {
    return needsIntervention.value ? 'pc-header-light-blue' : 'pc-header-blue';
});

const headerIcon = computed(() => 'fa-solid fa-comments');

const toggleExpand = (id) => {
    expandedStates[id] = !expandedStates[id];
};
const isExpanded = (id) => !!expandedStates[id];

const areAllExpanded = (groupName) => {
    const fbs = groupedFeedbacks.value[groupName];
    if (!fbs || fbs.length === 0) return false;
    return fbs.every(fb => expandedStates[fb.id]);
};

const toggleAll = (groupName) => {
    const fbs = groupedFeedbacks.value[groupName];
    if (!fbs || fbs.length === 0) return;
    
    const allExp = areAllExpanded(groupName);
    fbs.forEach(fb => {
        expandedStates[fb.id] = !allExp;
    });
};

const isPhaseActiveFor = (fb) => {
    const t = getVal(fb.type);
    if (t === 'clarification' && currentStatus.value === 'clarification') return true;
    // Les réactions n'appellent pas de réponse (expression simple)
    if (t === 'reaction') return false; 
    if (['objection', 'suggestion'].includes(t) && currentStatus.value === 'objection') return true;
    return false;
};

const statusLabels = {
    'submitted': 'Soumis',
    'acknowledged': 'Pris en compte',
    'withdrawn': 'Retiré',
    'treated': 'Traité',
    'rejected': 'Écarté',
    'clarification_requested': 'Précision demandée',
    'in_treatment': 'En cours'
};
const statusLabel = (s) => statusLabels[s] || s;

const getConsentGroup = (signal) => {
    return consents.value.find(c => getVal(c.signal) === signal);
};

const fetchFeedbacks = async () => {
    if (!props.decision && !props.historicalData) return;
    if (props.historicalData) {
        feedbacks.value = props.historicalData.feedbacks || [];
        consents.value = props.historicalData.consents || [];
        return;
    }
    try {
        const { data } = await axios.get(`/api/v1/decisions/${props.decision.id}/feedback`, {
            params: { version_id: currentVersionId.value }
        });
        feedbacks.value = data.feedbacks || [];
        consents.value = data.consents || [];
    } catch (e) { console.error(e); }
};

watch(() => props.historicalData, () => fetchFeedbacks(), { deep: true });
watch(() => props.decision.current_version?.id, () => fetchFeedbacks());
watch(() => props.decision.updated_at, () => fetchFeedbacks());

onMounted(() => fetchFeedbacks());

const submitFeedback = async (dataPayload) => {
    loadingBtn.value = true;
    try {
        await axios.post(`/api/v1/decisions/${props.decision.id}/feedback`, dataPayload);
        await fetchFeedbacks();
        emits('refresh');
    } catch (e) {
        alert(e.response?.data?.message || 'Erreur lors de la soumission.');
    } finally { loadingBtn.value = false; }
};

const joinFeedback = async (id) => {
    try {
        await axios.post(`/api/v1/feedback/${id}/join`);
        await fetchFeedbacks();
        emits('refresh');
    } catch (e) { alert(e.response?.data?.message || 'Vous ne pouvez plus rejoindre.'); }
};

const closeFeedback = async (fb, statusValue) => {
    try {
        await axios.put(`/api/v1/decisions/${props.decision.id}/feedback/${fb.id}/status`, { status: statusValue });
        await fetchFeedbacks();
        emits('refresh');
    } catch (e) { alert(e.response?.data?.message || 'Erreur'); }
};

const submitMessage = async (fb) => {
    const text = newMessages.value[fb.id];
    if (!text) return;
    sendingMsg.value = true;
    try {
        await axios.post(`/api/v1/feedback/${fb.id}/messages`, { content: text });
        newMessages.value[fb.id] = '';
        await fetchFeedbacks();
    } catch (e) {
        alert(e.response?.data?.message || 'Erreur');
    } finally { sendingMsg.value = false; }
};

const typeBadge = (type) => {
    const val = typeof type === 'object' ? type.value : type;
    if (val === 'objection') return 'badge-red';
    if (val === 'clarification') return 'badge-amber';
    if (val === 'reaction') return 'badge-blue';
    return 'badge-blue';
};

const isMe = (author_id) => {
    if (!authStore.user) return false;
    return String(author_id) === String(authStore.user.id);
};
const isClosed = (fb) => ['withdrawn', 'acknowledged', 'rejected', 'treated'].includes(getVal(fb.status));
const isJoined = (fb) => authStore.user && fb.joins?.some(j => j.user_id === authStore.user.id);

const canPostMessage = (fb) => {
    return isMe(fb.author_id) || isDecisionAuthor.value || isDecisionAnimator.value;
};

const isMyTurn = (fb) => {
    if (isClosed(fb)) return false;
    if (!isPhaseActiveFor(fb)) return false;
    if (!canPostMessage(fb)) return false;
    
    const lastMsg = fb.messages?.length > 0 ? fb.messages[fb.messages.length - 1] : null;
    const lastAuthorId = lastMsg ? lastMsg.author_id : fb.author_id;

    const isLastStaff = String(lastAuthorId) === String(props.decision?.author_id) || 
                        String(lastAuthorId) === String(props.decision?.animator_id);
    const isLastOpener = String(lastAuthorId) === String(fb.author_id);

    if (isMe(fb.author_id)) {
        // J'ai ouvert le thread, c'est mon tour si le staff a répondu en dernier
        return isLastStaff;
    }

    if (isDecisionAnimator.value) {
        // L'animateur doit pouvoir intervenir sur chaque échange : son tour si le dernier message n'est pas de lui
        return String(lastAuthorId) !== String(authStore.user.id);
    }

    if (isDecisionAuthor.value) {
        // Je suis porteur, c'est mon tour si l'auteur du ticket a parlé en dernier
        return isLastOpener;
    }

    return false;
};

const allMessages = (fb) => {
    const first = {
        id: 'first-' + fb.id,
        author_id: fb.author_id,
        author: fb.author,
        content: fb.content,
        created_at: fb.created_at
    };
    return [first, ...(fb.messages || [])];
};

// --- Role helpers for chat ---
const getRoleIcon = (author_id) => {
    const userId = String(author_id);
    if (userId === String(props.decision?.author_id)) return 'fa-solid fa-bullhorn text-blue-500';
    if (userId === String(props.decision?.animator_id)) return 'fa-solid fa-user-tie text-orange-500';
    return 'fa-solid fa-user text-green-600';
};
const getRoleLabel = (author_id) => {
    const userId = String(author_id);
    if (userId === String(props.decision?.author_id)) return 'Porteur';
    if (userId === String(props.decision?.animator_id)) return 'Animateur';
    return 'Utilisateur';
};
const getMsgStyle = (author_id) => {
    const userId = String(author_id);
    const isPorteur = userId === String(props.decision?.author_id);
    const isAnimateur = userId === String(props.decision?.animator_id);
    
    let border = '1px solid var(--gray-200)';
    let bg = 'var(--gray-50)';
    
    if (isPorteur) {
        border = '2px solid #3b82f6'; // Blue-500
        bg = '#eff6ff'; // Blue-50
    } else if (isAnimateur) {
        border = '2px solid #f59e0b'; // Amber/Orange-500
        bg = '#fffbeb'; // Amber-50
    } else {
        border = '2px solid #10b981'; // Green-500
        bg = '#ecfdf5'; // Green-50
    }

    return { 
        border, 
        background: bg,
        borderRadius: isMe(author_id) ? '12px 12px 2px 12px' : '12px 12px 12px 2px'
    };
};
</script>

<style scoped>
.p-16 { padding: 16px; }
.feedback-card {
  border-radius: var(--radius-md);
  border: 1px solid var(--gray-200);
  overflow: hidden;
  margin-bottom: 10px;
  background: white;
}
.fb-closed { opacity: 0.65; }
.feedback-card-header { padding: 10px 14px; display: flex; align-items: center; gap: 8px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); }
.feedback-card-body { padding: 16px 14px; }
.border-t { border-top: 1px solid var(--gray-100); }
.pt-16 { padding-top: 16px; }

.ticket-content { font-size: 13px; color: var(--gray-800); line-height: 1.5; padding: 12px; border-radius: 6px; background: var(--gray-50); }
.feedback-obj { border-left: 3px solid var(--red-500); }
.feedback-sug { border-left: 3px solid var(--amber-500); }
.feedback-rea { border-left: 3px solid var(--blue-500); }
.feedback-ok  { border-left: 3px solid var(--teal-500); }

.pulse-dot {
  width: 8px;
  height: 8px;
  background-color: #ef4444;
  border-radius: 50%;
  margin-right: 6px;
  box-shadow: 0 0 0 rgba(239, 68, 68, 0.7);
  animation: pulse-red 1.5s infinite;
  flex-shrink: 0;
}

@keyframes pulse-red {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

/* Fil de messages style Chat */
.thread-section { display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto; padding: 8px 4px; }
.msg-row { display: flex; gap: 8px; align-items: flex-end; }
.msg-me { flex-direction: row-reverse; }
.msg-other { justify-content: flex-start; }

.msg-avatar { 
    width: 24px; height: 24px; border-radius: 50%; background: white; 
    display: flex; align-items: center; justify-content: center; 
    font-size: 11px; border: 1px solid var(--gray-200); flex-shrink: 0;
    margin-bottom: 2px;
}

.msg-bubble-container { max-width: 80%; display: flex; flex-direction: column; }
.msg-me .msg-bubble-container { align-items: flex-end; }
.msg-other .msg-bubble-container { align-items: flex-start; }

.msg-bubble { padding: 10px 14px; position: relative; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
.msg-author-name { font-size: 10px; font-weight: 700; color: var(--gray-500); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.02em; }
.msg-text { font-size: 12.5px; white-space: pre-wrap; color: var(--gray-900); line-height: 1.5; }
.msg-meta { font-size: 9px; color: var(--gray-400); margin-top: 6px; text-align: right; font-style: italic; }

.msg-form { display: flex; gap: 8px; background: var(--gray-50); padding: 8px; border-radius: var(--radius-md); border: 1px solid var(--gray-200); }
.msg-form input { border: none; background: transparent; flex: 1; font-size: 13px; }
.msg-form input:focus { outline: none; }

.join-btn {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 4px 12px; border-radius: var(--radius-full);
  font-size: 11px; font-weight: 600;
  border: 1px solid var(--gray-300); background: white;
  color: var(--gray-600); cursor: pointer; transition: all 0.12s;
}
.join-btn:hover, .join-btn.joined { background: var(--blue-50); border-color: var(--blue-200); color: var(--blue-700); }

.phase-header { display: flex; align-items: center; border-bottom: 1px solid var(--gray-100); padding-bottom: 8px; }
.phase-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 2px 8px; border-radius: 4px; }
.badge-amber { background: var(--amber-100); color: var(--amber-700); }
.badge-blue { background: var(--blue-100); color: var(--blue-700); }
.badge-red { background: var(--red-100); color: var(--red-700); }

.alert-skip {
  padding: 10px 14px;
  background: var(--gray-50);
  border: 1px dashed var(--gray-300);
  border-radius: var(--radius-md);
  font-size: 11px;
  color: var(--gray-500);
  display: flex;
  align-items: center;
}
</style>

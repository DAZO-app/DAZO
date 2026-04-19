<template>
  <div class="premium-card">
    <div class="pc-header" :class="headerClass">
      <div class="pc-header-icon">{{ headerIcon }}</div>
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
        <!-- Section Clarifications -->
        <div v-if="groupedFeedbacks.clarifications.length > 0 || getConsentGroup('no_questions')" class="phase-group mb-24">
          <div class="phase-header mb-12">
             <span class="phase-badge badge-amber">Clarifications</span>
          </div>
          
          <!-- Validations simples -->
          <div v-if="getConsentGroup('no_questions')" class="feedback-card feedback-ok">
            <div class="feedback-card-header">
              <span class="badge badge-teal">C'EST CLAIR</span>
              <span style="font-size:12px; color:var(--gray-600)">{{ getConsentGroup('no_questions').users.length }} personne(s)</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">Validé</span>
            </div>
            <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
               <span class="text-muted">Ont validé :</span> {{ getConsentGroup('no_questions').users.join(', ') }}
            </div>
          </div>

          <div v-for="fb in groupedFeedbacks.clarifications" :key="fb.id" class="feedback-card" :class="{'fb-closed': isClosed(fb)}">
            <div class="feedback-card-header" style="cursor:pointer;" @click="toggleExpand(fb.id)">
              <span class="badge" :class="typeBadge(fb.type)">{{ getVal(fb.type).toUpperCase() }}</span>
              <span style="font-size:13px; font-weight:600">{{ fb.author?.name }}</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">{{ statusLabel(fb.status) }}</span>
              <span class="text-xs text-muted ml-8">{{ isExpanded(fb.id) ? '▼' : '▶' }}</span>
            </div>
            <div v-if="isExpanded(fb.id)" class="feedback-card-body">
              <div class="ticket-content mb-16" :class="'feedback-' + (getVal(fb.type) === 'clarification' ? 'sug' : 'obj')">
                {{ fb.content }}
              </div>
              <!-- Inclusion du thread et des actions -->
              <div class="thread-section">
                <div v-for="msg in fb.messages" :key="msg.id" class="msg-row" :class="isMe(msg.author_id) ? 'msg-me' : ''">
                  <div class="msg-bubble" :class="isMe(msg.author_id) ? 'msg-mine' : 'msg-standard'">
                    <div class="msg-text">{{ msg.content }}</div>
                    <div class="msg-meta">{{ isMe(msg.author_id) ? 'Moi' : msg.author?.name }} · {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                  </div>
                </div>
              </div>
              <form v-if="!isHistorical && !isClosed(fb) && canPostMessage(fb)" @submit.prevent="submitMessage(fb)" style="display:flex; gap:8px; margin-top:12px;">
                <input type="text" v-model="newMessages[fb.id]" class="input input-sm" placeholder="Répondre..." required style="flex:1;">
                <button type="submit" class="btn btn-primary btn-sm" :disabled="sendingMsg">Envoyer</button>
              </form>
              <div v-if="!isHistorical && !isClosed(fb)" class="mt-16 pt-16 border-t" style="display:flex; justify-content:space-between; align-items:center;">
                <button v-if="getVal(fb.type) !== 'clarification'" class="join-btn" @click="joinFeedback(fb.id)" :class="{ joined: isJoined(fb) }">
                  {{ isJoined(fb) ? '✓ Soutenu' : '💪 Rejoindre l\'objection' }} ({{ fb.joins?.length || 0 }})
                </button>
                <div v-if="isMe(fb.author_id)">
                   <button v-if="getVal(fb.type) === 'clarification'" class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'acknowledged')">✓ C'est clair</button>
                   <button v-else-if="getVal(fb.type) === 'objection'" class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'withdrawn')">✓ Plus d'objection</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section Réactions -->
        <div v-if="groupedFeedbacks.reactions.length > 0 || getConsentGroup('no_reaction')" class="phase-group mb-24">
          <div class="phase-header mb-12">
             <span class="phase-badge badge-blue">Réactions</span>
          </div>

          <!-- Validations simples -->
          <div v-if="getConsentGroup('no_reaction')" class="feedback-card feedback-ok">
            <div class="feedback-card-header">
              <span class="badge badge-teal">RAS</span>
              <span style="font-size:12px; color:var(--gray-600)">{{ getConsentGroup('no_reaction').users.length }} personne(s)</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">Validé</span>
            </div>
            <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
               <span class="text-muted">Ont validé :</span> {{ getConsentGroup('no_reaction').users.join(', ') }}
            </div>
          </div>

          <div v-for="fb in groupedFeedbacks.reactions" :key="fb.id" class="feedback-card" :class="{'fb-closed': isClosed(fb)}">
            <div class="feedback-card-header" style="cursor:pointer;" @click="toggleExpand(fb.id)">
              <span class="badge" :class="typeBadge(fb.type)">{{ getVal(fb.type).toUpperCase() }}</span>
              <span style="font-size:13px; font-weight:600">{{ fb.author?.name }}</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">{{ statusLabel(fb.status) }}</span>
              <span class="text-xs text-muted ml-8">{{ isExpanded(fb.id) ? '▼' : '▶' }}</span>
            </div>
            <div v-if="isExpanded(fb.id)" class="feedback-card-body">
              <div class="ticket-content mb-16 feedback-rea">
                {{ fb.content }}
              </div>
              <!-- Inclusion du thread et des actions -->
              <div class="thread-section">
                <div v-for="msg in fb.messages" :key="msg.id" class="msg-row" :class="isMe(msg.author_id) ? 'msg-me' : ''">
                  <div class="msg-bubble" :class="isMe(msg.author_id) ? 'msg-mine' : 'msg-standard'">
                    <div class="msg-text">{{ msg.content }}</div>
                    <div class="msg-meta">{{ isMe(msg.author_id) ? 'Moi' : msg.author?.name }} · {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                  </div>
                </div>
              </div>
              <form v-if="!isHistorical && !isClosed(fb) && canPostMessage(fb)" @submit.prevent="submitMessage(fb)" style="display:flex; gap:8px; margin-top:12px;">
                <input type="text" v-model="newMessages[fb.id]" class="input input-sm" placeholder="Répondre..." required style="flex:1;">
                <button type="submit" class="btn btn-primary btn-sm" :disabled="sendingMsg">Envoyer</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Section Objections -->
        <div v-if="groupedFeedbacks.objections.length > 0 || getConsentGroup('no_objection') || getConsentGroup('abstention')" class="phase-group">
          <div class="phase-header mb-12">
             <span class="phase-badge badge-red">Objections & Suggestions</span>
          </div>

          <!-- Validations simples : Pas d'objection -->
          <div v-if="getConsentGroup('no_objection')" class="feedback-card feedback-ok">
            <div class="feedback-card-header">
              <span class="badge badge-teal">PAS D'OBJECTION</span>
              <span style="font-size:12px; color:var(--gray-600)">{{ getConsentGroup('no_objection').users.length }} personne(s)</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">Validé</span>
            </div>
            <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
               <span class="text-muted">Ont validé :</span> {{ getConsentGroup('no_objection').users.join(', ') }}
            </div>
          </div>

          <!-- Validations simples : Abstention -->
          <div v-if="getConsentGroup('abstention')" class="feedback-card" style="border-left: 3px solid var(--gray-400)">
            <div class="feedback-card-header">
              <span class="badge badge-gray">ABSTENTION</span>
              <span style="font-size:12px; color:var(--gray-600)">{{ getConsentGroup('abstention').users.length }} personne(s)</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">S'abstient</span>
            </div>
            <div class="feedback-card-body" style="padding:8px 14px 12px; font-size:12px; color:var(--gray-600)">
               <span class="text-muted">Se sont abstenus :</span> {{ getConsentGroup('abstention').users.join(', ') }}
            </div>
          </div>

          <div v-for="fb in groupedFeedbacks.objections" :key="fb.id" class="feedback-card" :class="{'fb-closed': isClosed(fb)}">
            <div class="feedback-card-header" style="cursor:pointer;" @click="toggleExpand(fb.id)">
              <span class="badge" :class="typeBadge(fb.type)">{{ getVal(fb.type).toUpperCase() }}</span>
              <span style="font-size:13px; font-weight:600">{{ fb.author?.name }}</span>
              <span class="badge badge-gray text-xs" style="margin-left:auto">{{ statusLabel(fb.status) }}</span>
              <span class="text-xs text-muted ml-8">{{ isExpanded(fb.id) ? '▼' : '▶' }}</span>
            </div>
            <div v-if="isExpanded(fb.id)" class="feedback-card-body">
              <div class="ticket-content mb-16 feedback-obj">
                {{ fb.content }}
              </div>
              <!-- Inclusion du thread et des actions -->
              <div class="thread-section">
                <div v-for="msg in fb.messages" :key="msg.id" class="msg-row" :class="isMe(msg.author_id) ? 'msg-me' : ''">
                  <div class="msg-bubble" :class="isMe(msg.author_id) ? 'msg-mine' : 'msg-standard'">
                    <div class="msg-text">{{ msg.content }}</div>
                    <div class="msg-meta">{{ isMe(msg.author_id) ? 'Moi' : msg.author?.name }} · {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                  </div>
                </div>
              </div>
              <form v-if="!isHistorical && !isClosed(fb) && canPostMessage(fb)" @submit.prevent="submitMessage(fb)" style="display:flex; gap:8px; margin-top:12px;">
                <input type="text" v-model="newMessages[fb.id]" class="input input-sm" placeholder="Répondre..." required style="flex:1;">
                <button type="submit" class="btn btn-primary btn-sm" :disabled="sendingMsg">Envoyer</button>
              </form>
              <div v-if="!isHistorical && !isClosed(fb)" class="mt-16 pt-16 border-t" style="display:flex; justify-content:space-between; align-items:center;">
                <button class="join-btn" @click="joinFeedback(fb.id)" :class="{ joined: isJoined(fb) }">
                  {{ isJoined(fb) ? '✓ Soutenu' : '💪 Rejoindre l\'objection' }} ({{ fb.joins?.length || 0 }})
                </button>
                <div v-if="isMe(fb.author_id)">
                   <button class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'withdrawn')">✓ Plus d'objection</button>
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
    return {
        clarifications: feedbacks.value.filter(fb => getVal(fb.type) === 'clarification'),
        reactions: feedbacks.value.filter(fb => getVal(fb.type) === 'reaction'),
        objections: feedbacks.value.filter(fb => ['objection', 'suggestion'].includes(getVal(fb.type)))
    };
});

const headerClass = computed(() => {
    if (isClarificationPhase.value) return 'pc-header-amber';
    if (isReactionPhase.value) return 'pc-header-blue';
    if (isObjectionPhase.value) return 'pc-header-red';
    return 'pc-header-indigo';
});

const headerIcon = computed(() => {
    if (isClarificationPhase.value) return '💬';
    if (isReactionPhase.value) return '😊';
    if (isObjectionPhase.value) return '🛑';
    return '⚡';
});

const toggleExpand = (id) => {
    expandedStates[id] = !expandedStates[id];
};
const isExpanded = (id) => !!expandedStates[id];

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

/* Fil de messages */
.thread-section { display: flex; flex-direction: column; gap: 8px; max-height: 300px; overflow-y: auto; padding-right: 4px; }
.msg-row { display: flex; justify-content: flex-start; }
.msg-me { justify-content: flex-end; }
.msg-bubble { padding: 8px 12px; border-radius: var(--radius-md); max-width: 85%; }
.msg-standard { background: var(--gray-100); border: 1px solid var(--gray-200); }
.msg-mine { background: var(--blue-50); border: 1px solid var(--blue-100); }
.msg-text { font-size: 12px; white-space: pre-wrap; color: var(--gray-800); line-height: 1.4; }
.msg-meta { font-size: 10px; color: var(--gray-500); margin-top: 4px; text-align: right; }

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
</style>

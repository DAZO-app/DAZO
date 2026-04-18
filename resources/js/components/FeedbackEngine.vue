<template>
  <div class="card">
    <div class="card-header">
      <span class="card-title">{{ titleLabel }}</span>
      <span class="badge badge-gray" style="margin-left:auto">{{ activeFeedbacks.length }} en cours</span>
    </div>
    
    <div class="card-body" style="padding:0">
      <div v-if="feedbacks.length === 0" class="text-sm text-center text-muted" style="padding:16px;">
        Aucune demande ou objection enregistrée sur cette version.
      </div>
      
      <div v-else class="p-16">
        <div v-for="fb in feedbacks" :key="fb.id" class="feedback-card" :class="{'fb-closed': isClosed(fb)}">
          
          <!-- En-tête du ticket -->
          <div class="feedback-card-header" style="cursor:pointer;" @click="toggleExpand(fb.id)">
            <span class="badge" :class="typeBadge(fb.type)">{{ fb.type.toUpperCase() }}</span>
            <span style="font-size:13px; font-weight:600">{{ fb.author?.name }}</span>
            <span class="badge badge-gray text-xs" style="margin-left:auto">{{ fb.status }}</span>
            <span class="text-xs text-muted ml-8">{{ expandedId === fb.id ? '▼' : '▶' }}</span>
          </div>

          <!-- Contenu du ticket -->
          <div v-if="expandedId === fb.id" class="feedback-card-body">
            <div class="ticket-content mb-16" :class="'feedback-' + (fb.type === 'clarification' ? 'sug' : 'obj')">
              {{ fb.content }}
            </div>

            <!-- Espace Thread -->
            <div class="thread-section">
              <div v-for="msg in fb.messages" :key="msg.id" class="msg-row" :class="isMe(msg.author_id) ? 'msg-me' : ''">
                <div class="msg-bubble" :class="isMe(msg.author_id) ? 'msg-mine' : 'msg-standard'">
                  <div class="msg-text">{{ msg.content }}</div>
                  <div class="msg-meta">{{ isMe(msg.author_id) ? 'Moi' : msg.author?.name }} · {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                </div>
              </div>
              <div v-if="!fb.messages?.length" class="text-xs text-muted text-center mb-12">Aucune réponse pour le moment.</div>
            </div>

            <!-- Formulaire de réponse -->
            <form v-if="!isClosed(fb) && canPostMessage(fb)" @submit.prevent="submitMessage(fb)" style="display:flex; gap:8px; margin-top:12px;">
              <input type="text" v-model="newMessages[fb.id]" class="input input-sm" placeholder="Répondre..." required style="flex:1;">
              <button type="submit" class="btn btn-primary btn-sm" :disabled="sendingMsg">Envoyer</button>
            </form>

            <div v-if="!isClosed(fb)" class="mt-16 pt-16 border-t" style="display:flex; justify-content:space-between; align-items:center;">
              <div>
                <button v-if="fb.type !== 'clarification'" class="join-btn" @click="joinFeedback(fb.id)" :class="{ joined: isJoined(fb) }">
                  {{ isJoined(fb) ? '✓ Soutenu' : '💪 Rejoindre l\'objection' }} ({{ fb.joins?.length || 0 }})
                </button>
              </div>
              <div v-if="isMe(fb.author_id)">
                 <button v-if="fb.type === 'clarification'" class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'acknowledged')">✓ La proposition est claire</button>
                 <button v-else-if="fb.type === 'objection'" class="btn btn-sm" style="background:var(--teal-50); color:var(--teal-700); border:1px solid var(--teal-200);" @click="closeFeedback(fb, 'withdrawn')">✓ Plus d'objection</button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulaire de Nouveau Ticket -->
  <div class="card mt-16" v-if="canCreateTicket">
    <div v-if="hasAlreadySubmitted" class="card-body text-center" style="background:var(--blue-50); border:1px solid var(--blue-200); border-radius:var(--radius-md);">
       <div class="text-sm font-semibold text-blue-800">Vous avez déjà un retour en cours.</div>
       <div class="text-xs text-blue-600 mt-4">Veuillez échanger avec le pilote au sein de votre fil de discussion existant.</div>
    </div>
    <div v-else>
      <div class="card-header"><span class="card-title">{{ isClarificationPhase ? 'Demander une clarification' : 'Soumettre un retour formel' }}</span></div>
      <div class="card-body">
      <form @submit.prevent="submitFeedback">
        <div class="form-group" v-if="isObjectionPhase">
          <label class="label">Type de retour</label>
          <select v-model="form.type" class="select" required>
            <option value="objection">Objection Bloquante</option>
            <option value="suggestion">Suggestion (Non bloquante)</option>
          </select>
        </div>
        <div class="form-group" v-else>
           <label class="label">Votre question ou demande</label>
           <!-- Lock implicitly to clarification -->
        </div>
        
        <div class="form-group">
          <textarea v-model="form.content" class="textarea" rows="3" placeholder="Description de votre demande / objection..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block" :disabled="loadingBtn">Ouvrir le ticket</button>
      </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const props = defineProps(['decision']);
const emits = defineEmits(['refresh']);

const authStore = useAuthStore();
const feedbacks = ref([]);
const newMessages = ref({});
const expandedId = ref(null);
const loadingBtn = ref(false);
const sendingMsg = ref(false);

const form = ref({ type: 'clarification', content: '' });

const isClarificationPhase = computed(() => props.decision.status === 'clarification');
const isObjectionPhase = computed(() => props.decision.status === 'objection');
const titleLabel = computed(() => isClarificationPhase.value ? 'Fil des Clarifications' : 'Fil des Objections & Suggestions');

const activeFeedbacks = computed(() => feedbacks.value.filter(fb => !isClosed(fb)));

const hasAlreadySubmitted = computed(() => {
    if(!authStore.user) return false;
    if(isClarificationPhase.value) {
        return activeFeedbacks.value.some(fb => fb.type === 'clarification' && fb.author_id === authStore.user.id);
    }
    if(isObjectionPhase.value) {
        return activeFeedbacks.value.some(fb => ['objection', 'suggestion'].includes(fb.type) && fb.author_id === authStore.user.id);
    }
    return false;
});

const isDecisionAuthor = computed(() => {
    if(!authStore.user) return false;
    return props.decision.participants?.find(p => p.role === 'author' && p.user_id === authStore.user.id);
});

const isDecisionAnimator = computed(() => {
    if(!authStore.user) return false;
    return props.decision.participants?.find(p => p.role === 'animator' && p.user_id === authStore.user.id);
});

const canCreateTicket = computed(() => {
    if(!authStore.user || !['clarification', 'objection'].includes(props.decision.status)) return false;
    
    // Le porteur et l'animateur ne créent jamais de ticket :
    // ils répondent uniquement aux retours des autres utilisateurs.
    if (isDecisionAuthor.value || isDecisionAnimator.value) return false;
    
    // Les autres membres (participants) peuvent créer
    return true;
});

watch(() => props.decision.status, () => {
    if(isClarificationPhase.value) form.value.type = 'clarification';
    if(isObjectionPhase.value) form.value.type = 'objection';
}, {immediate: true});

const toggleExpand = (id) => {
    expandedId.value = expandedId.value === id ? null : id;
};

const fetchFeedbacks = async () => {
    try {
        const { data } = await axios.get(`/api/v1/decisions/${props.decision.id}/feedback`, {
            params: { version_id: props.decision.current_version?.id }
        });
        feedbacks.value = data.feedbacks || [];
    } catch (e) { console.error(e); }
};

onMounted(() => fetchFeedbacks());

const submitFeedback = async () => {
    loadingBtn.value = true;
    try {
        await axios.post(`/api/v1/decisions/${props.decision.id}/feedback`, {
            type: isClarificationPhase.value ? 'clarification' : form.value.type,
            content: form.value.content
        });
        form.value.content = '';
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
    if (type === 'objection') return 'badge-red';
    if (type === 'clarification') return 'badge-amber';
    return 'badge-blue';
};

const isMe = (author_id) => {
    if (!authStore.user) return false;
    return String(author_id) === String(authStore.user.id);
};
const isClosed = (fb) => ['withdrawn', 'acknowledged', 'rejected', 'treated'].includes(fb.status);
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
</style>

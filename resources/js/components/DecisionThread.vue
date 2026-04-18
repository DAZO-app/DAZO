<template>
  <div class="card mb-16">
    <div class="card-header">
      <span class="card-title">Fil de discussion global ({{ phaseLabel }})</span>
    </div>
    <div class="card-body">
      
      <!-- Liste Ping Pong -->
      <div v-for="msg in messages" :key="msg.id" class="message" :class="isMe(msg) ? 'msg-me' : 'msg-other'">
        <!-- Avatar pour other -->
        <div v-if="!isMe(msg)" class="avatar av-purple" style="width:24px;height:24px;font-size:10px;margin-top:2px;">
           {{ initials(msg.author?.name) }}
        </div>
        
        <div class="message-bubble" :class="bubbleClass(msg)">
          <div style="white-space: pre-wrap">{{ msg.content }}</div>
          <div class="msg-meta">
            {{ isMe(msg) ? 'Moi' : msg.author?.name }} 
            <span v-if="msg.is_moderator_note" class="badge badge-amber badge-sm" style="font-size:9px;padding:0 4px">Modération</span> 
            · {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}
          </div>
        </div>

        <!-- Avatar pour me -->
        <div v-if="isMe(msg)" class="avatar av-blue" style="width:24px;height:24px;font-size:10px;margin-top:2px;">
           {{ initials(msg.author?.name) }}
        </div>
      </div>

      <div v-if="messages.length === 0" class="text-sm text-center text-muted mb-16">
        Aucun message dans ce tour.
      </div>

      <!-- Formulaire restreint -->
      <form v-if="canPost" @submit.prevent="sendMessage" style="display:flex;gap:8px;margin-top:16px;">
        <input type="text" v-model="newMsg" class="input" placeholder="Écrire un message..." required>
        <button type="submit" class="btn btn-primary">Envoyer</button>
      </form>
      <div v-else class="alert alert-warning mt-16" style="justify-content:center">
        Les messages sont fermés pour votre rôle ou pour ce statut ({{ decision.status }}).
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const props = defineProps(['decision']);
const authStore = useAuthStore();
const messages = ref([]);
const newMsg = ref('');

const fetchMessages = async () => {
    try {
        const { data } = await axios.get(`/api/v1/decisions/${props.decision.id}/thread`, {
            params: { tour: props.decision.status }
        });
        messages.value = data.messages || [];
    } catch(e) {}
};

onMounted(() => fetchMessages());

const phaseLabel = computed(() => {
    return props.decision.status === 'clarification' ? 'Clarification' : 'Réaction';
});

const canPost = computed(() => {
    const s = props.decision.status;
    return s === 'clarification' || s === 'reaction';
});

const isMe = (msg) => authStore.user && msg.author_id === authStore.user.id;

const initials = (name) => {
    if (!name) return '?';
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase();
};

const bubbleClass = (msg) => {
    if (msg.is_moderator_note) return 'msg-animateur';
    return isMe(msg) ? 'msg-porteur' : 'msg-standard';
};

const sendMessage = async () => {
    if (!newMsg.value) return;
    try {
        await axios.post(`/api/v1/decisions/${props.decision.id}/thread`, {
            content: newMsg.value,
        });
        newMsg.value = '';
        fetchMessages();
    } catch (e) {
        alert(e.response?.data?.message || 'Erreur');
    }
};
</script>

<style scoped>
/* PING-PONG */
.message { display: flex; gap: 8px; margin-bottom: 10px; }
.msg-me { justify-content: flex-end; }
.msg-other { justify-content: flex-start; }

.message-bubble {
  padding: 8px 12px;
  border-radius: var(--radius-md);
  font-size: 12px; line-height: 1.55;
  max-width: 85%;
}
.msg-porteur { background: var(--blue-50); border: 1px solid var(--blue-100); color: var(--gray-800); }
.msg-animateur { background: var(--amber-50); border: 1px solid var(--amber-100); color: var(--gray-800); }
.msg-standard { background: var(--gray-100); border: 1px solid var(--gray-200); color: var(--gray-800); }
.msg-meta { font-size: 10px; color: var(--gray-400); margin-top: 3px; }
.avatar { flex-shrink: 0; }
</style>

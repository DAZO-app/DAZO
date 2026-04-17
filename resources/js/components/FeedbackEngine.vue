<template>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Fil des Feedbacks (Objections & Suggestions)</span>
      <span class="badge badge-gray" style="margin-left:auto">{{ feedbacks.length }}</span>
    </div>
    
    <div class="card-body" style="padding:0">
      <div v-if="feedbacks.length === 0" class="text-sm text-center text-muted" style="padding:16px;">
        Aucun feedback encore posté sur cette version.
      </div>
      
      <div v-else class="p-16">
        <!-- Liste des feedbacks selon dazo-ui.html: OBJECTION CARD -->
        <div v-for="fb in feedbacks" :key="fb.id" class="feedback-card">
          <div class="feedback-card-header" :class="fb.type === 'objection' ? 'feedback-obj-header' : 'feedback-sug-header'">
            <span class="badge" :class="fb.type==='objection'?'badge-red':'badge-blue'">{{ fb.type.toUpperCase() }}</span>
            <span style="font-size:12px; font-weight:600">{{ fb.author?.first_name }} {{ fb.author?.last_name }}</span>
            <span class="badge badge-gray text-xs" style="margin-left:auto">{{ fb.status }}</span>
          </div>
          <div class="feedback-card-body" :class="fb.type === 'objection' ? 'feedback-obj' : 'feedback-sug'">
            {{ fb.content }}
          </div>
          <div class="feedback-card-footer">
            <button class="join-btn" @click="joinFeedback(fb.id)" :class="{ joined: isJoined(fb) }">
              {{ isJoined(fb) ? '✓ Soutenu' : '💪 Rejoindre l\'objection' }} ({{ fb.joins?.length || 0 }})
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulaire conditionnel: Accessible si on est en phase OBJECTION -->
  <div class="card mt-16" v-if="isActivePhase && showForm">
    <div class="card-header"><span class="card-title">Soumettre un retour Formel</span></div>
    <div class="card-body">
      <form @submit.prevent="submitFeedback">
        <div class="form-group">
          <label class="label">Type de retour</label>
          <select v-model="form.type" class="select" required>
            <option value="objection">Objection Bloquante</option>
            <option value="suggestion">Suggestion (Non bloquante)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="label">Détails</label>
          <textarea v-model="form.content" class="textarea" placeholder="Argumentez votre position..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block" :disabled="loadingBtn">Envoyer mon feedback</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const props = defineProps(['decision']);
const emits = defineEmits(['refresh']);

const authStore = useAuthStore();
const feedbacks = ref([]);
const loadingBtn = ref(false);

const form = ref({ type: 'objection', content: '' });

const isActivePhase = computed(() => props.decision.status === 'objection');
const showForm = ref(true); // TODO: hide if user already consented or submitted

const fetchFeedbacks = async () => {
    try {
        const { data } = await axios.get(`/api/v1/decisions/${props.decision.id}/feedback`, {
            params: { version_id: props.decision.current_version?.id }
        });
        feedbacks.value = data.feedbacks || [];
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    fetchFeedbacks();
});

const submitFeedback = async () => {
    loadingBtn.value = true;
    try {
        await axios.post(`/api/v1/decisions/${props.decision.id}/feedback`, form.value);
        form.value.content = '';
        await fetchFeedbacks();
        emits('refresh'); // trigger check if adoption or stats changed
    } catch (e) {
        alert(e.response?.data?.message || 'Erreur lors de la soumission.');
    } finally {
        loadingBtn.value = false;
    }
};

const joinFeedback = async (id) => {
    try {
        await axios.post(`/api/v1/feedback/${id}/join`);
        await fetchFeedbacks();
        emits('refresh');
    } catch (e) {
        alert(e.response?.data?.message || 'Vous ne pouvez plus rejoindre.');
    }
};

const isJoined = (fb) => {
    if (!authStore.user) return false;
    return fb.joins?.some(j => j.user_id === authStore.user.id);
};
</script>

<style scoped>
.p-16 { padding: 16px; }
.feedback-card {
  border-radius: var(--radius-md);
  border: 1px solid var(--gray-200);
  overflow: hidden;
  margin-bottom: 10px;
}
.feedback-card-header { padding: 10px 14px; display: flex; align-items: center; gap: 8px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); }
.feedback-card-body { padding: 12px 14px; font-size: 13px; color: var(--gray-700); line-height: 1.6; }
.feedback-card-footer { padding: 8px 14px; background: var(--gray-50); border-top: 1px solid var(--gray-100); display: flex; align-items: center; gap: 8px; }

.feedback-obj { border-left: 3px solid var(--red-600); }
.feedback-sug { border-left: 3px solid var(--blue-600); }

.join-btn {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 10px; border-radius: var(--radius-full);
  font-size: 11px; font-weight: 600;
  border: 1px solid var(--gray-300); background: white;
  color: var(--gray-600); cursor: pointer; transition: all 0.12s;
}
.join-btn:hover { background: var(--blue-50); border-color: var(--blue-200); color: var(--blue-700); }
.join-btn.joined { background: var(--blue-50); border-color: var(--blue-200); color: var(--blue-700); }
</style>

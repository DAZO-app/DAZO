<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-card modal-reminder">
      <div class="modal-header pc-header pc-header-indigo">
        <div class="pc-header-icon">
          <i class="fa-solid fa-paper-plane"></i>
        </div>
        <div class="pc-header-content">
          <div class="pc-header-title">Relancer les participants</div>
          <div class="pc-header-sub">{{ pendingUsers.length }} personne(s) en attente</div>
        </div>
        <button type="button" class="btn-close-premium" @click="$emit('close')" style="position: relative; z-index: 2;">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="modal-body p-0">
        <div v-if="loading" class="text-center py-40">
          <div class="spinner-premium mb-16"></div>
          <p class="text-muted text-sm font-medium">Analyse de la participation...</p>
        </div>

        <template v-else>
          <div v-if="pendingUsers.length > 0" class="p-24">
            <div class="phase-info-banner mb-20">
              <div class="phase-info-icon">
                <i class="fa-solid fa-clock-rotate-left"></i>
              </div>
              <div class="phase-info-text">
                Phase actuelle : <strong>{{ currentPhaseLabel }}</strong>
                <p class="text-xs opacity-80">Relancez ceux qui n'ont pas encore contribué.</p>
              </div>
            </div>
            
            <div class="user-list-premium mb-20">
              <div v-for="user in pendingUsers" :key="user.id" class="user-item-premium">
                <div class="user-avatar-premium">
                  {{ user.name.charAt(0) }}
                </div>
                <div class="user-details-premium">
                  <div class="user-name-premium">{{ user.name }}</div>
                  <div class="user-email-premium">{{ user.email }}</div>
                </div>
                <div class="user-status-tag">En attente</div>
              </div>
            </div>

            <div class="alert alert-info shadow-sm">
              <div class="alert-icon-container">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
              </div>
              <div class="alert-content">
                <strong>Notification intelligente</strong>
                <p class="text-xs mt-2 opacity-90">Un email personnalisé contenant un lien direct sera envoyé à chaque participant.</p>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-48 px-24">
            <div class="success-illustration mb-24">
              <div class="success-circle-outer">
                <div class="success-circle-inner">
                  <i class="fa-solid fa-check text-2xl"></i>
                </div>
              </div>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Participation complète !</h3>
            <p class="text-gray-500 mt-8 max-w-xs mx-auto">Tous les participants éligibles ont contribué à cette phase. Aucune relance n'est nécessaire.</p>
          </div>
        </template>
      </div>

      <div class="modal-footer">
        <button class="btn btn-ghost" @click="$emit('close')">Fermer</button>
        <button 
          v-if="pendingUsers.length > 0"
          class="btn btn-primary btn-premium-action" 
          :disabled="sending" 
          @click="sendReminders"
        >
          <i v-if="sending" class="fa-solid fa-circle-notch fa-spin mr-8"></i>
          <i v-else class="fa-solid fa-paper-plane mr-8"></i>
          {{ sending ? 'Envoi en cours...' : 'Envoyer les relances' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  decision: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close']);

const loading = ref(true);
const sending = ref(false);
const pendingUsers = ref([]);

const currentPhaseLabel = computed(() => {
  const status = props.decision.status;
  const map = {
    'clarification': 'Clarification',
    'reaction': 'Réaction',
    'objection': 'Objection'
  };
  return map[status] || status;
});

const fetchPending = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get(`/api/v1/decisions/${props.decision.id}/pending-participants`);
    pendingUsers.value = data.pending_users || [];
  } catch (err) {
    console.error("Erreur chargement participants en attente", err);
  } finally {
    loading.value = false;
  }
};

const sendReminders = async () => {
  sending.value = true;
  try {
    const { data } = await axios.post(`/api/v1/decisions/${props.decision.id}/remind`);
    // On pourrait utiliser un toast ici au lieu d'un alert
    alert(data.message);
    emit('close');
  } catch (err) {
    alert(err.response?.data?.message || "Erreur lors de l'envoi des relances.");
  } finally {
    sending.value = false;
  }
};

onMounted(fetchPending);
</script>

<style scoped>
.modal-reminder {
  max-width: 520px;
}

.btn-close-premium {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-close-premium:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.spinner-premium {
  width: 40px;
  height: 40px;
  border: 3px solid var(--blue-50);
  border-top-color: var(--blue-600);
  border-radius: 50%;
  margin: 0 auto;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.phase-info-banner {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 12px 16px;
  background: var(--indigo-50);
  border: 1px solid var(--indigo-100);
  border-radius: var(--radius-lg);
  color: var(--blue-900);
}

.phase-info-icon {
  width: 38px;
  height: 38px;
  background: white;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: var(--blue-600);
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.user-list-premium {
  max-height: 280px;
  overflow-y: auto;
  padding-right: 4px;
}

.user-item-premium {
  display: flex;
  align-items: center;
  padding: 10px 12px;
  border-radius: var(--radius-md);
  transition: background 0.2s;
  border: 1px solid transparent;
}

.user-item-premium:hover {
  background: var(--gray-50);
  border-color: var(--gray-100);
}

.user-avatar-premium {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, var(--blue-100), var(--blue-200));
  color: var(--blue-800);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  margin-right: 14px;
  box-shadow: inset 0 -2px 0 rgba(0,0,0,0.05);
}

.user-details-premium {
  flex: 1;
}

.user-name-premium {
  font-weight: 700;
  font-size: 14px;
  color: var(--gray-900);
}

.user-email-premium {
  font-size: 11px;
  color: var(--gray-500);
}

.user-status-tag {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--amber-600);
  background: var(--amber-50);
  padding: 2px 8px;
  border-radius: 4px;
}

.alert-icon-container {
  font-size: 20px;
  color: var(--blue-600);
  margin-top: 2px;
}

.success-circle-outer {
  width: 80px;
  height: 80px;
  background: var(--teal-50);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.success-circle-inner {
  width: 56px;
  height: 56px;
  background: var(--teal-500);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
}

.btn-premium-action {
  padding-left: 24px;
  padding-right: 24px;
  box-shadow: 0 4px 12px rgba(45, 53, 128, 0.2);
}
</style>

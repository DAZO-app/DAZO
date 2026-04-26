<template>
  <div v-if="visible" class="modal-overlay" @click.self="$emit('cancel')">
    <div class="modal-card notification-modal">
      <div class="modal-body text-center">
        <div class="notification-icon-wrapper">
          <div class="notification-icon-outer"></div>
          <div class="notification-icon-inner">
            <i class="fa-solid fa-paper-plane"></i>
          </div>
        </div>
        
        <h3 class="modal-title-custom">Publication & Notifications</h3>
        <p class="modal-description">
          Votre proposition est prête. Souhaitez-vous informer les membres du cercle par email pour lancer la phase de <strong>{{ phaseLabel }}</strong> ?
        </p>
        
        <div class="modal-actions-list">
          <button class="btn btn-primary btn-premium-lg" @click="$emit('confirm', true)">
            <span class="btn-icon-left"><i class="fa-solid fa-envelope"></i></span>
            Oui, notifier le cercle
          </button>
          
          <button class="btn btn-ghost btn-premium-ghost" @click="$emit('confirm', false)">
            Publier sans envoyer d'email
          </button>
        </div>
        
        <button class="modal-close-btn" @click="$emit('cancel')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  visible: Boolean,
  targetStatus: {
    type: String,
    default: 'clarification'
  }
});

const emit = defineEmits(['confirm', 'cancel']);

const phaseLabel = computed(() => {
  if (props.targetStatus === 'objection') return 'objection';
  return 'clarification';
});
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); 
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 16px;
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.notification-modal {
  max-width: 420px;
  width: 100%;
  border-radius: 24px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
  background: white;
  position: relative;
  overflow: hidden;
  padding: 40px 32px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.notification-icon-wrapper {
  position: relative;
  width: 90px;
  height: 90px;
  margin: 0 auto 24px;
}

.notification-icon-outer {
  position: absolute;
  inset: 0;
  background: var(--blue-50);
  border-radius: 32px;
  transform: rotate(15deg);
  opacity: 0.7;
}

.notification-icon-inner {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, var(--blue-600), var(--blue-700));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 28px;
  font-size: 32px;
  box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
}

.modal-title-custom {
  font-size: 20px;
  font-weight: 800;
  color: var(--gray-900);
  margin-bottom: 12px;
}

.modal-description {
  font-size: 14px;
  color: var(--gray-600);
  line-height: 1.6;
  margin-bottom: 32px;
}

.modal-actions-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.btn-premium-lg {
  padding: 16px;
  border-radius: 16px;
  font-size: 15px;
  font-weight: 700;
  justify-content: center;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-premium-lg:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.3);
}

.btn-premium-ghost {
  color: var(--gray-500);
  font-weight: 600;
  padding: 12px;
  font-size: 13px;
}

.btn-premium-ghost:hover {
  color: var(--gray-900);
  background: var(--gray-50);
}

.btn-icon-left {
  margin-right: 12px;
  font-size: 18px;
  opacity: 0.9;
}

.modal-close-btn {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  color: var(--gray-400);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.modal-close-btn:hover {
  background: var(--gray-100);
  color: var(--gray-900);
}
</style>

<template>
  <div v-if="visible" class="modal-overlay" @click.self="$emit('cancel')">
    <div class="modal-card notification-modal">
      <div class="modal-header">
        <span class="modal-title">Notification des participants</span>
        <button class="btn btn-ghost btn-icon" @click="$emit('cancel')"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body text-center py-24">
        <div class="notification-icon-large mb-16">
          <i class="fa-solid fa-paper-plane"></i>
        </div>
        <h3 class="text-lg font-bold mb-8">Souhaitez-vous notifier les participants ?</h3>
        <p class="text-sm text-muted mb-24 px-24">
          Un email sera envoyé aux membres du cercle pour les informer que la proposition est maintenant en phase de <strong>clarification</strong>.
        </p>
        
        <div class="flex flex-col gap-12">
          <button class="btn btn-primary btn-lg w-full" @click="$emit('confirm', true)">
            <i class="fa-solid fa-envelope mr-8"></i> Oui, notifier par email
          </button>
          <button class="btn btn-ghost w-full" @click="$emit('confirm', false)">
            Non, cycle normal (sans email)
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  visible: Boolean
});
defineEmits(['confirm', 'cancel']);
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.5); 
  display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 16px;
  backdrop-filter: blur(2px);
}
.notification-modal {
  max-width: 400px !important;
  border-radius: 20px !important;
  box-shadow: 0 20px 50px rgba(0,0,0,0.3) !important;
}
.notification-icon-large {
  font-size: 48px;
  background: var(--blue-50);
  color: var(--blue-600);
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  margin: 0 auto;
}
.btn-lg {
  padding: 14px 24px;
  font-size: 15px;
  font-weight: 700;
}
.mr-8 { margin-right: 8px; }
.px-24 { padding-left: 24px; padding-right: 24px; }
.py-24 { padding-top: 24px; padding-bottom: 24px; }
.mb-24 { margin-bottom: 24px; }
</style>

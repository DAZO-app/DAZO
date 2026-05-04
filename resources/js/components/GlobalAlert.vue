<template>
  <div v-if="isVisible" class="modal-overlay" style="z-index: 2147483647;" @click.self="close(false)">
    <div class="modal-container rounded-lg shadow-xl overflow-hidden" style="max-width: 400px; width: 100%; background: white; animation: modalIn 0.2s ease;">
      <div class="modal-header flex items-center justify-between p-16 border-b border-gray-200">
        <h3 class="modal-title m-0 text-base font-bold text-gray-900">
          <i :class="isConfirm ? 'fa-solid fa-circle-question text-blue-500 mr-8' : 'fa-solid fa-circle-info text-blue-500 mr-8'"></i>
          {{ title }}
        </h3>
        <button class="modal-close bg-transparent border-none text-gray-400 hover:text-gray-600 cursor-pointer text-lg p-4" @click="close(false)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body p-20 text-gray-700 text-sm leading-relaxed" style="white-space: pre-wrap;">
        {{ message }}
      </div>
      <div class="modal-footer flex justify-end gap-12 p-16 bg-gray-50 border-t border-gray-200">
        <button v-if="isConfirm" class="btn btn-gray" @click="close(false)">Annuler</button>
        <button class="btn btn-primary shadow-blue" @click="close(true)">{{ isConfirm ? 'Confirmer' : 'OK' }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isVisible = ref(false);
const message = ref('');
const title = ref('Information');
const isConfirm = ref(false);
let resolvePromise = null;

const showAlert = (msg, modalTitle = 'Information') => {
  // Enforce string conversion for safety against objects being passed to alert()
  message.value = typeof msg === 'object' ? JSON.stringify(msg) : String(msg);
  title.value = modalTitle;
  isConfirm.value = false;
  isVisible.value = true;
  return new Promise((resolve) => {
    resolvePromise = resolve;
  });
};

const showConfirm = (msg, modalTitle = 'Confirmation') => {
  message.value = typeof msg === 'object' ? JSON.stringify(msg) : String(msg);
  title.value = modalTitle;
  isConfirm.value = true;
  isVisible.value = true;
  return new Promise((resolve) => {
    resolvePromise = resolve;
  });
};

const close = (result) => {
  isVisible.value = false;
  if (resolvePromise) {
    resolvePromise(result);
    resolvePromise = null;
  }
};

let originalAlert = null;

onMounted(() => {
  originalAlert = window.alert;

  window.alert = (msg) => {
    return showAlert(msg);
  };
  
  window.dazoAlert = showAlert;
  window.dazoConfirm = showConfirm;
});

onUnmounted(() => {
  if (originalAlert) window.alert = originalAlert;
});
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6); /* Slate-900 / 60% */
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
}

@keyframes modalIn {
  from { opacity: 0; transform: translateY(-10px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

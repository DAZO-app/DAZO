<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-container revision-path-modal">
      <div class="modal-header">
        <div class="modal-title-group">
          <div class="modal-icon-bg">
            <i class="fa-solid fa-code-branch"></i>
          </div>
          <div>
            <h3 class="modal-title">Cycle de la nouvelle version</h3>
            <p class="modal-subtitle">Comment souhaitez-vous poursuivre le processus ?</p>
          </div>
        </div>
        <button class="modal-close" @click="$emit('close')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="modal-body p-24">
        <div class="path-options">
          <!-- Option Clarification (Standard) -->
          <button 
            class="path-option-card" 
            :class="{ active: selected === 'clarification' }"
            @click="selected = 'clarification'"
          >
            <div class="path-icon standard">
              <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div class="path-content">
              <div class="path-title">Cycle complet (Standard)</div>
              <p class="path-desc">
                La décision repasse par toutes les étapes : Clarification, puis Réaction, puis Objection.
                <strong>Recommandé</strong> si la révision modifie profondément la proposition.
              </p>
            </div>
            <div class="path-radio">
              <div class="radio-circle"></div>
            </div>
          </button>

          <!-- Option Objection (Accélérée) -->
          <button 
            class="path-option-card" 
            :class="{ active: selected === 'objection' }"
            @click="selected = 'objection'"
          >
            <div class="path-icon accelerated">
              <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="path-content">
              <div class="path-title">Passage direct en Objection</div>
              <p class="path-desc">
                Saute les phases de clarification et de réaction pour cette version.
                Utile si la révision ne fait qu'ajuster des points déjà discutés et validés.
              </p>
            </div>
            <div class="path-radio">
              <div class="radio-circle"></div>
            </div>
          </button>
        </div>

        <div v-if="selected === 'objection'" class="alert-info mt-24">
          <i class="fa-solid fa-circle-info mr-8"></i>
          <span>Les participants seront invités directement à valider ou objecter sur cette nouvelle version.</span>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-gray" @click="$emit('close')">Annuler</button>
        <button class="btn btn-primary" @click="confirm" :disabled="!selected">
          Suivant
          <i class="fa-solid fa-arrow-right ml-8"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const selected = ref('clarification');
const emit = defineEmits(['close', 'confirm']);

const confirm = () => {
  if (selected.value) {
    emit('confirm', selected.value);
  }
};
</script>

<style scoped>
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

.modal-container {
  width: 100%;
  max-width: 580px;
  background: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  animation: modalIn 0.2s ease;
}

@keyframes modalIn {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--gray-200);
}

.modal-title-group {
  display: flex;
  align-items: center;
}

.modal-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--gray-900);
  margin: 0;
}

.modal-subtitle {
  font-size: 12px;
  color: var(--gray-500);
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  font-size: 18px;
  color: var(--gray-400);
  cursor: pointer;
  transition: color 0.2s;
}

.modal-close:hover {
  color: var(--gray-600);
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px 20px;
  background: var(--gray-50);
  border-top: 1px solid var(--gray-200);
}

.path-options {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.path-option-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  background: white;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-lg);
  text-align: left;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.path-option-card:hover {
  border-color: var(--blue-300);
  background: var(--gray-50);
}

.path-option-card.active {
  border-color: var(--blue-600);
  background: var(--blue-50);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
}

.path-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.path-icon.standard {
  background: var(--amber-100);
  color: var(--amber-700);
}

.path-icon.accelerated {
  background: var(--blue-100);
  color: var(--blue-700);
}

.path-content {
  flex: 1;
}

.path-title {
  font-weight: 700;
  font-size: 16px;
  color: var(--gray-900);
  margin-bottom: 4px;
}

.path-desc {
  font-size: 13px;
  color: var(--gray-600);
  line-height: 1.5;
  margin: 0;
}

.path-radio {
  padding-top: 4px;
}

.radio-circle {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid var(--gray-300);
  display: flex;
  align-items: center;
  justify-content: center;
}

.path-option-card.active .radio-circle {
  border-color: var(--blue-600);
}

.path-option-card.active .radio-circle::after {
  content: '';
  width: 10px;
  height: 10px;
  background: var(--blue-600);
  border-radius: 50%;
}

.alert-info {
  background: var(--blue-50);
  border: 1px solid var(--blue-100);
  color: var(--blue-800);
  padding: 12px 16px;
  border-radius: var(--radius-md);
  font-size: 13px;
  display: flex;
  align-items: center;
}

.modal-icon-bg {
  width: 40px;
  height: 40px;
  background: var(--blue-50);
  color: var(--blue-600);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  margin-right: 12px;
}
</style>

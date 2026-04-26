<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-container notification-level-modal">
      <div class="modal-header">
        <div class="modal-title-group">
          <div class="modal-icon-bg">
            <i class="fa-solid fa-bell"></i>
          </div>
          <div>
            <h3 class="modal-title">Préférences de notification</h3>
            <p class="modal-subtitle">Personnalisez les emails que vous recevez pour cette décision.</p>
          </div>
        </div>
        <button class="modal-close" @click="$emit('close')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="modal-body p-24">
        <div class="level-options">
          <button 
            v-for="level in levels" 
            :key="level.value"
            class="level-option-card" 
            :class="{ active: currentLevel === level.value }"
            @click="select(level.value)"
          >
            <div class="level-icon" :class="level.value">
              <i :class="level.icon"></i>
            </div>
            <div class="level-content">
              <div class="level-title">{{ level.label }}</div>
              <p class="level-desc">{{ level.desc }}</p>
            </div>
            <div class="level-check">
              <i v-if="currentLevel === level.value" class="fa-solid fa-circle-check"></i>
              <div v-else class="check-circle"></div>
            </div>
          </button>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-gray" @click="$emit('close')">Fermer</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  currentLevel: {
    type: String,
    default: 'all'
  }
});

const emit = defineEmits(['close', 'select']);

const levels = [
  { 
    value: 'all', 
    label: 'Toutes les mises à jour', 
    desc: 'Recevoir un mail pour chaque modification, commentaire ou changement de phase.', 
    icon: 'fa-solid fa-bell' 
  },
  { 
    value: 'relevant', 
    label: 'Mises à jour me concernant', 
    desc: 'Seulement pour vos participations directes ou si vous êtes porteur/animateur.', 
    icon: 'fa-solid fa-bell-concierge' 
  },
  { 
    value: 'phase_change', 
    label: 'Changements de phase uniquement', 
    desc: 'Recevoir un mail uniquement quand la décision passe à une étape suivante.', 
    icon: 'fa-solid fa-bolt' 
  },
  { 
    value: 'none', 
    label: 'Aucune notification', 
    desc: 'Désactiver totalement les envois d\'emails pour cette décision.', 
    icon: 'fa-regular fa-bell-slash' 
  },
];

const select = (value) => {
  emit('select', value);
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 16px;
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-container {
  background: white;
  border-radius: 24px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
  overflow: hidden;
  position: relative;
  animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalIn {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 28px;
  border-bottom: 1px solid var(--gray-100);
}

.modal-title-group {
  display: flex;
  align-items: center;
  gap: 16px;
}

.modal-icon-bg {
  width: 44px;
  height: 44px;
  background: var(--blue-50);
  color: var(--blue-600);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.modal-title {
  font-size: 18px;
  font-weight: 800;
  color: var(--gray-900);
  margin: 0;
  line-height: 1.2;
}

.modal-subtitle {
  font-size: 13px;
  color: var(--gray-500);
  margin: 4px 0 0;
  line-height: 1.4;
}

.modal-close {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  color: var(--gray-400);
  cursor: pointer;
  transition: all 0.2s;
}

.modal-close:hover {
  background: var(--gray-100);
  color: var(--gray-900);
}

.modal-body {
  padding: 24px 28px;
}

.modal-footer {
  padding: 16px 28px;
  background: var(--gray-50);
  display: flex;
  justify-content: flex-end;
  border-top: 1px solid var(--gray-100);
}

.level-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.level-option-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px;
  background: white;
  border: 2px solid var(--gray-100);
  border-radius: 16px;
  text-align: left;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.level-option-card:hover {
  border-color: var(--blue-200);
  background: var(--gray-50);
  transform: translateY(-1px);
}

.level-option-card.active {
  border-color: var(--blue-600);
  background: var(--blue-50);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
}

.level-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.level-icon.all { background: white; color: var(--blue-600); border: 1px solid var(--blue-100); }
.level-icon.relevant { background: white; color: var(--amber-600); border: 1px solid var(--amber-100); }
.level-icon.phase_change { background: white; color: #7048e8; border: 1px solid #dbe4ff; }
.level-icon.none { background: white; color: var(--gray-500); border: 1px solid var(--gray-200); }

.level-content {
  flex: 1;
}

.level-title {
  font-weight: 700;
  font-size: 15px;
  color: var(--gray-900);
  margin-bottom: 2px;
}

.level-desc {
  font-size: 12px;
  color: var(--gray-500);
  line-height: 1.5;
  margin: 0;
}

.level-check {
  font-size: 22px;
  color: var(--blue-600);
}

.check-circle {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 2px solid var(--gray-200);
}
</style>

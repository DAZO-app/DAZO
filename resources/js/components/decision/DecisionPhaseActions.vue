<template>
  <div class="steps-bar">
    <div class="steps">
      <div class="step" :class="getStepClass('clarification')">
        <div class="step-num">1</div>
        <span class="step-label hidden-mobile-text">Clarification</span>
      </div>
      <div class="step-sep"></div>
      <div class="step" :class="getStepClass('reaction')">
        <div class="step-num">2</div>
        <span class="step-label hidden-mobile-text">Réaction</span>
      </div>
      <div class="step-sep"></div>
      <div class="step" :class="getStepClass('objection')">
        <div class="step-num">3</div>
        <span class="step-label hidden-mobile-text">Objection</span>
      </div>
      <div class="step-sep"></div>
      <div class="step" :class="getStepClass('adopted')">
        <div class="step-num"><i class="fa-solid fa-check"></i></div>
        <span class="step-label hidden-mobile-text">Adopté</span>
      </div>
    </div>

    <div class="status-actions" v-if="isAuthorOrAnimator">
      <button
        v-for="action in stepActions"
        :key="action.key"
        class="btn btn-sm"
        :class="action.primary ? 'btn-primary' : 'btn-secondary'"
        :disabled="disabled"
        @click="action.run"
      >
        {{ action.label }}
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  currentStatus: { type: String, default: '' },
  isAuthorOrAnimator: { type: Boolean, default: false },
  stepActions: { type: Array, default: () => [] },
  disabled: { type: Boolean, default: false }
});

const getStepClass = (step) => {
  const steps = ['draft', 'clarification', 'reaction', 'objection', 'revision', 'adopted', 'abandoned'];
  const currentIndex = steps.indexOf(props.currentStatus);
  const stepIndex = steps.indexOf(step);

  if (props.currentStatus === 'abandoned') {
    return 'step-inactive';
  }

  if (props.currentStatus === 'adopted' || props.currentStatus === 'adopted_override') {
    return 'step-done';
  }

  if (step === 'adopted') {
    return 'step-pending';
  }

  if (currentIndex === stepIndex) {
    if (step === 'objection' || step === 'revision') return 'step-active-warning';
    return 'step-active';
  } else if (currentIndex > stepIndex) {
    return 'step-done';
  } else {
    return 'step-pending';
  }
};
</script>

<style scoped>
.steps-bar {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-lg);
  padding: 12px 20px;
  margin-bottom: 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.steps {
  display: flex;
  align-items: center;
  gap: 0;
  flex-wrap: wrap;
}

.step {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 500;
}

.step-num {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 600;
  flex-shrink: 0;
}

.step-done .step-num { background: var(--teal-600); color: white; }
.step-active .step-num { background: var(--blue-800); color: white; }
.step-active-warning .step-num { background: var(--amber-500); color: white; }
.step-pending .step-num { background: var(--gray-200); color: var(--gray-500); }
.step-inactive .step-num { background: var(--red-100); color: var(--red-400); }

.step-done .step-label { color: var(--teal-600); }
.step-active .step-label { color: var(--blue-800); font-weight: 600; }
.step-active-warning .step-label { color: var(--amber-600); font-weight: 600; }
.step-pending .step-label { color: var(--gray-400); }
.step-inactive .step-label { color: var(--red-400); }

.step-sep {
  width: 24px;
  height: 1px;
  background: var(--gray-200);
  margin: 0 4px;
  flex-shrink: 0;
}

.status-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: flex-end;
}

@media (max-width: 768px) {
  .steps-bar {
    flex-direction: column;
    align-items: stretch;
  }
  .status-actions {
    justify-content: stretch;
  }
  .status-actions .btn {
    flex: 1;
  }
  .hidden-mobile-text {
    display: none;
  }
}
</style>

<template>
  <main class="main" v-if="loading"><div class="p-24 text-center">Chargement...</div></main>
  
  <main class="main" v-else-if="decision">
    <div class="page-header">
      <div>
        <div class="page-title">{{ decision.title }}</div>
        <div class="page-subtitle">
          Cercle: {{ decision.circle?.name }} · Version: {{ decision.current_version?.version_number }}
        </div>
      </div>
      <div class="page-actions">
        <span class="badge" :class="statusClass">{{ decision.status.toUpperCase() }}</span>
      </div>
    </div>

    <!-- Timeline des étapes via les statuts -->
    <div class="steps-bar bg-white px-20 py-10 border-b">
      <div class="steps">
        <div class="step" :class="getStepClass('clarification')">
          <div class="step-num">1</div><span class="step-label hidden-mobile-text">Clarification</span>
        </div>
        <div class="step-sep"></div>
        <div class="step" :class="getStepClass('reaction')">
          <div class="step-num">2</div><span class="step-label hidden-mobile-text">Réaction</span>
        </div>
        <div class="step-sep"></div>
        <div class="step" :class="getStepClass('objection')">
          <div class="step-num">3</div><span class="step-label hidden-mobile-text">Objection</span>
        </div>
        <div class="step-sep"></div>
        <div class="step" :class="getStepClass('adopted')">
          <div class="step-num">✓</div><span class="step-label hidden-mobile-text">Adopté</span>
        </div>
      </div>
    </div>

    <div class="page-body">
      <div class="grid-layout">
        
        <!-- Corps de texte -->
        <div class="col-main">
          <div class="card mb-16">
            <div class="card-header">
              <span class="card-title">Contenu de la décision</span>
            </div>
            <div class="card-body" style="white-space: pre-wrap; font-size: 14px; line-height: 1.6;">{{ decision.current_version?.content }}</div>
          </div>
        </div>

        <!-- Sidebar contextuelle: Feedbacks / Actions -->
        <div class="col-side">
          <div class="card mb-16" v-if="decision.status === 'objection'">
            <div class="card-header">
              <span class="status-dot dot-amber"></span>
              <span class="card-title">Phase d'objection active</span>
            </div>
            <div class="card-body">
              <p class="text-xs text-muted mb-12">Vous devez exprimer votre consentement ou soumettre une objection.</p>
              
              <div class="vote-row mb-16">
                <button class="vote-btn vote-ok" @click="submitConsent('no_objection')">
                  <span class="vote-icon">👍</span> Sans objection
                </button>
                <button class="vote-btn vote-abs" @click="submitConsent('abstention')">
                  <span class="vote-icon">👀</span> Abstention
                </button>
              </div>
            </div>
          </div>
          
          <!-- Module des Feedbacks -->
          <FeedbackEngine v-if="decision.status === 'objection' || decision.status === 'revision' || decision.status === 'adopted'" :decision="decision" @refresh="refreshDecision" />

          <!-- Module du Fil de discussion (Clarification/Reaction) -->
          <DecisionThread v-if="decision.status === 'clarification' || decision.status === 'reaction'" :decision="decision" />

        </div>

      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useDecisionStore } from '../stores/decision';
import axios from 'axios';
import FeedbackEngine from '../components/FeedbackEngine.vue';
import DecisionThread from '../components/DecisionThread.vue';

const route = useRoute();
const store = useDecisionStore();

const loading = computed(() => store.loading);
const decision = computed(() => store.currentDecision);
const openFeedbackModal = ref(false);

onMounted(() => {
    store.fetchDecisionById(route.params.id);
});

const submitConsent = async (type) => {
    try {
        await axios.post(`/api/v1/decisions/${decision.value.id}/versions/${decision.value.current_version.id}/consent`, { type });
        alert('Consentement enregistré !');
        refreshDecision();
    } catch (e) {
        alert(e.response?.data?.message || 'Erreur');
    }
};

const refreshDecision = () => {
    store.fetchDecisionById(decision.value.id);
};

const statusClass = computed(() => {
    const s = decision.value?.status;
    return s === 'adopted' ? 'badge-teal' : (s === 'objection' ? 'badge-amber' : 'badge-blue');
});

// Mock simpliste pour progress bar
const getStepClass = (step) => {
    const s = decision.value?.status;
    const order = ['draft', 'clarification', 'reaction', 'objection', 'adopted'];
    const idxStep = order.indexOf(step);
    const idxCurr = order.indexOf(s);
    if(s === step) return 'step-active';
    if(idxCurr > idxStep) return 'step-done';
    return 'step-pending';
};
</script>

<style scoped>
.p-24 { padding: 24px; }
.px-20 { padding: 0 20px; }
.py-10 { padding: 10px 0; }
.border-b { border-bottom: 1px solid var(--gray-200); }
.bg-white { background: white; }

.grid-layout { display: flex; flex-direction: column; gap: 16px; }
@media (min-width: 900px) {
  .grid-layout { flex-direction: row; }
  .col-main { flex: 2; }
  .col-side { flex: 1; min-width: 300px; }
}

/* Steps (from dazo-ui.html) */
.steps { display: flex; align-items: center; gap: 0; }
.step { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 500; }
.step-num { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; flex-shrink: 0; }
.step-done .step-num { background: var(--teal-600); color: white; }
.step-active .step-num { background: var(--blue-800); color: white; }
.step-pending .step-num { background: var(--gray-200); color: var(--gray-500); }
.step-done .step-label { color: var(--teal-600); }
.step-active .step-label { color: var(--blue-800); font-weight: 600; }
.step-pending .step-label { color: var(--gray-400); }
.step-sep { width: 24px; height: 1px; background: var(--gray-200); margin: 0 4px; flex-shrink: 0; }

@media (max-width: 500px) {
  .hidden-mobile-text { display: none; }
}

/* Votes */
.vote-row { display: flex; gap: 8px; }
.vote-btn { flex: 1; padding: 10px; border-radius: var(--radius-md); font-family: var(--font-sans); font-size: 12px; font-weight: 600; border: 2px solid transparent; cursor: pointer; transition: all 0.12s; display: flex; flex-direction: column; align-items: center; gap: 4px; }
.vote-ok { background: var(--teal-50); color: var(--teal-600); border-color: var(--teal-100); }
.vote-ok:hover, .vote-ok.selected { background: var(--teal-600); color: white; border-color: var(--teal-600); }
.vote-abs { background: var(--gray-100); color: var(--gray-600); border-color: var(--gray-200); }
.vote-abs:hover, .vote-abs.selected { background: var(--gray-500); color: white; border-color: var(--gray-500); }
.vote-icon { font-size: 18px; line-height: 1; }
</style>

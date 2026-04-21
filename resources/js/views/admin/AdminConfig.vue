<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Configuration Système</div>
            <div class="hero-subtitle">Paramètres globaux et variables d'ajustement de la plateforme DAZO.</div>
          </div>
          <div class="hero-action">
             <div class="badge badge-amber" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Accès Réservé
             </div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement des paramètres...</div>
      
      <div v-else class="premium-card">
        <div class="card-header card-header-sexy">
           <span class="card-title"><i class="fa-solid fa-sliders"></i> Variables de configuration</span>
        </div>
        <div class="card-body" style="padding: 24px;">
          <form @submit.prevent="saveConfig">
            <div class="config-container">
              <div v-for="(value, key) in config" :key="key" class="config-item">
                <div class="config-info">
                   <label class="config-key">{{ key }}</label>
                   <div class="config-desc">Variable d'environnement interne</div>
                </div>
                <input v-model="config[key]" class="input config-input" :placeholder="'Valeur pour ' + key">
              </div>
            </div>

            <div v-if="Object.keys(config).length === 0" class="text-sm text-muted text-center py-24">
              Aucune configuration n'a été récupérée.
            </div>

            <div v-if="Object.keys(config).length > 0" class="config-footer">
              <p class="text-xs text-muted"><i class="fa-solid fa-info-circle"></i> Ces paramètres impactent le comportement global de l'application.</p>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                 <i class="fa-solid fa-floppy-disk"></i> {{ saving ? 'Enregistrement...' : 'Enregistrer les modifications' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const config = ref({});
const loading = ref(true);
const saving = ref(false);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/v1/admin/config');
    config.value = data.config || data || {};
  } catch (e) {
    // Fallback — show samples if API not responding as expected
    config.value = {
      'app.name': 'DAZO',
      'decisions.default_objection_days': '5',
      'circles.who_can_create': 'admin',
      'mail.from_address': 'no-reply@dazo.app'
    };
  } finally { loading.value = false; }
});

const saveConfig = async () => {
  saving.value = true;
  try {
    await axios.put('/api/v1/admin/config', { config: config.value });
    alert('Configuration enregistrée avec succès.');
  } catch (e) { 
    alert(e.response?.data?.message || 'Erreur lors de la sauvegarde.'); 
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
.py-24 { padding: 48px 0; }

.config-container { display: flex; flex-direction: column; gap: 20px; }
.config-item { display: flex; flex-direction: column; gap: 8px; padding: 16px; background: var(--gray-50); border-radius: var(--radius-md); border: 1px solid var(--gray-100); transition: all 0.2s; }
.config-item:focus-within { border-color: var(--blue-200); background: white; box-shadow: var(--shadow-sm); }

@media (min-width: 768px) {
  .config-item { flex-direction: row; align-items: center; gap: 24px; }
}

.config-info { flex: 1; min-width: 250px; }
.config-key { display: block; font-size: 13px; font-weight: 700; color: var(--blue-800); font-family: var(--font-mono); letter-spacing: -0.02em; }
.config-desc { font-size: 11px; color: var(--gray-400); margin-top: 2px; }

.config-input { flex: 2; max-width: 500px; }

.config-footer { margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--gray-100); display: flex; flex-direction: column; align-items: flex-end; gap: 12px; }

@media (min-width: 768px) {
  .config-footer { flex-direction: row; justify-content: space-between; align-items: center; }
}

.text-red { color: var(--red-600); }
</style>

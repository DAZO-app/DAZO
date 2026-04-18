<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Configuration</div>
        <div class="page-subtitle">Paramètres globaux de l'instance</div>
      </div>
    </div>
    <div class="page-body">
      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else class="card">
        <div class="card-body">
          <form @submit.prevent="saveConfig">
            <div v-for="(value, key) in config" :key="key" class="config-row">
              <label class="label config-key">{{ key }}</label>
              <input v-model="config[key]" class="input config-input">
            </div>
            <div v-if="Object.keys(config).length === 0" class="text-sm text-muted text-center py-24">
              Aucune configuration. Ajoutez des paramètres via l'API admin.
            </div>
            <div v-if="Object.keys(config).length > 0" style="margin-top:16px; text-align:right;">
              <button type="submit" class="btn btn-primary">Enregistrer</button>
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

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/v1/admin/config');
    config.value = data.config || data || {};
  } catch (e) {
    // Fallback — show defaults
    config.value = {
      'app.name': 'DAZO',
      'decisions.default_objection_days': '5',
      'circles.who_can_create': 'admin',
    };
  } finally { loading.value = false; }
});

const saveConfig = async () => {
  try {
    await axios.put('/api/v1/admin/config', { config: config.value });
    alert('Configuration enregistrée.');
  } catch (e) { alert(e.response?.data?.message || 'Erreur'); }
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.config-row { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
.config-key { min-width: 220px; font-size: 12px; font-weight: 600; color: var(--gray-700); font-family: var(--font-mono); }
.config-input { flex: 1; }
</style>

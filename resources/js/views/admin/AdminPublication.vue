<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">API</div>
            <div class="hero-subtitle">Gérez les données exposées publiquement et configurez l'accès API pour les intégrations tierces.</div>
          </div>
          <div class="hero-action">
             <button class="btn btn-primary btn-lg shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-cloud-arrow-up mr-8"></i> {{ saving ? 'Enregistrement...' : 'Enregistrer les paramètres' }}
             </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-48">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement des paramètres...</p>
      </div>

      <div v-else class="config-layout mt-32">
        <!-- NAVIGATION GAUCHE -->
        <aside class="config-nav">
          <div class="nav-group-title">API</div>
          <button v-for="s in sections" :key="s.id"
                  @click="activeSection = s.id"
                  class="nav-item" :class="{ active: activeSection === s.id }">
            <i :class="s.icon"></i>
            <span>{{ s.label }}</span>
            <i v-if="activeSection === s.id" class="fa-solid fa-chevron-right ml-auto text-xs opacity-50"></i>
          </button>
        </aside>

        <!-- CONTENU CENTRAL -->
        <div class="config-content">

          <!-- SECTION CLÉ API -->
          <div v-if="activeSection === 'api_key'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-key"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Clé d'API</div>
                <div class="pc-header-sub">Clé d'authentification requise pour accéder aux routes publiques</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="alert alert-info mb-24">
                <i class="fa-solid fa-circle-info"></i>
                <p>Pour utiliser l'API publique (<code>/api/v1/public/decisions</code>), le client doit envoyer cette clé via l'en-tête HTTP <code>X-API-Key</code> ou le paramètre <code>?api_key=</code>.</p>
              </div>

              <div class="form-group">
                <label class="config-label">Clé secrète actuelle</label>
                <div style="display: flex; gap: 16px; align-items: center;">
                  <input type="text" v-model="config.public_api_key" class="input input-lg" readonly
                    style="font-family: monospace; letter-spacing: 2px; background: var(--gray-50);"
                    :placeholder="config.public_api_key ? '' : 'Aucune clé active'">
                  <button class="btn btn-outline" @click="generateApiKey">
                    <i class="fa-solid fa-rotate-right mr-8"></i> {{ config.public_api_key ? 'Regénérer' : 'Générer une clé' }}
                  </button>
                  <button v-if="config.public_api_key" class="btn btn-ghost text-red-500" @click="revokeApiKey">
                    <i class="fa-solid fa-trash-can mr-8"></i> Révoquer
                  </button>
                </div>
                <p class="help-text">La regénération révoque immédiatement l'ancienne clé pour tous les clients.</p>
                <div class="config-key">variable : <code>public_api_key</code></div>
              </div>
            </div>
          </div>

          <!-- SECTION FILTRES API -->
          <div v-if="activeSection === 'api_filters'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-sliders"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Filtres de l'API</div>
                <div class="pc-header-sub">Filtres dynamiques autorisés dans les requêtes de l'API</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <p class="help-text mb-24">Activez les paramètres de filtrage que les clients externes pourront utiliser sur l'endpoint public.</p>
              <div class="checkbox-list inline-list">
                <label v-for="filter in availableFilters" :key="filter.value" class="checkbox-item">
                  <input type="checkbox" :value="filter.value" v-model="config.public_filters">
                  <span>{{ filter.label }} (<code>?{{ filter.value }}=...</code>)</span>
                </label>
              </div>
              <div class="config-key mt-16">variable : <code>public_filters</code></div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Filtres
              </button>
            </div>
          </div>

          <!-- SECTION SNIPPET GENERATOR -->
          <div v-if="activeSection === 'snippet'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-code"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Snippet Generator</div>
                <div class="pc-header-sub">Générez des extraits de code pour intégrer les décisions sur votre site</div>
              </div>
            </div>
            <div class="pc-body p-48 text-center">
              <i class="fa-solid fa-code fa-3x mb-16" style="color: var(--amber-600); opacity: 0.25;"></i>
              <h3 class="text-lg font-bold text-gray-800 mb-8">Générateur de widgets</h3>
              <p class="text-muted italic" style="max-width: 440px; margin: 0 auto;">
                Prochainement : générateur de widgets et snippets HTML/JS pour l'affichage dynamique des décisions sur vos sites tiers.
              </p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const config = ref({
  public_api_key: '',
  public_circles: [],
  public_categories: [],
  public_statuses: [],
  public_filters: []
});

const circles = ref([]);
const categories = ref([]);
const loading = ref(true);
const saving = ref(false);
const activeSection = ref('api_key');

const sections = [
  { id: 'api_key', label: 'Clé API', icon: 'fa-solid fa-key' },
  { id: 'api_filters', label: 'Filtres API', icon: 'fa-solid fa-sliders' },
  { id: 'snippet', label: 'Snippet Generator', icon: 'fa-solid fa-code' },
];

const availableStatuses = [
  { value: 'draft', label: 'Brouillon' },
  { value: 'clarification', label: 'Clarification' },
  { value: 'reaction', label: 'Réaction' },
  { value: 'objection', label: 'Objection' },
  { value: 'revision', label: 'En Révision' },
  { value: 'adopted', label: 'Adoptée' },
  { value: 'suspended', label: 'Suspendue' },
  { value: 'abandoned', label: 'Abandonnée' },
  { value: 'rejected', label: 'Rejetée' },
];

const availableFilters = [
  { value: 'category', label: 'Par Catégorie' },
  { value: 'circle', label: 'Par Cercle' },
  { value: 'status', label: 'Par Statut' },
  { value: 'author', label: 'Par Auteur' },
  { value: 'search', label: 'Recherche texte (titre)' },
];

const mapConfig = (data) => ({
  public_api_key: data.public_api_key || '',
  public_circles: Array.isArray(data.public_circles) ? data.public_circles : [],
  public_categories: Array.isArray(data.public_categories) ? data.public_categories : [],
  public_statuses: Array.isArray(data.public_statuses) ? data.public_statuses : [],
  public_filters: Array.isArray(data.public_filters) ? data.public_filters : [],
});

const generateApiKey = async () => {
  if (!confirm("Voulez-vous générer une nouvelle clé API ? L'ancienne sera immédiatement révoquée.")) return;
  try {
    const { data } = await axios.post('/api/v1/admin/config/api-key');
    config.value = mapConfig(data.config);
    alert('Nouvelle clé générée : ' + data.api_key);
  } catch (e) {
    alert('Erreur lors de la génération de la clé.');
  }
};

const revokeApiKey = async () => {
  if (!confirm('Voulez-vous révoquer la clé API actuelle ? Tout accès externe sera coupé.')) return;
  try {
    const { data } = await axios.delete('/api/v1/admin/config/api-key');
    config.value = mapConfig(data.config);
    alert('Clé révoquée avec succès.');
  } catch (e) {
    alert('Erreur lors de la révocation.');
  }
};

onMounted(async () => {
  try {
    const [configRes, circlesRes, categoriesRes] = await Promise.all([
      axios.get('/api/v1/admin/config'),
      axios.get('/api/v1/circles'),
      axios.get('/api/v1/categories'),
    ]);
    const data = configRes.data.config || configRes.data || {};
    config.value = mapConfig(data);
    circles.value = circlesRes.data.circles || [];
    categories.value = categoriesRes.data.categories || [];
  } catch (e) {
    console.error('Fetch error', e);
  } finally {
    loading.value = false;
  }
});

const saveConfig = async () => {
  saving.value = true;
  try {
    await axios.put('/api/v1/admin/config', { config: config.value });
    alert('Paramètres de publication enregistrés avec succès.');
  } catch (e) {
    alert('Erreur lors de la sauvegarde.');
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
@import "../../../css/admin-config.css";

.checkbox-list {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  padding: 8px 0;
}
.checkbox-list.inline-list {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  padding: 16px;
}
.checkbox-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 16px;
  cursor: pointer;
  border-radius: 8px;
  transition: background 0.15s;
  border: 1px solid var(--gray-200);
  background: var(--gray-50);
}
.checkbox-item:hover { background: var(--blue-50); border-color: var(--blue-300); }
.checkbox-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--blue-600); }
.checkbox-item span { font-size: 13px; font-weight: 600; color: var(--gray-700); }
.btn-outline { background: white; border: 1px solid var(--gray-300); color: var(--gray-700); }
.btn-outline:hover { background: var(--gray-50); border-color: var(--blue-400); }
.text-red-500 { color: #ef4444; }
.alert-info { background: var(--blue-50); border: 1px solid var(--blue-100); color: var(--blue-800); }
</style>

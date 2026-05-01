<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Publication & API Publique</div>
            <div class="hero-subtitle">Gérez les données exposées publiquement via l'API XML (pour CMS tiers, plugins, etc.).</div>
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
        <div class="config-content">
          
          <!-- API KEY SECTION -->
          <div class="premium-card animate-fade-in mb-32">
            <div class="pc-header pc-header-teal">
              <div class="pc-header-icon"><i class="fa-solid fa-key"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Clé d'API (Authentification)</div>
                <div class="pc-header-sub">Clé requise pour accéder aux routes publiques.</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="alert alert-info mb-24">
                <i class="fa-solid fa-circle-info"></i>
                <p>Pour utiliser l'API publique (<code>/api/v1/public/decisions</code>), le client doit envoyer cette clé via l'en-tête HTTP <code>X-API-Key</code> ou le paramètre d'URL <code>?api_key=</code>.</p>
              </div>

              <div class="form-group">
                <label class="config-label">Clé secrète actuelle</label>
                <div style="display: flex; gap: 16px; align-items: center;">
                  <input type="text" v-model="config.public_api_key" class="input input-lg" readonly style="font-family: monospace; letter-spacing: 2px; background: var(--gray-50);" :placeholder="config.public_api_key ? '' : 'Aucune clé active'">
                  <button class="btn btn-outline" @click="generateApiKey">
                    <i class="fa-solid fa-rotate-right mr-8"></i> {{ config.public_api_key ? 'Regénérer' : 'Générer une clé' }}
                  </button>
                  <button v-if="config.public_api_key" class="btn btn-ghost text-red-500" @click="revokeApiKey">
                    <i class="fa-solid fa-trash-can mr-8"></i> Révoquer
                  </button>
                </div>
                <p class="help-text">La regénération d'une clé révoquera immédiatement l'accès aux clients utilisant l'ancienne clé.</p>
              </div>
            </div>
          </div>

          <!-- PERMISSIONS SECTION -->
          <div class="premium-card animate-fade-in mb-32">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-filter"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Périmètre de Publication</div>
                <div class="pc-header-sub">Sélectionnez quelles décisions (même marquées "publiques") ont le droit d'être exposées.</div>
              </div>
            </div>
            <div class="pc-body p-24">
              
              <div class="grid-2 gap-32">
                <!-- CERCLES -->
                <div class="form-group">
                  <label class="config-label">Cercles autorisés</label>
                  <p class="help-text mb-12">Seules les décisions appartenant à ces cercles pourront être exposées.</p>
                  <div class="checkbox-list">
                    <label v-for="circle in circles" :key="circle.id" class="checkbox-item">
                      <input type="checkbox" :value="circle.id" v-model="config.public_circles">
                      <span>{{ circle.name }}</span>
                    </label>
                  </div>
                </div>

                <!-- CATEGORIES -->
                <div class="form-group">
                  <label class="config-label">Catégories autorisées</label>
                  <p class="help-text mb-12">Seules les décisions rattachées à ces catégories pourront être exposées.</p>
                  <div class="checkbox-list">
                    <label v-for="cat in categories" :key="cat.id" class="checkbox-item">
                      <input type="checkbox" :value="cat.id" v-model="config.public_categories">
                      <span>{{ cat.name }}</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- STATUSES -->
              <div class="form-group mt-32">
                <label class="config-label">Statuts exposés</label>
                <p class="help-text mb-12">Généralement, seules les décisions finales (adoptées, rejetées) ou en phase finale d'objection sont publiques.</p>
                <div class="checkbox-list inline-list">
                  <label v-for="status in availableStatuses" :key="status.value" class="checkbox-item">
                    <input type="checkbox" :value="status.value" v-model="config.public_statuses">
                    <span>{{ status.label }}</span>
                  </label>
                </div>
              </div>

            </div>
          </div>

          <!-- FILTERS SECTION -->
          <div class="premium-card animate-fade-in">
            <div class="pc-header pc-header-purple">
              <div class="pc-header-icon"><i class="fa-solid fa-sliders"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Filtres de l'API</div>
                <div class="pc-header-sub">Filtres dynamiques autorisés dans les requêtes de l'API (ex: ?category=finance)</div>
              </div>
            </div>
            <div class="pc-body p-24">
               <div class="form-group">
                  <div class="checkbox-list inline-list">
                    <label v-for="filter in availableFilters" :key="filter.value" class="checkbox-item">
                      <input type="checkbox" :value="filter.value" v-model="config.public_filters">
                      <span>{{ filter.label }} (<code>?{{ filter.value }}=...</code>)</span>
                    </label>
                  </div>
               </div>
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
    { value: 'search', label: 'Recherche texte (titre)' }
];

const mapConfig = (data) => {
    return {
        public_api_key: data.public_api_key || '',
        public_circles: Array.isArray(data.public_circles) ? data.public_circles : [],
        public_categories: Array.isArray(data.public_categories) ? data.public_categories : [],
        public_statuses: Array.isArray(data.public_statuses) ? data.public_statuses : [],
        public_filters: Array.isArray(data.public_filters) ? data.public_filters : []
    };
};

const generateApiKey = async () => {
    if (!confirm('Voulez-vous générer une nouvelle clé API ? L\'ancienne sera immédiatement révoquée.')) return;
    try {
        const { data } = await axios.post('/api/v1/admin/config/api-key');
        config.value = mapConfig(data.config);
        alert('Nouvelle clé générée avec succès : ' + data.api_key);
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
        axios.get('/api/v1/categories')
    ]);

    const data = configRes.data.config || configRes.data || {};
    config.value = mapConfig(data);

    circles.value = circlesRes.data.data || circlesRes.data || [];
    categories.value = categoriesRes.data.data || categoriesRes.data || [];

  } catch (e) {
    console.error("Fetch error", e);
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
.config-layout { display: flex; flex-direction: column; }

/* Config Fields */
.config-label { display: block; font-size: 15px; font-weight: 800; color: var(--gray-800); margin-bottom: 8px; }

.input-lg { padding: 14px 18px; font-size: 16px; flex: 1; }

.checkbox-list {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  max-height: 200px;
  overflow-y: auto;
  padding: 8px 0;
}

.checkbox-list.inline-list {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px;
    max-height: none;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px;
  cursor: pointer;
  transition: background 0.1s;
}

.checkbox-item:hover {
  background: var(--gray-50);
}

.checkbox-item input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.checkbox-item span {
  font-size: 14px;
  color: var(--gray-800);
}

/* Helpers */
.help-text { font-size: 12px; color: var(--gray-500); line-height: 1.5; }
.shadow-blue { box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4); }

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

</style>

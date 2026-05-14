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
          
          <!-- SECTION AUTORISATION ACCES -->
          <div v-if="activeSection === 'api_access'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-shield-halved"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Autorisation d'accès</div>
                <div class="pc-header-sub">Définissez les périmètres de données accessibles via l'API</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <p class="help-text mb-24">Ces paramètres contrôlent quelles données sont retournées par l'API publique. Les décisions privées restent inaccessibles par défaut.</p>

              <!-- CERCLES -->
              <div class="form-group mb-32">
                <label class="config-label">Cercles autorisés</label>
                <p class="help-text mb-16">Seules les décisions rattachées à ces cercles (et marquées publiques) seront exposées.</p>
                <div class="selection-chips">
                  <label v-for="circle in circles" :key="circle.id" class="chip-item">
                    <input type="checkbox" :value="circle.id" v-model="config.api_circles">
                    <div class="chip-content">
                      <i class="fa-solid fa-users-gear"></i>
                      <span>{{ circle.name }}</span>
                    </div>
                  </label>
                  <div v-if="circles.length === 0" class="text-muted p-16 text-xs italic">Aucun cercle trouvé.</div>
                </div>
                <div class="config-key">variable : <code>api_circles</code></div>
              </div>

              <!-- CATEGORIES -->
              <div class="form-group mb-32">
                <label class="config-label">Catégories autorisées</label>
                <p class="help-text mb-16">Seules les décisions rattachées à ces catégories pourront être exposées via l'API.</p>
                <div class="selection-chips">
                  <label v-for="cat in categories" :key="cat.id" class="chip-item">
                    <input type="checkbox" :value="cat.id" v-model="config.api_categories">
                    <div class="chip-content">
                      <i class="fa-solid fa-tag"></i>
                      <span>{{ cat.name }}</span>
                    </div>
                  </label>
                  <div v-if="categories.length === 0" class="text-muted p-16 text-xs italic">Aucune catégorie trouvée.</div>
                </div>
                <div class="config-key">variable : <code>api_categories</code></div>
              </div>

              <!-- STATUTS -->
              <div class="form-group">
                <label class="config-label">Statuts exposés</label>
                <p class="help-text mb-16">Définissez quels statuts de décisions sont consultables en externe.</p>
                <div class="selection-chips">
                  <label v-for="status in availableStatuses" :key="status.value" class="chip-item">
                    <input type="checkbox" :value="status.value" v-model="config.api_statuses">
                    <div class="chip-badge" 
                         :style="{ 
                           color: status.defaultPrimary,
                           backgroundColor: status.defaultSecondary
                         }">
                      <i class="fa-solid fa-circle-check"></i>
                      <span>{{ status.label }}</span>
                    </div>
                  </label>
                </div>
                <div class="config-key">variable : <code>api_statuses</code></div>
              </div>

            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Autorisations
              </button>
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
                  <input type="checkbox" :value="filter.value" v-model="config.api_filters">
                  <span>{{ filter.label }} (<code>?{{ filter.value }}=...</code>)</span>
                </label>
              </div>
              <div class="config-key mt-16">variable : <code>api_filters</code></div>
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
            <div class="pc-body p-24">
                <div class="grid-layout">
                    <!-- Config Form -->
                    <div class="col-side" style="min-width: 350px;">
                        <div class="form-group mb-20">
                            <label class="label">Type de Widget</label>
                            <div class="flex gap-12 mt-8">
                                <button class="btn flex-1" :class="snippetConfig.type === 'single' ? 'btn-blue' : 'btn-outline'" @click="snippetConfig.type = 'single'">Décision Unique</button>
                                <button class="btn flex-1" :class="snippetConfig.type === 'list' ? 'btn-blue' : 'btn-outline'" @click="snippetConfig.type = 'list'">Liste de Décisions</button>
                            </div>
                        </div>

                        <div v-if="snippetConfig.type === 'single'" class="form-group mb-20">
                            <label class="label">ID de la décision (Public)</label>
                            <input type="text" v-model="snippetConfig.decision_id" class="input w-full" placeholder="Ex: 42">
                            <p class="help-text">La décision doit être marquée comme publique.</p>
                        </div>

                        <div v-if="snippetConfig.type === 'list'" class="form-group mb-20">
                            <label class="label">Filtrer par Cercle</label>
                            <select v-model="snippetConfig.circle_id" class="input w-full">
                                <option value="">Tous les cercles</option>
                                <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>

                        <div class="form-group mb-20">
                            <label class="label">Thème Visuel</label>
                            <select v-model="snippetConfig.theme" class="input w-full">
                                <option value="light">Clair (Standard)</option>
                                <option value="dark">Sombre (Premium)</option>
                                <option value="auto">Adaptatif (Système)</option>
                            </select>
                        </div>

                        <div class="mt-32 pt-24 border-top">
                            <label class="label mb-12">Code à copier</label>
                            <div class="snippet-code-box">
                                <pre><code>&lt;script src="{{ baseUrl }}/widgets/loader.js"&gt;&lt;/script&gt;
&lt;div class="dazo-widget" 
     data-type="{{ snippetConfig.type }}"
     data-id="{{ snippetConfig.decision_id }}"
     data-theme="{{ snippetConfig.theme }}"&gt;
&lt;/div&gt;</code></pre>
                                <button class="btn btn-sm btn-white copy-btn" @click="copySnippet">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Area -->
                    <div class="col-main">
                        <div class="preview-container" :class="snippetConfig.theme">
                            <div class="preview-header">
                                <div class="preview-dot"></div>
                                <div class="preview-dot"></div>
                                <div class="preview-dot"></div>
                                <span class="ml-12 text-xs opacity-50">Aperçu du Widget</span>
                            </div>
                            <div class="preview-content">
                                <!-- Placeholder pour le rendu réel du widget -->
                                <div class="widget-placeholder">
                                    <div class="flex items-center gap-12 mb-16">
                                        <div class="w-40 h-40 rounded bg-blue-100 flex items-center justify-center">
                                            <i class="fa-solid fa-gavel text-blue-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="h-12 bg-gray-200 rounded w-3/4 mb-8"></div>
                                            <div class="h-8 bg-gray-100 rounded w-1/2"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-8">
                                        <div class="h-8 bg-gray-100 rounded w-full"></div>
                                        <div class="h-8 bg-gray-100 rounded w-full"></div>
                                        <div class="h-8 bg-gray-100 rounded w-2/3"></div>
                                    </div>
                                    <div class="mt-20 pt-16 border-top flex justify-between items-center">
                                        <div class="h-16 bg-blue-50 rounded w-24"></div>
                                        <div class="text-[10px] text-blue-600 font-bold uppercase">Voir sur DAZO</div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
  api_circles: [],
  api_categories: [],
  api_statuses: [],
  api_filters: []
});

const circles = ref([]);
const categories = ref([]);
const loading = ref(true);
const saving = ref(false);
const activeSection = ref('api_key');
const baseUrl = window.location.origin;

const snippetConfig = ref({
    type: 'single',
    decision_id: '',
    circle_id: '',
    category_id: '',
    limit: 5,
    theme: 'light',
    show_meta: true
});

const copySnippet = () => {
    const code = document.querySelector('.snippet-code-box pre code').innerText;
    navigator.clipboard.writeText(code);
    alert('Code copié dans le presse-papier !');
};

const sections = [
  { id: 'api_key', label: 'Clé API', icon: 'fa-solid fa-key' },
  { id: 'api_access', label: 'Autorisation d\'accès', icon: 'fa-solid fa-shield-halved' },
  { id: 'api_filters', label: 'Filtres API', icon: 'fa-solid fa-sliders' },
  { id: 'snippet', label: 'Snippet Generator', icon: 'fa-solid fa-code' },
];

const availableStatuses = [
  { value: 'draft', label: 'Brouillon', defaultPrimary: '#64748b', defaultSecondary: '#f1f5f9' },
  { value: 'clarification', label: 'Clarification', defaultPrimary: '#f59e0b', defaultSecondary: '#fffbeb' },
  { value: 'reaction', label: 'Réaction', defaultPrimary: '#3b82f6', defaultSecondary: '#eff6ff' },
  { value: 'objection', label: 'Objection', defaultPrimary: '#ef4444', defaultSecondary: '#fef2f2' },
  { value: 'revision', label: 'En Révision', defaultPrimary: '#8b5cf6', defaultSecondary: '#f5f3ff' },
  { value: 'adopted', label: 'Adoptée', defaultPrimary: '#10b981', defaultSecondary: '#ecfdf5' },
  { value: 'suspended', label: 'Suspendue', defaultPrimary: '#6366f1', defaultSecondary: '#eef2ff' },
  { value: 'abandoned', label: 'Abandonnée', defaultPrimary: '#94a3b8', defaultSecondary: '#f8fafc' },
  { value: 'rejected', label: 'Rejetée', defaultPrimary: '#475569', defaultSecondary: '#f1f5f9' },
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
  api_circles: Array.isArray(data.api_circles) ? data.api_circles : [],
  api_categories: Array.isArray(data.api_categories) ? data.api_categories : [],
  api_statuses: Array.isArray(data.api_statuses) ? data.api_statuses : [],
  api_filters: Array.isArray(data.api_filters) ? data.api_filters : [],
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
      axios.get('/api/v1/admin/circles?per_page=100'),
      axios.get('/api/v1/admin/categories?per_page=100'),
    ]);
    const data = configRes.data.config || configRes.data || {};
    config.value = mapConfig(data);
    circles.value = circlesRes.data.data || circlesRes.data.circles || [];
    categories.value = categoriesRes.data.data || categoriesRes.data.categories || [];
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

/* Snippet Generator Styles */
.snippet-code-box {
  background: var(--gray-900);
  border-radius: 12px;
  padding: 16px;
  position: relative;
  border: 1px solid rgba(255,255,255,0.1);
  margin-top: 12px;
}
.snippet-code-box pre {
  margin: 0;
  color: #a5d6ff;
  font-family: var(--font-mono);
  font-size: 11px;
  line-height: 1.6;
  overflow-x: auto;
}
.snippet-code-box .copy-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  opacity: 0.6;
  transition: opacity 0.2s;
}
.snippet-code-box .copy-btn:hover { opacity: 1; }

.preview-container {
  background: #f1f5f9;
  border-radius: 16px;
  border: 1px solid var(--gray-200);
  overflow: hidden;
  height: 100%;
  min-height: 440px;
  display: flex;
  flex-direction: column;
}
.preview-container.dark { background: #0f172a; border-color: #1e293b; }

.preview-header {
  background: white;
  border-bottom: 1px solid var(--gray-200);
  padding: 12px 16px;
  display: flex;
  align-items: center;
}
.preview-container.dark .preview-header { background: #1e293b; border-color: #334155; color: rgba(255,255,255,0.5); }

.preview-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #e2e8f0;
  margin-right: 6px;
}
.preview-container.dark .preview-dot { background: #475569; }

.preview-content {
  flex: 1;
  padding: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.widget-placeholder {
  width: 100%;
  max-width: 350px;
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  border: 1px solid var(--gray-100);
}
.preview-container.dark .widget-placeholder { background: #1e293b; border-color: #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
.preview-container.dark .h-12 { background: #334155 !important; }
.preview-container.dark .h-8 { background: #475569 !important; }
.preview-container.dark .border-top { border-color: #334155; }
.preview-container.dark .text-gray-800 { color: white !important; }
.preview-container.dark .text-muted { color: rgba(255,255,255,0.4) !important; }

.space-y-8 > * + * { margin-top: 8px; }
</style>

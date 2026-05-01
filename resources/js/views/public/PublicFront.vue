<template>
  <div class="public-front dazo-public-listing">
    <div class="public-container-wide pb-48 pt-32">

      <!-- Bouton flottant loupe (mobile, filtres fermés) -->
      <button v-if="isMobileFiltersHidden" class="floating-search-btn dazo-public-mobile-trigger" @click="isMobileFiltersHidden = false">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>

      <!-- ── Barre d'actions (Popins) ── -->
      <div class="filters-actions-bar dazo-public-actions-bar">
        
        <!-- Bouton Rechercher -->
        <div class="popin-wrapper" v-click-outside="() => closePopin('search')">
          <button 
            class="action-btn" 
            :class="{ active: activePopin === 'search' || store.filters.search }"
            @click="togglePopin('search')"
          >
            <i class="fa-solid fa-magnifying-glass"></i>
            <span class="btn-label">Rechercher</span>
            <span v-if="store.filters.search" class="active-dot"></span>
          </button>
          
          <div v-if="activePopin === 'search'" class="popin popin-search">
            <div class="popin-header">
              <input 
                v-model="tempSearch" 
                type="text" 
                class="popin-input" 
                placeholder="Titre, contenu, cercle, thématique..."
                @input="onSearchInput"
                @keyup.enter="applySearch"
              >
              <div class="popin-actions-row">
                <button class="btn btn-primary" @click="applySearch">
                  <i class="fa-solid fa-magnifying-glass"></i> Rechercher
                </button>
                <button class="btn btn-outline" @click="cancelSearch">
                  <i class="fa-solid fa-xmark"></i> Annuler
                </button>
              </div>
            </div>
            
            <div v-if="store.suggestions.results && tempSearch.length >= 2" class="suggestions-list">
              <div v-if="store.loadingSuggestions" class="suggestions-loading">
                <i class="fa-solid fa-circle-notch fa-spin"></i> Recherche en cours...
              </div>
              
              <template v-else>
                <!-- Titres -->
                <div v-if="store.suggestions.results.titles?.length > 0" class="suggestion-group">
                  <label>Titres</label>
                  <div v-for="t in store.suggestions.results.titles" :key="t.id" class="suggestion-item" @click="selectSuggestion('decision', t.id)">
                    {{ t.title }}
                  </div>
                </div>
                <!-- Contenu -->
                <div v-if="store.suggestions.results.content?.length > 0" class="suggestion-group">
                  <label>Contenu</label>
                  <div v-for="c in store.suggestions.results.content" :key="c.id" class="suggestion-item" @click="selectSuggestion('decision', c.id)">
                    {{ c.title }} <small>(match contenu)</small>
                  </div>
                </div>
                <!-- Cercles -->
                <div v-if="store.suggestions.results.circles?.length > 0" class="suggestion-group">
                  <label>Cercles</label>
                  <div v-for="c in store.suggestions.results.circles" :key="c.id" class="suggestion-item" @click="selectSuggestion('circle', c.id)">
                    {{ c.name }}
                  </div>
                </div>
                <!-- Thématiques -->
                <div v-if="store.suggestions.results.categories?.length > 0" class="suggestion-group">
                  <label>Thématiques</label>
                  <div v-for="c in store.suggestions.results.categories" :key="c.id" class="suggestion-item" @click="selectSuggestion('category', c.id)">
                    {{ c.name }}
                  </div>
                </div>
                <div v-if="isSuggestionsEmpty" class="suggestions-empty">Aucun résultat spécifique trouvé</div>
              </template>
            </div>
            
          </div>
        </div>

        <!-- Bouton Filtrer -->
        <div class="popin-wrapper" v-click-outside="() => closePopin('filter')">
          <button 
            class="action-btn" 
            :class="{ active: activePopin === 'filter' || hasActiveFiltersNoSearch }"
            @click="togglePopin('filter')"
          >
            <i class="fa-solid fa-filter"></i>
            <span class="btn-label">Filtrer</span>
            <span v-if="hasActiveFiltersNoSearch" class="active-dot"></span>
          </button>
          
          <div v-if="activePopin === 'filter'" class="popin popin-filter">
            <div class="filter-grid">
              <!-- Phase -->
              <div class="filter-field">
                <label>Phase</label>
                <select v-model="store.filters.status" class="popin-select">
                  <option value="">Toutes les phases</option>
                  <option v-for="s in store.meta.statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
              </div>
              <!-- Cercle -->
              <div class="filter-field">
                <label>Cercle</label>
                <select v-model="store.filters.circle" class="popin-select">
                  <option value="">Tous les cercles</option>
                  <option v-for="c in store.meta.circles" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
                </select>
              </div>
              <!-- Thématique -->
              <div class="filter-field">
                <label>Thématique</label>
                <select v-model="store.filters.category" class="popin-select">
                  <option value="">Toutes les thématiques</option>
                  <option v-for="c in store.meta.categories" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
                </select>
              </div>
              <!-- Auteur -->
              <div class="filter-field">
                <label>Auteur</label>
                <select v-model="store.filters.author" class="popin-select">
                  <option value="">Tous les auteurs</option>
                  <option v-for="a in store.meta.authors" :key="a.id" :value="String(a.id)">{{ a.name }}</option>
                </select>
              </div>
            </div>

            <hr class="popin-divider">

            <div class="date-filter-section">
              <label>Période de publication</label>
              <div class="date-presets">
                <button class="preset-btn" @click="setDatePreset('today')">Aujourd'hui</button>
                <button class="preset-btn" @click="setDatePreset('1w')">1 semaine</button>
                <button class="preset-btn" @click="setDatePreset('3m')">3 mois</button>
                <button class="preset-btn" @click="setDatePreset('6m')">6 mois</button>
                <button class="preset-btn" @click="setDatePreset('1y')">1 an</button>
              </div>
              <div class="date-inputs">
                <div class="date-field">
                  <span>Du</span>
                  <input type="date" v-model="store.filters.date_start" class="popin-input-date">
                </div>
                <div class="date-field">
                  <span>Au</span>
                  <input type="date" v-model="store.filters.date_end" class="popin-input-date">
                </div>
              </div>
            </div>

            <div class="popin-footer popin-footer-three">
              <button class="btn btn-primary" @click="applyFilters">
                <i class="fa-solid fa-check"></i> Appliquer
              </button>
              <button class="btn btn-outline" @click="resetFilters">
                <i class="fa-solid fa-rotate-left"></i> Réinitialiser
              </button>
              <button class="btn btn-outline" @click="activePopin = null">
                <i class="fa-solid fa-xmark"></i> Annuler
              </button>
            </div>
          </div>
        </div>

        <!-- Bouton Trier -->
        <div class="popin-wrapper" v-click-outside="() => closePopin('sort')">
          <button 
            class="action-btn" 
            :class="{ active: activePopin === 'sort' }"
            @click="togglePopin('sort')"
          >
            <i class="fa-solid fa-arrow-down-wide-short"></i>
            <span class="btn-label">Trier</span>
          </button>
          
          <div v-if="activePopin === 'sort'" class="popin popin-sort">
            <div class="sort-options">
              <div 
                v-for="opt in sortOptions" 
                :key="opt.value" 
                class="sort-item" 
                :class="{ selected: store.filters.sort.startsWith(opt.value) }"
                @click="setSortCriteria(opt.value)"
              >
                <span>{{ opt.label }}</span>
                <i v-if="store.filters.sort.startsWith(opt.value)" 
                   class="fa-solid" 
                   :class="store.filters.sort.endsWith('desc') ? 'fa-arrow-down' : 'fa-arrow-up'"
                ></i>
              </div>
            </div>
            <div class="popin-footer mobile-only">
              <button class="btn btn-ghost btn-sm w-full" @click="activePopin = null">Fermer</button>
            </div>
          </div>
        </div>

        <!-- Bouton RAZ -->
        <div class="popin-wrapper">
          <button 
            class="action-btn raz-btn" 
            @click="resetAll"
          >
            <i class="fa-solid fa-rotate-left"></i>
            <span class="btn-label">RAZ</span>
          </button>
        </div>

        <!-- Séparateur Desktop -->
        <div class="bar-divider desktop-only"></div>

        <!-- Toggle Affichage (Desktop Only) -->
        <div class="popin-wrapper desktop-only">
          <button 
            class="action-btn view-btn" 
            @click="store.filters.viewMode = store.filters.viewMode === 'grid' ? 'list' : 'grid'"
            :title="store.filters.viewMode === 'grid' ? 'Passer en vue liste' : 'Passer en vue tuiles'"
          >
            <i :class="store.filters.viewMode === 'grid' ? 'fa-solid fa-list' : 'fa-solid fa-grip'"></i>
            <span class="btn-label">{{ store.filters.viewMode === 'grid' ? 'Liste' : 'Tuiles' }}</span>
          </button>
        </div>
      </div>

      <!-- ── Chargement ── -->
      <div v-if="store.loading" class="text-center py-48 text-muted dazo-public-loading">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement des décisions...</p>
      </div>

      <!-- ── Vide ── -->
      <div v-else-if="store.decisions.length === 0" class="empty-state dazo-public-empty">
        <div class="empty-icon"><i class="fa-solid fa-folder-open"></i></div>
        <h3>Aucune décision trouvée</h3>
        <p class="text-muted">Il n'y a actuellement aucune décision publique correspondant à vos critères.</p>
        <button v-if="hasActiveFilters" class="btn btn-secondary mt-16 dazo-public-btn-reset" @click="resetFilters">Effacer les filtres</button>
      </div>

      <!-- ── Contenu (Grille ou Liste) ── -->
      <div v-else :class="store.filters.viewMode === 'grid' ? 'decisions-grid' : 'decisions-list'" class="dazo-public-content">
        <router-link
          v-for="decision in store.decisions"
          :key="decision.id"
          :to="{ name: 'PublicDecision', params: { id: decision.id } }"
          class="decision-card dazo-public-item"
          :class="{ 'is-list-mode': effectiveViewMode === 'list' }"
        >
          <!-- ── LAYOUT TUILE (GRID) ── -->
          <template v-if="effectiveViewMode === 'grid'">
            <!-- Barre de titre bleue -->
            <div class="card-header-bar">
              <span v-if="decision.circle" class="meta-item circle-tag clickable" @click.prevent="applyFilter('circle', String(decision.circle.id))">
                <i class="fa-solid fa-circle-nodes"></i> {{ decision.circle.name }}
              </span>
              <span 
                class="status-badge clickable dazo-public-badge"
                :class="'status-' + decision.status"
                @click.prevent="applyFilter('status', decision.status)"
              >{{ getStatusLabel(decision.status) }}</span>
            </div>

            <!-- Titre -->
            <h3 class="decision-title dazo-public-card-title">{{ decision.title }}</h3>

            <!-- Auteurs (Empilés) -->
            <div class="decision-authors-line stacked">
              <div class="author-row">
                Proposé par : 
                <span class="author-link" @click.prevent="applyFilter('author', String(decision.author?.user_id))">
                  {{ decision.author?.user?.name || 'N/A' }}
                </span>
              </div>
              <div class="author-row">
                Animé par :
                <span class="author-link" @click.prevent="applyFilter('author', String(decision.current_animator?.user_id))">
                  {{ decision.current_animator?.user?.name || 'Non assigné' }}
                </span>
              </div>
            </div>

            <!-- Métadonnées & Hashtags -->
            <div class="card-bottom-row">
              <div class="decision-metadata-line">
                <span class="version-badge-square">v{{ decision.current_version?.version_number || 1 }}</span>
                <span class="dates-info">
                  {{ formatDate(decision.created_at) }}
                  <template v-if="decision.updated_at && decision.updated_at !== decision.created_at">
                    - {{ formatDate(decision.updated_at) }}
                  </template>
                </span>
              </div>
              <div class="category-tags dazo-public-card-tags" v-if="decision.categories?.length > 0">
                <span
                  v-for="cat in decision.categories"
                  :key="cat.id"
                  class="hashtag-link dazo-public-card-tag"
                  @click.prevent="applyFilter('category', String(cat.id))"
                >#{{ cat.name }}</span>
              </div>
            </div>
          </template>

          <!-- ── LAYOUT LISTE (DETAIL) ── -->
          <template v-else-if="effectiveViewMode === 'list'">
            <!-- Barre de titre bleue -->
            <div class="card-header-bar">
              <span v-if="decision.circle" class="meta-item circle-tag clickable" @click.prevent="applyFilter('circle', String(decision.circle.id))">
                <i class="fa-solid fa-circle-nodes"></i> {{ decision.circle.name }}
              </span>
              <span 
                class="status-badge clickable dazo-public-badge"
                :class="'status-' + decision.status"
                @click.prevent="applyFilter('status', decision.status)"
              >{{ getStatusLabel(decision.status) }}</span>
            </div>

            <!-- Titre -->
            <h3 class="decision-title dazo-public-card-title">{{ decision.title }}</h3>

            <!-- Auteurs (Même ligne) -->
            <div class="decision-authors-line inline">
              Proposé par : 
              <span class="author-link" @click.prevent="applyFilter('author', String(decision.author?.user_id))">
                {{ decision.author?.user?.name || 'N/A' }}
              </span>, 
              animé par : 
              <span class="author-link" @click.prevent="applyFilter('author', String(decision.current_animator?.user_id))">
                {{ decision.current_animator?.user?.name || 'Non assigné' }}
              </span>.
            </div>

            <!-- Métadonnées & Hashtags -->
            <div class="card-bottom-row list-bottom">
              <div class="decision-metadata-line">
                <span class="version-badge-square">v{{ decision.current_version?.version_number || 1 }}</span>
                <span class="dates-info">
                  {{ formatDate(decision.created_at) }}
                  <template v-if="decision.updated_at && decision.updated_at !== decision.created_at">
                    - {{ formatDate(decision.updated_at) }}
                  </template>
                </span>
              </div>

              <div class="category-tags dazo-public-card-tags" v-if="decision.categories?.length > 0">
                <span
                  v-for="cat in decision.categories"
                  :key="cat.id"
                  class="hashtag-link dazo-public-card-tag"
                  @click.prevent="applyFilter('category', String(cat.id))"
                >#{{ cat.name }}</span>
              </div>
            </div>
          </template>
        </router-link>
      </div>

      <!-- ── Pagination ── -->
      <div v-if="store.pagination.last_page > 1" class="pagination dazo-public-pagination">
        <button class="btn btn-ghost dazo-public-page-btn" :disabled="store.pagination.current_page === 1" @click="store.fetchDecisions(store.pagination.current_page - 1)">
          <i class="fa-solid fa-chevron-left"></i> Précédent
        </button>
        <span class="page-info dazo-public-page-info">Page {{ store.pagination.current_page }} sur {{ store.pagination.last_page }}</span>
        <button class="btn btn-ghost dazo-public-page-btn" :disabled="store.pagination.current_page === store.pagination.last_page" @click="store.fetchDecisions(store.pagination.current_page + 1)">
          Suivant <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { usePublicFrontStore } from '../../stores/publicFront';
import { useRouter } from 'vue-router';

const store = usePublicFrontStore();
const router = useRouter();

// ── Gestion de la largeur pour forcer le mode tuile ──
const windowWidth = ref(window.innerWidth);
const onResize = () => { windowWidth.value = window.innerWidth; };
const isSmallScreen = computed(() => windowWidth.value < 700);
const effectiveViewMode = computed(() => isSmallScreen.value ? 'grid' : store.filters.viewMode);

// ── État des Popins ──
const activePopin = ref(null);
const searchInput = ref(null);
const tempSearch = ref(store.filters.search);

const togglePopin = (name) => {
  if (activePopin.value === name) {
    activePopin.value = null;
  } else {
    activePopin.value = name;
    if (name === 'search') {
      nextTick(() => searchInput.value?.focus());
    }
  }
};

const closePopin = (name) => {
  if (activePopin.value === name) activePopin.value = null;
};

// ── Directive v-click-outside simple ──
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value();
      }
    };
    document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent);
  },
};

// ── Recherche & Suggestions ──
let searchTimeout = null;
const onSearchInput = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    store.fetchSuggestions(tempSearch.value);
  }, 300);
};

const applySearch = () => {
  store.filters.search = tempSearch.value;
  store.fetchDecisions(1);
  activePopin.value = null;
};

const cancelSearch = () => {
  tempSearch.value = store.filters.search;
  activePopin.value = null;
};

const selectSuggestion = (type, id) => {
  if (type === 'decision') {
    router.push({ name: 'PublicDecision', params: { id } });
  } else {
    store.filters[type] = String(id);
    store.filters.search = '';
    tempSearch.value = '';
    store.fetchDecisions(1);
  }
  activePopin.value = null;
};

const isSuggestionsEmpty = computed(() => {
  const r = store.suggestions.results;
  return !r.titles?.length && !r.content?.length && !r.circles?.length && !r.categories?.length;
});

// ── Filtres & Dates ──
const applyFilters = () => {
  store.fetchDecisions(1);
  activePopin.value = null;
};

const setDatePreset = (preset) => {
  const now = new Date();
  const start = new Date();
  
  if (preset === 'today') {
    // start is already today
  } else if (preset === '1w') {
    start.setDate(now.getDate() - 7);
  } else if (preset === '3m') {
    start.setMonth(now.getMonth() - 3);
  } else if (preset === '6m') {
    start.setMonth(now.getMonth() - 6);
  } else if (preset === '1y') {
    start.setFullYear(now.getFullYear() - 1);
  }
  
  store.filters.date_start = start.toISOString().split('T')[0];
  store.filters.date_end = now.toISOString().split('T')[0];
};

const resetFilters = () => {
  store.filters.status = '';
  store.filters.circle = '';
  store.filters.category = '';
  store.filters.author = '';
  store.filters.date_start = '';
  store.filters.date_end = '';
  store.fetchDecisions(1);
};

// ── Tri ──
const sortOptions = [
  { label: 'Date de création', value: 'created' },
  { label: 'Date de mise à jour', value: 'updated' },
  { label: 'Ordre alphabétique', value: 'alpha' },
];

const setSortCriteria = (criteria) => {
  const current = store.filters.sort;
  if (current.startsWith(criteria)) {
    // Toggle direction
    const nextDir = current.endsWith('desc') ? 'asc' : 'desc';
    store.filters.sort = `${criteria}_${nextDir}`;
  } else {
    store.filters.sort = `${criteria}_desc`;
  }
  store.fetchDecisions(1);
};

// ── Global ──
const hasActiveFiltersNoSearch = computed(() => 
  store.filters.status || store.filters.circle || store.filters.category || 
  store.filters.author || store.filters.date_start || store.filters.date_end
);

const hasAnyFilter = computed(() => 
  store.filters.search || hasActiveFiltersNoSearch.value
);

const resetAll = () => {
  store.filters.search = '';
  tempSearch.value = '';
  resetFilters();
};

const applyFilter = (key, value) => {
  store.filters[key] = value;
  store.fetchDecisions(1);
};

const formatDate = (d) =>
  d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '';

const getStatusLabel = (s) =>
  ({ draft: 'Brouillon', clarification: 'Clarification', reaction: 'Réaction', objection: 'Objection', adopted: 'Adoptée', abandoned: 'Abandonnée' }[s] || s);

onMounted(async () => {
  window.addEventListener('resize', onResize);
  await store.fetchMeta();
  if (store.decisions.length === 0) {
    store.fetchDecisions();
  }
});

onUnmounted(() => {
  window.removeEventListener('resize', onResize);
});
</script>

<style scoped>
/* ── Barre d'actions (Filtres) ── */
.filters-actions-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  padding: 12px 20px;
  border-radius: 16px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
  position: sticky;
  top: 106px;
  z-index: 100;
  margin-bottom: 32px;
}

@supports (backdrop-filter: blur(10px)) {
  .filters-actions-bar {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
  }
}

.popin-wrapper {
  position: relative;
  flex: 1;
}

.bar-divider {
  width: 1px;
  height: 24px;
  background: var(--gray-200);
  margin: 0 4px;
}

.desktop-only {
  display: flex !important;
}

.mobile-only {
  display: none !important;
}

/* ── Boutons d'action ── */
.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 10px 18px;
  border: 1px solid var(--gray-200);
  background: white;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-700);
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  width: 100%; /* Occupe tout le wrapper */
}

.action-btn:hover {
  background: var(--gray-50);
  border-color: var(--gray-300);
}

.action-btn.active {
  background: var(--blue-50);
  border-color: var(--blue-200);
  color: var(--blue-700);
}

.active-dot {
  width: 6px;
  height: 6px;
  background: var(--blue-600);
  border-radius: 50%;
  position: absolute;
  top: 6px;
  right: 6px;
}

/* ── Popins ── */
.popin {
  position: absolute;
  top: 100%; /* Positionné juste en dessous du wrapper */
  margin-top: 12px;
  left: 0;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
  border: 1px solid var(--gray-200);
  padding: 20px;
  min-width: 300px;
  z-index: 1000;
  animation: popin-anim 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  /* Harmonisation du bas */
  overflow: hidden;
}

@keyframes popin-anim {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.popin-header {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
}

.popin-input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  font-size: 14px;
}

.popin-select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  font-size: 14px;
  background: white;
}

.popin-footer, .popin-footer-three {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid var(--gray-100);
}

.popin-footer-three {
  justify-content: space-between;
  gap: 8px;
}

.popin-footer-three .btn {
  flex: 1;
  padding: 8px 4px;
  font-size: 12px;
  white-space: nowrap;
}

.btn-outline {
  background: white;
  border: 1px solid var(--gray-200);
  color: var(--gray-600);
  border-radius: 8px;
  padding: 8px 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-outline:hover {
  background: var(--gray-50);
  border-color: var(--gray-300);
  color: var(--gray-800);
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

/* ── Popin Search ── */
.popin-search { min-width: 450px; }

.popin-search .popin-header {
  flex-direction: column;
  gap: 12px;
}

.popin-search .popin-header .popin-input {
  width: 100%;
}

.popin-actions-row {
  display: flex;
  gap: 8px;
  width: 100%;
}

.popin-actions-row button {
  flex: 1;
}

.suggestions-list {
  max-height: 400px;
  overflow-y: auto;
  background: var(--gray-50);
  border-radius: 8px;
  padding: 12px;
}

.suggestion-group {
  margin-bottom: 12px;
}

.suggestion-group label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--gray-400);
  margin-bottom: 6px;
  padding-left: 8px;
}

.suggestion-item {
  padding: 8px 12px;
  font-size: 14px;
  color: var(--gray-700);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.15s;
}

.suggestion-item:hover {
  background: white;
  color: var(--blue-600);
  box-shadow: var(--shadow-sm);
}

.suggestion-item small { color: var(--gray-400); margin-left: 4px; }

.suggestions-loading, .suggestions-empty {
  text-align: center;
  padding: 20px;
  font-size: 14px;
  color: var(--gray-500);
}

/* ── Popin Filter ── */
.popin-filter { min-width: 450px; }

.filter-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.filter-field label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--gray-500);
  margin-bottom: 6px;
}

.popin-divider {
  margin: 20px 0;
  border: 0;
  border-top: 1px solid var(--gray-100);
}

.date-filter-section label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--gray-500);
  margin-bottom: 10px;
}

.date-presets {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
}

.preset-btn {
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  background: var(--gray-100);
  border: 0;
  border-radius: 20px;
  color: var(--gray-600);
  cursor: pointer;
  transition: all 0.2s;
}

.preset-btn:hover {
  background: var(--blue-100);
  color: var(--blue-700);
}

.date-inputs {
  display: flex;
  gap: 16px;
}

.date-field {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--gray-500);
}

.popin-input-date {
  flex: 1;
  padding: 6px 10px;
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  font-size: 13px;
}

/* ── Popin Sort ── */
.popin-sort { min-width: 240px; padding: 12px; }

.sort-options {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.sort-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: var(--gray-700);
  cursor: pointer;
  transition: all 0.15s;
}

.sort-item:hover {
  background: var(--gray-50);
}

.sort-item.selected {
  background: var(--blue-50);
  color: var(--blue-700);
  font-weight: 600;
}

/* ── Grille de décisions ── */
.decisions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
}

.decisions-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.decisions-list .decision-card {
  width: 100%;
}

.decision-card {
  background: white;
  border-radius: 16px;
  border: 1px solid var(--gray-200);
  text-decoration: none;
  color: inherit;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.decision-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.07);
  border-color: var(--blue-300);
}

.card-header-bar {
  background: linear-gradient(135deg, var(--blue-700) 0%, var(--blue-900) 100%);
  color: white;
  padding: 12px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  position: relative;
  overflow: hidden;
}

.card-header-bar::after {
  content: ""; position: absolute; top: -50%; right: -10%; width: 200px; height: 200px;
  background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%; pointer-events: none;
}

.card-header-bar .circle-tag {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  position: relative;
  z-index: 1;
}

.card-header-bar .status-badge {
  position: relative;
  z-index: 1;
}

.decision-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--gray-800);
  margin: 20px 24px 8px 24px;
  line-height: 1.4;
}

.decision-metadata-line {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 12px;
  color: var(--gray-500);
  margin: 0;
}


.version-badge-square {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-100);
  color: var(--gray-700);
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px; /* "Carré" avec bords légèrement adoucis */
  border: 1px solid var(--gray-200);
  min-width: 32px;
}

.dates-info {
  font-weight: 500;
}

.decision-authors-line {
  font-size: 13px;
  color: var(--gray-600);
  padding: 0 16px;
}

.decision-authors-line.stacked {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.decision-authors-line.inline {
  display: block;
  margin-bottom: 12px;
}

.author-link {
  font-weight: 600;
  color: var(--blue-600);
  cursor: pointer;
  text-decoration: underline dotted;
  transition: color 0.2s;
}

.author-link:hover {
  color: var(--blue-800);
  text-decoration: underline;
}

.card-bottom-row {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  width: 100%;
  gap: 16px;
  padding: 16px;
}

.is-list-mode .card-bottom-row {
  justify-content: space-between;
}

.decision-card:not(.is-list-mode) .card-bottom-row {
  flex-direction: column;
  align-items: flex-start;
  gap: 12px;
}

.decision-card:not(.is-list-mode) .category-tags {
  align-self: flex-end;
}

.category-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  justify-content: flex-end; /* S'assure que c'est à droite */
}

.hashtag-link {
  font-size: 12px;
  font-weight: 600;
  color: var(--blue-600);
  cursor: pointer;
  transition: all 0.2s;
  padding: 2px 6px;
  border-radius: 4px;
}

.hashtag-link:hover {
  color: var(--blue-800);
  background: var(--blue-50);
}

.meta-item {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.circle-tag { background: var(--gray-100); color: var(--gray-600); }
.circle-tag.clickable:hover { background: var(--blue-50); color: var(--blue-700); }

/* ── Badges ── */
.status-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 4px 10px;
  border-radius: 20px;
}

.status-badge.clickable:hover { opacity: 0.75; }
.status-draft        { background: var(--gray-50);   color: var(--gray-600);   border: 1px solid var(--gray-200); }
.status-clarification{ background: var(--blue-50);   color: var(--blue-800);   border: 1px solid var(--blue-200); }
.status-reaction     { background: var(--blue-50);   color: var(--blue-800);   border: 1px solid var(--blue-200); }
.status-objection    { background: var(--amber-50);  color: var(--amber-600);  border: 1px solid var(--amber-100); }
.status-revision     { background: var(--purple-50); color: var(--purple-600); border: 1px solid var(--purple-100); }
.status-adopted      { background: var(--teal-50);   color: var(--teal-600);   border: 1px solid var(--teal-100); }
.status-abandoned    { background: var(--red-50);    color: var(--red-600);    border: 1px solid var(--red-100); }

/* ── Vue Liste ── */
.decisions-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
}

.decision-list-item {
  background: white;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid var(--gray-200);
  text-decoration: none;
  color: inherit;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  transition: all 0.2s;
  box-shadow: var(--shadow-sm);
  width: 100%;
}

.decision-list-item:hover {
  border-color: var(--blue-300);
  box-shadow: var(--shadow-md);
  transform: translateX(4px);
}

.item-main {
  flex: 1;
}

.item-side {
  text-align: right;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
}

.side-bottom.category-tags {
  justify-content: flex-end;
}

/* ── Mobile ── */
@media (max-width: 768px) {
  .filters-actions-bar {
    gap: 8px;
    padding: 10px;
    top: 70px; /* Ajustement car le header est souvent plus petit en mobile */
    border-radius: 0 0 16px 16px;
  }
  
  .popin-wrapper {
    flex: 1;
  }
  
  .action-btn {
    padding: 10px 5px;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
    flex: 1;
  }
  
  .action-btn .btn-label {
    font-size: 10px;
    text-transform: uppercase;
  }
  
  .popin {
    position: fixed;
    top: 120px; /* Fixed top avec margin */
    bottom: auto;
    left: 10px;
    right: 10px;
    border-radius: 16px;
    min-width: 0;
    width: calc(100% - 20px);
    max-height: 70vh;
    overflow-y: auto;
    animation: popin-anim 0.3s ease-out;
    z-index: 2000;
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
  }

  /* Search spécifique mobile */
  .popin-search .popin-header {
    gap: 12px;
  }

  .decisions-list, .decisions-grid {
    display: flex !important;
    flex-direction: column !important;
    gap: 16px;
  }

  .decision-card, .decision-list-item {
    display: flex !important;
    flex-direction: column !important;
    padding: 20px;
    border-radius: 16px;
    width: 100% !important;
    box-shadow: var(--shadow-sm);
  }

  .popin-footer-three {
    flex-wrap: wrap;
  }
  .popin-footer-three .btn {
    flex: 1 1 calc(33.333% - 8px);
  }

  .card-top-row {
    margin-bottom: 12px;
  }

  .decision-title {
    font-size: 16px;
  }

  .decision-metadata-line {
    flex-wrap: wrap;
    gap: 6px;
  }

  .card-bottom-row {
    margin-top: 12px;
  }

  .desktop-only {
    display: none !important;
  }
  .mobile-only {
    display: flex !important;
  }
}

.empty-state { text-align: center; padding: 64px 20px; background: white; border-radius: 16px; border: 1px dashed var(--gray-300); }
.empty-icon { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
.empty-state h3 { font-size: 18px; font-weight: 700; color: var(--gray-800); margin-bottom: 8px; }

.pagination { display: flex; justify-content: center; align-items: center; gap: 16px; margin-top: 48px; }
.page-info { font-size: 14px; font-weight: 600; color: var(--gray-600); }
</style>

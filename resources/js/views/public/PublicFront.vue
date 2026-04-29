<template>
  <div class="public-front">
    <div class="public-container-wide pb-48 pt-32">

      <!-- Bouton flottant loupe (mobile, filtres fermés) -->
      <button v-if="isMobileFiltersHidden" class="floating-search-btn" @click="isMobileFiltersHidden = false">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>

      <!-- ── Moteur de Recherche & Filtres (Style Backend) ── -->
      <div class="search-engine-card">
        <div class="search-main-row">
          <div class="search-input-group">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input
              type="text"
              v-model="store.filters.search"
              placeholder="Rechercher par titre, auteur, mot-clé..."
              class="main-search-input"
              @keyup.enter="store.fetchDecisions(1)"
            />
          </div>
          <button class="btn btn-primary btn-search-trigger" @click="store.fetchDecisions(1)">
            <i class="fa-solid fa-filter mr-8"></i> Afficher
          </button>
        </div>

        <div class="filters-secondary-row">
          <!-- État / Phase -->
          <div class="filter-control" v-if="store.meta.statuses.length > 0">
            <label>État</label>
            <select v-model="store.filters.status" class="select-custom">
              <option value="">Tous les états</option>
              <option v-for="s in store.meta.statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
          </div>

          <!-- Cercle -->
          <div class="filter-control" v-if="store.meta.circles.length > 0">
            <label>Cercle</label>
            <select v-model="store.filters.circle" class="select-custom">
              <option value="">Tous les cercles</option>
              <option v-for="c in store.meta.circles" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
            </select>
          </div>

          <!-- Catégorie -->
          <div class="filter-control" v-if="store.meta.categories.length > 0">
            <label>Thématique</label>
            <select v-model="store.filters.category" class="select-custom">
              <option value="">Toutes thématiques</option>
              <option v-for="c in store.meta.categories" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
            </select>
          </div>

          <!-- Auteur -->
          <div class="filter-control" v-if="store.meta.authors.length > 0">
            <label>Auteur</label>
            <select v-model="store.filters.author" class="select-custom">
              <option value="">Tous auteurs</option>
              <option v-for="a in store.meta.authors" :key="a.id" :value="String(a.id)">{{ a.name }}</option>
            </select>
          </div>

          <!-- Tri -->
          <div class="filter-control">
            <label>Tri</label>
            <select v-model="store.filters.sort" class="select-custom">
              <option value="created_desc">Plus récents</option>
              <option value="created_asc">Plus anciens</option>
              <option value="updated_desc">Mise à jour (Derniers)</option>
              <option value="alpha_asc">A → Z</option>
              <option value="alpha_desc">Z → A</option>
            </select>
          </div>

          <div class="filter-actions-end">
            <button v-if="hasActiveFilters" class="btn btn-ghost btn-sm" @click="resetFilters">
              <i class="fa-solid fa-rotate-left"></i> Réinitialiser
            </button>
          </div>
        </div>
      </div>

      <!-- ── Chargement ── -->
      <div v-if="store.loading" class="text-center py-48 text-muted">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement des décisions...</p>
      </div>

      <!-- ── Vide ── -->
      <div v-else-if="store.decisions.length === 0" class="empty-state">
        <div class="empty-icon"><i class="fa-solid fa-folder-open"></i></div>
        <h3>Aucune décision trouvée</h3>
        <p class="text-muted">Il n'y a actuellement aucune décision publique correspondant à vos critères.</p>
        <button v-if="hasActiveFilters" class="btn btn-secondary mt-16" @click="resetFilters">Effacer les filtres</button>
      </div>

      <!-- ── Grille ── -->
      <div v-else class="decisions-grid">
        <router-link
          v-for="decision in store.decisions"
          :key="decision.id"
          :to="{ name: 'PublicDecision', params: { id: decision.id } }"
          class="decision-card"
        >
          <div class="card-header-block">
            <h3 class="decision-title">{{ decision.title }}</h3>
            <div class="decision-meta-info">
              <span class="date">{{ formatDate(decision.created_at) }}</span>
              <span class="version-tag"> - v{{ decision.current_version?.version_number || 1 }}</span>
            </div>
            <div class="status-container mt-8">
              <span
                class="status-badge clickable"
                :class="'status-' + decision.status"
                @click.prevent="applyFilter('status', decision.status)"
                title="Filtrer par cette phase"
              >{{ getStatusLabel(decision.status) }}</span>
            </div>
          </div>

          <div class="card-footer">
            <div class="category-tags" v-if="decision.categories?.length > 0">
              <span
                v-for="cat in decision.categories"
                :key="cat.id"
                class="hashtag-link"
                @click.prevent="applyFilter('category', String(cat.id))"
              >#{{ cat.name }}</span>
            </div>
            <div class="footer-right">
              <span
                v-if="decision.circle"
                class="meta-item circle-tag clickable"
                @click.prevent="applyFilter('circle', String(decision.circle.id))"
                title="Filtrer par ce cercle"
              >
                <i class="fa-solid fa-circle-nodes"></i> {{ decision.circle.name }}
              </span>
            </div>
          </div>
        </router-link>
      </div>

      <!-- ── Pagination ── -->
      <div v-if="store.pagination.last_page > 1" class="pagination">
        <button class="btn btn-ghost" :disabled="store.pagination.current_page === 1" @click="store.fetchDecisions(store.pagination.current_page - 1)">
          <i class="fa-solid fa-chevron-left"></i> Précédent
        </button>
        <span class="page-info">Page {{ store.pagination.current_page }} sur {{ store.pagination.last_page }}</span>
        <button class="btn btn-ghost" :disabled="store.pagination.current_page === store.pagination.last_page" @click="store.fetchDecisions(store.pagination.current_page + 1)">
          Suivant <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePublicFrontStore } from '../../stores/publicFront';

const store = usePublicFrontStore();
const isMobileFiltersHidden = ref(true);

// Applique un filtre ET met à jour automatiquement le select correspondant (via v-model sur store.filters)
const applyFilter = (key, value) => {
  store.filters[key] = value;
  store.fetchDecisions(1);
};

const debounceFetch = () => {
  // Désactivé au profit du bouton "Afficher" ou de la touche Entrée
};

const hasActiveFilters = computed(() =>
  store.filters.search || store.filters.status || store.filters.circle || store.filters.category || store.filters.author
);

const resetFilters = () => {
  store.filters.search   = '';
  store.filters.status   = '';
  store.filters.circle   = '';
  store.filters.category = '';
  store.filters.author   = '';
  store.filters.sort     = 'created_desc';
  store.fetchDecisions(1);
};

const formatDate = (d) =>
  d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '';

const getStatusLabel = (s) =>
  ({ draft: 'Brouillon', clarification: 'Clarification', reaction: 'Réaction', objection: 'Objection', adopted: 'Adoptée', abandoned: 'Abandonnée' }[s] || s);

onMounted(async () => {
  await store.fetchMeta();
  if (store.decisions.length === 0) {
    store.fetchDecisions();
  }
});
</script>

<style scoped>
/* ── Search Engine Card (Unified Filters) ── */
.search-engine-card {
  background: white;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  border: 1px solid var(--gray-200);
  margin-bottom: 40px;
  position: sticky;
  top: 110px;
  z-index: 90;
}

.search-main-row {
  display: flex;
  gap: 16px;
  margin-bottom: 20px;
}

.search-input-group {
  flex: 1;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  font-size: 16px;
}

.main-search-input {
  width: 100%;
  padding: 14px 20px 14px 50px;
  border: 1px solid var(--gray-200);
  border-radius: 12px;
  font-size: 16px;
  background: var(--gray-50);
  transition: all 0.2s;
  font-weight: 500;
}

.main-search-input:focus {
  background: white;
  border-color: var(--blue-500);
  box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
  outline: none;
}

.btn-search-trigger {
  padding: 0 32px;
  height: 52px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 15px;
  box-shadow: 0 4px 12px rgba(59,130,246,0.3);
}

.filters-secondary-row {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  align-items: flex-end;
  padding-top: 16px;
  border-top: 1px solid var(--gray-100);
}

.filter-control {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.filter-control label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--gray-500);
  letter-spacing: 0.05em;
  margin-left: 4px;
}

.select-custom {
  padding: 8px 32px 8px 12px;
  border: 1px solid var(--gray-200);
  border-radius: 10px;
  font-size: 13px;
  background-color: white;
  color: var(--gray-700);
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 14px;
  transition: all 0.2s;
  min-width: 140px;
}

.select-custom:hover {
  border-color: var(--gray-300);
}

.select-custom:focus {
  outline: none;
  border-color: var(--blue-400);
  box-shadow: 0 0 0 3px rgba(59,130,246,0.05);
}

.filter-actions-end {
  margin-left: auto;
  align-self: center;
}

@media (max-width: 1024px) {
  .search-engine-card { position: static; }
  .search-main-row { flex-direction: column; }
  .btn-search-trigger { width: 100%; }
  .filters-secondary-row { flex-direction: column; align-items: stretch; }
  .select-custom { min-width: 0; width: 100%; }
  .filter-actions-end { margin-left: 0; width: 100%; margin-top: 8px; }
}

/* ── Decisions grid ── */
.decisions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }

.decision-card {
  background: white; border-radius: 16px; padding: 24px;
  border: 1px solid var(--gray-200); text-decoration: none; color: inherit;
  transition: all 0.2s cubic-bezier(0.4,0,0.2,1); display: flex; flex-direction: column;
  box-shadow: var(--shadow-sm);
}
.decision-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.07); border-color: var(--blue-300); }

.card-header-block { flex: 1; margin-bottom: 16px; }
.decision-title {
  font-size: 18px; font-weight: 700; color: var(--gray-800); margin: 0 0 6px; line-height: 1.4;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.decision-meta-info { display: flex; align-items: center; font-size: 12px; color: var(--gray-500); font-weight: 500; margin-bottom: 4px; }
.version-tag { color: var(--gray-400); margin-left: 4px; }

.card-footer {
  margin-top: auto; padding-top: 16px; border-top: 1px solid var(--gray-100);
  display: flex; align-items: center; justify-content: space-between; gap: 8px;
}
.category-tags { display: flex; flex-wrap: wrap; gap: 6px; flex: 1; }
.hashtag-link {
  font-size: 12px; font-weight: 600; color: var(--blue-600);
  cursor: pointer; transition: all 0.2s; padding: 2px 6px; border-radius: 4px;
}
.hashtag-link:hover { color: var(--blue-800); background: var(--blue-50); }
.footer-right { flex-shrink: 0; }
.meta-item { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; }
.circle-tag { background: var(--gray-100); color: var(--gray-600); }
.clickable { cursor: pointer; transition: all 0.2s; }
.circle-tag.clickable:hover { background: var(--blue-50); color: var(--blue-700); }

/* ── Status badges ── */
.status-badge { display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 10px; border-radius: 20px; }
.status-badge.clickable:hover { opacity: 0.75; }
.status-draft        { background: var(--gray-100);  color: var(--gray-600); }
.status-clarification{ background: var(--blue-100);  color: var(--blue-700); }
.status-reaction     { background: var(--amber-100); color: var(--amber-700); }
.status-objection    { background: var(--red-100);   color: var(--red-700); }
.status-adopted      { background: var(--teal-100);  color: var(--teal-700); }
.status-abandoned    { background: var(--gray-200);  color: var(--gray-700); }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 64px 20px; background: white; border-radius: 16px; border: 1px dashed var(--gray-300); }
.empty-icon { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
.empty-state h3 { font-size: 18px; font-weight: 700; color: var(--gray-800); margin-bottom: 8px; }

/* ── Pagination ── */
.pagination { display: flex; justify-content: center; align-items: center; gap: 16px; margin-top: 48px; }
.page-info { font-size: 14px; font-weight: 600; color: var(--gray-600); }
</style>

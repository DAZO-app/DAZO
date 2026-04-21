<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Thématiques & Catégories</div>
            <div class="hero-subtitle">Structurez l'expression collective en organisant les décisions par thèmes et couleurs.</div>
          </div>
          <div class="hero-action">
            <button class="btn btn-secondary" @click="openCreate">
              <i class="fa-solid fa-plus"></i> Nouvelle catégorie
            </button>
          </div>
        </div>
      </div>

      <!-- FILTER BAR -->
      <div class="filter-bar">
        <div class="filter-group main-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input v-model="filters.search" placeholder="Rechercher une catégorie..." class="input-inline">
        </div>
        <div class="filter-row">
           <button class="btn btn-ghost btn-sm ml-auto" @click="resetFilters">
            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
          </button>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal-card">
          <div class="modal-header modal-header-indigo">
            <span class="modal-title">{{ form.id ? 'Éditer la catégorie' : 'Nouvelle catégorie' }}</span>
            <button class="btn btn-ghost btn-icon text-white" @click="showModal = false"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCategory">
              <div class="form-group">
                <label class="label">Nom *</label>
                <input v-model="form.name" class="input" required placeholder="Ex: Finance, Stratégie...">
              </div>
              <div class="form-group">
                <label class="label">Description</label>
                <textarea v-model="form.description" class="textarea" rows="2" placeholder="À quoi sert cette catégorie ?"></textarea>
              </div>
              <div class="form-group">
                <label class="label">Couleur d'identification</label>
                <div style="display:flex;gap:12px;align-items:center;">
                  <input type="color" v-model="form.color" style="width:40px;height:40px;padding:0;border:none;border-radius:var(--radius-md);cursor:pointer;">
                  <input v-model="form.color" class="input" placeholder="#1e40af" style="flex:1;">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-ghost" @click="showModal = false">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer la catégorie</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else class="cat-grid">
        <div v-for="cat in categories" :key="cat.id" class="premium-card">
          <div class="card-header card-header-sexy justify-between" :style="{ borderLeft: '4px solid ' + (cat.color || '#cccccc') }">
            <span class="card-title" style="display:flex; align-items:center;">
              <div class="cat-color-pill" :style="{ background: cat.color || '#cccccc' }"></div>
              {{ cat.name }}
            </span>
          </div>
          <div class="card-body">
            <div class="cat-desc-large">{{ cat.description || 'Aucune description fournie.' }}</div>
            <div class="cat-actions-footer">
              <button class="btn btn-secondary btn-sm" @click="openEdit(cat)">
                <i class="fa-solid fa-pen"></i> Modifier
              </button>
              <button class="btn btn-danger btn-sm" @click="deleteCategory(cat)">
                <i class="fa-solid fa-trash"></i> Supprimer
              </button>
            </div>
          </div>
        </div>
        <EmptyState v-if="categories.length === 0" message="Aucune catégorie correspondante." />
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import EmptyState from '../../components/EmptyState.vue';

const categories = ref([]);
const loading = ref(true);
const showModal = ref(false);
const form = ref({ id: null, name: '', description: '', color: '#1e40af', icon: '' });

const filters = ref({
  search: ''
});

const loadCategories = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/categories', { params: filters.value });
    categories.value = data.categories || [];
  } catch (e) {
  } finally { loading.value = false; }
};

const resetFilters = () => {
  filters.value.search = '';
};

onMounted(loadCategories);

let searchTimeout = null;
watch(filters, () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadCategories();
  }, 300);
}, { deep: true });

const openCreate = () => {
  form.value = { id: null, name: '', description: '', color: '#10b981', icon: '' };
  showModal.value = true;
};

const openEdit = (cat) => {
  form.value = { ...cat };
  showModal.value = true;
};

const saveCategory = async () => {
  try {
    if (form.value.id) {
      await axios.put(`/api/v1/admin/categories/${form.value.id}`, form.value);
    } else {
      await axios.post('/api/v1/admin/categories', form.value);
    }
    showModal.value = false;
    await loadCategories();
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la sauvegarde.');
  }
};

const deleteCategory = async (cat) => {
  if (confirm(`Supprimer la catégorie ${cat.name} ?`)) {
    try {
      await axios.delete(`/api/v1/admin/categories/${cat.id}`);
      await loadCategories();
    } catch (e) {
      alert(e.response?.data?.message || 'Erreur lors de la suppression.');
    }
  }
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.cat-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
@media (min-width: 768px) {
  .cat-grid { grid-template-columns: 1fr 1fr; }
}

.cat-color-pill { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 10px; box-shadow: 0 0 0 2px white, 0 0 0 3px rgba(0,0,0,0.05); }
.cat-desc-large { font-size: 13px; color: var(--gray-600); min-height: 48px; margin-bottom: 16px; line-height: 1.5; }
.cat-actions-footer { display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid var(--gray-50); padding-top: 14px; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
.modal-card { background: white; border-radius: var(--radius-lg); width: 100%; max-width: 480px; box-shadow: var(--shadow-lg); overflow: hidden; animation: modalIn 0.2s ease; }
@keyframes modalIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; color: white; }
.modal-header-indigo { background: var(--blue-700); }
.modal-title { font-size: 15px; font-weight: 600; }
.modal-body { padding: 24px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 16px; margin-top: 16px; border-top: 1px solid var(--gray-100); }

/* FILTER BAR */
.filter-bar { background: white; border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100); }
.main-search { position: relative; margin-bottom: 0px; }
.main-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gray-400); }
.input-inline { width: 100%; padding: 12px 12px 12px 42px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font-size: 14px; background: var(--gray-50); }
.input-inline:focus { border-color: var(--blue-500); outline: none; box-shadow: 0 0 0 3px var(--blue-50); background: white; }

.filter-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; margin-top: 0; }
.ml-auto { margin-left: auto; }
</style>

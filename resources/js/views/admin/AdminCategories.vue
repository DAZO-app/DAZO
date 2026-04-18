<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Catégories</div>
        <div class="page-subtitle">Organiser les décisions par thème</div>
      </div>
      <div class="page-actions">
        <button class="btn btn-primary btn-sm" @click="openCreate">+ Nouvelle catégorie</button>
      </div>
    </div>
    <div class="page-body">

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal-card">
          <div class="modal-header">
            <span class="modal-title">{{ form.id ? 'Éditer' : 'Nouvelle catégorie' }}</span>
            <button class="btn btn-ghost btn-icon" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCategory">
              <div class="form-group">
                <label class="label">Nom *</label>
                <input v-model="form.name" class="input" required>
              </div>
              <div class="form-group">
                <label class="label">Description</label>
                <textarea v-model="form.description" class="textarea" rows="2"></textarea>
              </div>
              <div class="form-group">
                <label class="label">Couleur (code HEX)</label>
                <div style="display:flex;gap:8px;align-items:center;">
                  <input type="color" v-model="form.color" style="width:32px;height:32px;padding:0;border:none;border-radius:4px;">
                  <input v-model="form.color" class="input" placeholder="#1e40af" style="flex:1;">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-ghost" @click="showModal = false">Annuler</button>
                <button type="submit" class="btn btn-primary">Sauvegarder</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else class="card">
        <div class="card-body">
          <div v-for="cat in categories" :key="cat.id" class="cat-row">
            <div class="cat-color" :style="{ background: cat.color || '#cccccc' }"></div>
            <div class="cat-info">
              <div class="cat-name">{{ cat.name }}</div>
              <div class="cat-desc">{{ cat.description }}</div>
            </div>
            <button class="btn btn-ghost btn-sm" @click="openEdit(cat)">✏️ Éditer</button>
            <button class="btn btn-ghost btn-sm text-red" @click="deleteCategory(cat)">🗑️</button>
          </div>
          <div v-if="categories.length === 0" class="text-sm text-muted text-center py-24">Aucune catégorie.</div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const categories = ref([]);
const loading = ref(true);

const showModal = ref(false);
const form = ref({ id: null, name: '', description: '', color: '#1e40af', icon: '' });

const loadCategories = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/categories');
    categories.value = data.categories || [];
  } catch (e) {
  } finally { loading.value = false; }
};

onMounted(loadCategories);

const openCreate = () => {
  form.value = { id: null, name: '', description: '', color: '#1e40af', icon: '' };
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
.cat-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--gray-100); }
.cat-row:last-child { border-bottom: none; }
.cat-color { width: 16px; height: 16px; border-radius: 4px; flex-shrink: 0; }
.cat-info { flex: 1; }
.cat-name { font-size: 13px; font-weight: 600; color: var(--gray-900); }
.cat-desc { font-size: 11px; color: var(--gray-500); }
.text-red { color: var(--red-600); }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
.modal-card { background: white; border-radius: var(--radius-lg); width: 100%; max-width: 480px; box-shadow: var(--shadow-lg); overflow: hidden; animation: modalIn 0.2s ease; }
@keyframes modalIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--gray-200); }
.modal-title { font-size: 15px; font-weight: 600; }
.modal-body { padding: 20px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; margin-top: 8px; border-top: 1px solid var(--gray-100); }
</style>

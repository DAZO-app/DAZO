<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card mb-32">
        <div class="hero-flex">
          <div>
             <nav class="breadcrumb mb-8" style="color:rgba(255,255,255,0.4)">
                <router-link to="/wiki" style="color:rgba(255,255,255,0.6)">Centre d'Aide</router-link>
                <i class="fa-solid fa-chevron-right mx-8"></i>
                <span>Administration</span>
            </nav>
            <div class="hero-title">Gestion du Wiki</div>
            <div class="hero-subtitle">Organisez vos articles par catégorie et gérez l'ordre d'affichage.</div>
          </div>
          <div class="hero-action flex gap-12">
            <button class="btn btn-ghost text-white" @click="saveOrder" :disabled="savingOrder">
               <i v-if="savingOrder" class="fa-solid fa-spinner fa-spin mr-8"></i>
               <i v-else class="fa-solid fa-save mr-8"></i>
               Enregistrer l'ordre
            </button>
            <button class="btn btn-secondary" @click="$router.push({ name: 'WikiCreate' })">
              <i class="fa-solid fa-plus mr-8"></i> Nouvel article
            </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center py-48">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500"></i>
      </div>

      <div v-else>
        <!-- DEBUG INFO IF EMPTY -->
        <div v-if="categories.length === 0 && standalonePages.length === 0" class="text-center py-48 bg-white rounded-xl border-2 border-dashed border-gray-200">
           <i class="fa-solid fa-folder-open text-4xl text-gray-200 mb-16"></i>
           <div class="text-gray-500 font-medium">Aucun contenu trouvé. Créez votre premier article !</div>
        </div>

        <!-- DRAGGABLE CATEGORIES -->
        <draggable 
          v-model="categories" 
          group="categories" 
          item-key="id"
          handle=".category-handle"
          @end="updateOrders"
          class="categories-list"
        >
          <template #item="{ element: category }">
            <div class="premium-card mb-24 category-block">
              <div class="pc-header pc-header-indigo p-16 flex items-center justify-between">
                <div class="flex items-center gap-12 flex-1">
                  <div class="category-handle cursor-move text-white/50 hover:text-white transition-colors">
                    <i class="fa-solid fa-grip-vertical"></i>
                  </div>
                  
                  <div v-if="editingCategory === category.id" class="flex items-center gap-8 flex-1 mr-24">
                    <input 
                      v-model="category.name" 
                      class="input input-sm bg-white/10 text-white border-white/20 focus:bg-white focus:text-gray-900"
                      @keyup.enter="saveCategoryName(category)"
                      @blur="saveCategoryName(category)"
                      ref="editInput"
                    >
                  </div>
                  <div v-else class="flex items-center gap-12 group cursor-pointer" @click="editingCategory = category.id">
                    <div class="pc-header-title">{{ category.name }}</div>
                    <i class="fa-solid fa-pen text-xxs opacity-0 group-hover:opacity-50 transition-opacity"></i>
                  </div>
                </div>

                <div class="flex items-center gap-8">
                  <span class="badge badge-white/10 text-white text-xxs px-8 py-2">{{ category.pages.length }} articles</span>
                  <button class="btn btn-icon btn-sm text-white/50 hover:text-white hover:bg-white/10" @click="deleteCategory(category)" title="Supprimer la catégorie">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </div>

              <div class="pc-body bg-gray-50/50 p-16">
                <draggable 
                  v-model="category.pages" 
                  group="pages" 
                  item-key="id"
                  @end="updateOrders"
                  class="pages-drop-zone min-h-32"
                >
                  <template #item="{ element: page }">
                    <div class="wiki-admin-row group">
                      <div class="flex items-center gap-12 flex-1">
                        <i class="fa-solid fa-grip-lines text-gray-300 group-hover:text-gray-400 cursor-move"></i>
                        <div class="flex-1 min-width-0">
                          <div class="font-semibold text-sm truncate">{{ page.title }}</div>
                          <div class="text-xxs text-muted">/wiki/{{ page.slug }}</div>
                        </div>
                      </div>
                      <div class="flex items-center gap-8">
                        <span v-if="!page.is_published" class="badge badge-orange text-xxs">Brouillon</span>
                        <div class="flex gap-4">
                          <button class="btn btn-ghost btn-xs btn-icon" @click="$router.push({ name: 'WikiEdit', params: { id: page.id } })">
                            <i class="fa-solid fa-pen"></i>
                          </button>
                          <button class="btn btn-ghost btn-xs btn-icon text-red" @click="deletePage(page)">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </template>
                </draggable>
                <div v-if="category.pages.length === 0" class="text-center py-16 text-muted italic text-xs">
                  Glissez un article ici pour l'ajouter à cette catégorie.
                </div>
              </div>
            </div>
          </template>
        </draggable>

        <!-- STANDALONE PAGES (No Category) -->
        <div v-if="standalonePages.length > 0 || categories.length > 0" class="premium-card mb-24 border-dashed border-gray-300 bg-transparent shadow-none">
          <div class="pc-header bg-gray-100 text-gray-600 p-16">
            <div class="pc-header-title text-sm uppercase letter-spacing-wide">Articles sans catégorie</div>
          </div>
          <div class="pc-body p-16">
             <draggable 
                v-model="standalonePages" 
                group="pages" 
                item-key="id"
                @end="updateOrders"
                class="pages-drop-zone min-h-32"
              >
                <template #item="{ element: page }">
                  <div class="wiki-admin-row group bg-white border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-12 flex-1">
                      <i class="fa-solid fa-grip-lines text-gray-300 group-hover:text-gray-400 cursor-move"></i>
                      <div class="flex-1 min-width-0">
                        <div class="font-semibold text-sm truncate">{{ page.title }}</div>
                        <div class="text-xxs text-muted">/wiki/{{ page.slug }}</div>
                      </div>
                    </div>
                    <div class="flex items-center gap-8">
                      <span v-if="!page.is_published" class="badge badge-orange text-xxs">Brouillon</span>
                      <div class="flex gap-4">
                        <button class="btn btn-ghost btn-xs btn-icon" @click="$router.push({ name: 'WikiEdit', params: { id: page.id } })">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-ghost btn-xs btn-icon text-red" @click="deletePage(page)">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </template>
              </draggable>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

// ... (lines 1-161 unchanged)
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';

const categories = ref([]);
const standalonePages = ref([]);
const loading = ref(true);
const savingOrder = ref(false);
const editingCategory = ref(null);

const fetchAll = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/wiki');
    categories.value = data.categories || [];
    standalonePages.value = data.standalone_pages || [];
    console.log('Admin Wiki Data:', data);
  } catch (err) {
    console.error('Admin Wiki load error', err);
  } finally {
    loading.value = false;
  }
};
// ...

onMounted(fetchAll);

const updateOrders = () => {
  // Logic to update orders in memory before saving if needed
};

const saveOrder = async () => {
  savingOrder.value = true;
  try {
    const payload = {
      categories: categories.value.map((cat, index) => ({
        id: cat.id,
        order: index,
        pages: cat.pages.map(p => ({ id: p.id }))
      })),
      standalone_pages: standalonePages.value.map(p => ({ id: p.id }))
    };
    await axios.post('/api/v1/admin/wiki/reorder', payload);
    // Optional: show toast
  } catch (err) {
    alert('Erreur lors de l\'enregistrement de l\'ordre.');
  } finally {
    savingOrder.value = false;
  }
};

const saveCategoryName = async (category) => {
  if (!category.name.trim()) return;
  editingCategory.value = null;
  try {
    await axios.put(`/api/v1/admin/wiki/categories/${category.id}`, { name: category.name });
  } catch (err) {
    alert('Erreur lors de la mise à jour du nom.');
    fetchAll(); // Reset
  }
};

const deleteCategory = async (category) => {
  if (!confirm(`Supprimer la catégorie "${category.name}" ? Les articles qu'elle contient deviendront "sans catégorie".`)) return;
  try {
    await axios.delete(`/api/v1/admin/wiki/categories/${category.id}`);
    fetchAll();
  } catch (err) {
    alert('Erreur lors de la suppression.');
  }
};

const deletePage = async (page) => {
  if (!confirm(`Supprimer l'article "${page.title}" ?`)) return;
  try {
    await axios.delete(`/api/v1/admin/wiki/${page.id}`);
    fetchAll();
  } catch (err) {
    alert('Erreur lors de la suppression.');
  }
};
</script>

<style scoped>
.categories-list { display: flex; flex-direction: column; gap: 8px; }
.category-block { transition: transform 0.2s, box-shadow 0.2s; }
.category-block.ghost { opacity: 0.5; background: var(--blue-50); }

.wiki-admin-row { 
  display: flex; 
  align-items: center; 
  gap: 16px; 
  padding: 10px 16px; 
  background: white; 
  border-radius: 8px; 
  margin-bottom: 8px; 
  transition: all 0.2s;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.wiki-admin-row:hover { transform: translateX(4px); border-color: var(--blue-200); }
.wiki-admin-row:last-child { margin-bottom: 0; }

.pages-drop-zone { min-height: 40px; }

.mx-8 { margin-left: 8px; margin-right: 8px; }
.text-red { color: var(--red-600); }
.text-xxs { font-size: 10px; }
.text-white\/50 { color: rgba(255, 255, 255, 0.5); }
.text-white\/20 { color: rgba(255, 255, 255, 0.2); }
.bg-white\/10 { background: rgba(255, 255, 255, 0.1); }
.border-white\/20 { border-color: rgba(255, 255, 255, 0.2); }

.cursor-move { cursor: move; }
</style>

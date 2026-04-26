<template>
  <main class="main">
    <div class="page-body">
      <!-- HEADER PREMIUM (Blue Title) -->
      <div class="premium-card mb-32 overflow-hidden shadow-lg border-none">
        <div class="pc-header pc-header-blue py-24 px-32">
          <div class="pc-header-icon shadow-none" style="width: 48px; height: 48px; font-size: 20px; background: rgba(255,255,255,0.2); color: white; border: none;">
            <i class="fa-solid fa-pen-nib"></i>
          </div>
          <div class="pc-header-content flex-1">
             <nav class="breadcrumb-mini mb-6">
                <router-link to="/admin/wiki" class="text-white hover:underline opacity-80">Gestion Wiki</router-link>
                <i class="fa-solid fa-chevron-right mx-8 opacity-50 text-white"></i>
                <span class="text-white opacity-90 font-semibold">{{ isEdit ? 'Édition' : 'Nouvel article' }}</span>
             </nav>
             <h1 class="text-2xl md:text-3xl font-black mb-0 text-white">
               {{ isEdit ? 'Modifier l\'article' : 'Rédiger une aide' }}
             </h1>
          </div>
          <div class="pc-header-actions flex gap-12">
            <button class="btn btn-ghost text-white opacity-80 hover:opacity-100 hover:bg-white/10" @click="$router.push({ name: 'AdminWiki' })">
              <i class="fa-solid fa-times mr-6"></i> Annuler
            </button>
            <button class="btn btn-secondary shadow-lg px-24 py-10" @click="savePage" :disabled="saving" style="font-size: 14px;">
               <i v-if="saving" class="fa-solid fa-spinner fa-spin mr-8"></i>
               <i v-else class="fa-solid fa-check-circle mr-6"></i>
               {{ isEdit ? 'Enregistrer' : 'Créer l\'article' }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="loading && isEdit" class="text-center py-48">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500"></i>
      </div>

      <div v-else class="wiki-editor-layout">
         <!-- LEFT: MAIN CONTENT -->
         <div class="editor-main">
            <div class="premium-card">
               <div class="pc-body p-32">
                  <div class="form-group mb-32">
                    <label class="label text-blue-900 font-black text-xs uppercase letter-spacing-wide mb-12 flex items-center">
                      <i class="fa-solid fa-font mr-8 opacity-50"></i> Titre de l'article
                    </label>
                    <input v-model="form.title" class="input input-lg border-blue-100 focus:border-blue-400 focus:ring-4 focus:ring-blue-100" placeholder="Ex: Comment voter une décision ?" required>
                  </div>

                  <div class="form-group">
                    <label class="label text-blue-900 font-black text-xs uppercase letter-spacing-wide mb-12 flex items-center">
                      <i class="fa-solid fa-align-left mr-8 opacity-50"></i> Contenu de l'aide
                    </label>
                    <RichTextEditor v-model="form.content" placeholder="Rédigez l'article ici..." />
                  </div>
               </div>
            </div>
         </div>

         <!-- RIGHT: SETTINGS -->
         <div class="editor-sidebar">
            <div class="premium-card">
               <div class="pc-header pc-header-blue" style="padding: 16px 24px;">
                  <div class="pc-header-icon" style="width: 28px; height: 28px; font-size: 13px;"><i class="fa-solid fa-sliders"></i></div>
                  <div class="pc-header-title text-sm">Paramètres</div>
               </div>
               <div class="pc-body p-24">
                  <div class="form-group mb-24 relative">
                    <label class="label text-xs uppercase letter-spacing-wide mb-8">Catégorie</label>
                    <div class="relative">
                      <input 
                        v-model="form.category_name" 
                        @input="handleCategoryInput"
                        @focus="showSuggestions = true"
                        class="input input-sm bg-white" 
                        placeholder="Ex: Gouvernance"
                        autocomplete="off"
                      >
                      <Transition name="fade">
                        <div v-if="showSuggestions && suggestions.length > 0" class="suggestions-dropdown shadow-xl border border-gray-100 rounded-lg absolute z-50 w-full bg-white mt-4 max-h-200 overflow-y-auto">
                          <div 
                            v-for="cat in suggestions" 
                            :key="cat.id" 
                            @click="selectCategory(cat)"
                            class="suggestion-item p-12 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition-colors border-b border-gray-50 last:border-0"
                          >
                            <span class="text-sm font-semibold text-gray-700">{{ cat.name }}</span>
                            <i class="fa-solid fa-plus-circle text-blue-300 text-xs"></i>
                          </div>
                        </div>
                      </Transition>
                    </div>
                    <div class="text-xxs text-muted mt-6">Saisissez un nom ou choisissez une catégorie existante.</div>
                  </div>

                  <div class="form-group mb-24">
                    <label class="label text-xs uppercase letter-spacing-wide mb-8">Slug (URL)</label>
                    <input v-model="form.slug" class="input input-sm bg-white" placeholder="auto-genere-si-vide">
                    <div class="text-xxs text-muted mt-6">Identifiant unique dans l'URL (/wiki/...)</div>
                  </div>

                  <div class="divider-light mb-24"></div>

                  <div class="flex items-center justify-between mb-8">
                     <label class="text-sm font-bold cursor-pointer text-gray-700" @click="form.is_published = !form.is_published">
                        Publier l'article
                     </label>
                     <div class="toggle" :class="{ active: form.is_published }" @click="form.is_published = !form.is_published">
                        <div class="toggle-dot"></div>
                     </div>
                  </div>
                  <div class="text-xs text-muted">L'article sera visible par tous les utilisateurs.</div>
               </div>
            </div>

            <div v-if="isEdit" class="mt-24">
               <button class="btn btn-danger btn-sm w-full py-12 shadow-sm border-none" @click="deletePage">
                  <i class="fa-solid fa-trash mr-8"></i> Supprimer définitivement
               </button>
            </div>
         </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import RichTextEditor from '../../components/RichTextEditor.vue';

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => !!route.params.id);
const loading = ref(false);
const saving = ref(false);
const suggestions = ref([]);
const showSuggestions = ref(false);
let suggestionTimeout = null;

const form = ref({
  title: '',
  content: '',
  category_name: '',
  wiki_category_id: null,
  slug: '',
  is_published: true
});

const fetchPage = async () => {
  if (!isEdit.value) return;
  loading.value = true;
  try {
    const { data } = await axios.get(`/api/v1/admin/wiki/${route.params.id}`);
    form.value = { 
      ...data.page,
      category_name: data.page.category?.name || ''
    };
  } catch (err) {
    alert('Erreur lors du chargement de l\'article.');
    router.push({ name: 'AdminWiki' });
  } finally {
    loading.value = false;
  }
};

const handleCategoryInput = () => {
  if (suggestionTimeout) clearTimeout(suggestionTimeout);
  
  if (!form.value.category_name) {
    suggestions.value = [];
    form.value.wiki_category_id = null;
    return;
  }

  showSuggestions.value = true;

  suggestionTimeout = setTimeout(async () => {
    try {
      const { data } = await axios.get('/api/v1/admin/wiki/categories/search', { params: { q: form.value.category_name } });
      suggestions.value = data.categories;
    } catch (err) {
      console.error('Suggestion error', err);
    }
  }, 300);
};

const selectCategory = (cat) => {
  form.value.category_name = cat.name;
  form.value.wiki_category_id = cat.id;
  suggestions.value = [];
  showSuggestions.value = false;
};

onMounted(fetchPage);

const savePage = async () => {
  if (!form.value.title || !form.value.content) {
    alert('Veuillez remplir le titre et le contenu.');
    return;
  }

  saving.value = true;
  try {
    if (isEdit.value) {
      await axios.put(`/api/v1/admin/wiki/${route.params.id}`, form.value);
    } else {
      await axios.post('/api/v1/admin/wiki', form.value);
    }
    router.push({ name: 'AdminWiki' });
  } catch (err) {
    alert(err.response?.data?.message || 'Erreur lors de l\'enregistrement.');
  } finally {
    saving.value = false;
  }
};

const deletePage = async () => {
  if (!confirm('Souhaitez-vous vraiment supprimer cet article définitivement ?')) return;
  try {
    await axios.delete(`/api/v1/admin/wiki/${route.params.id}`);
    router.push({ name: 'AdminWiki' });
  } catch (err) {
    alert('Erreur lors de la suppression.');
  }
};
</script>

<style scoped>
.breadcrumb-mini { display: flex; align-items: center; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
.breadcrumb-mini a { color: white; opacity: 0.8; text-decoration: none; }
.breadcrumb-mini a:hover { opacity: 1; text-decoration: underline; }

.wiki-editor-layout { display: flex; gap: 32px; align-items: flex-start; }
.editor-main { flex: 1; min-width: 0; }
.editor-sidebar { width: 320px; flex-shrink: 0; position: sticky; top: 16px; }

.input-lg { font-size: 24px; font-weight: 800; padding: 20px 24px; border-radius: 12px; }
.label { letter-spacing: 0.1em; }
.text-xxs { font-size: 10px; }

.divider-light { height: 1px; background: var(--gray-100); }

.toggle { width: 44px; height: 22px; background: var(--gray-200); border-radius: 11px; position: relative; cursor: pointer; transition: background 0.2s; }
.toggle.active { background: var(--teal-500); }
.toggle-dot { width: 18px; height: 18px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.toggle.active .toggle-dot { transform: translateX(22px); }

.px-32 { padding-left: 32px; padding-right: 32px; }
.py-20 { padding-top: 20px; padding-bottom: 20px; }
.shadow-none { box-shadow: none; }
.font-black { font-weight: 900; }

.suggestions-dropdown { border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-xl); top: 100%; left: 0; right: 0; background: white; border: 1px solid var(--gray-100); }
.suggestion-item { padding: 12px 16px; transition: all 0.2s; }
.suggestion-item:hover { background: var(--blue-50); padding-left: 20px; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.input { width: 100%; padding: 12px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); background: var(--gray-50); font-size: 14px; transition: all 0.2s; }
.input:focus { border-color: var(--blue-500); background: white; box-shadow: 0 0 0 3px var(--blue-50); outline: none; }
</style>

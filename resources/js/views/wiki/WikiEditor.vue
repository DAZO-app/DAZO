<template>
  <main class="main">
    <div class="page-body">
      <!-- HEADER PREMIUM -->
      <div class="premium-card mb-32 overflow-hidden shadow-lg border-none">
        <div class="pc-header pc-header-blue py-20 px-32">
          <div class="pc-header-icon shadow-none bg-white/20" style="width: 42px; height: 42px; font-size: 18px;">
            <i class="fa-solid fa-pen-nib"></i>
          </div>
          <div class="pc-header-content flex-1">
             <nav class="breadcrumb-mini mb-4">
                <router-link to="/admin/wiki">Gestion Wiki</router-link>
                <i class="fa-solid fa-chevron-right mx-6 opacity-30"></i>
                <span class="opacity-60">{{ isEdit ? 'Édition' : 'Nouvel article' }}</span>
             </nav>
             <h1 class="pc-header-title text-2xl md:text-3xl font-black mb-0">
               {{ isEdit ? 'Modifier l\'article' : 'Rédiger une aide' }}
             </h1>
          </div>
          <div class="pc-header-actions flex gap-12">
            <button class="btn btn-ghost text-white hover:bg-white/10" @click="$router.push({ name: 'AdminWiki' })">
              <i class="fa-solid fa-times mr-6"></i> Annuler
            </button>
            <button class="btn btn-secondary shadow-lg px-20" @click="savePage" :disabled="saving">
               <i v-if="saving" class="fa-solid fa-spinner fa-spin mr-8"></i>
               <i v-else class="fa-solid fa-check-circle mr-6"></i>
               {{ isEdit ? 'Enregistrer' : 'Créer' }}
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
                  <div class="form-group mb-24">
                    <label class="label text-xs uppercase letter-spacing-wide mb-8">Catégorie</label>
                    <input v-model="form.category" class="input input-sm bg-white" placeholder="Ex: Gouvernance">
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

const form = ref({
  title: '',
  content: '',
  category: '',
  slug: '',
  is_published: true
});

const fetchPage = async () => {
  if (!isEdit.value) return;
  loading.value = true;
  try {
    const { data } = await axios.get(`/api/v1/admin/wiki/${route.params.id}`);
    form.value = { ...data.page };
  } catch (err) {
    alert('Erreur lors du chargement de l\'article.');
    router.push({ name: 'AdminWiki' });
  } finally {
    loading.value = false;
  }
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
</style>

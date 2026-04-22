<template>
  <main class="main">
    <div v-if="loading" class="page-body text-center py-48">
      <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500"></i>
      <div class="mt-12 text-muted">Chargement de l'article...</div>
    </div>

    <div v-else-if="page" class="page-body">
      <!-- HEADER PREMIUM (Title block) -->
      <div class="premium-card mb-32 overflow-hidden shadow-lg border-none">
        <div class="pc-header pc-header-blue py-20 px-32">
          <div class="pc-header-icon shadow-none bg-white/20" style="width: 42px; height: 42px; font-size: 18px;">
            <i class="fa-solid fa-file-invoice"></i>
          </div>
          <div class="pc-header-content flex-1">
             <nav class="breadcrumb-mini mb-4">
                <router-link to="/wiki">Centre d'aide</router-link>
                <i class="fa-solid fa-chevron-right mx-6 opacity-30"></i>
                <span class="opacity-60">{{ page.category || 'Général' }}</span>
             </nav>
             <h1 class="pc-header-title text-2xl md:text-3xl font-black mb-0">{{ page.title }}</h1>
          </div>
          <div v-if="isAdmin" class="pc-header-actions">
            <button class="btn btn-secondary btn-sm" @click="$router.push({ name: 'WikiEdit', params: { id: page.id } })">
              <i class="fa-solid fa-pen-to-square mr-6"></i> Modifier
            </button>
          </div>
        </div>
        <div class="pc-footer bg-blue-50/50 px-32 py-10 border-t border-blue-100 flex items-center justify-between">
           <div class="text-xs text-blue-600 font-medium">
             <i class="fa-regular fa-clock mr-6"></i> Dernière mise à jour le {{ formatDate(page.updated_at) }}
           </div>
           <div v-if="!page.is_published" class="badge badge-orange text-xxs">BROUILLON</div>
        </div>
      </div>

      <!-- MAIN CONTENT LAYOUT -->
      <div class="wiki-grid-layout">
        <!-- COL MAIN: ARTICLE CONTENT -->
        <div class="col-main">
          <div class="premium-card">
            <!-- ADDED EXTRA PADDING AND MARGINS -->
            <div class="pc-body p-40 md:p-56 wiki-prose" v-html="page.content"></div>
            
            <div class="pc-footer p-24 border-t border-gray-50 bg-gray-50/50 text-center">
              <div class="text-sm text-gray-500 mb-16">Cet article vous a-t-il aidé ?</div>
              <div class="flex justify-center gap-12">
                  <button class="btn btn-ghost btn-sm border border-gray-200 bg-white hover:bg-green-50 hover:text-green-600 hover:border-green-200"><i class="fa-regular fa-thumbs-up mr-6"></i> Oui</button>
                  <button class="btn btn-ghost btn-sm border border-gray-200 bg-white hover:bg-red-50 hover:text-red-600 hover:border-red-200"><i class="fa-regular fa-thumbs-down mr-6"></i> Non</button>
              </div>
            </div>
          </div>
        </div>

        <!-- COL SIDE: RELATED & NAVIGATION -->
        <div class="col-side">
          <div class="sidebar-sticky">
            <!-- CATEGORY NAVIGATION -->
            <div class="premium-card mb-24">
              <div class="pc-header pc-header-blue py-12 px-20">
                <div class="pc-header-icon" style="width: 28px; height: 28px; font-size: 13px;"><i class="fa-solid fa-layer-group"></i></div>
                <div class="pc-header-title text-sm">{{ page.category || 'Général' }}</div>
              </div>
              <div class="pc-body p-8">
                 <div class="side-nav-list">
                    <router-link 
                      v-for="item in categoryPages" 
                      :key="item.id" 
                      :to="{ name: 'WikiDetail', params: { slug: item.slug } }"
                      class="side-nav-item"
                      :class="{ active: item.id === page.id }"
                    >
                      <i class="fa-solid fa-file-lines mr-10 text-xs text-blue-400"></i>
                      <span class="truncate">{{ item.title }}</span>
                    </router-link>
                 </div>
              </div>
            </div>

            <!-- QUICK ACTIONS / HELP (CENTERED) -->
            <div class="premium-card bg-indigo-900 text-white overflow-hidden text-center">
               <div class="p-32 relative">
                  <i class="fa-solid fa-question-circle absolute opacity-5" style="font-size: 100px; left: 50%; top: 50%; transform: translate(-50%, -50%);"></i>
                  <div class="font-black text-lg mb-8">Besoin d'aide ?</div>
                  <div class="text-sm text-indigo-100 mb-20 leading-relaxed">
                    Vous ne trouvez pas de réponse à votre question ? Contactez un administrateur.
                  </div>
                  <button @click="showContactModal = true" class="btn btn-secondary w-full py-12 shadow-xl hover:-translate-y-1 transition-transform">
                    <i class="fa-solid fa-envelope mr-8"></i> Contacter un administrateur
                  </button>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="!loading" class="page-body">
       <EmptyState message="Cet article n'existe pas ou n'est plus disponible." />
       <div class="text-center mt-24">
         <router-link to="/wiki" class="btn btn-primary">Retour au centre d'aide</router-link>
       </div>
    </div>

    <!-- MODAL -->
    <ContactAdminModal 
      :is-open="showContactModal" 
      @close="showContactModal = false"
    />
  </main>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';
import EmptyState from '../../components/EmptyState.vue';
import ContactAdminModal from '../../components/modals/ContactAdminModal.vue';

const route = useRoute();
const authStore = useAuthStore();
const isAdmin = computed(() => ['admin', 'superadmin'].includes(authStore.user?.role));

const page = ref(null);
const categoryPages = ref([]);
const loading = ref(true);
const showContactModal = ref(false);

const fetchData = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get(`/api/v1/wiki/${route.params.slug}`);
    page.value = data.page;

    const { data: catData } = await axios.get('/api/v1/wiki');
    categoryPages.value = catData.pages.filter(p => (p.category || 'Général') === (page.value.category || 'Général'));
  } catch (err) {
    console.error('Wiki Detail load error', err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);
watch(() => route.params.slug, fetchData);

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<style scoped>
.breadcrumb-mini { display: flex; align-items: center; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
.breadcrumb-mini a { color: white; opacity: 0.8; text-decoration: none; }
.breadcrumb-mini a:hover { opacity: 1; }

.wiki-grid-layout { display: grid; grid-template-columns: 1fr; gap: 32px; align-items: flex-start; }
@media (min-width: 1024px) { .wiki-grid-layout { grid-template-columns: 1fr 340px; } }

.sidebar-sticky { position: sticky; top: 16px; }

.wiki-prose {
  line-height: 1.8;
  color: var(--gray-800);
  font-size: 17px;
}

/* TYPOGRAPHY REFINEMENT */
:deep(.wiki-prose h1) { font-size: 32px; font-weight: 900; color: var(--blue-900); margin: 48px 0 24px; letter-spacing: -0.02em; }
:deep(.wiki-prose h2) { font-size: 24px; font-weight: 800; color: var(--blue-800); margin: 40px 0 20px; padding-bottom: 12px; border-bottom: 3px solid var(--blue-50); }
:deep(.wiki-prose h3) { font-size: 20px; font-weight: 700; color: var(--gray-900); margin: 32px 0 16px; }
:deep(.wiki-prose p) { margin-bottom: 24px; }
:deep(.wiki-prose strong) { color: var(--gray-900); font-weight: 800; }
:deep(.wiki-prose blockquote) { 
  margin: 40px 0; 
  padding: 32px; 
  background: var(--blue-50)/50; 
  border-left: 6px solid var(--blue-500); 
  border-radius: 4px 16px 16px 4px;
  font-style: italic;
  font-size: 1.1em;
  color: var(--blue-900);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}

/* SIDE NAV DESIGN */
.side-nav-list { display: flex; flex-direction: column; }
.side-nav-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  text-decoration: none;
  font-size: 13.5px;
  color: var(--gray-600);
  border-radius: var(--radius-md);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.side-nav-item:hover { background: var(--blue-50); color: var(--blue-700); padding-left: 20px; }
.side-nav-item.active { background: var(--blue-600); color: white; font-weight: 700; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
.side-nav-item.active i { color: white; opacity: 0.8; }

.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mx-6 { margin-left: 6px; margin-right: 6px; }
.px-32 { padding-left: 32px; padding-right: 32px; }
.p-40 { padding: 40px; }
@media (min-width: 768px) { .p-56 { padding: 56px; } }
.py-20 { padding-top: 20px; padding-bottom: 20px; }
.py-10 { padding-top: 10px; padding-bottom: 10px; }
.text-xxs { font-size: 10px; }
.font-black { font-weight: 900; }
.shadow-none { box-shadow: none; }
</style>

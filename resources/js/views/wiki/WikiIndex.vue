<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card mb-32">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Centre d'Aide & Wiki</div>
            <div class="hero-subtitle">Retrouvez toutes les ressources et guides pour maîtriser DAZO et piloter vos décisions.</div>
          </div>
          <div v-if="isAdmin" class="hero-action">
            <button class="btn btn-secondary" @click="$router.push({ name: 'AdminWiki' })">
              <i class="fa-solid fa-gears"></i> Gérer le Wiki
            </button>
          </div>
        </div>
      </div>

      <!-- SEARCH BAR -->
      <div class="premium-card mb-32">
        <div class="pc-header pc-header-indigo" style="padding: 16px 24px;">
          <div class="pc-header-icon" style="width: 32px; height: 32px; font-size: 14px;"><i class="fa-solid fa-magnifying-glass"></i></div>
          <div class="pc-header-content">
            <div class="pc-header-title">Recherche de guides</div>
            <div class="pc-header-sub">Tapez un mot-clé pour trouver un article spécifique.</div>
          </div>
        </div>
        <div class="pc-body p-24">
          <div class="search-input-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input 
              v-model="searchQuery" 
              @input="handleSearch"
              class="input pl-32" 
              placeholder="Ex: Élection sans candidat, objection, cercles..."
            >
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center py-48">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500"></i>
        <div class="mt-8 text-muted">Chargement de la connaissance...</div>
      </div>

      <div v-else>
        <!-- SEARCH RESULTS -->
        <div v-if="searchQuery.length > 1" class="results-grid">
           <div v-for="page in pages" :key="page.id" class="premium-card hover-lift" @click="$router.push({ name: 'WikiDetail', params: { slug: page.slug } })">
              <div class="pc-header pc-header-blue" style="padding: 12px 16px;">
                <div class="pc-header-icon" style="width: 28px; height: 28px; font-size: 12px;"><i class="fa-solid fa-file-lines"></i></div>
                <div class="pc-header-title" style="font-size: 14px;">{{ page.title }}</div>
              </div>
              <div class="pc-body p-16">
                 <div class="text-xs text-muted mb-4 uppercase">{{ page.category?.name || 'Général' }}</div>
                 <div class="text-sm line-clamp-2" v-html="excerpt(page.content)"></div>
              </div>
           </div>
           <EmptyState v-if="pages.length === 0" message="Désolé, nous n'avons aucun guide correspondant à cette recherche." />
        </div>

        <!-- THEMATIC NAVIGATION -->
        <div v-else class="wiki-categories-wrap">
          <div v-for="category in categories" :key="category.id" class="premium-card mb-24 overflow-hidden">
            <div 
              class="pc-header pc-header-blue cursor-pointer select-none flex items-center justify-between" 
              style="padding: 16px 24px;"
              @click="toggleCategory(category.id)"
            >
              <div class="flex items-center gap-12">
                <div class="pc-header-icon" style="width: 32px; height: 32px; font-size: 14px; background: rgba(255,255,255,0.2);"><i class="fa-solid fa-folder-open"></i></div>
                <div class="pc-header-content">
                  <div class="pc-header-title">{{ category.name }}</div>
                  <div class="pc-header-sub text-white opacity-80" style="font-size: 11px;">{{ category.pages.length }} article(s) disponible(s)</div>
                </div>
              </div>
              <i class="fa-solid" :class="expandedCategories.includes(category.id) ? 'fa-chevron-up' : 'fa-chevron-down'" style="font-size: 14px; opacity: 0.6;"></i>
            </div>
            
            <div v-if="expandedCategories.includes(category.id)" class="pc-body bg-gray-50/50 p-24">
              <div class="wiki-grid">
                <router-link 
                  v-for="page in category.pages" 
                  :key="page.id" 
                  :to="{ name: 'WikiDetail', params: { slug: page.slug } }"
                  class="wiki-card bg-white"
                >
                  <div class="wiki-card-icon">
                    <i class="fa-solid fa-file-lines"></i>
                  </div>
                  <div class="wiki-card-content">
                    <div class="wiki-card-title">{{ page.title }}</div>
                    <div class="wiki-card-desc line-clamp-1" v-html="excerpt(page.content, 60)"></div>
                  </div>
                  <i class="fa-solid fa-chevron-right wiki-card-arrow"></i>
                </router-link>
              </div>
            </div>
          </div>

          <!-- STANDALONE PAGES -->
          <div v-if="standalonePages.length > 0" class="mb-48 mt-32">
            <h3 class="section-title mb-16">Autres guides</h3>
            <div class="wiki-grid">
              <router-link 
                v-for="page in standalonePages" 
                :key="page.id" 
                :to="{ name: 'WikiDetail', params: { slug: page.slug } }"
                class="wiki-card"
              >
                <div class="wiki-card-icon">
                  <i class="fa-solid fa-book"></i>
                </div>
                <div class="wiki-card-content">
                  <div class="wiki-card-title">{{ page.title }}</div>
                  <div class="wiki-card-desc line-clamp-1" v-html="excerpt(page.content, 60)"></div>
                </div>
                <i class="fa-solid fa-chevron-right wiki-card-arrow"></i>
              </router-link>
            </div>
          </div>

          <EmptyState v-if="categories.length === 0 && standalonePages.length === 0" message="Le centre d'aide est en cours de rédaction." />
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';
import EmptyState from '../../components/EmptyState.vue';

const authStore = useAuthStore();
const isAdmin = computed(() => ['admin', 'superadmin'].includes(authStore.user?.role));

const pages = ref([]);
const categories = ref([]);
const standalonePages = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const expandedCategories = ref([]);
let searchTimeout = null;

const fetchPages = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/wiki', { params: { search: searchQuery.value } });
    console.log('Wiki API response:', data);
    
    if (searchQuery.value && searchQuery.value.length > 1) {
      pages.value = data.pages || [];
    } else {
      categories.value = data.categories || [];
      standalonePages.value = data.standalone_pages || [];
      
      // Open first category by default if none are expanded
      if (categories.value.length > 0 && expandedCategories.value.length === 0) {
        expandedCategories.value.push(categories.value[0].id);
      }
    }
  } catch (err) {
    console.error('Wiki load error', err);
  } finally {
    loading.value = false;
  }
};

const toggleCategory = (id) => {
  const index = expandedCategories.value.indexOf(id);
  if (index > -1) {
    expandedCategories.value.splice(index, 1);
  } else {
    expandedCategories.value.push(id);
  }
};

const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(fetchPages, 300);
};

onMounted(fetchPages);

const excerpt = (content, limit = 100) => {
  if (!content) return '';
  const plainText = content.replace(/<[^>]*>?/gm, '');
  if (plainText.length <= limit) return plainText;
  return plainText.substring(0, limit) + '...';
};
</script>

<style scoped>
.collapse-enter-active, .collapse-leave-active { transition: all 0.3s ease; max-height: 1000px; opacity: 1; }
.collapse-enter-from, .collapse-leave-to { max-height: 0; opacity: 0; overflow: hidden; }

.section-title { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--blue-700); border-bottom: 3px solid var(--blue-100); padding-bottom: 8px; display: inline-block; }

.wiki-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }

.wiki-card { display: flex; align-items: center; gap: 16px; padding: 16px; background: white; border: 1px solid var(--gray-100); border-radius: var(--radius-lg); text-decoration: none; transition: all 0.2s; box-shadow: var(--shadow-sm); }
.wiki-card:hover { border-color: var(--blue-300); box-shadow: var(--shadow-md); transform: translateY(-2px); }

.wiki-card-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.wiki-card-content { flex: 1; min-width: 0; }
.wiki-card-title { font-size: 15px; font-weight: 600; color: var(--gray-900); margin-bottom: 2px; }
.wiki-card-desc { font-size: 12px; color: var(--gray-500); }
.wiki-card-arrow { color: var(--gray-300); font-size: 12px; }

.hover-lift { cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
.hover-lift:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }

.results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }

.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.search-input-wrap { position: relative; }
.search-input-wrap .pl-32 { padding-left: 36px; }
.search-input-wrap .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--gray-400); font-size: 14px; }

.input { width: 100%; padding: 12px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); background: var(--gray-50); font-size: 14px; transition: all 0.2s; }
.input:focus { border-color: var(--blue-500); background: white; box-shadow: 0 0 0 3px var(--blue-50); outline: none; }
</style>

<template>
  <div class="public-front dazo-public-detail">
    <div class="public-container-wide pb-48 pt-32">

      <div v-if="loading" class="text-center py-48 text-muted dazo-public-loading">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement de la page...</p>
      </div>

      <div v-else-if="!pageTitle" class="empty-state dazo-public-empty">
        <div class="empty-icon"><i class="fa-solid fa-file-circle-xmark"></i></div>
        <h3>Page introuvable</h3>
        <p class="text-muted">La page que vous recherchez n'existe pas ou n'est plus accessible.</p>
        <router-link to="/front" class="btn btn-primary mt-16">Retour à l'accueil</router-link>
      </div>

      <div v-else class="decision-detail-card dazo-public-card-detail" style="background: white; border-radius: 16px; border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); overflow: hidden;">
        
        <div class="detail-header-content dazo-public-detail-header" style="padding: 32px 40px 24px; border-bottom: 1px solid var(--gray-100);">
          <h1 class="article-title dazo-public-detail-title" style="font-size: 30px; font-weight: 800; color: var(--gray-900); margin: 0;">{{ pageTitle }}</h1>
        </div>

        <div class="detail-body dazo-public-detail-body" style="padding: 32px 40px;">
          <div class="article-html-content dazo-public-content prose prose-blue max-w-none" v-html="pageContent"></div>
        </div>

        <div class="card-nav-footer dazo-public-detail-footer" style="padding: 16px 24px; background: var(--gray-50); border-top: 1px solid var(--gray-200); display: flex; justify-content: center;">
          <router-link to="/front" class="btn btn-white shadow-sm" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 10px 20px; border-radius: 12px; border: 1px solid var(--gray-200); color: var(--gray-700);">
            <i class="fa-solid fa-arrow-left"></i> Retour
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useConfigStore } from '../../stores/config';

const route = useRoute();
const configStore = useConfigStore();
const loading = ref(true);
const pageTitle = ref('');
const pageContent = ref('');

const loadPage = () => {
  loading.value = true;
  const slug = route.params.slug;
  
  // Find which page key corresponds to this slug
  let foundKey = null;
  for (const key of ['legal', 'privacy', 'terms']) {
    if (configStore.config['page_' + key + '_slug'] === slug) {
      foundKey = key;
      break;
    }
  }

  if (foundKey && configStore.config['page_' + foundKey + '_enabled'] === 'true') {
    pageTitle.value = configStore.config['page_' + foundKey + '_title'];
    pageContent.value = configStore.config['page_' + foundKey + '_content'];
  } else {
    pageTitle.value = '';
    pageContent.value = '';
  }
  loading.value = false;
};

onMounted(loadPage);
watch(() => route.params.slug, loadPage);
</script>

<style scoped>
.page-card {
  min-height: 600px;
}
.page-content :deep(h2) {
  margin-top: 2rem;
  margin-bottom: 1rem;
  font-weight: 700;
  font-size: 1.5rem;
  color: var(--gray-800);
}
.page-content :deep(p) {
  margin-bottom: 1.25rem;
  line-height: 1.7;
  color: var(--gray-700);
}
</style>

<template>
  <div class="public-page dazo-public-page">
    <div class="public-container py-48">
      <div v-if="loading" class="text-center py-48">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x text-blue-500 mb-16"></i>
        <p class="text-muted">Chargement de la page...</p>
      </div>

      <div v-else-if="!pageTitle" class="text-center py-48">
        <div class="empty-icon text-gray-300 mb-24"><i class="fa-solid fa-file-circle-xmark fa-4x"></i></div>
        <h2 class="text-2xl font-bold text-gray-800 mb-16">Page non trouvée</h2>
        <p class="text-muted mb-24">La page que vous recherchez n'existe pas ou n'est plus accessible.</p>
        <router-link to="/public" class="btn btn-primary">Retour à l'accueil</router-link>
      </div>

      <div v-else class="page-card bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="page-header bg-gradient-to-r from-blue-700 to-blue-600 p-32 text-white">
          <h1 class="text-3xl font-extrabold">{{ pageTitle }}</h1>
        </div>
        <div class="page-content p-32 prose prose-blue max-w-none" v-html="pageContent"></div>
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

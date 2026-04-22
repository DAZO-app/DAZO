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
            <div class="hero-subtitle">Créez et organisez les articles d'aide pour vos utilisateurs.</div>
          </div>
          <div class="hero-action">
            <button class="btn btn-secondary" @click="$router.push({ name: 'WikiCreate' })">
              <i class="fa-solid fa-plus"></i> Nouvel article
            </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center py-48">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500"></i>
      </div>

      <div v-else class="premium-card">
        <div class="pc-header pc-header-indigo" style="padding: 16px 24px;">
           <div class="pc-header-icon" style="width: 32px; height: 32px; font-size: 14px;"><i class="fa-solid fa-list-ul"></i></div>
           <div class="pc-header-content">
             <div class="pc-header-title">Tous les articles</div>
             <div class="pc-header-sub">{{ pages.length }} article(s) enregistré(s)</div>
           </div>
        </div>
        <div class="pc-body">
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Catégorie</th>
                  <th>Statut</th>
                  <th>Dernière modif.</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="page in pages" :key="page.id">
                  <td class="font-semibold">{{ page.title }} <br> <span class="text-xs text-muted">/wiki/{{ page.slug }}</span></td>
                  <td><span class="badge badge-blue">{{ page.category || 'Général' }}</span></td>
                  <td>
                    <span v-if="page.is_published" class="badge badge-teal">Publié</span>
                    <span v-else class="badge badge-orange">Brouillon</span>
                  </td>
                  <td class="text-sm">{{ formatDate(page.updated_at) }}</td>
                  <td class="text-right">
                    <div class="flex justify-end gap-8">
                       <button class="btn btn-ghost btn-sm btn-icon" @click="$router.push({ name: 'WikiDetail', params: { slug: page.slug } })" title="Voir">
                         <i class="fa-solid fa-eye"></i>
                       </button>
                       <button class="btn btn-ghost btn-sm btn-icon" @click="$router.push({ name: 'WikiEdit', params: { id: page.id } })" title="Éditer">
                         <i class="fa-solid fa-pen"></i>
                       </button>
                       <button class="btn btn-ghost btn-sm btn-icon text-red" @click="deletePage(page)" title="Supprimer">
                         <i class="fa-solid fa-trash"></i>
                       </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="pages.length === 0">
                  <td colspan="5" class="text-center py-48 text-muted italic">Aucun article wiki pour le moment.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const pages = ref([]);
const loading = ref(true);

const fetchPages = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/wiki');
    pages.value = data.pages;
  } catch (err) {
    console.error('Admin Wiki load error', err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchPages);

const deletePage = async (page) => {
  if (!confirm(`Supprimer l'article "${page.title}" ?`)) return;
  try {
    await axios.delete(`/api/v1/admin/wiki/${page.id}`);
    fetchPages();
  } catch (err) {
    alert('Erreur lors de la suppression.');
  }
};

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<style scoped>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 16px 24px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--gray-500); border-bottom: 1px solid var(--gray-100); }
.data-table td { padding: 16px 24px; border-bottom: 1px solid var(--gray-50); font-size: 14px; }
.data-table tr:hover { background: var(--gray-50); }

.mx-8 { margin-left: 8px; margin-right: 8px; }
.text-red { color: var(--red-600); }
</style>

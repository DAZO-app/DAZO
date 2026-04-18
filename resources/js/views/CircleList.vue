<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Cercles</div>
        <div class="page-subtitle">Rejoignez et gérez vos communautés de décision</div>
      </div>
    </div>

    <div class="page-body">
      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else-if="error" class="alert alert-error">{{ error }}</div>

      <div v-else class="circles-grid">
        <div v-for="circle in circles" :key="circle.id" class="circle-card" @click="goTo(circle.id)">
          <div class="circle-icon">◎</div>
          <div class="circle-info">
            <div class="circle-name">{{ circle.name }}</div>
            <div class="circle-desc">{{ circle.description || 'Aucune description' }}</div>
            <div class="circle-meta">
              <span class="badge" :class="typeBadge(circle.type)">{{ circle.type }}</span>
            </div>
          </div>
          <div class="circle-arrow">›</div>
        </div>
        <div v-if="circles.length === 0" class="text-center text-muted py-24">
          Vous n'êtes membre d'aucun cercle.
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCircleStore } from '../stores/circle';

const store = useCircleStore();
const router = useRouter();

const circles = computed(() => store.circles);
const loading = computed(() => store.loading);
const error = computed(() => store.error);

onMounted(() => store.fetchCircles());

const goTo = (id) => router.push({ name: 'CircleDetail', params: { id } });

const typeBadge = (type) => {
  const map = { open: 'badge-teal', closed: 'badge-red', observer_open: 'badge-blue' };
  return map[type] || 'badge-gray';
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.circles-grid { display: flex; flex-direction: column; gap: 8px; }
.circle-card {
  display: flex; align-items: center; gap: 14px; padding: 16px 20px;
  background: white; border-radius: var(--radius-md); border: 1px solid var(--gray-200);
  cursor: pointer; transition: all 0.12s;
}
.circle-card:hover { border-color: var(--blue-400); box-shadow: var(--shadow-sm); }
.circle-icon { font-size: 24px; color: var(--blue-600); flex-shrink: 0; }
.circle-info { flex: 1; min-width: 0; }
.circle-name { font-size: 14px; font-weight: 600; color: var(--gray-900); }
.circle-desc { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
.circle-meta { margin-top: 6px; }
.circle-arrow { font-size: 20px; color: var(--gray-300); font-weight: 300; }
</style>

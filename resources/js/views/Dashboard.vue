<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Bonjour {{ firstName }} 👋</div>
        <div class="page-subtitle">Tableau de bord</div>
      </div>
      <div class="page-actions">
        <button class="btn btn-primary btn-sm">+ Nouvelle décision</button>
      </div>
    </div>

    <div class="page-body">
      <div class="grid-2">
        <div class="card">
          <div class="card-header">
            <span class="status-dot dot-red"></span>
            <span class="card-title">En attente de mon vote</span>
          </div>
          <div class="card-body">
             <div class="text-sm text-muted text-center" style="padding:20px 0">Aucune décision requise.</div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">Activité récente</span>
          </div>
          <div class="card-body">
            <div class="text-sm text-muted text-center" style="padding:20px 0">Aucune activité dans vos cercles.</div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const user = computed(() => authStore.user);
const firstName = computed(() => user.value?.name?.split(' ')[0] || 'à vous');
</script>

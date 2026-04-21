<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Monitoring Serveur</div>
            <div class="hero-subtitle">Surveillez les ressources système, les logs et les services en cours d'exécution.</div>
          </div>
          <div class="hero-action">
            <span class="badge badge-blue"><i class="fa-solid fa-server mr-4"></i> CPU: 12%</span>
          </div>
        </div>
      </div>

      <div class="premium-card">
        <EmptyState message="Console Serveur : Bientôt disponible" />
      </div>
    </div>
  </main>
</template>

<script setup>
import EmptyState from '../../components/EmptyState.vue';
// Future logic for server stats
</script>

<style scoped>
.py-48 { padding: 80px 20px; }
.mb-24 { margin-bottom: 24px; }
.mb-8 { margin-bottom: 8px; }
.mt-32 { margin-top: 32px; }
.max-w-md { max-width: 450px; }
.mx-auto { margin-left: auto; margin-right: auto; }
.mr-4 { margin-right: 4px; }
</style>

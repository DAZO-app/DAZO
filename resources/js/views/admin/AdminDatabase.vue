<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Base de Données</div>
            <div class="hero-subtitle">Visualisez l'état du moteur de stockage et gérez les optimisations.</div>
          </div>
          <div class="hero-action">
            <span class="badge badge-teal"><i class="fa-solid fa-check-circle mr-4"></i> Connecté</span>
          </div>
        </div>
      </div>

      <div class="premium-card">
        <EmptyState message="Espace BDD : Bientôt disponible" />
      </div>
    </div>
  </main>
</template>

<script setup>
import EmptyState from '../../components/EmptyState.vue';
// Future logic for DB stats
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

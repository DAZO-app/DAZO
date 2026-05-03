<template>
  <div class="unauthorized-page flex flex-col items-center justify-center min-h-screen bg-gray-50 p-6 text-center">
    
    <div class="empty-state-container mb-8">
      <img src="/DAZO-picto-carre-gris.svg" class="empty-state-img" alt="Unauthorized" />
      <div class="empty-state-message">
        <span class="text-red-600">Accès refusé</span>
      </div>
    </div>

    <h2 class="text-2xl font-bold text-gray-700 mb-4">Vous n'avez pas les autorisations nécessaires</h2>
    
    <p class="text-gray-500 mb-8 max-w-md">
      La page que vous essayez d'afficher nécessite des droits d'accès que votre compte actuel ne possède pas.
    </p>

    <router-link to="/" class="btn btn-primary">
      <i class="fa-solid fa-arrow-left mr-2"></i> Retour au tableau de bord
    </router-link>

  </div>
</template>

<script setup>
import { useConfigStore } from '../stores/config';

const configStore = useConfigStore();
</script>

<style scoped>
.unauthorized-page {
  font-family: var(--font-sans, system-ui, sans-serif);
}

.empty-state-container {
  position: relative;
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.empty-state-img {
  width: 280px;
  height: 280px;
  opacity: 0.8;
  pointer-events: none;
}

.empty-state-message {
  position: absolute;
  width: 100%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-state-message span {
  font-size: 24px;
  font-weight: 900;
  color: var(--gray-400);
  line-height: 1.4;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.text-red-600 {
  color: #dc2626 !important;
  opacity: 0.3;
}

</style>

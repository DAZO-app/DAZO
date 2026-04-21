<template>
  <div class="empty-state">
    <div class="empty-state-container">
      <img src="/DAZO-picto-carre-gris.svg" class="empty-state-img" alt="Pas de données" />
      <div class="empty-state-message">
        <span>{{ message }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Aucune donnée disponible.'
  }
});
</script>

<style scoped>
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  width: 100%;
  min-height: 240px;
}

.empty-state-container {
  position: relative;
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.empty-state-img {
  width: 180px;
  height: 180px;
  opacity: 0.4;
  pointer-events: none;
}

.empty-state-message {
  position: absolute;
  width: 220px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-state-message span {
  font-size: 14px;
  font-weight: 800;
  color: #1a1a1a;
  line-height: 1.4;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
</style>

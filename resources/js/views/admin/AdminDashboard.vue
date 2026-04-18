<template>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">⚙ Administration</div>
        <div class="page-subtitle">Gestion de la plateforme DAZO</div>
      </div>
    </div>

    <div class="page-body">
      <div class="admin-grid">
        <router-link to="/admin/circles" class="admin-card">
          <div class="admin-icon">◎</div>
          <div class="admin-label">Cercles</div>
          <div class="admin-desc">Créer et gérer les cercles et leurs membres</div>
        </router-link>
        <router-link to="/admin/users" class="admin-card">
          <div class="admin-icon">👥</div>
          <div class="admin-label">Utilisateurs</div>
          <div class="admin-desc">Gérer les comptes et les rôles</div>
        </router-link>
        <router-link to="/admin/categories" class="admin-card">
          <div class="admin-icon">🏷</div>
          <div class="admin-label">Catégories</div>
          <div class="admin-desc">Organiser les décisions par thème</div>
        </router-link>
        <router-link to="/admin/config" class="admin-card">
          <div class="admin-icon">🔧</div>
          <div class="admin-label">Configuration</div>
          <div class="admin-desc">Paramètres globaux de l'instance</div>
        </router-link>
      </div>
    </div>
  </main>
</template>

<style scoped>
.admin-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
.admin-card {
  display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px;
  padding: 28px 20px; background: white; border-radius: var(--radius-lg);
  border: 1px solid var(--gray-200); text-decoration: none; color: inherit;
  cursor: pointer; transition: all 0.15s;
}
.admin-card:hover { border-color: var(--blue-400); box-shadow: var(--shadow-md); transform: translateY(-2px); }
.admin-icon { font-size: 28px; }
.admin-label { font-size: 15px; font-weight: 600; color: var(--gray-900); }
.admin-desc { font-size: 12px; color: var(--gray-500); line-height: 1.4; }
</style>

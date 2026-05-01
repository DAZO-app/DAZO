<template>
  <div class="public-layout">
    <!-- En-tête public -->
    <header class="public-header">
      <div class="public-container-wide header-inner">
        <div class="header-brand" style="flex: 0 0 auto;">
          <a href="#" class="brand-link" @click.prevent="resetAndGoHome">
            <img v-if="configStore.logoUrl" :src="configStore.logoUrl" alt="Logo" class="brand-logo" />
            <span class="brand-name" v-if="!configStore.logoUrl">{{ configStore.appName }}</span>
          </a>
        </div>
        
        <div class="header-center dazo-header-titles">
          <div class="header-main-title">Décisions Publiques</div>
          <div class="header-sub-title">Consultez et suivez les décisions ouvertes de notre organisation.</div>
        </div>
        
        <div class="header-actions">
          <template v-if="!authStore.isAuthenticated">
            <div class="header-auth-buttons">
              <router-link v-if="configStore.config.enable_registration === 'true' || configStore.config.enable_registration === true" 
                           to="/register" class="btn btn-white-ghost btn-sm btn-auth" title="S'inscrire">
                <i class="fa-solid fa-user-plus"></i>
                <span class="btn-text-mobile-hide ml-6">S'inscrire</span>
              </router-link>
              <router-link to="/login" class="btn btn-white btn-sm shadow-md btn-auth" title="Se connecter">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span class="btn-text-mobile-hide ml-6">Se connecter</span>
              </router-link>
            </div>
          </template>
          <template v-else>
            <div class="auth-success-mini">
              <span class="welcome-text">Bonjour, <strong>{{ authStore.user?.name }}</strong></span>
              <router-link to="/" class="btn btn-white btn-sm shadow-md btn-auth" title="Tableau de bord">
                <i class="fa-solid fa-house"></i>
                <span class="btn-text-mobile-hide ml-6">Tableau de bord</span>
              </router-link>
            </div>
          </template>
        </div>
      </div>
    </header>

    <!-- Contenu principal -->
    <main class="public-main">
      <slot>
        <router-view></router-view>
      </slot>
    </main>

    <!-- Pied de page public -->
    <footer class="public-footer">
      <div class="public-container-wide footer-inner">
        <p class="footer-text">&copy; {{ new Date().getFullYear() }} {{ configStore.appName }}. Tous droits réservés.</p>
        <div class="footer-links">
          <a v-if="configStore.config.legal_mentions_url" :href="configStore.config.legal_mentions_url" target="_blank" rel="noopener noreferrer">Mentions Légales</a>
          <a v-if="configStore.config.privacy_policy_url" :href="configStore.config.privacy_policy_url" target="_blank" rel="noopener noreferrer">Confidentialité</a>
          <a v-if="configStore.config.terms_of_service_url" :href="configStore.config.terms_of_service_url" target="_blank" rel="noopener noreferrer">CGU</a>
          <router-link to="/login">Espace Membre</router-link>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useConfigStore } from '../stores/config';
import { useAuthStore } from '../stores/auth';
import { usePublicFrontStore } from '../stores/publicFront';
import { useRouter } from 'vue-router';

const configStore = useConfigStore();
const authStore = useAuthStore();
const publicFrontStore = usePublicFrontStore();
const router = useRouter();

const resetAndGoHome = () => {
  if (router.currentRoute.value.name === 'PublicDecision') {
    // Si on est dans le détail, on retourne simplement au listing (qui conserve ses filtres dans le store)
    router.push({ name: 'PublicFront' });
  } else {
    // Si on est déjà sur le listing, on réinitialise tout
    publicFrontStore.filters.search = '';
    publicFrontStore.filters.status = '';
    publicFrontStore.filters.category = '';
    publicFrontStore.filters.circle = '';
    publicFrontStore.filters.author = '';
    publicFrontStore.filters.sort = 'created_desc';
    publicFrontStore.fetchDecisions(1);
    router.push({ name: 'PublicFront' });
  }
};
</script>

<style scoped>
.public-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--gray-50);
  font-family: 'Inter', sans-serif;
}

/* En-tête bleu dégradé inversé avec effet glow */
.public-header {
  background: linear-gradient(135deg, var(--blue-900) 0%, var(--blue-700) 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.public-header::after {
  content: ""; 
  position: absolute; 
  top: -50%; 
  right: -5%; 
  width: 350px; 
  height: 350px;
  background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%; 
  pointer-events: none;
}

.header-inner {
  height: 90px; /* Hauteur augmentée */
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  z-index: 1; /* au dessus du glow */
}

.header-brand {
  display: flex;
  align-items: center;
  height: 100%;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 20px; /* Espace plus grand entre logo et texte */
  text-decoration: none;
  height: 100%;
}

.brand-logo {
  height: 60px; /* Taille augmentée */
  width: auto;
  object-fit: contain;
}

.brand-name {
  font-size: 20px;
  font-weight: 800;
  color: white;
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.header-center {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 0 20px;
}

.header-main-title {
  font-size: 26px;
  font-weight: 800;
  color: white;
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.header-sub-title {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.85);
  margin-top: 2px;
  font-weight: 400;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 0 0 auto;
}

.header-auth-buttons {
  display: flex;
  gap: 10px;
  align-items: center;
}

.auth-success-mini {
  display: flex;
  align-items: center;
  gap: 16px;
}

.welcome-text {
  color: white;
  font-size: 14px;
}

/* Boutons auth unifiés */
.btn-auth {
  min-width: 140px;
  justify-content: center;
}

/* Bouton fantôme blanc */
.btn-white-ghost {
  background: transparent;
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.8);
}
.btn-white-ghost:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: white;
}

/* Main */
.public-main {
  flex: 1;
  padding-top: 24px; /* Un peu d'espace sous le header */
}

/* Pied de page */
.public-footer {
  background: linear-gradient(135deg, var(--blue-700) 0%, var(--blue-900) 100%);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 8px 0;
  margin-top: auto;
  position: sticky;
  bottom: 0;
  z-index: 100;
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15);
}

.footer-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.footer-text {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.85);
}

.footer-links {
  display: flex;
  gap: 16px;
  align-items: center;
}

.footer-links a {
  font-size: 13px;
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: underline;
}

@media (max-width: 768px) {
  .header-sub-title {
    display: none;
  }
  .header-main-title {
    font-size: 18px;
  }
  .header-actions {
    flex-direction: row;
    align-items: center;
    gap: 8px;
  }
  .btn-text-mobile-hide {
    display: none;
  }
  .btn-auth {
    min-width: 32px;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 50%;
    font-size: 14px;
  }
}
@media (max-width: 640px) {
  .brand-name {
    display: none;
  }
}
</style>

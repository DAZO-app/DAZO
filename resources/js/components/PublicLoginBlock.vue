<template>
  <div class="public-login-block" :class="{ 'has-captcha': configStore.config.recaptcha_site_key }">
    <form v-if="!authStore.isAuthenticated" @submit.prevent="handleLogin" class="login-form-inline">
      <div class="input-group">
        <i class="fa-solid fa-envelope field-icon"></i>
        <input 
          type="email" 
          v-model="email" 
          placeholder="Email" 
          class="header-input" 
          required
          :disabled="loading"
        >
      </div>
      
      <div class="input-group">
        <i class="fa-solid fa-lock field-icon"></i>
        <input 
          type="password" 
          v-model="password" 
          placeholder="Mot de passe" 
          class="header-input" 
          required
          :disabled="loading"
        >
      </div>

      <button type="submit" class="btn btn-primary btn-sm btn-login" :disabled="loading">
        <i v-if="loading" class="fa-solid fa-circle-notch fa-spin"></i>
        <span v-else>Connexion</span>
      </button>

      <!-- Petit séparateur -->
      <div class="social-mini-sep" v-if="hasSocial"></div>

      <!-- Boutons Social compacts -->
      <div class="social-mini-buttons" v-if="hasSocial">
        <button 
          v-for="p in socialProviders" 
          :key="p.key" 
          type="button"
          class="mini-social-btn"
          @click="handleSocial(p.key)"
          :title="'Se connecter avec ' + p.label"
        >
          <span v-html="p.icon"></span>
        </button>
      </div>
    </form>

    <!-- Affichage d'erreur discret -->
    <div v-if="error" class="login-error-tooltip">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useConfigStore } from '../stores/config';
import { useRouter } from 'vue-router';
import axios from 'axios';

const authStore = useAuthStore();
const configStore = useConfigStore();
const router = useRouter();

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const hasSocial = true; // On pourrait filtrer selon la config

const socialProviders = [
  { key: 'google', label: 'Google', icon: '<i class="fa-brands fa-google"></i>' },
  { key: 'github', label: 'GitHub', icon: '<i class="fa-brands fa-github"></i>' },
  { key: 'microsoft', label: 'Microsoft', icon: '<i class="fa-brands fa-microsoft"></i>' },
];

const handleLogin = async () => {
  // Si un reCAPTCHA est configuré, on ne peut pas le gérer proprement en inline
  // On redirige vers la page de login complète pour la sécurité
  if (configStore.config.recaptcha_site_key) {
    router.push({ name: 'Login', query: { email: email.value } });
    return;
  }

  loading.value = true;
  error.value = '';
  try {
    await authStore.login(email.value, password.value);
    router.push({ name: 'Dashboard' });
  } catch (e) {
    error.value = e.response?.data?.message || 'Identifiants invalides';
    setTimeout(() => error.value = '', 5000);
  } finally {
    loading.value = false;
  }
};

const handleSocial = async (provider) => {
  try {
    const { data } = await axios.get(`/api/v1/auth/social/${provider}/redirect`);
    window.location.href = data.url;
  } catch (e) {
    error.value = 'Erreur OAuth';
    setTimeout(() => error.value = '', 3000);
  }
};
</script>

<style scoped>
.public-login-block {
  position: relative;
}

.login-form-inline {
  display: flex;
  align-items: center;
  gap: 8px;
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.field-icon {
  position: absolute;
  left: 10px;
  font-size: 12px;
  color: rgba(255,255,255,0.5);
  pointer-events: none;
}

.header-input {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 8px 12px 8px 28px;
  color: white;
  font-size: 13px;
  width: 140px;
  transition: all 0.2s;
}

.header-input:focus {
  outline: none;
  background: rgba(255, 255, 255, 0.2);
  border-color: white;
  width: 170px; /* Petit effet d'expansion */
}

.header-input::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.btn-login {
  height: 36px;
  padding: 0 16px;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.social-mini-sep {
  width: 1px;
  height: 20px;
  background: rgba(255, 255, 255, 0.2);
  margin: 0 4px;
}

.social-mini-buttons {
  display: flex;
  gap: 6px;
}

.mini-social-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.05);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 14px;
}

.mini-social-btn:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: white;
  transform: translateY(-2px);
}

.login-error-tooltip {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 8px;
  background: var(--red-600);
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 1000;
  white-space: nowrap;
  animation: shake 0.4s ease-in-out;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}

@media (max-width: 1100px) {
  .header-input { width: 110px; }
  .header-input:focus { width: 130px; }
}

@media (max-width: 900px) {
  .social-mini-sep, .social-mini-buttons { display: none; }
}

@media (max-width: 768px) {
  .login-form-inline {
    display: none; /* On cache le bloc inline en mobile, on utilisera les boutons existants dans PublicLayout */
  }
}
</style>

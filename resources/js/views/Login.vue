<template>
  <div class="login-wrapper">
    <div class="card login-card">
      <div class="card-header justify-center" style="border:none;flex-direction:column;gap:8px;align-items:center;">
        <img src="/DAZO-logo-carre-noir.svg" alt="DAZO" class="login-logo">
        <div class="login-tagline">Decision At Zero Objection</div>
      </div>
      <div class="card-body">
        <!-- Pending approval message -->
        <div v-if="pendingMessage" class="alert alert-warning mb-16">
          <i class="fa-solid fa-clock"></i>
          {{ pendingMessage }}
        </div>

        <form @submit.prevent="handleLogin">
          <div v-if="error" class="alert alert-error mb-16">{{ error }}</div>
          <div class="text-sm text-muted mb-16" style="text-align:center;">Compte de démonstration: admin@dazo.test / password</div>
          
          <div class="form-group">
            <label class="label">Email</label>
            <input type="email" v-model="email" class="input" required>
          </div>
          
          <div class="form-group">
            <label class="label">Mot de passe</label>
            <input type="password" v-model="password" class="input" required>
          </div>
          
          <Recaptcha 
            v-if="configStore.config.recaptcha_site_key"
            :site-key="configStore.config.recaptcha_site_key"
            @verify="onRecaptchaVerify"
            @expire="onRecaptchaExpire"
            @error="onRecaptchaError"
          />

          <button type="submit" class="btn btn-primary btn-block mt-16" :disabled="loading || (configStore.config.recaptcha_site_key && !recaptchaToken)">
            {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
          </button>
          
          <div style="text-align: center; margin-top: 16px;">
            <router-link to="/forgot-password" style="font-size: 13px; color: var(--blue-600); text-decoration: none;">Mot de passe oublié ?</router-link>
          </div>
        </form>

        <!-- Social Login Buttons -->
        <SocialLoginButtons label="ou se connecter avec" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useConfigStore } from '../stores/config';
import { useRouter, useRoute } from 'vue-router';
import SocialLoginButtons from '../components/SocialLoginButtons.vue';
import Recaptcha from '../components/Recaptcha.vue';

const email = ref('admin@dazo.test');
const password = ref('password');
const error = ref('');
const loading = ref(false);
const pendingMessage = ref('');
const recaptchaToken = ref('');

const authStore = useAuthStore();
const configStore = useConfigStore();
const router = useRouter();
const route = useRoute();

const onRecaptchaVerify = (token) => {
  recaptchaToken.value = token;
};
const onRecaptchaExpire = () => {
  recaptchaToken.value = '';
};
const onRecaptchaError = () => {
  error.value = "Erreur avec reCAPTCHA, veuillez réessayer.";
  recaptchaToken.value = '';
};

// Handle OAuth callback (token in URL from SocialAuthController)
onMounted(async () => {
  const params = new URLSearchParams(window.location.search);
  const token = params.get('token');
  const provider = params.get('provider');
  const errorParam = params.get('error');

  if (errorParam === 'account_pending') {
    const pendingEmail = params.get('email') || '';
    pendingMessage.value = `Votre compte (${pendingEmail}) a été créé. Un administrateur doit valider votre inscription avant que vous puissiez vous connecter.`;
    // Clean the URL
    window.history.replaceState({}, '', '/login');
    return;
  }

  if (errorParam === 'account_inactive') {
    error.value = 'Ce compte a été désactivé par un administrateur.';
    window.history.replaceState({}, '', '/login');
    return;
  }

  if (errorParam === 'oauth_failed') {
    error.value = `La connexion via ${provider || 'ce fournisseur'} a échoué. Veuillez réessayer.`;
    window.history.replaceState({}, '', '/login');
    return;
  }

  if (token) {
    // Store the token and fetch user
    localStorage.setItem('dazo_token', token);
    localStorage.setItem('dazo_logged_in', 'true');
    await authStore.fetchUser();

    // Clean the URL
    window.history.replaceState({}, '', '/login');

    // Handle invitation redirect
    const invitationToken = params.get('invitation_token');
    if (invitationToken) {
      router.push({ name: 'InvitationAccept', params: { token: invitationToken } });
    } else {
      router.push({ name: 'Dashboard' });
    }
  }
});

const handleLogin = async () => {
    if (configStore.config.recaptcha_site_key && !recaptchaToken.value) {
        error.value = "Veuillez valider le reCAPTCHA.";
        return;
    }

    loading.value = true;
    error.value = '';
    try {
        await authStore.login(email.value, password.value, recaptchaToken.value);
        router.push({ name: 'Dashboard' });
    } catch (e) {
        error.value = e.response?.data?.message || 'Identifiants invalides';
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  width: 100%;
  padding: 20px;
  background: var(--blue-50);
}
.login-card {
  width: 100%;
  max-width: 420px;
}
.login-logo {
  width: 160px;
  height: auto;
}
.login-tagline {
  font-size: 11px;
  color: var(--gray-500);
  letter-spacing: 0.08em;
  text-transform: uppercase;
}
</style>

<template>
  <PublicLayout>
    <div class="auth-wrapper">
      <div class="auth-blocks">
        
        <!-- Bloc 1 : Logo -->
        <div class="auth-block premium-card block-logo-card">
          <div class="pc-body p-24" style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <img src="/DAZO-logo-carre-noir.svg" alt="DAZO" class="auth-main-logo">
            <div class="auth-tagline">Decision At Zero Objection</div>
          </div>
        </div>

        <!-- Bloc 2 : Paramètres de connexion -->
        <div class="auth-block premium-card">
          <div class="pc-body p-24">
            <h2 class="text-center font-bold text-gray-800 text-xl mb-16">Se connecter</h2>
            <div class="text-sm text-muted mb-16" style="text-align:center;">Compte de démonstration : admin@dazo.test / password</div>
            
            <div v-if="pendingMessage" class="alert alert-warning mb-16">
              <i class="fa-solid fa-clock"></i>
              {{ pendingMessage }}
            </div>
            <form @submit.prevent="handleLogin">
              <div v-if="error" class="alert alert-error mb-16">{{ error }}</div>
              
              <div class="form-group">
                <label class="label">Email</label>
                <input type="email" v-model="email" class="input" required>
              </div>
              
              <div class="form-group">
                <label class="label">Mot de passe</label>
                <input type="password" v-model="password" class="input" required>
              </div>
              
              <Recaptcha 
                v-if="isRecaptchaEnabled"
                :site-key="configStore.config.recaptcha_site_key"
                @verify="onRecaptchaVerify"
                @expire="onRecaptchaExpire"
                @error="onRecaptchaError"
              />

              <button type="submit" class="btn btn-primary btn-block mt-16" :disabled="isButtonDisabled">
                {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
              </button>
              
              <div style="text-align: center; margin-top: 16px;">
                <router-link to="/forgot-password" class="auth-link">Mot de passe oublié ?</router-link>
              </div>
            </form>
          </div>
        </div>

        <!-- Bloc 3 : Social Login -->
        <div class="auth-block premium-card">
          <div class="pc-body p-24" style="height: 100%; display: flex; flex-direction: column; justify-content: center;">
            <div class="text-center font-bold text-gray-500 mb-16 text-sm uppercase tracking-wider">Ou se connecter avec</div>
            <SocialLoginButtons />
            <div class="text-center mt-24">
              <span class="text-sm text-muted">Pas encore de compte ? </span>
              <router-link to="/register" class="auth-link font-bold">S'inscrire</router-link>
            </div>
          </div>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useConfigStore } from '../stores/config';
import { useRouter, useRoute } from 'vue-router';
import SocialLoginButtons from '../components/SocialLoginButtons.vue';
import Recaptcha from '../components/Recaptcha.vue';
import PublicLayout from '../layouts/PublicLayout.vue';

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
    localStorage.setItem('dazo_token', token);
    localStorage.setItem('dazo_logged_in', 'true');
    await authStore.fetchUser();
    window.history.replaceState({}, '', '/login');

    const invitationToken = params.get('invitation_token');
    if (invitationToken) {
      router.push({ name: 'InvitationAccept', params: { token: invitationToken } });
    } else {
      router.push({ name: 'Dashboard' });
    }
  }
});

const isRecaptchaEnabled = computed(() => {
  const key = configStore.config.recaptcha_site_key;
  return !!(key && key !== '' && key !== 'null' && key !== 'undefined');
});

const isButtonDisabled = computed(() => {
  if (loading.value) return true;
  if (isRecaptchaEnabled.value && !recaptchaToken.value) return true;
  return false;
});

const handleLogin = async () => {
    if (isRecaptchaEnabled.value && !recaptchaToken.value) {
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
.auth-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  min-height: 100%;
}

.auth-blocks {
  width: 100%;
  max-width: 1200px;
  display: flex;
  flex-direction: row;
  gap: 24px;
  align-items: stretch;
}

@media (max-width: 900px) {
  .auth-blocks {
    flex-direction: column;
    max-width: 500px;
    align-items: stretch;
    margin: 0 auto;
  }
  .auth-block {
    width: 100%;
    flex: none;
  }
}

@media (max-width: 750px) {
  .auth-blocks {
    max-width: 100%;
    padding: 0;
  }
}

.auth-block {
  flex: 1 1 0%;
  display: flex;
  flex-direction: column;
}

.block-logo-card {
  text-align: center;
}

.auth-main-logo {
  width: 180px;
  height: auto;
  margin: 0 auto 16px auto;
  display: block;
}

.auth-tagline {
  font-size: 13px;
  color: var(--gray-500);
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-weight: 600;
}

.auth-link {
  font-size: 13px;
  color: var(--blue-600);
  text-decoration: none;
}
.auth-link:hover {
  text-decoration: underline;
}
</style>

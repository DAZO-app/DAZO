<template>
  <div class="auth-layout">
    <div class="auth-card auth-center">
      <div class="auth-logo">
        <img src="/DAZO-logo-V.svg" alt="DAZO" />
      </div>
      
      <div class="auth-header">
        <h1 class="auth-title">Mot de passe oublié</h1>
        <p class="auth-subtitle">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
      </div>

      <div v-if="successMsg" class="alert alert-success">{{ successMsg }}</div>
      <div v-if="errorMsg" class="alert alert-error">{{ errorMsg }}</div>

      <form @submit.prevent="sendResetLink" class="auth-form" v-if="!successMsg">
        <div class="form-group">
          <label for="email" class="form-label">Adresse email</label>
          <input 
            id="email" 
            v-model="email" 
            type="email" 
            class="form-control" 
            placeholder="vous@exemple.fr"
            required 
            :disabled="loading"
          />
          <div v-if="validationErrors.email" class="error-text">{{ validationErrors.email[0] }}</div>
        </div>

        <Recaptcha 
          v-if="isRecaptchaEnabled"
          :site-key="configStore.config.recaptcha_site_key"
          @verify="onRecaptchaVerify"
          @expire="onRecaptchaExpire"
          @error="onRecaptchaError"
        />

        <button type="submit" class="btn btn-primary btn-block p-12 mt-4" :disabled="isButtonDisabled">
          {{ loading ? 'Envoi en cours...' : 'Envoyer le lien' }}
        </button>
      </form>

      <div class="auth-footer mt-16">
        <router-link to="/login" class="auth-link">Retour à la connexion</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { useConfigStore } from '../stores/config';
import Recaptcha from '../components/Recaptcha.vue';

const email = ref('');
const loading = ref(false);
const successMsg = ref('');
const errorMsg = ref('');
const validationErrors = ref({});
const recaptchaToken = ref('');

const configStore = useConfigStore();

const onRecaptchaVerify = (token) => {
  recaptchaToken.value = token;
};
const onRecaptchaExpire = () => {
  recaptchaToken.value = '';
};
const onRecaptchaError = () => {
  errorMsg.value = "Erreur avec reCAPTCHA, veuillez réessayer.";
  recaptchaToken.value = '';
};
const isRecaptchaEnabled = computed(() => {
  const key = configStore.config.recaptcha_site_key;
  return !!(key && key !== '' && key !== 'null' && key !== 'undefined');
});

const isButtonDisabled = computed(() => {
  if (loading.value) return true;
  if (isRecaptchaEnabled.value && !recaptchaToken.value) return true;
  return false;
});
const sendResetLink = async () => {
    if (configStore.config.recaptcha_site_key && !recaptchaToken.value) {
        errorMsg.value = "Veuillez valider le reCAPTCHA.";
        return;
    }

    loading.value = true;
    successMsg.value = '';
    errorMsg.value = '';
    validationErrors.value = {};

    try {
        const { data } = await axios.post('/api/v1/auth/forgot-password', { 
            email: email.value,
            recaptcha_token: recaptchaToken.value
        });
        successMsg.value = "Un lien a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception.";
    } catch (error) {
        if (error.response && error.response.status === 422) {
            validationErrors.value = error.response.data.errors;
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMsg.value = error.response.data.message;
        } else {
            errorMsg.value = "Une erreur est survenue lors de l'envoi de l'email.";
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.auth-layout {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-50);
  background-image: radial-gradient(var(--gray-200) 1px, transparent 1px);
  background-size: 20px 20px;
  padding: 24px;
}

.auth-center {
  margin: 0 auto;
}

.auth-card {
  width: 100%;
  max-width: 440px;
  background: white;
  border-radius: var(--radius-xl);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.05);
  padding: 40px;
}

.auth-logo {
  display: flex;
  justify-content: center;
  margin-bottom: 32px;
}

.auth-logo img {
  height: 60px;
}

.auth-header {
  text-align: center;
  margin-bottom: 32px;
}

.auth-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 8px;
  letter-spacing: -0.02em;
}

.auth-subtitle {
  color: var(--gray-500);
  font-size: 15px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-700);
}

.form-control {
  padding: 12px 16px;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  font-size: 15px;
  color: var(--gray-900);
  transition: all 0.2s ease;
  background: var(--gray-50);
}

.form-control:focus {
  outline: none;
  border-color: var(--blue-500);
  background: white;
  box-shadow: 0 0 0 4px var(--blue-100);
}

.form-control::placeholder {
  color: var(--gray-400);
}

.error-text {
  color: var(--red-500);
  font-size: 13px;
  margin-top: 4px;
}

.mt-4 { margin-top: 4px; }
.mt-16 { margin-top: 16px; }
.p-12 { padding: 12px; }

.auth-footer {
  text-align: center;
  padding-top: 24px;
  border-top: 1px solid var(--gray-100);
}

.auth-link {
  color: var(--blue-600);
  font-weight: 500;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.15s ease;
}

.auth-link:hover {
  color: var(--blue-800);
  text-decoration: underline;
}
</style>

<template>
  <div class="auth-layout">
    <div class="auth-card auth-center">
      <div class="auth-logo">
        <img src="/DAZO-logo-V.svg" alt="DAZO" />
      </div>
      
      <div class="auth-header">
        <h1 class="auth-title">Nouveau mot de passe</h1>
        <p class="auth-subtitle">Créez un nouveau mot de passe pour votre compte DAZO.</p>
      </div>

      <div v-if="successMsg" class="alert alert-success">
        {{ successMsg }}
        <div class="mt-16">
            <router-link to="/login" class="btn btn-primary btn-block p-12">Se connecter</router-link>
        </div>
      </div>
      
      <div v-if="errorMsg" class="alert alert-error">{{ errorMsg }}</div>

      <form @submit.prevent="resetPassword" class="auth-form" v-if="!successMsg">
        <div class="form-group">
          <label for="password" class="form-label">Nouveau mot de passe</label>
          <input 
            id="password" 
            v-model="password" 
            type="password" 
            class="form-control" 
            required 
            :disabled="loading"
            placeholder="8 caractères minimum"
          />
          <div v-if="validationErrors.password" class="error-text">{{ validationErrors.password[0] }}</div>
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
          <input 
            id="password_confirmation" 
            v-model="password_confirmation" 
            type="password" 
            class="form-control" 
            required 
            :disabled="loading"
          />
        </div>

        <button type="submit" class="btn btn-primary btn-block p-12 mt-4" :disabled="loading">
          {{ loading ? 'Enregistrement...' : 'Enregistrer le mot de passe' }}
        </button>
      </form>

      <div class="auth-footer mt-16" v-if="!successMsg">
        <router-link to="/login" class="auth-link">Annuler</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();

const token = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');

const loading = ref(false);
const successMsg = ref('');
const errorMsg = ref('');
const validationErrors = ref({});

onMounted(() => {
    token.value = route.query.token || '';
    email.value = route.query.email || '';
});

const resetPassword = async () => {
    loading.value = true;
    successMsg.value = '';
    errorMsg.value = '';
    validationErrors.value = {};

    try {
        const { data } = await axios.post('/api/v1/auth/reset-password', { 
            token: token.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        });
        successMsg.value = "Votre mot de passe a été réinitialisé avec succès.";
    } catch (error) {
        if (error.response && error.response.status === 422) {
            validationErrors.value = error.response.data.errors;
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMsg.value = error.response.data.message;
        } else {
            errorMsg.value = "Une erreur est survenue lors de la réinitialisation.";
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

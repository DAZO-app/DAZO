<template>
  <div class="login-wrapper">
    <div class="card login-card">
      <div class="card-header justify-center" style="border:none;flex-direction:column;gap:8px;align-items:center;">
        <img src="/DAZO-logo-carre-noir.svg" alt="DAZO" class="login-logo">
        <div class="login-tagline">Decision At Zero Objection</div>
      </div>
      <div class="card-body">
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
          
          <button type="submit" class="btn btn-primary btn-block mt-16" :disabled="loading">
            {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
          </button>
          
          <div style="text-align: center; margin-top: 16px;">
            <router-link to="/forgot-password" style="font-size: 13px; color: var(--blue-600); text-decoration: none;">Mot de passe oublié ?</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const email = ref('admin@dazo.test');
const password = ref('password');
const error = ref('');
const loading = ref(false);

const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
    loading.value = true;
    error.value = '';
    try {
        await authStore.login(email.value, password.value);
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
  max-width: 400px;
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

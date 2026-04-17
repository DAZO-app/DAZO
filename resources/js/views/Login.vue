<template>
  <div class="login-wrapper">
    <div class="card login-card">
      <div class="card-header justify-center" style="border:none;flex-direction:column;gap:4px">
        <div class="sidebar-logo-mark" style="color:var(--blue-900);font-size:28px">DAZO</div>
        <div class="sidebar-logo-sub" style="color:var(--gray-500)">Decision At Zero Objection</div>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleLogin">
          <div v-if="error" class="alert alert-error mb-16">{{ error }}</div>
          <div class="text-sm text-muted mb-16">Compte de démonstration: admin@dazo.test / password</div>
          
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
  padding: 20px;
  background: var(--blue-50);
}
.login-card {
  width: 100%;
  max-width: 400px;
}
</style>

<template>
  <div class="accept-container">
    <div v-if="loading" class="loading-state">
      <i class="fa-solid fa-circle-notch fa-spin"></i>
      <p>Chargement du lien d'invitation...</p>
    </div>
    
    <div v-else-if="error" class="error-card">
      <div class="icon-error"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <h2>Oups !</h2>
      <p>{{ error }}</p>
      <router-link to="/login" class="btn-primary mt-16">Retour à la connexion</router-link>
    </div>

    <div v-else class="invite-card">
       <div class="icon-invite"><i class="fa-solid fa-circle-nodes"></i></div>
       <h2>Rejoindre le cercle</h2>
       <p class="mb-16">Vous avez été invité à rejoindre le cercle <strong>{{ inviteData.circle?.name }}</strong>.</p>
       
       <div v-if="authStore.isAuthenticated" class="auth-box">
          <p class="text-sm text-muted mb-16">Vous êtes connecté en tant que <strong>{{ authStore.user?.email }}</strong>.</p>
          <button @click="acceptInvite" class="btn-primary w-full" :disabled="submitting">
             <i v-if="submitting" class="fa-solid fa-spinner fa-spin mr-8"></i>
             Rejoindre maintenant
          </button>
       </div>
       <div v-else class="guest-box">
          <p class="text-sm text-muted mb-24">Pour rejoindre ce cercle, vous devez posséder un compte sur la plateforme.</p>
          <div class="flex flex-col gap-12">
            <router-link :to="{ name: 'Register', query: { invite_token: token } }" class="btn-primary">Créer un compte</router-link>
            <router-link :to="{ name: 'Login', query: { redirect: $route.fullPath } }" class="btn-ghost">Déjà un compte ? Connectez-vous</router-link>
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const submitting = ref(false);
const error = ref(null);
const inviteData = ref(null);
const token = ref(route.params.token);

const fetchData = async () => {
    try {
        const { data } = await axios.get(`/api/v1/invite/${token.value}`);
        inviteData.value = data;
    } catch (e) {
        error.value = e.response?.data?.message || "Lien d'invitation invalide ou expiré.";
    } finally {
        loading.value = false;
    }
};

const acceptInvite = async () => {
    submitting.value = true;
    try {
        await axios.post(`/api/v1/invite/${token.value}/accept`);
        router.push({ name: 'Dashboard', query: { joined: 'success' } });
    } catch (e) {
        error.value = e.response?.data?.message || "Impossible de rejoindre le cercle.";
    } finally {
        submitting.value = false;
    }
};

onMounted(fetchData);
</script>

<style scoped>
.accept-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0f172a;
  color: white;
  text-align: center;
  padding: 20px;
}

.loading-state i {
  font-size: 48px;
  color: #6366f1;
  margin-bottom: 16px;
}

.error-card, .invite-card {
  background: #1e293b;
  padding: 40px;
  border-radius: 24px;
  max-width: 440px;
  width: 100%;
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 20px 50px rgba(0,0,0,0.3);
}

.icon-error { font-size: 64px; color: #f87171; margin-bottom: 20px; }
.icon-invite { font-size: 64px; color: #6366f1; margin-bottom: 20px; }

.btn-primary {
  display: inline-block;
  background: #6366f1;
  color: white;
  padding: 14px 24px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-primary:hover:not(:disabled) { background: #4f46e5; transform: translateY(-2px); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-ghost {
  display: inline-block;
  color: rgba(255,255,255,0.7);
  padding: 12px;
  text-decoration: none;
  font-size: 14px;
}
.btn-ghost:hover { color: white; }

.text-sm { font-size: 14px; }
.text-muted { color: rgba(255,255,255,0.5); }
.mb-16 { margin-bottom: 16px; }
.mb-24 { margin-bottom: 24px; }
.mt-16 { margin-top: 16px; }
.w-full { width: 100%; }
.flex { display: flex; }
.flex-col { flex-direction: column; }
.gap-12 { gap: 12px; }
</style>

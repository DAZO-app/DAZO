<template>
  <div class="accept-container">
    <div v-if="loading" class="loading-state">
      <i class="fa-solid fa-circle-notch fa-spin"></i>
      <p>Traitement de votre invitation...</p>
    </div>
    
    <div v-else-if="error" class="error-card">
      <div class="icon-error"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <h2>Oups !</h2>
      <p>{{ error }}</p>
      <router-link to="/login" class="btn-primary mt-16">Retour à la connexion</router-link>
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
const error = ref(null);

onMounted(async () => {
    const token = route.params.token;
    if (!token) {
        error.value = "Lien d'invitation invalide.";
        loading.value = false;
        return;
    }

    try {
        // Validate token publicly
        const { data: inv } = await axios.get(`/api/v1/invitations/${token}`);
        
        // If authenticated, try to accept it
        if (authStore.isAuthenticated) {
            await axios.post(`/api/v1/invitations/${token}/accept`);
            router.push({ 
                name: 'Dashboard', 
                query: { invited: 'success' } 
            });
        } else {
            // Redirect to register with context
            router.push({ 
                name: 'Register', 
                query: { 
                    token: token,
                    email: inv.email,
                    circle_name: inv.circle.name
                } 
            });
        }
    } catch (e) {
        error.value = e.response?.data?.message || "Impossible de traiter l'invitation.";
        loading.value = false;
    }
});
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
}

.loading-state i {
  font-size: 48px;
  color: #6366f1;
  margin-bottom: 16px;
}

.error-card {
  background: #1e293b;
  padding: 40px;
  border-radius: 20px;
  max-width: 400px;
  border: 1px solid rgba(255,255,255,0.1);
}

.icon-error {
  font-size: 64px;
  color: #f87171;
  margin-bottom: 20px;
}

.btn-primary {
  display: inline-block;
  background: #6366f1;
  color: white;
  padding: 12px 24px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: #4f46e5;
  transform: translateY(-2px);
}

.mt-16 { margin-top: 16px; }
</style>

<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Mes Paramètres</div>
            <div class="hero-subtitle">Gérez vos informations personnelles et vos préférences de plateforme.</div>
          </div>
          <div class="hero-action">
            <div class="avatar av-blue" style="width:48px; height:48px; font-size:18px;">{{ userInitials }}</div>
          </div>
        </div>
      </div>

      <div class="settings-content">
        <!-- PROFIL CARD -->
        <div class="premium-card">
          <div class="card-header card-header-sexy">
             <span class="card-title"><i class="fa-solid fa-user-circle mr-8"></i> Mon Profil</span>
          </div>
          <div class="card-body">
            <form @submit.prevent="updateProfile" class="settings-form">
                <div v-if="successMsg" class="alert alert-success mb-16">{{ successMsg }}</div>
                <div v-if="errorMsg" class="alert alert-error mb-16">{{ errorMsg }}</div>

                <div class="form-group mb-16">
                    <label class="label">Nom complet</label>
                    <input v-model="form.name" type="text" class="input" required />
                    <div v-if="validationErrors.name" class="error-text">{{ validationErrors.name[0] }}</div>
                </div>

                <div class="form-group mb-24">
                    <label class="label">Adresse email</label>
                    <input v-model="form.email" type="email" class="input" required />
                    <div v-if="validationErrors.email" class="error-text">{{ validationErrors.email[0] }}</div>
                </div>

                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <i class="fa-solid fa-save mr-8"></i>
                    {{ loading ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                </button>
            </form>
          </div>
        </div>

        <!-- NOTIFICATIONS CARD -->
        <div class="premium-card">
          <div class="card-header card-header-sexy">
             <span class="card-title"><i class="fa-solid fa-bell mr-8"></i> Notifications</span>
             <span class="badge badge-gray badge-sm">Bientôt disponible</span>
          </div>
          <div class="card-body">
            <p class="text-xs text-muted mb-20 italic">Gérez vos préférences de notifications pour rester informé de l'activité dans vos cercles.</p>

            <div class="settings-form disabled-section">
                <div class="form-group checkbox-group mb-12">
                    <input type="checkbox" id="notif_email" checked disabled />
                    <label for="notif_email" class="text-sm">Recevoir un email quand je suis mentionné(e)</label>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="notif_decision" checked disabled />
                    <label for="notif_decision" class="text-sm">Être notifié(e) des décisions dans mes cercles</label>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const authStore = useAuthStore();

const form = ref({
    name: '',
    email: '',
});

const loading = ref(false);
const successMsg = ref('');
const errorMsg = ref('');
const validationErrors = ref({});

const userInitials = computed(() => {
  if (!authStore.user) return '?';
  return authStore.user.name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || '?';
});

onMounted(() => {
    if (authStore.user) {
        form.value.name = authStore.user.name;
        form.value.email = authStore.user.email;
    }
});

const updateProfile = async () => {
    loading.value = true;
    successMsg.value = '';
    errorMsg.value = '';
    validationErrors.value = {};

    try {
        const { data } = await axios.put('/api/v1/auth/me', form.value);
        successMsg.value = data.message || 'Profil mis à jour avec succès.';
        await authStore.fetchUser();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            validationErrors.value = error.response.data.errors;
        } else {
            errorMsg.value = 'Une erreur est survenue lors de la mise à jour.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.settings-content {
    display: flex;
    flex-direction: column;
    gap: 32px;
    max-width: 800px;
}
.settings-form {
    display: flex;
    flex-direction: column;
    max-width: 500px;
}
.checkbox-group {
    flex-direction: row;
    align-items: center;
    gap: 12px;
}
.checkbox-group label {
    margin: 0;
    cursor: default;
}
.error-text {
    color: var(--red-500);
    font-size: 13px;
    margin-top: 4px;
}
.disabled-section {
    opacity: 0.5;
}
.mb-12 { margin-bottom: 12px; }
.mb-16 { margin-bottom: 16px; }
.mb-20 { margin-bottom: 20px; }
.mb-24 { margin-bottom: 24px; }
.mr-8 { margin-right: 8px; }
.max-w-500 { max-width: 500px; }
</style>

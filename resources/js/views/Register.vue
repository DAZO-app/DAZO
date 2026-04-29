<template>
  <div class="login-wrapper">
    <div class="card login-card">
      <div class="card-header justify-center" style="border:none;flex-direction:column;gap:8px;align-items:center;">
        <img src="/DAZO-logo-carre-noir.svg" alt="DAZO" class="login-logo">
        <div class="login-tagline">Decision At Zero Objection</div>
        <h1 class="text-xl font-bold mt-8" style="color:var(--gray-800)">Créer votre compte</h1>
      </div>

      <div class="card-body">
        <form @submit.prevent="handleRegister" class="login-form">
          <div v-if="error" class="alert alert-error mb-16">
            {{ error }}
          </div>

          <!-- Social Registration Buttons -->
          <SocialLoginButtons 
            label="créer un compte avec" 
            :invitation-token="route.query.token || null"
          />

          <div class="form-group">
            <label for="name" class="label">Nom complet</label>
            <input 
              v-model="form.name" 
              type="text" 
              id="name" 
              class="input" 
              placeholder="Ex: Jean Dupont" 
              required
            >
          </div>

          <div class="form-group">
            <label for="email" class="label">Adresse e-mail</label>
            <input 
              v-model="form.email" 
              type="email" 
              id="email" 
              class="input" 
              placeholder="votre@email.com" 
              required
              :disabled="isInvite"
            >
          </div>

          <div class="form-group">
            <label for="password" class="label">Mot de passe</label>
            <input 
              v-model="form.password" 
              type="password" 
              id="password" 
              class="input" 
              placeholder="••••••••" 
              required
            >
            <!-- Strength Meter -->
            <div class="strength-meter mt-8">
                <div class="strength-bar" :style="{ width: strengthScore + '%', backgroundColor: strengthColor }"></div>
            </div>
            
            <!-- Checklist -->
            <div class="password-checklist mt-12">
                <div class="checklist-item" :class="{ active: hasLength }">
                    <i class="fa-solid" :class="hasLength ? 'fa-check' : 'fa-circle'"></i>
                    8 caractères minimum
                </div>
                <div class="checklist-item" :class="{ active: hasMixed }">
                    <i class="fa-solid" :class="hasMixed ? 'fa-check' : 'fa-circle'"></i>
                    Majuscule & Minuscule
                </div>
                <div class="checklist-item" :class="{ active: hasNumber }">
                    <i class="fa-solid" :class="hasNumber ? 'fa-check' : 'fa-circle'"></i>
                    Un chiffre
                </div>
                <div class="checklist-item" :class="{ active: hasSymbol }">
                    <i class="fa-solid" :class="hasSymbol ? 'fa-check' : 'fa-circle'"></i>
                    Un caractère spécial
                </div>
            </div>
          </div>

          <div class="form-group">
            <label for="password_confirmation" class="label">Confirmer le mot de passe</label>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              id="password_confirmation" 
              class="input" 
              placeholder="••••••••" 
              required
            >
          </div>

          <Recaptcha 
            v-if="configStore.config.recaptcha_site_key"
            :site-key="configStore.config.recaptcha_site_key"
            @verify="onRecaptchaVerify"
            @expire="onRecaptchaExpire"
            @error="onRecaptchaError"
          />

          <button type="submit" class="btn btn-primary btn-block mt-16" :disabled="loading || strengthScore < 100 || (configStore.config.recaptcha_site_key && !form.recaptcha_token)">
            <i v-if="loading" class="fa-solid fa-circle-notch fa-spin mr-8"></i>
            {{ loading ? 'Création...' : 'S\'inscrire' }}
          </button>

          <div style="text-align: center; margin-top: 24px;">
            <span class="text-sm text-muted">Déjà un compte ? </span>
            <router-link to="/login" class="link" style="font-size: 13px; color: var(--blue-600); text-decoration: none; font-weight: 600;">Se connecter</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useConfigStore } from '../stores/config';
import SocialLoginButtons from '../components/SocialLoginButtons.vue';
import Recaptcha from '../components/Recaptcha.vue';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const configStore = useConfigStore();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  recaptcha_token: ''
});

const loading = ref(false);
const error = ref(null);
const isInvite = ref(false);

const onRecaptchaVerify = (token) => {
  form.recaptcha_token = token;
};
const onRecaptchaExpire = () => {
  form.recaptcha_token = '';
};
const onRecaptchaError = () => {
  error.value = "Erreur avec reCAPTCHA, veuillez réessayer.";
  form.recaptcha_token = '';
};

// Password Validation Logic
const hasLength = computed(() => form.password.length >= 8);
const hasMixed = computed(() => /[a-z]/.test(form.password) && /[A-Z]/.test(form.password));
const hasNumber = computed(() => /[0-9]/.test(form.password));
const hasSymbol = computed(() => /[^a-zA-Z0-9]/.test(form.password));

const strengthScore = computed(() => {
    let score = 0;
    if (hasLength.value) score += 25;
    if (hasMixed.value) score += 25;
    if (hasNumber.value) score += 25;
    if (hasSymbol.value) score += 25;
    return score;
});

const strengthColor = computed(() => {
    if (strengthScore.value < 50) return '#ef4444'; // Red
    if (strengthScore.value < 75) return '#f59e0b'; // Amber
    if (strengthScore.value < 100) return '#3b82f6'; // Blue
    return '#10b981'; // Green (Solid)
});

onMounted(() => {
    if (route.query.email) {
        form.email = route.query.email;
        isInvite.value = true;
    }
});

const handleRegister = async () => {
    if (strengthScore.value < 100) {
        error.value = "Le mot de passe doit remplir tous les critères de sécurité.";
        return;
    }

    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post('/api/v1/auth/register', form);
        
        if (response.data.access_token) {
            localStorage.setItem('dazo_token', response.data.access_token);
            localStorage.setItem('dazo_logged_in', 'true');
            await authStore.fetchUser();
            
            if (route.query.token) {
                router.push({ name: 'InvitationAccept', params: { token: route.query.token } });
            } else {
                router.push({ name: 'Dashboard' });
            }
        } else {
            router.push({ name: 'Login', query: { registered: 'true' } });
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'Une erreur est survenue lors de l\'inscription.';
        if (e.response?.data?.errors) {
            const firstError = Object.values(e.response.data.errors)[0][0];
            error.value = firstError;
        }
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
  max-width: 440px;
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

/* Strength Meter */
.strength-meter {
    height: 4px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
}
.strength-bar {
    height: 100%;
    transition: all 0.3s ease;
}

/* Checklist */
.password-checklist {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.checklist-item {
    font-size: 11px;
    color: var(--gray-400);
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.15s;
}
.checklist-item i {
    font-size: 8px;
}
.checklist-item.active {
    color: var(--teal-600);
}
.checklist-item.active i {
    color: var(--teal-500);
}

.mr-8 { margin-right: 8px; }
.mt-8 { margin-top: 8px; }
.mt-12 { margin-top: 12px; }
</style>

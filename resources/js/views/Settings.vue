<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Mes Paramètres</div>
            <div class="hero-subtitle">Gérez vos informations personnelles, votre sécurité et vos préférences.</div>
          </div>
          <div class="hero-action">
            <div class="avatar av-blue" style="width:56px; height:56px; font-size:20px; border: 2px solid rgba(255,255,255,0.3);">{{ userInitials }}</div>
          </div>
        </div>
      </div>

      <div class="config-layout mt-32">
        <!-- NAVIGATION GAUCHE -->
        <aside class="config-nav">
          <div class="nav-group-title">Mon Compte</div>
          <button v-for="s in sections" :key="s.id" 
                  @click="activeSection = s.id" 
                  class="nav-item" :class="{ active: activeSection === s.id }">
            <i :class="s.icon"></i>
            <span>{{ s.label }}</span>
            <i v-if="activeSection === s.id" class="fa-solid fa-chevron-right ml-auto text-xs opacity-50"></i>
          </button>
        </aside>

        <!-- CONTENU CENTRAL -->
        <div class="config-content">
          
          <!-- SECTION PROFIL -->
          <div v-if="activeSection === 'profile'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-user-circle"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mon Profil</div>
                <div class="pc-header-sub">Informations publiques et identité sur DAZO</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <form @submit.prevent="updateProfile">
                <div v-if="successMsg" class="alert alert-success mb-24">{{ successMsg }}</div>
                <div v-if="errorMsg" class="alert alert-error mb-24">{{ errorMsg }}</div>

                <div class="form-group mb-24">
                  <label class="config-label">Nom complet</label>
                  <input v-model="profileForm.name" type="text" class="input input-lg" required />
                  <div v-if="validationErrors.name" class="input-error">{{ validationErrors.name[0] }}</div>
                </div>

                <div class="form-group mb-32">
                  <label class="config-label">Adresse email</label>
                  <input v-model="profileForm.email" type="email" class="input input-lg" required />
                  <div v-if="validationErrors.email" class="input-error">{{ validationErrors.email[0] }}</div>
                  <p class="help-text">Votre email est utilisé pour les notifications et la connexion.</p>
                </div>

                <div class="pt-16 border-top">
                  <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
                    <i class="fa-solid fa-save mr-8"></i>
                    {{ loading ? 'Enregistrement...' : 'Enregistrer le profil' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- SECTION MOT DE PASSE -->
          <div v-if="activeSection === 'password'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-red">
              <div class="pc-header-icon"><i class="fa-solid fa-key"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mot de passe</div>
                <div class="pc-header-sub">Sécurisez l'accès à votre compte</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <form @submit.prevent="updatePassword">
                <div v-if="pwdSuccessMsg" class="alert alert-success mb-24">{{ pwdSuccessMsg }}</div>
                <div v-if="pwdErrorMsg" class="alert alert-error mb-24">{{ pwdErrorMsg }}</div>

                <div class="form-group mb-24">
                  <label class="config-label">Mot de passe actuel</label>
                  <input v-model="pwdForm.current_password" type="password" class="input" required />
                  <div v-if="pwdValidationErrors.current_password" class="input-error">{{ pwdValidationErrors.current_password[0] }}</div>
                </div>

                <div class="grid-2 gap-24 mb-32">
                  <div class="form-group">
                    <label class="config-label">Nouveau mot de passe</label>
                    <input v-model="pwdForm.password" type="password" class="input" required />
                    <div v-if="pwdValidationErrors.password" class="input-error">{{ pwdValidationErrors.password[0] }}</div>
                  </div>
                  <div class="form-group">
                    <label class="config-label">Confirmer le nouveau mot de passe</label>
                    <input v-model="pwdForm.password_confirmation" type="password" class="input" required />
                  </div>
                </div>

                <div class="pt-16 border-top">
                  <button type="submit" class="btn btn-primary btn-lg" :disabled="pwdLoading">
                    <i class="fa-solid fa-shield-halved mr-8"></i>
                    {{ pwdLoading ? 'Mise à jour...' : 'Modifier mon mot de passe' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- SECTION COMPTES LIÉS (OAuth) -->
          <div v-if="activeSection === 'social'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-purple">
              <div class="pc-header-icon"><i class="fa-solid fa-link"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Comptes liés</div>
                <div class="pc-header-sub">Connectez vos comptes externes pour vous connecter plus rapidement</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div v-if="socialLoading" class="text-center py-32">
                <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500"></i>
              </div>
              <div v-else>
                <div v-if="socialSuccessMsg" class="alert alert-success mb-24">{{ socialSuccessMsg }}</div>
                <div v-if="socialErrorMsg" class="alert alert-error mb-24">{{ socialErrorMsg }}</div>

                <div class="social-accounts-list">
                  <div v-for="p in socialProviders" :key="p.provider" class="social-account-row">
                    <div class="social-account-info">
                      <div class="social-account-icon">
                        <i :class="socialIcons[p.provider]"></i>
                      </div>
                      <div>
                        <div class="social-account-name">{{ p.label }}</div>
                        <div class="social-account-status" :class="p.linked ? 'linked' : 'unlinked'">
                          {{ p.linked ? 'Connecté' : 'Non connecté' }}
                        </div>
                      </div>
                    </div>
                    <div>
                      <button v-if="p.linked" class="btn btn-danger btn-sm" @click="unlinkSocial(p.provider)" :disabled="socialActionLoading === p.provider">
                        <i v-if="socialActionLoading === p.provider" class="fa-solid fa-spinner fa-spin"></i>
                        <template v-else><i class="fa-solid fa-unlink mr-6"></i> Délier</template>
                      </button>
                      <button v-else class="btn btn-blue btn-sm" @click="linkSocial(p.provider)" :disabled="socialActionLoading === p.provider">
                        <i v-if="socialActionLoading === p.provider" class="fa-solid fa-spinner fa-spin"></i>
                        <template v-else><i class="fa-solid fa-link mr-6"></i> Lier</template>
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="!socialHasPassword" class="alert alert-warning mt-24">
                  <i class="fa-solid fa-triangle-exclamation"></i>
                  <span>Vous n'avez pas de mot de passe défini. Vous devez conserver au moins un compte lié pour pouvoir vous connecter.</span>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION NOTIFICATIONS -->
          <div v-if="activeSection === 'notifications'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-bell"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Notifications</div>
                <div class="pc-header-sub">Préférences d'alertes et de rappels</div>
              </div>
            </div>
            <div class="pc-body p-24">
              
              <div class="alert alert-info mb-32">
                <i class="fa-solid fa-lightbulb mr-12"></i>
                <span><strong>Conseil :</strong> Les notifications <strong>Web (Push)</strong> sont recommandées pour une meilleure réactivité et pour réduire l'encombrement de votre boîte mail.</span>
              </div>

              <div v-if="notifSuccessMsg" class="alert alert-success mb-24">{{ notifSuccessMsg }}</div>
              
              <div class="notif-table-container">
                <table class="notif-table">
                  <thead>
                    <tr>
                      <th>Événement</th>
                      <th class="text-center">Email</th>
                      <th class="text-center">Push / Web</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="cat in notifCategories" :key="cat.id">
                      <td>
                        <div class="notif-cat-info">
                          <div class="notif-cat-label"><i :class="cat.icon" class="mr-8"></i>{{ cat.label }}</div>
                          <div class="notif-cat-desc" style="font-size: 11px; opacity: 0.6; margin-top: 2px; padding-left: 24px;">{{ cat.desc }}</div>
                        </div>
                      </td>
                      <td class="text-center">
                        <label class="switch-sm">
                          <input type="checkbox" v-model="notifPrefs[cat.id].email">
                          <span class="slider round"></span>
                        </label>
                      </td>
                      <td class="text-center">
                        <label class="switch-sm">
                          <input type="checkbox" v-model="notifPrefs[cat.id].web">
                          <span class="slider round"></span>
                        </label>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="pt-24 border-top mt-24">
                <button @click="saveNotificationPrefs" class="btn btn-primary btn-lg" :disabled="notifLoading">
                  <i class="fa-solid fa-bell mr-8"></i>
                  {{ notifLoading ? 'Enregistrement...' : 'Enregistrer mes préférences' }}
                </button>
              </div>
            </div>
          </div>

          <!-- SECTION MES VUES -->
          <div v-if="activeSection === 'views'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-teal">
              <div class="pc-header-icon"><i class="fa-solid fa-layer-group"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mes Vues Personnalisées</div>
                <div class="pc-header-sub">Configurez vos raccourcis de filtrage dans la barre latérale</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div v-if="viewsSuccessMsg" class="alert alert-success mb-24">{{ viewsSuccessMsg }}</div>
              
              <div class="mb-32">
                <h4 class="config-label mb-8">Vues actives</h4>
                <p class="help-text mb-16">Sélectionnez les filtres que vous souhaitez voir apparaître dans votre menu "Mes Vues".</p>
                
                <div class="views-grid">
                  <div v-for="opt in viewOptions" :key="opt.id" class="view-opt-card" :class="{ active: isViewActive(opt.id) }" @click="toggleView(opt)">
                    <div class="view-opt-check">
                      <i :class="isViewActive(opt.id) ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                    </div>
                    <div class="view-opt-icon"><i :class="opt.icon"></i></div>
                    <div class="view-opt-label">{{ opt.label }}</div>
                  </div>
                </div>
              </div>

              <div class="pt-16 border-top">
                <button @click="saveViews" class="btn btn-primary btn-lg" :disabled="loading">
                  <i class="fa-solid fa-save mr-8"></i>
                  {{ loading ? 'Enregistrer mes vues' : 'Enregistrer mes vues' }}
                </button>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useDecisionStore } from '../stores/decision';
import axios from 'axios';

const authStore = useAuthStore();
const decisionStore = useDecisionStore();

const activeSection = ref('profile');
const sections = [
  { id: 'profile', label: 'Mon profil', icon: 'fa-solid fa-user-circle' },
  { id: 'password', label: 'Mot de passe', icon: 'fa-solid fa-key' },
  { id: 'social', label: 'Comptes liés', icon: 'fa-solid fa-link' },
  { id: 'notifications', label: 'Notifications', icon: 'fa-solid fa-bell' },
  { id: 'views', label: 'Mes vues', icon: 'fa-solid fa-layer-group' },
];

const notifCategories = [
  { id: 'new_decision', label: 'Nouvelles décisions', icon: 'fa-solid fa-plus-circle', desc: 'Lorsqu\'une décision est créée dans l\'un de vos cercles.' },
  { id: 'phase_change', label: 'Changements de phase', icon: 'fa-solid fa-forward-step', desc: 'Dès qu\'une décision à laquelle vous participez change d\'étape.' },
  { id: 'feedback', label: 'Messages & Mentions', icon: 'fa-solid fa-at', desc: 'Nouveau commentaire, feedback ou mention vous concernant.' },
  { id: 'deadline', label: 'Échéances proches', icon: 'fa-solid fa-clock-rotate-left', desc: 'Alertes de rappel avant la fin d\'une phase (24h avant).' },
];

const viewOptions = [
  { id: 'my-proposals', label: 'Mes propositions', icon: 'fa-solid fa-bullhorn', filters: { role: 'author' } },
  { id: 'my-animations', label: 'Mes animations', icon: 'fa-solid fa-user-tie', filters: { role: 'animator' } },
  { id: 'pending-actions', label: 'Réactions attendues', icon: 'fa-solid fa-clock', filters: { action: 'pending' } },
  { id: 'urgent-decisions', label: 'Décisions urgentes', icon: 'fa-solid fa-triangle-exclamation', filters: { urgency: 'urgent' } },
  { id: 'clarification-phase', label: 'En clarification', icon: 'fa-solid fa-comments', filters: { state: 'clarification' } },
  { id: 'reaction-phase', label: 'En réaction', icon: 'fa-solid fa-lightbulb', filters: { state: 'reaction' } },
  { id: 'objection-phase', label: 'En objection', icon: 'fa-solid fa-hand', filters: { state: 'objection' } },
];

const userViews = ref([]);
const viewsSuccessMsg = ref('');

const notifPrefs = reactive({});
notifCategories.forEach(c => {
  notifPrefs[c.id] = { email: true, web: true };
});

const notifLoading = ref(false);
const notifSuccessMsg = ref(false);

const fetchNotifPrefs = async () => {
  try {
    const { data } = await axios.get('/api/v1/auth/notifications');
    (data.preferences || []).forEach(p => {
      if (notifPrefs[p.category]) {
        notifPrefs[p.category].email = p.email_enabled;
        notifPrefs[p.category].web = p.web_enabled;
      }
    });
  } catch (e) {
    console.error("Fetch notif prefs error", e);
  }
};

const saveNotificationPrefs = async () => {
  notifLoading.value = true;
  notifSuccessMsg.value = '';
  try {
    const payload = Object.keys(notifPrefs).map(cat => ({
      category: cat,
      email_enabled: notifPrefs[cat].email,
      web_enabled: notifPrefs[cat].web
    }));
    await axios.put('/api/v1/auth/notifications', { preferences: payload });
    notifSuccessMsg.value = 'Vos préférences ont été enregistrées.';
    setTimeout(() => notifSuccessMsg.value = '', 3000);
  } catch (e) {
    alert('Erreur lors de la sauvegarde.');
  } finally {
    notifLoading.value = false;
  }
};

const isViewActive = (id) => userViews.value.some(v => v.id === id);

const toggleView = (opt) => {
  const idx = userViews.value.findIndex(v => v.id === opt.id);
  if (idx !== -1) userViews.value.splice(idx, 1);
  else userViews.value.push({ ...opt });
};

const saveViews = async () => {
  loading.value = true;
  viewsSuccessMsg.value = '';
  try {
    await axios.put('/api/v1/auth/me', { custom_views: userViews.value });
    viewsSuccessMsg.value = 'Vos vues ont été mises à jour.';
    await authStore.fetchUser();
  } catch (e) {
    alert('Erreur lors de la sauvegarde des vues.');
  } finally {
    loading.value = false;
  }
};

// Profile state
const profileForm = ref({ name: '', email: '' });
const loading = ref(false);
const successMsg = ref('');
const errorMsg = ref('');
const validationErrors = ref({});

// Password state
const pwdForm = ref({ current_password: '', password: '', password_confirmation: '' });
const pwdLoading = ref(false);
const pwdSuccessMsg = ref('');
const pwdErrorMsg = ref('');
const pwdValidationErrors = ref({});

const userInitials = computed(() => {
  if (!authStore.user) return '?';
  return authStore.user.name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || '?';
});

const favorites = computed(() => {
  return decisionStore.decisions.filter(d => d.my_settings?.is_favorite);
});

onMounted(() => {
    if (authStore.user) {
        profileForm.value.name = authStore.user.name;
        profileForm.value.email = authStore.user.email;
        userViews.value = JSON.parse(JSON.stringify(authStore.user.custom_views || []));
    }
    decisionStore.fetchDecisions();
    fetchNotifPrefs();
    fetchSocialAccounts();
});

const updateProfile = async () => {
    loading.value = true;
    successMsg.value = '';
    errorMsg.value = '';
    validationErrors.value = {};

    try {
        const { data } = await axios.put('/api/v1/auth/me', profileForm.value);
        successMsg.value = data.message || 'Profil mis à jour avec succès.';
        await authStore.fetchUser();
    } catch (error) {
        if (error.response?.status === 422) {
            validationErrors.value = error.response.data.errors;
        } else {
            errorMsg.value = 'Une erreur est survenue lors de la mise à jour.';
        }
    } finally {
        loading.value = false;
    }
};

const updatePassword = async () => {
    pwdLoading.value = true;
    pwdSuccessMsg.value = '';
    pwdErrorMsg.value = '';
    pwdValidationErrors.value = {};

    try {
        const { data } = await axios.put('/api/v1/auth/password', pwdForm.value);
        pwdSuccessMsg.value = data.message;
        pwdForm.value = { current_password: '', password: '', password_confirmation: '' };
    } catch (error) {
        if (error.response?.status === 422) {
            pwdValidationErrors.value = error.response.data.errors;
        } else {
            pwdErrorMsg.value = error.response?.data?.message || 'Une erreur est survenue.';
        }
    } finally {
        pwdLoading.value = false;
    }
};

// Social Accounts
const socialProviders = ref([]);
const socialHasPassword = ref(true);
const socialLoading = ref(false);
const socialSuccessMsg = ref('');
const socialErrorMsg = ref('');
const socialActionLoading = ref(null);

const socialIcons = {
  'google': 'fa-brands fa-google',
  'github': 'fa-brands fa-github',
  'facebook': 'fa-brands fa-facebook',
  'twitter': 'fa-brands fa-x-twitter',
  'linkedin-openid': 'fa-brands fa-linkedin',
  'gitlab': 'fa-brands fa-gitlab',
  'microsoft': 'fa-brands fa-microsoft',
  'apple': 'fa-brands fa-apple',
  'franceconnect': 'fa-solid fa-flag',
};

const fetchSocialAccounts = async () => {
  socialLoading.value = true;
  try {
    const { data } = await axios.get('/api/v1/auth/social/accounts');
    socialProviders.value = data.providers || [];
    socialHasPassword.value = data.has_password;
  } catch (e) {
    console.error('Fetch social accounts error', e);
  } finally {
    socialLoading.value = false;
  }
};

const linkSocial = async (provider) => {
  socialActionLoading.value = provider;
  socialErrorMsg.value = '';
  try {
    const { data } = await axios.get(`/api/v1/auth/social/${provider}/redirect`);
    window.location.href = data.url;
  } catch (e) {
    socialErrorMsg.value = e.response?.data?.message || 'Impossible de lier ce compte.';
    socialActionLoading.value = null;
  }
};

const unlinkSocial = async (provider) => {
  socialActionLoading.value = provider;
  socialSuccessMsg.value = '';
  socialErrorMsg.value = '';
  try {
    const { data } = await axios.delete(`/api/v1/auth/social/${provider}/unlink`);
    socialSuccessMsg.value = data.message;
    await fetchSocialAccounts();
    setTimeout(() => socialSuccessMsg.value = '', 3000);
  } catch (e) {
    socialErrorMsg.value = e.response?.data?.message || 'Impossible de délier ce compte.';
  } finally {
    socialActionLoading.value = null;
  }
};
</script>

<style scoped>
.config-layout { display: flex; gap: 32px; align-items: flex-start; }
.config-nav { width: 260px; flex-shrink: 0; position: sticky; top: 100px; }
.config-content { flex: 1; min-width: 0; }

.nav-group-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-400); letter-spacing: 0.1em; margin-bottom: 12px; padding-left: 12px; }

.nav-item {
  display: flex; align-items: center; gap: 14px; padding: 16px 20px; width: 100%;
  background: white; border: 1px solid var(--gray-200); border-radius: 14px;
  font-size: 14px; font-weight: 700; color: var(--gray-600); cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); text-align: left; margin-bottom: 8px;
}
.nav-item:hover { transform: translateX(4px); background: var(--gray-50); border-color: var(--blue-300); color: var(--blue-600); }
.nav-item.active { background: var(--blue-600); border-color: var(--blue-700); color: white; box-shadow: 0 10px 20px rgba(59,130,246,0.15); }
.nav-item i { font-size: 16px; }

.config-label { display: block; font-size: 15px; font-weight: 800; color: var(--gray-800); margin-bottom: 12px; }
.input-lg { padding: 14px 18px; font-size: 16px; }

.help-text { font-size: 12px; color: var(--gray-500); margin-top: 8px; line-height: 1.5; }
.input-error { color: var(--red-600); font-size: 12px; margin-top: 4px; font-weight: 600; }

.border-top { border-top: 1px solid var(--gray-100); }
.pt-16 { padding-top: 16px; }

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Toggle Cards */
.toggle-card { display: flex; justify-content: space-between; align-items: center; padding: 24px; background: var(--gray-50); border-radius: 16px; border: 1px solid var(--gray-100); }
.toggle-title { font-size: 16px; font-weight: 800; color: var(--gray-800); }
.toggle-description { font-size: 13px; color: var(--gray-500); margin-top: 4px; }

/* Views */
.views-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
.view-opt-card { 
  background: white; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.2s;
  display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; position: relative;
}
.view-opt-card:hover { border-color: var(--blue-400); background: var(--blue-50); }
.view-opt-card.active { border-color: var(--blue-600); background: var(--blue-50); box-shadow: 0 4px 12px rgba(59,130,246,0.1); }
.view-opt-check { position: absolute; top: 8px; right: 8px; font-size: 14px; color: var(--gray-300); }
.view-opt-card.active .view-opt-check { color: var(--blue-600); }
.view-opt-icon { font-size: 24px; color: var(--gray-400); margin-bottom: 4px; }
.view-opt-card.active .view-opt-icon { color: var(--blue-600); }
.view-opt-label { font-size: 13px; font-weight: 700; color: var(--gray-700); }

/* Notifications Table */
.notif-table-container { background: var(--gray-50); border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--gray-200); }
.notif-table { width: 100%; border-collapse: collapse; }
.notif-table th { background: var(--gray-100); padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--gray-500); border-bottom: 1px solid var(--gray-200); }
.notif-table td { padding: 16px; border-bottom: 1px solid var(--gray-200); vertical-align: middle; }
.notif-table tr:last-child td { border-bottom: none; }
.notif-cat-label { font-size: 13px; font-weight: 700; color: var(--gray-800); }
.notif-cat-desc { font-size: 11px; color: var(--gray-500); margin-top: 2px; }

/* Switch Toggle LG */
.switch-lg { position: relative; display: inline-block; width: 60px; height: 32px; }
.switch-lg input { opacity: 0; width: 0; height: 0; }
.switch-lg .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: .4s; }
.switch-lg .slider:before { position: absolute; content: ""; height: 24px; width: 24px; left: 4px; bottom: 4px; background-color: white; transition: .4s; }
.switch-lg input:checked + .slider { background-color: var(--blue-600); }
.switch-lg input:checked + .slider:before { transform: translateX(28px); }
.switch-lg .slider.round { border-radius: 34px; }
.switch-lg .slider.round:before { border-radius: 50%; }

/* Switch Toggle SM */
.switch-sm { position: relative; display: inline-block; width: 44px; height: 22px; }
.switch-sm input { opacity: 0; width: 0; height: 0; }
.switch-sm .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: .4s; }
.switch-sm .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; }
.switch-sm input:checked + .slider { background-color: var(--blue-600); }
.switch-sm input:checked + .slider:before { transform: translateX(22px); }
.switch-sm .slider.round { border-radius: 22px; }
.switch-sm .slider.round:before { border-radius: 50%; }

/* Favorites */
.empty-state-sm { text-align: center; padding: 32px; background: var(--gray-50); border-radius: 16px; border: 1px dashed var(--gray-200); color: var(--gray-500); font-size: 13px; }
.favorites-list { display: flex; flex-direction: column; gap: 8px; }
.fav-item { display: flex; align-items: center; gap: 16px; padding: 12px 16px; background: white; border: 1px solid var(--gray-200); border-radius: 12px; cursor: pointer; transition: all 0.2s; }
.fav-item:hover { border-color: var(--blue-400); background: var(--blue-50); transform: translateX(4px); }
.fav-icon { font-size: 16px; }
.fav-content { flex: 1; }
.fav-title { font-weight: 700; font-size: 13px; color: var(--gray-900); }
.fav-meta { font-size: 11px; color: var(--gray-500); margin-top: 2px; }

@media (max-width: 1024px) {
  .config-layout { flex-direction: column; }
  .config-nav { width: 100%; position: static; flex-direction: row; display: flex; overflow-x: auto; gap: 8px; padding-bottom: 8px; }
  .nav-item { width: auto; white-space: nowrap; margin-bottom: 0; }
  .nav-group-title { display: none; }
}

/* Social Accounts */
.social-accounts-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.social-account-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  background: var(--gray-50);
  border: 1px solid var(--gray-100);
  border-radius: 12px;
  transition: all 0.15s;
}

.social-account-row:hover {
  border-color: var(--gray-200);
  background: white;
}

.social-account-info {
  display: flex;
  align-items: center;
  gap: 14px;
}

.social-account-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: white;
  border: 1px solid var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: var(--gray-600);
  flex-shrink: 0;
}

.social-account-name {
  font-size: 13px;
  font-weight: 700;
  color: var(--gray-800);
}

.social-account-status {
  font-size: 11px;
  font-weight: 600;
  margin-top: 2px;
}

.social-account-status.linked {
  color: var(--teal-600);
}

.social-account-status.unlinked {
  color: var(--gray-400);
}
</style>

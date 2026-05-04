<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Configuration Système</div>
            <div class="hero-subtitle">Paramètres techniques, maintenance et clés de services tiers.</div>
          </div>
          <div class="hero-action">
             <button class="btn btn-primary btn-lg shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-cloud-arrow-up mr-8"></i> {{ saving ? 'Enregistrement...' : 'Enregistrer les modifications' }}
             </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-48">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement du centre de contrôle...</p>
      </div>
      
      <div v-else class="config-layout mt-32">
        <!-- NAVIGATION GAUCHE -->
        <aside class="config-nav">
          <div class="nav-group-title">Paramètres</div>
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
          

          <!-- SECTION RECAPTCHA -->
          <div v-if="activeSection === 'recaptcha'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-brands fa-google"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Google reCAPTCHA</div>
                <div class="pc-header-sub">Protection contre le spam sur les formulaires publics (v2 ou v3)</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <p class="help-text mb-24">Si les clés sont renseignées, le reCAPTCHA sera exigé sur les formulaires de connexion et d'inscription publics.</p>
              <div class="grid-2 gap-24">
                <div class="form-group">
                  <label class="config-label">Clé du Site (Site Key)</label>
                  <input v-model="config.recaptcha_site_key" class="input" placeholder="Clé publique">
                  <div class="config-key">variable : <code>recaptcha_site_key</code></div>
                </div>
                <div class="form-group">
                  <label class="config-label">Clé Secrète (Secret Key)</label>
                  <input type="password" v-model="config.recaptcha_secret_key" class="input" placeholder="Clé privée sécurisée">
                  <div class="config-key">variable : <code>recaptcha_secret_key</code></div>
                </div>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer reCAPTCHA
              </button>
            </div>
          </div>

          <div v-if="activeSection === 'mail_server'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-server"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Serveur de Messagerie</div>
                <div class="pc-header-sub">Configuration SMTP pour l'envoi des notifications</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="alert alert-info mb-24">
                <i class="fa-solid fa-circle-info"></i>
                <p>Ces paramètres surchargent la configuration par défaut du serveur si renseignés.</p>
              </div>

              <div class="grid-2 gap-24 mb-24">
                <div class="form-group">
                  <label class="config-label">Hôte SMTP</label>
                  <input v-model="config.mail_host" class="input" placeholder="smtp.mailtrap.io">
                </div>
                <div class="form-group">
                  <label class="config-label">Port</label>
                  <input v-model="config.mail_port" class="input" placeholder="587">
                </div>
              </div>

              <div class="grid-2 gap-24 mb-24">
                <div class="form-group">
                  <label class="config-label">Utilisateur</label>
                  <input v-model="config.mail_username" class="input">
                </div>
                <div class="form-group">
                  <label class="config-label">Mot de passe</label>
                  <input type="password" v-model="config.mail_password" class="input">
                </div>
              </div>

              <div class="form-group">
                <label class="config-label">Chiffrement</label>
                <select v-model="config.mail_encryption" class="select">
                  <option value="null">Aucun</option>
                  <option value="tls">TLS</option>
                  <option value="ssl">SSL</option>
                </select>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Serveur Mail
              </button>
            </div>
          </div>

          <div v-if="activeSection === 'oauth'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-key"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Clés OAuth</div>
                <div class="pc-header-sub">Configurez les services d'identification tiers</div>
              </div>
            </div>
            <div class="pc-body">
              <div class="accordion-container">
                <div v-for="prov in oauthList" :key="prov.key" class="accordion-item" :class="{ open: activeAccordionOAuth === prov.key }">
                  <div class="accordion-header" @click="toggleAccordionOAuth(prov.key)">
                    <div class="flex items-center gap-12">
                      <i :class="prov.icon" class="text-teal-500 opacity-60"></i>
                      <span class="font-bold">{{ prov.label }}</span>
                      <span v-if="config['auth_' + prov.key + '_enabled'] === 'true'" class="badge badge-teal ml-8">Actif</span>
                    </div>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                  </div>
                  <div class="accordion-body">
                    <div class="p-24 border-top">
                      <div class="toggle-card mb-24 bg-white border">
                        <div class="toggle-content">
                           <div class="toggle-title">Activer {{ prov.label }}</div>
                           <div class="toggle-description">Permettre aux utilisateurs de se connecter avec leur compte {{ prov.label }}.</div>
                        </div>
                        <label class="switch-lg">
                          <input type="checkbox" :checked="config['auth_' + prov.key + '_enabled'] === 'true'" @change="config['auth_' + prov.key + '_enabled'] = $event.target.checked ? 'true' : 'false'">
                          <span class="slider round"></span>
                        </label>
                      </div>
                      <div class="grid-2 gap-24 mb-24">
                        <div class="form-group">
                          <label class="config-label">Client ID</label>
                          <input v-model="config['auth_' + prov.key + '_client_id']" class="input" placeholder="ID client fourni par le service">
                        </div>
                        <div class="form-group">
                          <label class="config-label">Client Secret</label>
                          <input type="password" v-model="config['auth_' + prov.key + '_client_secret']" class="input" placeholder="Clé secrète confidentielle">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="config-label">URL de callback (Lecture seule)</label>
                        <input :value="configStore.appUrl + '/api/v1/auth/social/' + prov.key + '/callback'" class="input bg-gray-100" readonly>
                        <p class="help-text">Copiez cette URL dans les paramètres de votre application chez {{ prov.label }}.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer OAuth
              </button>
            </div>
          </div>

          <div v-if="activeSection === 'system'" class="premium-card animate-fade-in border-red">
            <div class="pc-header pc-header-red">
              <div class="pc-header-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mode Maintenance</div>
                <div class="pc-header-sub">Maintenance et réglages critiques du système</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="toggle-card border-red-soft">
                <div class="toggle-content">
                   <div class="toggle-title text-red">Mode Maintenance Actif</div>
                   <div class="toggle-description">Désactive l'accès à la plateforme pour tous les utilisateurs non-administrateurs.</div>
                   <div class="config-key inverse">variable : <code>maintenance_mode</code></div>
                </div>
                <label class="switch-lg danger">
                  <input type="checkbox" v-model="maintenanceModeBool">
                  <span class="slider round"></span>
                </label>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useConfigStore } from '../../stores/config';

const configStore = useConfigStore();
const config = ref({});
const loading = ref(true);
const saving = ref(false);
const activeSection = ref('recaptcha');


const sections = [
  { id: 'recaptcha', label: 'reCAPTCHA', icon: 'fa-brands fa-google' },
  { id: 'mail_server', label: 'Serveur Mail', icon: 'fa-solid fa-server' },
  { id: 'oauth', label: 'Clés OAuth', icon: 'fa-solid fa-key' },
  { id: 'system', label: 'Maintenance', icon: 'fa-solid fa-triangle-exclamation' },
];


const oauthList = [
  { key: 'google', label: 'Google', icon: 'fa-brands fa-google' },
  { key: 'github', label: 'GitHub', icon: 'fa-brands fa-github' },
  { key: 'facebook', label: 'Facebook', icon: 'fa-brands fa-facebook' },
  { key: 'twitter', label: 'X (Twitter)', icon: 'fa-brands fa-x-twitter' },
  { key: 'linkedin-openid', label: 'LinkedIn', icon: 'fa-brands fa-linkedin' },
  { key: 'gitlab', label: 'GitLab', icon: 'fa-brands fa-gitlab' },
  { key: 'microsoft', label: 'Microsoft', icon: 'fa-brands fa-microsoft' },
  { key: 'apple', label: 'Apple', icon: 'fa-brands fa-apple' },
  { key: 'franceconnect', label: 'FranceConnect', icon: 'fa-solid fa-id-card' },
];

const activeAccordionOAuth = ref(null);
const toggleAccordionOAuth = (key) => {
  activeAccordionOAuth.value = activeAccordionOAuth.value === key ? null : key;
};

const maintenanceModeBool = computed({
  get: () => config.value.maintenance_mode === 'true',
  set: (val) => config.value.maintenance_mode = val ? 'true' : 'false'
});

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/v1/admin/config');
    config.value = data.config || data || {};
  } catch (e) {
    console.error("Config fetch error", e);
  } finally {
    loading.value = false;
  }
});

const saveConfig = async () => {
  saving.value = true;
  try {
    const { data } = await axios.put('/api/v1/admin/config', { config: config.value });
    config.value = data.config;
    configStore.fetchInit();
    alert('Configuration système enregistrée avec succès.');
  } catch (e) {
    alert('Erreur lors de la sauvegarde.');
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
@import "../../../css/admin-config.css";
</style>

<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Configuration Système</div>
            <div class="hero-subtitle">Pilotez les paramètres globaux et les règles métier de la plateforme.</div>
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
          
          <!-- SECTION IDENTITÉ -->
          <div v-if="activeSection === 'identity'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-fingerprint"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Identité & Branding</div>
                <div class="pc-header-sub">Personnalisez l'apparence de votre instance DAZO</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <!-- LOGO UPLOAD (PRIORITY) -->
              <div class="form-group mb-32">
                <label class="config-label">Logo de la plateforme</label>
                <div class="logo-dropzone" @click="$refs.logoInput.click()">
                  <div class="logo-preview-large">
                    <img v-if="processedLogoUrl" :src="processedLogoUrl" alt="Logo" />
                    <div v-else class="no-logo-large">
                      <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div class="logo-overlay">
                      <i class="fa-solid fa-camera"></i>
                      <span>Changer le logo</span>
                    </div>
                  </div>
                  <div class="logo-info">
                    <div class="font-bold text-gray-800">Cliquer pour uploader</div>
                    <div class="text-xs text-muted">PNG, SVG ou JPG (max 2Mo)</div>
                    <div class="config-key mt-8">variable : <code>app_logo</code></div>
                  </div>
                  <input type="file" ref="logoInput" style="display: none" @change="handleLogoUpload" accept="image/*">
                </div>
              </div>

              <div class="form-group mb-32">
                <label class="config-label">Nom de l'organisation</label>
                <input v-model="config.app_name" class="input input-lg" placeholder="ex: DAZO Collective">
                <div class="config-key">variable : <code>app_name</code></div>
                <p class="help-text">Nom affiché dans la barre de titre, les menus et les communications sortantes.</p>
              </div>

              <div class="form-group">
                <label class="config-label">Palette de couleurs</label>
                <div class="color-grid">
                   <div class="color-option active" style="--c: #3b82f6"><span>Océan (Défaut)</span></div>
                   <div class="color-option disabled" style="--c: #10b981"><span>Émeraude</span></div>
                   <div class="color-option disabled" style="--c: #8b5cf6"><span>Améthyste</span></div>
                   <div class="color-option disabled" style="--c: #f59e0b"><span>Ambre</span></div>
                </div>
                <p class="help-text mt-8">Les thèmes personnalisés seront disponibles dans une future mise à jour.</p>
              </div>
            </div>
          </div>

          <!-- SECTION DÉCISIONS -->
          <div v-if="activeSection === 'decisions'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-amber">
              <div class="pc-header-icon"><i class="fa-solid fa-scale-balanced"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Cycle de Décision</div>
                <div class="pc-header-sub">Paramétrage des règles de gouvernance et délais</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="grid-2 gap-32 mb-32">
                <div class="form-group">
                  <label class="config-label">Délai de Réaction</label>
                  <div class="input-with-addon">
                    <input type="number" v-model="config.decision_reaction_days" class="input" min="1">
                    <span class="addon">jours</span>
                  </div>
                  <div class="config-key">variable : <code>decision_reaction_days</code></div>
                  <p class="help-text">Temps alloué pour collecter les avis des membres.</p>
                </div>
                <div class="form-group">
                  <label class="config-label">Délai d'Objection</label>
                  <div class="input-with-addon">
                    <input type="number" v-model="config.decision_objection_days" class="input" min="1">
                    <span class="addon">jours</span>
                  </div>
                  <div class="config-key">variable : <code>decision_objection_days</code></div>
                  <p class="help-text">Période critique pendant laquelle un membre peut bloquer une décision.</p>
                </div>
              </div>
              
              <div class="form-group">
                <label class="config-label">Fréquence de Révision par défaut</label>
                <div class="input-with-addon">
                  <input type="number" v-model="config.decision_revision_months" class="input" min="1">
                  <span class="addon">mois</span>
                </div>
                <div class="config-key">variable : <code>decision_revision_months</code></div>
                <p class="help-text">Délai suggéré avant de ré-examiner une proposition adoptée.</p>
              </div>
            </div>
          </div>

          <!-- SECTION ACCÈS -->
          <div v-if="activeSection === 'access'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-teal">
              <div class="pc-header-icon"><i class="fa-solid fa-user-shield"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Sécurité & Accès</div>
                <div class="pc-header-sub">Gestion des inscriptions et politique d'accès</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="toggle-card mb-32">
                <div class="toggle-content">
                   <div class="toggle-title">Inscriptions ouvertes</div>
                   <div class="toggle-description">Permettre à n'importe quel internaute de créer un compte.</div>
                   <div class="config-key inverse">variable : <code>public_registration</code></div>
                </div>
                <label class="switch-lg">
                  <input type="checkbox" v-model="publicRegistrationBool">
                  <span class="slider round"></span>
                </label>
              </div>

              <div class="form-group">
                <label class="config-label">Domaines de confiance</label>
                <input v-model="config.allowed_domains" class="input" placeholder="ex: mon-asso.org, coop.fr">
                <div class="config-key">variable : <code>allowed_domains</code></div>
                <p class="help-text">Restreindre les inscriptions à certains domaines (séparés par des virgules). Laisser vide pour tout autoriser.</p>
              </div>
            </div>
          </div>

          <!-- SECTION NOTIFICATIONS -->
          <div v-if="activeSection === 'notifications'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-indigo">
              <div class="pc-header-icon"><i class="fa-solid fa-bell"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Relances & Notifications</div>
                <div class="pc-header-sub">Configuration des alertes et du moteur de rappel</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="grid-2 gap-32 mb-32">
                <div class="form-group">
                  <label class="config-label">Nom de l'Expéditeur</label>
                  <input v-model="config.mail_sender_name" class="input">
                  <div class="config-key">variable : <code>mail_sender_name</code></div>
                </div>
                <div class="form-group">
                  <label class="config-label">Adresse de Contact</label>
                  <input v-model="config.mail_contact_address" class="input">
                  <div class="config-key">variable : <code>mail_contact_address</code></div>
                </div>
              </div>

              <div class="form-group">
                <label class="config-label">Délai de Relance de Courtoisie</label>
                <div class="input-with-addon">
                  <input type="number" v-model="config.reminder_hours_before" class="input" min="1">
                  <span class="addon">heures</span>
                </div>
                <div class="config-key">variable : <code>reminder_hours_before</code></div>
                <p class="help-text">Les utilisateurs recevront un rappel automatique X heures avant l'échéance d'une décision.</p>
              </div>
            </div>
          </div>

          <!-- SECTION SERVEUR MAIL -->
          <div v-if="activeSection === 'mail_server'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-indigo">
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
          </div>

          <!-- SECTION EMAILS -->
          <div v-if="activeSection === 'emails'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-purple">
              <div class="pc-header-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Personnalisation des Emails</div>
                <div class="pc-header-sub">Éditez le contenu des messages envoyés aux utilisateurs</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="form-group mb-24">
                <label class="config-label">Sujet de l'email de relance</label>
                <input v-model="config.reminder_email_subject" class="input" placeholder="⚠️ Rappel : La décision '{title}' arrive à échéance">
                <p class="help-text">Variables disponibles : <code>{title}</code></p>
              </div>

              <div class="form-group">
                <label class="config-label">Corps de l'email de relance</label>
                <textarea v-model="config.reminder_email_body" class="textarea" style="height: 150px;" placeholder="Bonjour {name}, ceci est un rappel..."></textarea>
                <p class="help-text">Variables disponibles : <code>{name}</code>, <code>{title}</code>, <code>{phase}</code>, <code>{deadline}</code>, <code>{url}</code></p>
              </div>
            </div>
          </div>

          <!-- SECTION MAINTENANCE -->
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

const config = ref({});
const loading = ref(true);
const saving = ref(false);
const activeSection = ref('identity');

const sections = [
  { id: 'identity', label: 'Identité', icon: 'fa-solid fa-fingerprint' },
  { id: 'decisions', label: 'Gouvernance', icon: 'fa-solid fa-scale-balanced' },
  { id: 'access', label: 'Sécurité', icon: 'fa-solid fa-user-shield' },
  { id: 'notifications', label: 'Alertes', icon: 'fa-solid fa-bell' },
  { id: 'mail_server', label: 'Serveur Mail', icon: 'fa-solid fa-server' },
  { id: 'emails', label: 'Contenu Emails', icon: 'fa-solid fa-envelope-open-text' },
  { id: 'system', label: 'Maintenance', icon: 'fa-solid fa-triangle-exclamation' },
];

const publicRegistrationBool = computed({
  get: () => config.value.public_registration === 'true',
  set: (val) => config.value.public_registration = val ? 'true' : 'false'
});

const maintenanceModeBool = computed({
  get: () => config.value.maintenance_mode === 'true',
  set: (val) => config.value.maintenance_mode = val ? 'true' : 'false'
});

const processedLogoUrl = computed(() => {
  if (!config.value.app_logo) return null;
  if (config.value.app_logo.startsWith('http')) return config.value.app_logo;
  return '/storage/' + config.value.app_logo;
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
    alert('Configuration enregistrée avec succès.');
  } catch (e) {
    alert('Erreur lors de la sauvegarde.');
  } finally {
    saving.value = false;
  }
};

const handleLogoUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('logo', file);

  try {
    const { data } = await axios.post('/api/v1/admin/config/logo', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    config.value = data.config;
    alert('Logo mis à jour.');
  } catch (e) {
    alert('Erreur lors de l\'upload du logo.');
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

/* Config Fields */
.config-label { display: block; font-size: 15px; font-weight: 800; color: var(--gray-800); margin-bottom: 12px; }
.config-key { font-family: var(--font-mono); font-size: 10px; color: var(--gray-400); margin-top: 8px; display: block; }
.config-key code { background: var(--gray-100); padding: 2px 6px; border-radius: 4px; color: var(--gray-500); }
.config-key.inverse { margin-top: 4px; margin-bottom: 0; }

.input-lg { padding: 14px 18px; font-size: 16px; }

.input-with-addon { display: flex; align-items: center; }
.input-with-addon .input { border-top-right-radius: 0; border-bottom-right-radius: 0; }
.input-with-addon .addon { 
  background: var(--gray-100); border: 1px solid var(--gray-300); border-left: none;
  padding: 0 16px; height: 44px; display: flex; align-items: center; font-size: 13px;
  font-weight: 600; color: var(--gray-500); border-radius: 0 8px 8px 0;
}

/* Logo Dropzone */
.logo-dropzone {
  display: flex; gap: 32px; align-items: center; padding: 24px;
  background: var(--gray-50); border: 2px dashed var(--gray-300); border-radius: 20px;
  cursor: pointer; transition: all 0.2s;
}
.logo-dropzone:hover { border-color: var(--blue-400); background: var(--blue-50); }

.logo-preview-large {
  width: 140px; height: 140px; background: white; border-radius: 16px; border: 1px solid var(--gray-200);
  position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05); flex-shrink: 0;
}
.logo-preview-large img { max-width: 80%; max-height: 80%; object-fit: contain; }
.no-logo-large { font-size: 48px; color: var(--gray-200); }

.logo-overlay {
  position: absolute; inset: 0; background: rgba(59, 130, 246, 0.9); color: white;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 8px; font-size: 12px; font-weight: 700; opacity: 0; transition: opacity 0.2s;
}
.logo-preview-large:hover .logo-overlay { opacity: 1; }

.logo-info { flex: 1; }

/* Toggle Cards */
.toggle-card { display: flex; justify-content: space-between; align-items: center; padding: 24px; background: var(--gray-50); border-radius: 16px; border: 1px solid var(--gray-100); }
.toggle-title { font-size: 16px; font-weight: 800; color: var(--gray-800); }
.toggle-description { font-size: 13px; color: var(--gray-500); margin-top: 4px; }

/* Colors */
.color-grid { display: flex; gap: 12px; }
.color-option { 
  flex: 1; padding: 12px; border-radius: 12px; border: 2px solid var(--gray-200);
  background: white; cursor: pointer; display: flex; align-items: center; gap: 10px;
  font-size: 12px; font-weight: 700; color: var(--gray-600);
}
.color-option::before { content: ""; width: 12px; height: 12px; border-radius: 50%; background: var(--c); }
.color-option.active { border-color: var(--c); background: rgba(var(--c-rgb, 59, 130, 246), 0.05); color: var(--gray-800); }
.color-option.disabled { opacity: 0.5; cursor: not-allowed; filter: grayscale(1); }

/* Emails placeholder */
.email-placeholder-list { display: flex; flex-direction: column; gap: 12px; }
.email-item {
  display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;
  background: white; border: 1px solid var(--gray-200); border-radius: 12px;
  cursor: pointer; transition: all 0.2s;
}
.email-item:hover { border-color: var(--blue-300); background: var(--blue-50); }
.email-item.active { border-color: var(--blue-600); background: var(--blue-50); }

/* Helpers */
.help-text { font-size: 12px; color: var(--gray-500); margin-top: 8px; line-height: 1.5; }
.text-red { color: var(--red-600); }
.border-red { border: 1px solid var(--red-200); }
.border-red-soft { border: 1px solid var(--red-100); background: #fff5f5; }
.shadow-blue { box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4); }

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Switch Toggle LG */
.switch-lg { position: relative; display: inline-block; width: 60px; height: 32px; }
.switch-lg input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--gray-300); transition: .3s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 24px; width: 24px; left: 4px; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
input:checked + .slider { background-color: var(--blue-600); }
input:checked + .slider:before { transform: translateX(28px); }
.switch-lg.danger input:checked + .slider { background-color: var(--red-600); }

@media (max-width: 1024px) {
  .config-layout { flex-direction: column; }
  .config-nav { width: 100%; position: static; flex-direction: row; display: flex; overflow-x: auto; gap: 8px; padding-bottom: 8px; }
  .nav-item { width: auto; white-space: nowrap; margin-bottom: 0; }
  .nav-group-title { display: none; }
}
</style>

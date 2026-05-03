<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Configuration du Site</div>
            <div class="hero-subtitle">Paramétrez l'identité, la gouvernance et les règles de votre instance.</div>
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
        <p>Chargement de la configuration...</p>
      </div>
      
      <div v-else class="config-layout mt-32">
        <!-- NAVIGATION GAUCHE -->
        <aside class="config-nav">
          <div class="nav-group-title">Menu Site</div>
          <button v-for="s in filteredSections" :key="s.id" 
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
                  </div>
                  <input type="file" ref="logoInput" style="display: none" @change="handleLogoUpload" accept="image/*">
                </div>
              </div>

              <div class="form-group mb-16">
                <label class="config-label">Nom de l'organisation</label>
                <input v-model="config.app_name" class="input input-lg" placeholder="ex: DAZO Collective">
                <p class="help-text">Nom affiché dans la barre de titre, les menus et les communications sortantes.</p>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer
              </button>
            </div>
          </div>

          <!-- SECTION THEME PUBLIC -->
          <div v-if="activeSection === 'theme_public'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-emerald">
              <div class="pc-header-icon"><i class="fa-solid fa-palette"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Thème Public</div>
                <div class="pc-header-sub">Apparence de l'interface de consultation publique</div>
              </div>
            </div>
            <div class="pc-body p-48 text-center">
                <img :src="configStore.defaultLogoUrl" alt="DAZO" style="height: 64px; margin: 0 auto 24px; opacity: 0.2;" />
                <h3 class="text-lg font-bold text-gray-800 mb-8">Personnalisation du thème public</h3>
                <p class="text-muted italic max-w-400 mx-auto">Prochainement : gestion de la palette de couleurs, des polices et des styles personnalisés pour votre interface publique.</p>
            </div>
          </div>

          <!-- SECTION THEME MEETING -->
          <div v-if="activeSection === 'theme_meeting'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-purple">
              <div class="pc-header-icon"><i class="fa-solid fa-desktop"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Thème Meeting</div>
                <div class="pc-header-sub">Apparence de l'interface de prise de décision en réunion</div>
              </div>
            </div>
            <div class="pc-body p-48 text-center">
                <img :src="configStore.defaultLogoUrl" alt="DAZO" style="height: 64px; margin: 0 auto 24px; opacity: 0.2;" />
                <h3 class="text-lg font-bold text-gray-800 mb-8">Personnalisation du thème Meeting</h3>
                <p class="text-muted italic max-w-400 mx-auto">Prochainement : thèmes sombres/clairs, ajustements de contraste et personnalisation visuelle du mode réunion.</p>
            </div>
          </div>

          <!-- SECTION GOUVERNANCE -->
          <div v-if="activeSection === 'decisions'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-amber">
              <div class="pc-header-icon"><i class="fa-solid fa-scale-balanced"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Gouvernance</div>
                <div class="pc-header-sub">Règles de décision et délais par défaut</div>
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
                  <p class="help-text">Temps alloué pour collecter les avis des membres.</p>
                </div>
                <div class="form-group">
                  <label class="config-label">Délai d'Objection</label>
                  <div class="input-with-addon">
                    <input type="number" v-model="config.decision_objection_days" class="input" min="1">
                    <span class="addon">jours</span>
                  </div>
                  <p class="help-text">Période critique pendant laquelle un membre peut bloquer une décision.</p>
                </div>
              </div>
              
              <div class="form-group">
                <label class="config-label">Fréquence de Révision par défaut</label>
                <div class="input-with-addon">
                  <input type="number" v-model="config.decision_revision_months" class="input" min="1">
                  <span class="addon">mois</span>
                </div>
                <p class="help-text">Délai suggéré avant de ré-examiner une proposition adoptée.</p>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer
              </button>
            </div>
          </div>

          <!-- SECTION SÉCURITÉ -->
          <div v-if="activeSection === 'access'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-teal">
              <div class="pc-header-icon"><i class="fa-solid fa-user-shield"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Sécurité & Accès</div>
                <div class="pc-header-sub">Gestion des inscriptions et du front public</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="toggle-card mb-24">
                <div class="toggle-content">
                   <div class="toggle-title">Front Public Activé</div>
                   <div class="toggle-description">Active la page d'accueil publique pour consulter les décisions sans connexion.</div>
                </div>
                <label class="switch-lg">
                  <input type="checkbox" v-model="enablePublicFrontBool">
                  <span class="slider round"></span>
                </label>
              </div>

              <div class="toggle-card mb-24">
                <div class="toggle-content">
                   <div class="toggle-title">Inscriptions ouvertes</div>
                   <div class="toggle-description">Permettre à n'importe quel internaute de créer un compte.</div>
                </div>
                <label class="switch-lg">
                  <input type="checkbox" v-model="publicRegistrationBool">
                  <span class="slider round"></span>
                </label>
              </div>

              <div class="toggle-card mb-32" v-if="publicRegistrationBool">
                <div class="toggle-content">
                   <div class="toggle-title">Approbation Administrateur Requise</div>
                   <div class="toggle-description">Les nouveaux comptes doivent être validés manuellement par un administrateur.</div>
                </div>
                <label class="switch-lg">
                  <input type="checkbox" v-model="requireAdminApprovalBool">
                  <span class="slider round"></span>
                </label>
              </div>

              <div class="form-group">
                <label class="config-label">Domaines de confiance</label>
                <input v-model="config.allowed_domains" class="input" placeholder="ex: mon-asso.org, coop.fr">
                <p class="help-text">Restreindre les inscriptions à certains domaines (séparés par des virgules).</p>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer
              </button>
            </div>
          </div>

          <!-- SECTION ALERTES -->
          <div v-if="activeSection === 'notifications'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-indigo">
              <div class="pc-header-icon"><i class="fa-solid fa-bell"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Relances & Alertes</div>
                <div class="pc-header-sub">Configuration des rappels automatiques</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="grid-2 gap-32 mb-32">
                <div class="form-group">
                  <label class="config-label">Nom de l'Expéditeur</label>
                  <input v-model="config.mail_sender_name" class="input">
                </div>
                <div class="form-group">
                  <label class="config-label">Adresse de Contact</label>
                  <input v-model="config.mail_contact_address" class="input">
                </div>
              </div>

              <div class="form-group">
                <label class="config-label">Délai de Relance de Courtoisie</label>
                <div class="input-with-addon">
                  <input type="number" v-model="config.reminder_hours_before" class="input" min="1">
                  <span class="addon">heures</span>
                </div>
                <p class="help-text">Rappel automatique X heures avant l'échéance d'une décision.</p>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer
              </button>
            </div>
          </div>

          <!-- SECTION EMAILS -->
          <div v-if="activeSection === 'emails'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Contenu des Emails</div>
                <div class="pc-header-sub">Personnalisez les messages envoyés aux membres</div>
              </div>
            </div>
            <div class="pc-body p-24">
                <!-- ACCORDIONS FOR EMAILS (Extracted from original) -->
                <div v-for="type in mailTypes" :key="type.key" class="mail-accordion mb-16">
                  <div class="mail-accordion-header" @click="toggleAccordionEmail(type.key)">
                    <div class="flex items-center gap-12">
                      <i class="fa-solid fa-chevron-right transition-transform" :class="{ 'rotate-90': activeAccordionEmail === type.key }"></i>
                      <span class="font-bold text-gray-700">{{ type.label }}</span>
                    </div>
                  </div>
                  <transition name="accordion">
                    <div v-if="activeAccordionEmail === type.key" class="mail-accordion-body p-20 bg-white border-top">
                      <div class="form-group mb-20">
                        <label class="config-label text-sm">Objet de l'email</label>
                        <input v-model="config['mail_' + type.key + '_subject']" class="input" :placeholder="type.defaultSubject">
                      </div>
                      <div class="form-group">
                        <label class="config-label text-sm">Corps du message (HTML autorisé)</label>
                        <RichTextEditor v-model="config['mail_' + type.key + '_body']" :minHeight="'200px'" />
                        <p class="help-text mt-8">Variables disponibles : <code>{user_name}</code>, <code>{decision_title}</code>, <code>{link}</code>, <code>{app_name}</code></p>
                      </div>
                    </div>
                  </transition>
                </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Emails
              </button>
            </div>
          </div>

          <!-- SECTION PAGES -->
          <div v-if="activeSection === 'pages'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-teal">
              <div class="pc-header-icon"><i class="fa-solid fa-file-lines"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Pages de Contenu</div>
                <div class="pc-header-sub">Édition des mentions légales et autres pages statiques</div>
              </div>
            </div>
            <div class="pc-body p-24">
                <div v-for="page in staticPages" :key="page.key" class="mail-accordion mb-16">
                  <div class="mail-accordion-header" @click="toggleAccordion(page.key)">
                    <div class="flex items-center gap-12">
                      <i class="fa-solid fa-chevron-right transition-transform" :class="{ 'rotate-90': activeAccordion === page.key }"></i>
                      <span class="font-bold text-gray-700">{{ page.label }}</span>
                    </div>
                  </div>
                  <transition name="accordion">
                    <div v-if="activeAccordion === page.key" class="mail-accordion-body p-20 bg-white border-top">
                      <RichTextEditor v-model="config['page_' + page.key + '_content']" :minHeight="'300px'" />
                    </div>
                  </transition>
                </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import RichTextEditor from '../../components/RichTextEditor.vue';
import { useConfigStore } from '../../stores/config';

const configStore = useConfigStore();
const config = ref({});
const loading = ref(true);
const saving = ref(false);
const activeSection = ref('identity');

const sections = [
  { id: 'identity', label: 'Identité', icon: 'fa-solid fa-fingerprint' },
  { id: 'theme_public', label: 'Thème Public', icon: 'fa-solid fa-palette' },
  { id: 'theme_meeting', label: 'Thème Meeting', icon: 'fa-solid fa-desktop' },
  { id: 'decisions', label: 'Gouvernance', icon: 'fa-solid fa-scale-balanced' },
  { id: 'access', label: 'Sécurité', icon: 'fa-solid fa-user-shield' },
  { id: 'notifications', label: 'Alertes', icon: 'fa-solid fa-bell' },
  { id: 'emails', label: 'Contenu Emails', icon: 'fa-solid fa-envelope-open-text' },
  { id: 'pages', label: 'Pages de contenu', icon: 'fa-solid fa-file-lines' },
];

const filteredSections = computed(() => sections);

const mailTypes = [
  { key: 'new_decision', label: 'Nouvelle Décision', defaultSubject: 'Nouvelle proposition de décision' },
  { key: 'phase_change', label: 'Changement de Phase', defaultSubject: 'Une décision a changé de phase' },
  { key: 'reminder', label: 'Rappel Échéance', defaultSubject: 'Rappel : Action requise sur une décision' },
  { key: 'decision_adopted', label: 'Décision Adoptée', defaultSubject: 'Une décision a été adoptée' },
  { key: 'decision_rejected', label: 'Décision Refusée', defaultSubject: 'Une décision n\'a pas été adoptée' },
];

const staticPages = [
  { key: 'legal', label: 'Mentions Légales' },
  { key: 'privacy', label: 'Confidentialité' },
  { key: 'terms', label: 'CGU / CGV' },
];

const activeAccordion = ref(null);
const activeAccordionEmail = ref(null);

const toggleAccordion = (key) => activeAccordion.value = activeAccordion.value === key ? null : key;
const toggleAccordionEmail = (key) => activeAccordionEmail.value = activeAccordionEmail.value === key ? null : key;

const fetchConfig = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/config');
    config.value = data.config || {};
  } catch (e) {
    console.error("Fetch config error", e);
  } finally {
    loading.value = false;
  }
};

const saveConfig = async () => {
  saving.value = true;
  try {
    await axios.post('/api/v1/admin/config', { config: config.value });
    alert('Configuration enregistrée avec succès.');
    configStore.fetchInit(); // Refresh global config
  } catch (e) {
    alert('Erreur lors de la sauvegarde.');
  } finally {
    saving.value = false;
  }
};

// Logo Upload
const processedLogoUrl = computed(() => {
  if (config.value.app_logo && config.value.app_logo.startsWith('data:')) return config.value.app_logo;
  if (config.value.app_logo) return `/storage/${config.value.app_logo}`;
  return null;
});

const handleLogoUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (event) => config.value.app_logo = event.target.result;
  reader.readAsDataURL(file);
};

// Booleans computed
const enablePublicFrontBool = computed({
  get: () => config.value.enable_public_front === 'true' || config.value.enable_public_front === true,
  set: (v) => config.value.enable_public_front = v ? 'true' : 'false'
});
const publicRegistrationBool = computed({
  get: () => config.value.public_registration === 'true' || config.value.public_registration === true,
  set: (v) => config.value.public_registration = v ? 'true' : 'false'
});
const requireAdminApprovalBool = computed({
  get: () => config.value.require_admin_approval === 'true' || config.value.require_admin_approval === true,
  set: (v) => config.value.require_admin_approval = v ? 'true' : 'false'
});

onMounted(fetchConfig);
</script>

<style scoped>
@import "../../../css/admin-config.css"; /* Assuming shared styles are moved or copied */
.nav-item.active { background: var(--teal-600); border-color: var(--teal-700); }
.pc-header-emerald { background: linear-gradient(135deg, #10b981, #059669); color: white; }
</style>

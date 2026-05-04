<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Configuration du Site</div>
            <div class="hero-subtitle">Pilotez l'identité, la gouvernance et les communications de la plateforme.</div>
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
          <div class="nav-group-title">Paramètres Site</div>
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

            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Identity
              </button>
            </div>
          </div>


          <!-- SECTION AFFICHAGE PUBLIC -->
          <div v-if="activeSection === 'public_display'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-globe"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Affichage Public</div>
                <div class="pc-header-sub">Sélectionnez quelles décisions sont visibles sur l'interface publique</div>
              </div>
            </div>
            <div class="pc-body p-24">

              <!-- CERCLES -->
              <div class="form-group mb-32">
                <label class="config-label">Cercles autorisés</label>
                <p class="help-text mb-12">Seules les décisions appartenant à ces cercles pourront être exposées publiquement.</p>
                <div class="checkbox-list inline-list">
                  <label v-for="circle in circles" :key="circle.id" class="checkbox-item">
                    <input type="checkbox" :value="circle.id" v-model="config.public_circles">
                    <span>{{ circle.name }}</span>
                  </label>
                  <div v-if="circles.length === 0" class="text-muted p-16 text-xs italic">Aucun cercle trouvé.</div>
                </div>
                <div class="config-key">variable : <code>public_circles</code></div>
              </div>

              <!-- CATEGORIES -->
              <div class="form-group mb-32">
                <label class="config-label">Catégories autorisées</label>
                <p class="help-text mb-12">Seules les décisions rattachées à ces catégories pourront être exposées.</p>
                <div class="checkbox-list inline-list">
                  <label v-for="cat in categories" :key="cat.id" class="checkbox-item">
                    <input type="checkbox" :value="cat.id" v-model="config.public_categories">
                    <span>{{ cat.name }}</span>
                  </label>
                  <div v-if="categories.length === 0" class="text-muted p-16 text-xs italic">Aucune catégorie trouvée.</div>
                </div>
                <div class="config-key">variable : <code>public_categories</code></div>
              </div>

              <!-- STATUTS -->
              <div class="form-group">
                <label class="config-label">Statuts exposés</label>
                <p class="help-text mb-12">Généralement, seules les décisions finales (adoptées, rejetées) sont rendues publiques.</p>
                <div class="checkbox-list inline-list">
                  <label v-for="status in availableStatuses" :key="status.value" class="checkbox-item">
                    <input type="checkbox" :value="status.value" v-model="config.public_statuses">
                    <span>{{ status.label }}</span>
                  </label>
                </div>
                <div class="config-key">variable : <code>public_statuses</code></div>
              </div>

            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Affichage
              </button>
            </div>
          </div>


          <!-- SECTION THEME PUBLIC -->
          <div v-if="activeSection === 'theme_public'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-globe"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Thème Front Public</div>
                <div class="pc-header-sub">Apparence de l'interface de consultation publique</div>
              </div>
            </div>
            <div class="pc-body p-48 text-center">
              <i class="fa-solid fa-palette fa-3x mb-16" style="color: var(--teal-500); opacity: 0.3;"></i>
              <h3 class="text-lg font-bold text-gray-800 mb-8">Personnalisation du thème public</h3>
              <p class="text-muted italic" style="max-width: 420px; margin: 0 auto;">Prochainement : gestion de la palette de couleurs, des polices et des styles personnalisés pour votre interface publique.</p>
            </div>
          </div>

          <!-- SECTION THEME MEETING -->
          <div v-if="activeSection === 'theme_meeting'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-desktop"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Thème Mode Meeting</div>
                <div class="pc-header-sub">Apparence de l'interface de prise de décision en réunion</div>
              </div>
            </div>
            <div class="pc-body p-48 text-center">
              <i class="fa-solid fa-display fa-3x mb-16" style="color: #845ef7; opacity: 0.3;"></i>
              <h3 class="text-lg font-bold text-gray-800 mb-8">Personnalisation du thème Meeting</h3>
              <p class="text-muted italic" style="max-width: 420px; margin: 0 auto;">Prochainement : thèmes sombres/clairs, ajustements de contraste et personnalisation visuelle du mode réunion.</p>
            </div>
          </div>

          <!-- SECTION DÉCISIONS -->

          <div v-if="activeSection === 'decisions'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
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
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Gouvernance
              </button>
            </div>
          </div>

          <!-- SECTION PHASES -->
          <div v-if="activeSection === 'phases'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-layer-group"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Couleurs des Phases</div>
                <div class="pc-header-sub">Personnalisez l'affichage visuel des différentes étapes d'une décision</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <p class="help-text mb-24">Définissez les couleurs qui seront utilisées pour identifier chaque phase dans les listes et les détails de décisions.</p>
              
              <div class="phases-config-grid">
                <div v-for="phase in availableStatuses" :key="phase.value" class="phase-config-card">
                  <div class="phase-config-header">
                    <div class="phase-preview-badge" 
                         :style="{ 
                           color: config['phase_' + phase.value + '_primary'] || phase.defaultPrimary,
                           borderColor: config['phase_' + phase.value + '_primary'] || phase.defaultPrimary,
                           backgroundColor: config['phase_' + phase.value + '_secondary'] || phase.defaultSecondary
                         }">
                      {{ phase.label }}
                    </div>
                  </div>
                  
                  <div class="phase-config-body">
                    <div class="form-group mb-12">
                      <label class="text-xs font-bold text-gray-500 mb-4 block">Couleur Primaire (Texte/Bordure)</label>
                      <div class="color-picker-wrapper">
                        <input type="color" v-model="config['phase_' + phase.value + '_primary']" class="color-input">
                        <input type="text" v-model="config['phase_' + phase.value + '_primary']" class="input input-sm font-mono" placeholder="#000000">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="text-xs font-bold text-gray-500 mb-4 block">Couleur Secondaire (Fond)</label>
                      <div class="color-picker-wrapper">
                        <input type="color" v-model="config['phase_' + phase.value + '_secondary']" class="color-input">
                        <input type="text" v-model="config['phase_' + phase.value + '_secondary']" class="input input-sm font-mono" placeholder="#000000">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer les Phases
              </button>
            </div>
          </div>


          <div v-if="activeSection === 'access'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-user-shield"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Sécurité & Accès</div>
                <div class="pc-header-sub">Gestion des inscriptions, du front public et des protections</div>
              </div>
            </div>
            <div class="pc-body p-24">

              <div class="toggle-card mb-24">
                <div class="toggle-content">
                   <div class="toggle-title">Front Public Activé</div>
                   <div class="toggle-description">Active la page d'accueil publique pour consulter les décisions sans connexion.</div>
                   <div class="config-key inverse">variable : <code>enable_public_front</code></div>
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
                   <div class="config-key inverse">variable : <code>public_registration</code></div>
                </div>
                <label class="switch-lg">
                  <input type="checkbox" v-model="publicRegistrationBool">
                  <span class="slider round"></span>
                </label>
              </div>

              <div class="toggle-card mb-32" v-if="publicRegistrationBool">
                <div class="toggle-content">
                   <div class="toggle-title">Approbation Administrateur Requise</div>
                   <div class="toggle-description">Les nouveaux comptes doivent être validés manuellement par un administrateur avant de pouvoir se connecter.</div>
                   <div class="config-key inverse">variable : <code>require_admin_approval</code></div>
                </div>
                <label class="switch-lg">
                  <input type="checkbox" v-model="requireAdminApprovalBool">
                  <span class="slider round"></span>
                </label>
              </div>

              <div class="form-group mb-32">
                <label class="config-label">Domaines de confiance</label>
                <input v-model="config.allowed_domains" class="input" placeholder="ex: mon-asso.org, coop.fr">
                <div class="config-key">variable : <code>allowed_domains</code></div>
                <p class="help-text">Restreindre les inscriptions à certains domaines (séparés par des virgules). Laisser vide pour tout autoriser.</p>
              </div>

              <hr class="mb-32" style="border: none; border-top: 1px solid var(--gray-200);" />

              <h3 class="font-bold text-gray-800 mb-16"><i class="fa-brands fa-google mr-8 text-blue-500"></i> Protection Google reCAPTCHA (v2 ou v3)</h3>
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
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Sécurité
              </button>
            </div>
          </div>


          <div v-if="activeSection === 'notifications'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
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
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Alertes
              </button>
            </div>
          </div>


          <div v-if="activeSection === 'emails'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Personnalisation des Emails</div>
                <div class="pc-header-sub">Éditez le contenu des messages envoyés aux utilisateurs</div>
              </div>
            </div>
            <div class="pc-body">
              <div class="accordion-container">
                <!-- TEMPLATE GÉNÉRAL (PREMIER MENU) -->
                <div class="accordion-item" :class="{ open: activeAccordionEmail === 'general' }">
                  <div class="accordion-header" @click="toggleAccordionEmail('general')">
                    <div class="flex items-center gap-12">
                      <i class="fa-solid fa-gears text-indigo-600"></i>
                      <span class="font-bold text-indigo-700">Template Général (Global Wrapper)</span>
                    </div>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                  </div>
                  <div class="accordion-body">
                    <div class="p-24 border-top bg-indigo-50/10">
                      
                      <div class="alert alert-info mb-24">
                        <i class="fa-solid fa-circle-info"></i>
                        <div>
                          <p class="font-bold mb-4">Variables disponibles :</p>
                          <div class="flex flex-wrap gap-8">
                            <code class="bg-blue-100 text-blue-700 px-4 py-2 rounded"> {logo} </code>
                            <code class="bg-blue-100 text-blue-700 px-4 py-2 rounded"> {logo_perso} </code>
                            <code class="bg-blue-100 text-blue-700 px-4 py-2 rounded"> {site_link} </code>
                            <code class="bg-blue-100 text-blue-700 px-4 py-2 rounded"> {site_link_register} </code>
                            <code class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded font-bold"> {message} </code>
                          </div>
                          <p class="mt-8 text-xs opacity-80">Les variables sont alimentées par les configurations correspondantes (Branding, Identité, etc.).</p>
                        </div>
                      </div>

                      <div class="form-group mb-32">
                        <label class="config-label">Structure HTML du Wrapper</label>
                        <textarea v-model="config.mail_template_wrapper" class="input font-mono text-sm" rows="12" placeholder="<html>... {message} ...</html>"></textarea>
                        <p class="help-text mt-8">C'est le squelette HTML commun à tous les envois.</p>
                      </div>

                      <!-- PREVIEW SECTION -->
                      <div class="preview-section mt-32 border-top pt-24">
                        <div class="flex items-center justify-between mb-16">
                          <h4 class="font-bold text-gray-800"><i class="fa-solid fa-eye mr-8 text-blue-500"></i> Prévisualisation en temps réel</h4>
                          <div class="flex items-center gap-12">
                            <span class="text-xs font-bold text-gray-500">Simuler le message :</span>
                            <select v-model="previewMailType" class="select select-sm py-4 px-12 h-auto text-xs">
                              <option v-for="mail in emailList" :key="mail.key" :value="mail.key">{{ mail.label }}</option>
                            </select>
                          </div>
                        </div>
                        
                        <div class="mail-preview-container border rounded-xl overflow-hidden shadow-sm bg-white">
                          <div class="preview-header bg-gray-100 border-bottom p-12 text-xs flex gap-8 items-center">
                            <div class="flex gap-4">
                              <div class="w-8 h-8 rounded-full bg-red-400"></div>
                              <div class="w-8 h-8 rounded-full bg-amber-400"></div>
                              <div class="w-8 h-8 rounded-full bg-green-400"></div>
                            </div>
                            <span class="text-gray-400 ml-8">Sujet : {{ mockedSubject }}</span>
                          </div>
                          <div class="preview-body" v-html="renderedPreview"></div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- AUTRES EMAILS -->
                <div v-for="mail in emailList" :key="mail.key" class="accordion-item" :class="{ open: activeAccordionEmail === mail.key }">
                  <div class="accordion-header" @click="toggleAccordionEmail(mail.key)">
                    <div class="flex items-center gap-12 flex-1">
                      <i :class="mail.icon" class="text-indigo-500 opacity-60"></i>
                      <span class="font-bold">{{ mail.label }}</span>
                    </div>
                    <div class="flex items-center gap-12" @click.stop>
                      <span class="text-xs font-semibold text-gray-400">{{ config['mail_' + mail.key + '_enabled'] === 'true' ? 'Activé' : 'Désactivé' }}</span>
                      <label class="switch-sm">
                        <input type="checkbox" v-model="config['mail_' + mail.key + '_enabled']" true-value="true" false-value="false">
                        <span class="slider round"></span>
                      </label>
                      <i class="fa-solid fa-chevron-down arrow ml-8"></i>
                    </div>
                  </div>
                  <div class="accordion-body">
                    <div class="p-24 border-top">
                      <div class="form-group mb-24">
                        <label class="config-label">Sujet de l'email</label>
                        <input v-model="config['mail_' + mail.key + '_subject']" class="input" :placeholder="'Sujet de ' + mail.label">
                      </div>
                      <div class="form-group mb-32">
                        <label class="config-label">Corps de l'email</label>
                        <RichTextEditor v-model="config['mail_' + mail.key + '_body']" />
                        <p class="help-text mt-8"><code>{{ mail.help }}</code></p>
                      </div>

                      <!-- TEST D'ENVOI -->
                      <div class="p-20 bg-gray-50 border rounded-xl">
                        <h4 class="font-bold text-sm mb-12"><i class="fa-solid fa-paper-plane mr-8 text-indigo-500"></i> Tester cet email</h4>
                        <div class="flex gap-12">
                          <input type="email" v-model="testEmailAddress" class="input" placeholder="Adresse email de test">
                          <button class="btn btn-primary btn-icon" @click="sendTestEmail(mail.key)" :disabled="sendingTest === mail.key" title="Envoyer un test">
                             <i class="fa-solid" :class="sendingTest === mail.key ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                          </button>
                        </div>
                        <p class="help-text mt-8">L'email sera envoyé avec des données fictives à <strong>{{ testEmailAddress || 'votre adresse' }}</strong>.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Emails
              </button>
            </div>
          </div>

          <!-- SECTION OAUTH -->

          <div v-if="activeSection === 'pages'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-file-lines"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Gestion des Pages</div>
                <div class="pc-header-sub">Éditez le contenu des pages légales et informatives</div>
              </div>
            </div>
            <div class="pc-body">
              <div class="accordion-container">
                <div v-for="page in pagesList" :key="page.key" class="accordion-item" :class="{ open: activeAccordion === page.key }">
                  <div class="accordion-header" @click="toggleAccordion(page.key)">
                    <div class="flex items-center gap-12">
                      <i :class="page.icon" class="text-blue-500 opacity-60"></i>
                      <span class="font-bold">{{ page.label }} ({{ config['page_' + page.key + '_slug'] }})</span>
                    </div>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                  </div>
                  <div class="accordion-body">
                    <div class="p-24 border-top">
                      <div class="toggle-card mb-24 bg-white border">
                        <div class="toggle-content">
                           <div class="toggle-title">Activer la page {{ page.label }}</div>
                           <div class="toggle-description">Rendre cette page accessible publiquement via son URL et dans le pied de page.</div>
                        </div>
                        <label class="switch-lg">
                          <input type="checkbox" v-model="config['page_' + page.key + '_enabled']" true-value="true" false-value="false">
                          <span class="slider round"></span>
                        </label>
                      </div>
                      <div class="grid-2 gap-24 mb-24">
                        <div class="form-group">
                          <label class="config-label">Titre de la page</label>
                          <input v-model="config['page_' + page.key + '_title']" class="input" placeholder="Ex: Mentions Légales">
                        </div>
                        <div class="form-group">
                          <label class="config-label">Slug (URL)</label>
                          <input v-model="config['page_' + page.key + '_slug']" class="input" placeholder="ex: mentions-legales">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="config-label">Contenu HTML</label>
                        <RichTextEditor v-model="config['page_' + page.key + '_content']" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Pages
              </button>
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
const sendingTest = ref(null);
const circles = ref([]);
const categories = ref([]);
const testEmailAddress = ref('');
const activeSection = ref('identity');
const currentUser = ref(null);
const previewMailType = ref('reminder');

const sections = [
  { id: 'identity', label: 'Identité', icon: 'fa-solid fa-fingerprint' },
  { id: 'public_display', label: 'Affichage Public', icon: 'fa-solid fa-globe' },
  { id: 'theme_public', label: 'Thème Public', icon: 'fa-solid fa-globe' },
  { id: 'theme_meeting', label: 'Thème Meeting', icon: 'fa-solid fa-desktop' },
  { id: 'decisions', label: 'Gouvernance', icon: 'fa-solid fa-scale-balanced' },
  { id: 'phases', label: 'Phases', icon: 'fa-solid fa-layer-group' },
  { id: 'access', label: 'Sécurité', icon: 'fa-solid fa-user-shield' },
  { id: 'notifications', label: 'Alertes', icon: 'fa-solid fa-bell' },
  { id: 'emails', label: 'Contenu Emails', icon: 'fa-solid fa-envelope-open-text' },
  { id: 'pages', label: 'Pages de contenu', icon: 'fa-solid fa-file-lines' },
];

const activeAccordion = ref(null);
const activeAccordionEmail = ref(null);

const toggleAccordion = (key) => {
  activeAccordion.value = activeAccordion.value === key ? null : key;
};

const toggleAccordionEmail = (key) => {
  activeAccordionEmail.value = activeAccordionEmail.value === key ? null : key;
};


const availableStatuses = [
  { value: 'draft', label: 'Brouillon', defaultPrimary: '#64748b', defaultSecondary: '#f1f5f9' },
  { value: 'clarification', label: 'Clarification', defaultPrimary: '#f59e0b', defaultSecondary: '#fffbeb' },
  { value: 'reaction', label: 'Réaction', defaultPrimary: '#3b82f6', defaultSecondary: '#eff6ff' },
  { value: 'objection', label: 'Objection', defaultPrimary: '#ef4444', defaultSecondary: '#fef2f2' },
  { value: 'revision', label: 'En Révision', defaultPrimary: '#8b5cf6', defaultSecondary: '#f5f3ff' },
  { value: 'adopted', label: 'Adoptée', defaultPrimary: '#10b981', defaultSecondary: '#ecfdf5' },
  { value: 'suspended', label: 'Suspendue', defaultPrimary: '#6366f1', defaultSecondary: '#eef2ff' },
  { value: 'abandoned', label: 'Abandonnée', defaultPrimary: '#94a3b8', defaultSecondary: '#f8fafc' },
  { value: 'rejected', label: 'Rejetée', defaultPrimary: '#475569', defaultSecondary: '#f1f5f9' },
];

const pagesList = [
  { key: 'legal', label: 'Mentions Légales', icon: 'fa-solid fa-scale-balanced' },
  { key: 'privacy', label: 'Politique de Confidentialité', icon: 'fa-solid fa-user-lock' },
  { key: 'terms', label: 'Conditions Générales (CGU)', icon: 'fa-solid fa-handshake-angle' }
];

const emailList = [
  { key: 'reminder', label: 'Rappel de décision', icon: 'fa-solid fa-clock-rotate-left', help: 'Variables : {name}, {title}, {phase}, {deadline}, {url}' },
  { key: 'extended', label: 'Relance Prolongation', icon: 'fa-solid fa-clock-rotate-left', help: 'Variables : {name}, {title}, {phase}, {deadline}, {url}' },
  { key: 'invitation', label: 'Invitation au cercle', icon: 'fa-solid fa-envelope-open-text', help: 'Variables : {inviter}, {circle}, {description}, {url}' },
  { key: 'notification', label: 'Nouvelle phase', icon: 'fa-solid fa-bullhorn', help: 'Variables : {name}, {title}, {phase}, {url}' },
  { key: 'contact', label: 'Message de contact', icon: 'fa-solid fa-message', help: 'Variables : {name}, {email}, {subject}, {message}' },
];

const publicRegistrationBool = computed({
  get: () => config.value.public_registration === 'true',
  set: (val) => config.value.public_registration = val ? 'true' : 'false'
});

const enablePublicFrontBool = computed({
  get: () => config.value.enable_public_front === 'true',
  set: (val) => config.value.enable_public_front = val ? 'true' : 'false'
});

const requireAdminApprovalBool = computed({
  get: () => config.value.require_admin_approval === 'true',
  set: (val) => config.value.require_admin_approval = val ? 'true' : 'false'
});

const processedLogoUrl = computed(() => {
  if (!config.value.app_logo) return null;
  if (config.value.app_logo.startsWith('http')) return config.value.app_logo;
  return '/storage/' + config.value.app_logo;
});

const configStoreRef = { appUrl: window.location.origin };

onMounted(async () => {
  try {
    const circleRes = await axios.get('/api/v1/admin/circles?per_page=100');
    circles.value = circleRes.data.data || [];
    const catRes = await axios.get('/api/v1/admin/categories?per_page=100');
    categories.value = catRes.data.data || [];
    const { data } = await axios.get('/api/v1/admin/config');
    config.value = data.config || data || {};

    // Initialiser les couleurs par défaut si absentes pour rafraîchir les inputs color
    availableStatuses.forEach(s => {
      if (!config.value['phase_' + s.value + '_primary']) {
        config.value['phase_' + s.value + '_primary'] = s.defaultPrimary;
      }
      if (!config.value['phase_' + s.value + '_secondary']) {
        config.value['phase_' + s.value + '_secondary'] = s.defaultSecondary;
      }
    });

    const meRes = await axios.get('/api/v1/auth/me');
    currentUser.value = meRes.data.user || meRes.data;
    if (currentUser.value && currentUser.value.email) {
      testEmailAddress.value = currentUser.value.email;
    }
  } catch (e) {
    console.error("Config fetch error", e);
  } finally {
    loading.value = false;
  }
});

watch(() => currentUser.value, (newVal) => {
  if (newVal && newVal.email) testEmailAddress.value = newVal.email;
}, { immediate: true });

const sendTestEmail = async (mailKey) => {
  if (!testEmailAddress.value) return alert('Veuillez saisir une adresse email.');
  sendingTest.value = mailKey;
  try {
    let body = config.value['mail_' + mailKey + '_body'] || '';
    let subject = config.value['mail_' + mailKey + '_subject'] || '';
    const baseUrl = window.location.origin;
    const mockData = {
      '{name}': currentUser.value?.name || 'Utilisateur',
      '{title}': 'DÉCISION DE TEST', '{phase}': 'Objection',
      '{deadline}': '31/12/2026 à 18:00', '{url}': baseUrl + '/decisions/test',
      '{inviter}': 'Administrateur', '{circle}': 'Cercle Pilote',
      '{description}': 'Description de test.', '{email}': currentUser.value?.email || 'test@example.com',
      '{subject}': 'Sujet de test', '{message}': 'Message de test depuis le panneau de configuration.'
    };
    for (const [key, val] of Object.entries(mockData)) {
      body = body.split(key).join(val);
      subject = subject.split(key).join(val);
    }
    await axios.post('/api/v1/admin/config/test-email', { to_email: testEmailAddress.value, subject, body });
    alert('Email de test envoyé !');
  } catch (e) {
    alert('Erreur lors de l\'envoi du test : ' + (e.response?.data?.message || e.message));
  } finally {
    sendingTest.value = null;
  }
};

const mockedSubject = computed(() => {
  if (!config.value || !previewMailType.value) return '';
  return config.value['mail_' + previewMailType.value + '_subject'] || '';
});

const renderedPreview = computed(() => {
  if (!config.value || !config.value.mail_template_wrapper) return '';
  let body = config.value['mail_' + previewMailType.value + '_body'] || '';
  const wrapper = config.value.mail_template_wrapper;
  const mockData = {
    '{name}': currentUser.value?.name || 'Utilisateur', '{title}': 'TITRE DE LA DÉCISION',
    '{phase}': 'Objection', '{deadline}': '31/12/2026 à 18:00', '{url}': '#',
    '{inviter}': 'Jean Dupont', '{circle}': 'Cercle de Gouvernance',
    '{description}': 'Description de test.', '{email}': currentUser.value?.email || 'admin@dazo.app',
    '{subject}': 'Sujet de test', '{message}': 'Contenu du message de contact.'
  };
  for (const [key, val] of Object.entries(mockData)) { body = body.split(key).join(val); }
  let logo = config.value.mail_template_logo || config.value.app_logo || '';
  if (logo) {
    if (logo.startsWith('data:')) {
      // base64 - use as-is
    } else if (logo.startsWith('http')) {
      // absolute URL - use as-is
    } else if (logo.startsWith('/storage/') || logo.startsWith('/')) {
      logo = window.location.origin + logo;
    } else {
      logo = window.location.origin + '/storage/' + logo;
    }
  } else {
    logo = window.location.origin + configStore.defaultLogoUrl;
  }
  const wrapData = {
    '{logo}': logo, '{logo_perso}': config.value.mail_template_logo_perso || '',
    '{site_link}': config.value.mail_template_site_link || window.location.origin,
    '{site_link_register}': config.value.mail_template_site_link_register || (window.location.origin + '/register'),
    '{message}': body
  };
  let html = wrapper;
  for (const [key, val] of Object.entries(wrapData)) { html = html.split(key).join(val); }
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = html;
  return tempDiv.innerHTML;
});

const saveConfig = async () => {
  saving.value = true;
  try {
    const { data } = await axios.put('/api/v1/admin/config', { config: config.value });
    config.value = data.config;
    configStore.fetchInit();
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
    const { data } = await axios.post('/api/v1/admin/config/logo', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
    config.value = data.config;
    alert('Logo mis à jour.');
  } catch (e) {
    alert('Erreur lors de l\'upload du logo.');
  }
};
</script>

<style scoped>
@import "../../../css/admin-config.css";

.phases-config-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 20px;
}

.phase-config-card {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.2s;
}

.phase-config-card:hover {
  border-color: var(--blue-300);
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.phase-config-header {
  padding: 16px;
  background: var(--gray-50);
  border-bottom: 1px solid var(--gray-200);
  display: flex;
  justify-content: center;
}

.phase-preview-badge {
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  border: 1px solid transparent;
  transition: all 0.2s;
}

.phase-config-body {
  padding: 16px;
}

.color-picker-wrapper {
  display: flex;
  gap: 8px;
  align-items: center;
}

.color-input {
  width: 32px;
  height: 32px;
  padding: 0;
  border: 1px solid var(--gray-200);
  border-radius: 6px;
  cursor: pointer;
  background: none;
}

.input-sm {
  padding: 4px 8px;
  font-size: 12px;
}
</style>

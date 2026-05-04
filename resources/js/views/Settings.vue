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
            <img v-if="authStore.user?.avatar_url" :src="processedUserAvatar" class="avatar shadow-lg" style="width:56px; height:56px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);" />
            <div v-else class="avatar av-blue" style="width:56px; height:56px; font-size:20px; border: 2px solid rgba(255,255,255,0.3);">{{ userInitials }}</div>
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
                <div class="pc-header-sub">Identité et informations personnelles</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <!-- AVATAR UPLOAD -->
              <div class="form-group mb-32">
                <label class="config-label">Photo de profil</label>
                <div class="flex items-center gap-24">
                  <div class="avatar-preview-wrap" @click="$refs.avatarInput.click()">
                    <img v-if="processedUserAvatar" :src="processedUserAvatar" alt="Avatar" class="avatar-preview-img" />
                    <div v-else class="avatar-preview-placeholder">{{ userInitials }}</div>
                    <div class="avatar-edit-overlay">
                      <i class="fa-solid fa-camera"></i>
                    </div>
                  </div>
                  <div class="avatar-upload-info">
                    <button class="btn btn-white btn-sm mb-8" @click="$refs.avatarInput.click()">
                      <i class="fa-solid fa-upload mr-8"></i> Choisir une image
                    </button>
                    <p class="text-xs text-muted">Format JPG, PNG ou GIF. Max 2Mo.</p>
                    <input type="file" ref="avatarInput" style="display:none" @change="handleAvatarUpload" accept="image/*">
                  </div>
                </div>
              </div>

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

          <!-- SECTION TABLEAU DE BORD (MODULAIRE) -->
          <div v-if="activeSection === 'dashboard'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-indigo">
              <div class="pc-header-icon"><i class="fa-solid fa-gauge-high"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mon Tableau de bord</div>
                <div class="pc-header-sub">Personnalisez l'affichage de votre cockpit personnel</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div v-if="dashSuccessMsg" class="alert alert-success mb-24">{{ dashSuccessMsg }}</div>
              
              <div class="mb-32">
                <h4 class="config-label mb-8">Widgets disponibles</h4>
                <p class="help-text mb-24">Activez ou désactivez les blocs d'information que vous souhaitez voir sur votre page d'accueil.</p>
                
                <draggable v-model="userWidgets" item-key="id" handle=".drag-handle" class="widgets-config-list" animation="200">
                  <template #item="{ element: w }">
                  <div class="widget-config-row" :class="{ disabled: !w.enabled }">
                    <div class="flex-items-center">
                      <div class="drag-handle" style="cursor: grab; padding-right: 16px; color: #aaa;"><i class="fa-solid fa-grip-vertical"></i></div>
                      <div class="widget-config-info">
                        <div class="widget-config-icon">
                          <i :class="getWidgetIcon(w.id)"></i>
                        </div>
                        <div>
                          <div class="widget-config-label">{{ w.label }}</div>
                          <div class="widget-config-desc">{{ getWidgetDesc(w.id) }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="widget-config-actions">
                      <div class="widget-width-selector" v-if="w.enabled">
                         <button @click="setWidgetWidth(w, 'quarter')" class="ww-btn" :class="{active: w.width === 'quarter'}" title="1/4 de largeur">1/4</button>
                         <button @click="setWidgetWidth(w, 'third')" class="ww-btn" :class="{active: w.width === 'third'}" title="1/3 de largeur">1/3</button>
                         <button @click="setWidgetWidth(w, 'half')" class="ww-btn" :class="{active: w.width === 'half'}" title="1/2 de largeur">1/2</button>
                         <button @click="setWidgetWidth(w, 'full')" class="ww-btn" :class="{active: w.width === 'full'}" title="Pleine largeur">1/1</button>
                      </div>
                      <label class="switch-sm">
                        <input type="checkbox" v-model="w.enabled">
                        <span class="slider round"></span>
                      </label>
                    </div>
                  </div>
                  </template>
                </draggable>
              </div>

              <div class="pt-16 border-top">
                <button @click="saveDashboardConfig" class="btn btn-primary btn-lg" :disabled="loading">
                  <i class="fa-solid fa-save mr-8"></i>
                  Enregistrer la configuration
                </button>
              </div>
            </div>
          </div>

          <!-- SECTION MES VUES -->
          <div v-if="activeSection === 'views'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-teal">
              <div class="pc-header-icon"><i class="fa-solid fa-layer-group"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mes Vues</div>
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

              <div class="mb-32 mt-32">
                <h4 class="config-label mb-8">Ajouter des vues personnalisées</h4>
                <p class="help-text mb-24">Créez vos propres filtres pour accéder rapidement aux décisions qui vous importent.</p>
                
                <div class="custom-view-creator premium-card p-24 mb-32" style="background: var(--gray-50);">
                  <div class="grid-2 gap-24">
                    <div class="form-group">
                      <label class="form-label">Nom de la vue</label>
                      <input type="text" v-model="customViewForm.label" class="form-control" placeholder="Ex: Projets Urgent">
                    </div>
                    <div class="form-group">
                      <label class="form-label">Icône</label>
                      <div class="icon-selector">
                        <button v-for="ico in availableIcons" :key="ico" 
                          @click="customViewForm.icon = ico"
                          class="btn-icon-opt" :class="{ active: customViewForm.icon === ico }">
                          <i :class="ico"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="grid-2 gap-24 mt-16">
                    <div class="form-group">
                      <label class="form-label">Type de filtre</label>
                      <select v-model="customViewForm.filterType" class="form-control">
                        <option value="status">Par Statut</option>
                        <option value="circle">Par Cercle</option>
                        <option value="category">Par Catégorie</option>
                        <option value="search">Recherche texte</option>
                        <option value="author_id">Par Porteur</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Valeur du filtre</label>
                      <select v-if="customViewForm.filterType === 'status'" v-model="customViewForm.filterValue" class="form-control">
                        <option value="draft">Brouillon</option>
                        <option value="clarification">Clarification</option>
                        <option value="reaction">Réactions</option>
                        <option value="objection">Objections</option>
                        <option value="adopted">Adopté</option>
                        <option value="revision">En révision</option>
                      </select>
                      <select v-else-if="customViewForm.filterType === 'circle'" v-model="customViewForm.filterValue" class="form-control">
                        <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
                      </select>
                      <select v-else-if="customViewForm.filterType === 'category'" v-model="customViewForm.filterValue" class="form-control">
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                      </select>
                      <input v-else type="text" v-model="customViewForm.filterValue" class="form-control" placeholder="Valeur...">
                    </div>
                  </div>
                  
                  <div class="mt-24 text-right">
                    <button @click="addCustomView" class="btn btn-teal">
                      <i class="fa-solid fa-plus mr-8"></i> Ajouter cette vue
                    </button>
                  </div>
                </div>

                <div v-if="userViews.filter(v => v.id.startsWith('custom-')).length" class="mt-24">
                  <h5 class="text-sm font-bold uppercase tracking-wider text-muted mb-16">Vos vues créées</h5>
                  <div class="views-grid">
                    <div v-for="v in userViews.filter(v => v.id.startsWith('custom-'))" :key="v.id" class="view-opt-card active">
                      <div class="view-opt-icon"><i :class="v.icon"></i></div>
                      <div class="view-opt-label">{{ v.label }}</div>
                      <button @click="removeCustomView(v.id)" class="btn-remove-view" title="Supprimer">
                        <i class="fa-solid fa-times"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="pt-16 border-top">
                <button @click="saveViews" class="btn btn-primary btn-lg" :disabled="loading">
                  <i class="fa-solid fa-save mr-8"></i>
                  Enregistrer mes vues
                </button>
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
                <span><strong>Conseil :</strong> Les notifications <strong>Web (Push)</strong> sont recommandées pour une meilleure réactivité.</span>
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
                    <div class="pwd-strength-meter mt-8" v-if="pwdForm.password">
                      <div class="pwd-strength-bar" :class="'strength-' + passwordStrength"></div>
                      <div class="flex justify-between mt-4">
                        <span class="text-xs font-bold" :class="'text-strength-' + passwordStrength">{{ strengthLabel }}</span>
                        <span class="text-xs opacity-50">{{ pwdForm.password.length }} car.</span>
                      </div>
                    </div>
                    <div v-if="pwdValidationErrors.password" class="input-error">{{ pwdValidationErrors.password[0] }}</div>
                  </div>
                  <div class="form-group">
                    <label class="config-label">Confirmer le nouveau mot de passe</label>
                    <div class="input-with-icon">
                      <input v-model="pwdForm.password_confirmation" type="password" class="input" :class="{ 'input-error-border': !passwordMatch && pwdForm.password_confirmation }" required />
                      <i v-if="pwdForm.password_confirmation" class="fa-solid input-icon-right" :class="passwordMatch ? 'fa-check-circle text-teal-500' : 'fa-circle-xmark text-red-500'"></i>
                    </div>
                    <p v-if="!passwordMatch && pwdForm.password_confirmation" class="input-error">Les mots de passe ne correspondent pas.</p>
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
                  <div v-if="socialProviders.length === 0" class="p-32 text-center text-muted">
                    Aucun fournisseur de connexion sociale n'est actuellement activé sur cette instance.
                  </div>
                </div>

                <div v-if="!socialHasPassword" class="alert alert-warning mt-24">
                  <i class="fa-solid fa-triangle-exclamation"></i>
                  <span>Vous n'avez pas de mot de passe défini. Vous devez conserver au moins un compte lié pour pouvoir vous connecter.</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import draggable from 'vuedraggable';
import { ref, reactive, onMounted, computed, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useDecisionStore } from '../stores/decision';
import { useConfigStore } from '../stores/config';
import axios from 'axios';

const authStore = useAuthStore();
const decisionStore = useDecisionStore();
const configStore = useConfigStore();

const activeSection = ref('profile');
const sections = [
  { id: 'profile', label: 'Mon profil', icon: 'fa-solid fa-user-circle' },
  { id: 'dashboard', label: 'Mon tableau de bord', icon: 'fa-solid fa-gauge-high' },
  { id: 'views', label: 'Mes vues', icon: 'fa-solid fa-layer-group' },
  { id: 'notifications', label: 'Notifications', icon: 'fa-solid fa-bell' },
  { id: 'password', label: 'Mot de passe', icon: 'fa-solid fa-key' },
  { id: 'social', label: 'Comptes liés', icon: 'fa-solid fa-link' },
];

const AVAILABLE_WIDGETS = [
  { id: 'stats', label: 'Statistiques', defaultEnabled: true, defaultWidth: 'full' },
  { id: 'tickets', label: 'Clarifications & Objections', defaultEnabled: true, defaultWidth: 'full' },
  { id: 'urgencies', label: 'Urgences', defaultEnabled: true, defaultWidth: 'full' },
  { id: 'my_proposals', label: 'Mes propositions', defaultEnabled: true, defaultWidth: '1/3' },
  { id: 'my_animated', label: 'Mes animations', defaultEnabled: true, defaultWidth: '1/3' },
  { id: 'circles_watch', label: 'Mes cercles (flux)', defaultEnabled: true, defaultWidth: '1/3' },
  { id: 'my_circles', label: 'Mes cercles', defaultEnabled: true, defaultWidth: '1/2' },
  { id: 'categories', label: 'Catégories', defaultEnabled: true, defaultWidth: '1/2' }
];

const initializeWidgets = (savedWidgets) => {
  const saved = savedWidgets || [];
  const merged = AVAILABLE_WIDGETS.map(w => {
    const existing = saved.find(s => s.id === w.id);
    return {
      id: w.id,
      label: w.label,
      enabled: existing ? (existing.enabled !== undefined ? existing.enabled : w.defaultEnabled) : w.defaultEnabled,
      width: existing ? (existing.width || w.defaultWidth) : w.defaultWidth
    };
  });
  // Keep order if saved
  const ordered = [];
  saved.forEach(s => {
    const m = merged.find(item => item.id === s.id);
    if (m) ordered.push(m);
  });
  // Add new ones at the end
  merged.forEach(m => {
    if (!ordered.find(o => o.id === m.id)) ordered.push(m);
  });
  return ordered;
};

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
const userWidgets = ref([]);
const dashSuccessMsg = ref('');

const notifPrefs = reactive({});
notifCategories.forEach(c => {
  notifPrefs[c.id] = { email: true, web: true };
});

const notifLoading = ref(false);
const notifSuccessMsg = ref('');

const customViewForm = ref({ label: '', icon: 'fa-solid fa-filter', filterType: 'status', filterValue: '' });
const circles = ref([]);
const categories = ref([]);
const availableIcons = [
  'fa-solid fa-filter', 'fa-solid fa-star', 'fa-solid fa-tag', 'fa-solid fa-circle-nodes',
  'fa-solid fa-folder', 'fa-solid fa-bolt', 'fa-solid fa-clock', 'fa-solid fa-check-double',
  'fa-solid fa-user', 'fa-solid fa-comments', 'fa-solid fa-heart'
];

const fetchMetaData = async () => {
  try {
    const [cRes, catRes] = await Promise.all([
      axios.get('/api/v1/circles'),
      axios.get('/api/v1/categories')
    ]);
    circles.value = cRes.data.circles || [];
    categories.value = catRes.data.categories || [];
  } catch (e) {
    console.error("Settings fetch meta error", e);
  }
};

const addCustomView = () => {
  if (!customViewForm.value.label) return alert('Veuillez donner un nom à votre vue.');
  
  const newView = {
    id: 'custom-' + Date.now(),
    label: customViewForm.value.label,
    icon: customViewForm.value.icon,
    filters: { [customViewForm.value.filterType]: customViewForm.value.filterValue }
  };
  
  userViews.value.push(newView);
  customViewForm.value = { label: '', icon: 'fa-solid fa-filter', filterType: 'status', filterValue: '' };
};

const removeCustomView = (id) => {
  userViews.value = userViews.value.filter(v => v.id !== id);
};

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
    setTimeout(() => viewsSuccessMsg.value = '', 3000);
  } catch (e) {
    alert('Erreur lors de la sauvegarde des vues.');
  } finally {
    loading.value = false;
  }
};

const getWidgetIcon = (id) => {
  const icons = {
    stats: 'fa-solid fa-chart-line',
    tickets: 'fa-solid fa-comments',
    urgencies: 'fa-solid fa-triangle-exclamation',
    my_proposals: 'fa-solid fa-bullhorn',
    my_animated: 'fa-solid fa-user-tie',
    circles_watch: 'fa-solid fa-user-group',
    my_circles: 'fa-solid fa-circle-nodes',
    categories: 'fa-solid fa-folder-tree'
  };
  return icons[id] || 'fa-solid fa-cube';
};

const getWidgetDesc = (id) => {
  const descs = {
    stats: 'Résumé chiffré de votre activité sur la plateforme.',
    tickets: 'Liste de vos clarifications et objections en attente.',
    urgencies: 'Décisions dont l\'échéance est proche (< 24h).',
    my_proposals: 'Décisions dont vous êtes le porteur.',
    my_animated: 'Décisions que vous facilitez.',
    circles_watch: 'Flux des décisions actives dans vos cercles.',
    my_circles: 'Liste de vos cercles rejoints.',
    categories: 'Liste des catégories disponibles.'
  };
  return descs[id] || '';
};

const setWidgetWidth = (w, width) => {
  w.width = width;
};

const saveDashboardConfig = async () => {
  loading.value = true;
  dashSuccessMsg.value = '';
  try {
    await axios.put('/api/v1/auth/me', { dashboard_widgets: userWidgets.value });
    dashSuccessMsg.value = 'Configuration du tableau de bord enregistrée.';
    await authStore.fetchUser();
    setTimeout(() => dashSuccessMsg.value = '', 3000);
  } catch (e) {
    alert('Erreur lors de la sauvegarde.');
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

const passwordStrength = computed(() => {
  const p = pwdForm.value.password;
  if (!p) return 0;
  let s = 0;
  if (p.length >= 6) s++;
  if (p.length >= 10) s++;
  if (/[A-Z]/.test(p)) s++;
  if (/[0-9]/.test(p)) s++;
  if (/[^A-Za-z0-9]/.test(p)) s++;
  return Math.min(s, 4);
});

const strengthLabel = computed(() => {
  const labels = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
  return labels[passwordStrength.value];
});

const passwordMatch = computed(() => {
  if (!pwdForm.value.password_confirmation) return true;
  return pwdForm.value.password === pwdForm.value.password_confirmation;
});

const processedUserAvatar = computed(() => {
    const avatar = authStore.user?.avatar_url;
    if (!avatar) return null;
    if (avatar.startsWith('http')) return avatar;
    return `/storage/${avatar}`;
});

const handleAvatarUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('avatar', file);

    try {
        await axios.post('/api/v1/auth/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        await authStore.fetchUser();
        successMsg.value = "Photo de profil mise à jour.";
        setTimeout(() => successMsg.value = '', 3000);
    } catch (error) {
        alert("Erreur lors de l'upload de l'avatar.");
    }
};

onMounted(() => {
    if (authStore.user) {
        profileForm.value.name = authStore.user.name;
        profileForm.value.email = authStore.user.email;
        userViews.value = JSON.parse(JSON.stringify(authStore.user.custom_views || []));
        userWidgets.value = initializeWidgets(authStore.user.dashboard_widgets);
    }
    fetchNotifPrefs();
    fetchSocialAccounts();
    fetchMetaData();
});

watch(() => authStore.user, (u) => {
    if (u) {
        profileForm.value.name = u.name;
        profileForm.value.email = u.email;
        userViews.value = JSON.parse(JSON.stringify(u.custom_views || []));
        userWidgets.value = initializeWidgets(u.dashboard_widgets);
    }
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
        setTimeout(() => successMsg.value = '', 3000);
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
        setTimeout(() => pwdSuccessMsg.value = '', 3000);
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
  'linkedin': 'fa-brands fa-linkedin',
  'linkedin-openid': 'fa-brands fa-linkedin',
  'gitlab': 'fa-brands fa-gitlab',
  'microsoft': 'fa-brands fa-microsoft',
  'apple': 'fa-brands fa-apple',
  'franceconnect': 'fa-solid fa-id-card',
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

/* Avatar Preview */
.avatar-preview-wrap {
  width: 100px; height: 100px; border-radius: 20px; background: var(--gray-100); border: 2px solid var(--gray-200);
  position: relative; cursor: pointer; overflow: hidden; display: flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}
.avatar-preview-wrap:hover { border-color: var(--blue-400); }
.avatar-preview-img { width: 100%; height: 100%; object-fit: cover; }
.avatar-preview-placeholder { font-size: 32px; font-weight: 800; color: var(--gray-400); }
.avatar-edit-overlay {
  position: absolute; inset: 0; background: rgba(0,0,0,0.4); color: white; display: flex; align-items: center; justify-content: center;
  font-size: 20px; opacity: 0; transition: opacity 0.2s;
}
.avatar-preview-wrap:hover .avatar-edit-overlay { opacity: 1; }

/* Dashboard Config */
.widgets-config-list { display: flex; flex-direction: column; gap: 12px; }
.widget-config-row { 
  display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; 
  background: white; border: 1px solid var(--gray-200); border-radius: 16px; transition: all 0.2s;
}
.widget-config-row:hover { border-color: var(--blue-300); background: var(--gray-50); }
.widget-config-row.disabled { opacity: 0.6; background: var(--gray-100); }
.flex-items-center { display: flex; align-items: center; }
.widget-config-info { display: flex; align-items: center; gap: 16px; }
.widget-config-icon { 
  width: 44px; height: 44px; border-radius: 12px; background: white; border: 1px solid var(--gray-200);
  display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--indigo-600);
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.widget-config-label { font-size: 14px; font-weight: 800; color: var(--gray-800); }
.widget-config-desc { font-size: 11px; color: var(--gray-500); margin-top: 2px; }
.widget-config-actions { display: flex; align-items: center; gap: 20px; }

.widget-width-selector { 
  display: flex; background: var(--gray-100); padding: 3px; border-radius: 8px; border: 1px solid var(--gray-200);
}
.ww-btn {
  padding: 4px 10px; font-size: 10px; font-weight: 800; border-radius: 6px; border: none;
  background: transparent; color: var(--gray-500); cursor: pointer; transition: all 0.2s;
}
.ww-btn:hover { color: var(--blue-600); }
.ww-btn.active { background: white; color: var(--blue-600); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

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

.icon-selector { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
.btn-icon-opt { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--gray-200); background: white; cursor: pointer; transition: all 0.2s; color: var(--gray-600); }
.btn-icon-opt:hover { border-color: var(--blue-400); color: var(--blue-500); }
.btn-icon-opt.active { background: var(--blue-500); color: white; border-color: var(--blue-600); }

.btn-remove-view { position: absolute; top: -8px; right: -8px; width: 22px; height: 22px; border-radius: 50%; background: var(--red-500); color: white; border: 2px solid white; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: all 0.2s; z-index: 10; }
.btn-remove-view:hover { background: var(--red-600); transform: scale(1.1); }
.view-opt-label { font-size: 13px; font-weight: 700; color: var(--gray-700); }

/* Notifications Table */
.notif-table-container { background: var(--gray-50); border-radius: 12px; overflow: hidden; border: 1px solid var(--gray-200); }
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

@media (max-width: 1024px) {
  .config-layout { flex-direction: column; }
  .config-nav { width: 100%; position: static; flex-direction: row; display: flex; overflow-x: auto; gap: 8px; padding-bottom: 8px; }
  .nav-item { width: auto; white-space: nowrap; margin-bottom: 0; }
  .nav-group-title { display: none; }
}

/* Social Accounts */
.social-accounts-list { display: flex; flex-direction: column; gap: 8px; }
.social-account-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--gray-50); border: 1px solid var(--gray-100); border-radius: 12px; transition: all 0.15s; }
.social-account-row:hover { border-color: var(--gray-200); background: white; }
.social-account-info { display: flex; align-items: center; gap: 14px; }
.social-account-icon { width: 36px; height: 36px; border-radius: 10px; background: white; border: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: center; font-size: 16px; color: var(--gray-600); flex-shrink: 0; }
.social-account-name { font-size: 13px; font-weight: 700; color: var(--gray-800); }
.social-account-status { font-size: 11px; font-weight: 600; margin-top: 2px; }
.social-account-status.linked { color: var(--teal-600); }
.social-account-status.unlinked { color: var(--gray-400); }

/* Password Strength */
.pwd-strength-meter { height: 4px; background: var(--gray-200); border-radius: 2px; position: relative; overflow: hidden; }
.pwd-strength-bar { height: 100%; width: 0; transition: all 0.3s ease; }
.strength-0 { width: 5%; background: var(--red-500); }
.strength-1 { width: 25%; background: var(--red-500); }
.strength-2 { width: 50%; background: var(--amber-500); }
.strength-3 { width: 75%; background: var(--blue-500); }
.strength-4 { width: 100%; background: var(--teal-500); }

.text-strength-0 { color: var(--red-600); }
.text-strength-1 { color: var(--red-600); }
.text-strength-2 { color: var(--amber-600); }
.text-strength-3 { color: var(--blue-600); }
.text-strength-4 { color: var(--teal-600); }

.input-with-icon { position: relative; }
.input-icon-right { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; }
.input-error-border { border-color: var(--red-500) !important; }
</style>

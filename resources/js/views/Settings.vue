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
                
                <draggable v-model="userWidgets" item-key="id" class="widgets-config-list" animation="250" @end="handleDragEnd" ghost-class="widget-ghost" drag-class="widget-dragging">
                  <template #item="{ element: w }">
                  <div :id="'widget-config-' + w.id" class="widget-config-row-container" :class="[(!w.enabled ? 'w-config-full' : 'w-config-' + (w.width || 'full')), { 'is-disabled': !w.enabled, 'just-activated': activeWidgetId === w.id }]">
                    <div class="widget-config-row" :class="{ disabled: !w.enabled }">
                      <!-- Barre de contrôle verticale à gauche -->
                      <div class="widget-config-sidebar">
                        <div class="widget-config-icon">
                          <i :class="getWidgetIcon(w.id)"></i>
                        </div>
                        
                        <div class="widget-config-actions-vertical" @mousedown.stop @touchstart.stop>
                          <select v-if="w.enabled" v-model="w.width" class="ww-select-compact">
                            <option value="quarter">1/4</option>
                            <option value="third">1/3</option>
                            <option value="half">1/2</option>
                            <option value="full">1/1</option>
                          </select>
                        </div>
                      </div>
                      
                      <!-- Contenu à droite -->
                      <div class="widget-config-content">
                        <div class="widget-config-label">{{ w.label }}</div>
                        <div class="widget-config-desc">{{ getWidgetDesc(w.id) }}</div>
                      </div>

                      <!-- Switch en bas à droite -->
                      <div class="widget-config-toggle-br" @mousedown.stop @touchstart.stop>
                        <label class="switch-sm" title="Activer / Désactiver">
                          <input type="checkbox" v-model="w.enabled" @change="handleWidgetToggle(w)">
                          <span class="slider round"></span>
                        </label>
                      </div>
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
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-layer-group"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Mes Vues</div>
                <div class="pc-header-sub">Configurez vos raccourcis de filtrage dans la barre latérale</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div v-if="viewsSuccessMsg" class="alert alert-success mb-24">{{ viewsSuccessMsg }}</div>

              <!-- TUILES ACTIVES / INACTIVES (draggable) -->
              <div class="mb-32" v-if="userViews.length">
                <h4 class="config-label mb-12">Vues personnalisées
                  <span class="config-label-hint">Glissez pour réordonner · les vues désactivées passent en bas</span>
                </h4>

                <draggable
                  v-model="userViews"
                  item-key="id"
                  class="cv-tiles-list"
                  animation="220"
                  ghost-class="cv-tile-ghost"
                  drag-class="cv-tile-dragging"
                  handle=".cv-tile-drag-zone"
                  @end="autoSaveViews"
                >
                  <template #item="{ element: v }">
                    <div
                      :id="'cv-tile-' + v.id"
                      class="cv-tile cv-tile-card"
                      :class="{ 'cv-tile--disabled': !v.enabled, 'cv-tile--activated': activeViewId === v.id }"
                    >
                      <!-- Edit button -->
                      <button class="cv-btn-edit cv-btn-edit-abs" @click.stop="editCustomView(v)" title="Éditer">
                        <i class="fa-solid fa-pencil"></i>
                      </button>

                      <!-- Zone draggable (hors actions) -->
                      <div class="cv-tile-drag-zone" title="Déplacer">
                        <div class="cv-tile-icon">
                          <img v-if="v.favicon" :src="v.favicon" class="cv-favicon" alt="">
                          <i v-else :class="v.icon || 'fa-solid fa-layer-group'"></i>
                        </div>

                        <div class="cv-tile-label">{{ v.label }}</div>
                        <div class="cv-tile-desc" v-if="v.description">{{ v.description }}</div>
                        <div class="cv-tile-filters">
                          <span v-for="(val, key) in (v.filters || {})" :key="key" class="cv-filter-tag">
                            {{ cvFilterLabel(key, val) }}
                          </span>
                        </div>
                      </div>

                      <!-- Footer with toggle -->
                      <div class="cv-tile-footer" style="justify-content: flex-end;">
                        <label class="switch-sm cv-tile-switch" title="Activer / Désactiver" @click.stop>
                          <input type="checkbox" v-model="v.enabled" @change="handleViewToggle(v)">
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </div>
                  </template>
                </draggable>
              </div>

              <!-- BLOC CREATION / EDITION -->
              <div class="mb-24">
                <div class="cv-creator-card">

                  <div class="cv-creator-header">
                    <span v-if="!editingViewId"><i class="fa-solid fa-plus-circle mr-8"></i>Ajouter une vue personnalisée</span>
                    <span v-else><i class="fa-solid fa-pen-to-square mr-8"></i>Modifier la vue</span>
                  </div>

                  <div class="cv-creator-body">

                    <!-- Ligne 1 : Nom de la vue -->
                    <div class="cv-form-row mb-16">
                      <div class="cv-form-group" style="flex: 1;">
                        <label class="form-label">Nom de la vue</label>
                        <input type="text" v-model="customViewForm.label" class="form-control cv-input-sexy" placeholder="Ex: Projets urgents">
                      </div>
                    </div>

                    <!-- Ligne 2 : Icône -->
                    <div class="cv-form-row mb-16">
                      <div class="cv-form-group" style="flex: 0 0 auto;">
                        <label class="form-label">Icône</label>
                        <div class="cv-icon-row">
                          <div class="icon-selector">
                            <button v-for="ico in availableIcons" :key="ico"
                              @click="customViewForm.icon = ico"
                              class="btn-icon-opt" :class="{ active: customViewForm.icon === ico }">
                              <i :class="ico"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Ligne 2 : Filtres -->
                    <div class="cv-form-row mt-16">
                      <div class="cv-form-group">
                        <label class="form-label">État</label>
                        <select v-model="customViewForm.filters.state" class="form-control cv-input-sexy">
                          <option value="">Tous</option>
                          <option value="draft">Brouillon</option>
                          <option value="active">En cours</option>
                          <option value="clarification">Clarification</option>
                          <option value="reaction">Réaction</option>
                          <option value="objection">Objection</option>
                          <option value="revision">Révision</option>
                          <option value="adopted">Adoptée</option>
                          <option value="abandoned">Abandonnée</option>
                        </select>
                      </div>
                      <div class="cv-form-group">
                        <label class="form-label">Mon rôle</label>
                        <select v-model="customViewForm.filters.role" class="form-control cv-input-sexy">
                          <option value="">Tous rôles</option>
                          <option value="author">Porteur</option>
                          <option value="animator">Animateur</option>
                          <option value="participant">Participant</option>
                          <option value="observer">Observateur</option>
                        </select>
                      </div>
                      <div class="cv-form-group">
                        <label class="form-label">Cercle</label>
                        <select v-model="customViewForm.filters.circle" class="form-control cv-input-sexy">
                          <option value="">Tous</option>
                          <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                      </div>
                      <div class="cv-form-group">
                        <label class="form-label">Catégorie</label>
                        <select v-model="customViewForm.filters.category" class="form-control cv-input-sexy">
                          <option value="">Toutes</option>
                          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                      </div>
                      <div class="cv-form-group">
                        <label class="form-label">Tri</label>
                        <select v-model="customViewForm.filters.sort" class="form-control cv-input-sexy">
                          <option value="">Défaut</option>
                          <option value="created_desc">Récents d'abord</option>
                          <option value="created_asc">Anciens d'abord</option>
                          <option value="updated_desc">Maj récente</option>
                          <option value="alpha_asc">A → Z</option>
                          <option value="alpha_desc">Z → A</option>
                        </select>
                      </div>
                      <div class="cv-form-group" style="flex: 2;">
                        <label class="form-label">Recherche texte</label>
                        <input type="text" v-model="customViewForm.filters.search" class="form-control cv-input-sexy" placeholder="Mot-clé, auteur...">
                      </div>
                    </div>

                    <!-- Actions création -->
                    <div class="cv-creator-actions mt-20">
                      <div v-if="!editingViewId">
                        <button @click="addCustomView" class="btn btn-blue">
                          <i class="fa-solid fa-eye mr-6"></i> Activer cette vue
                        </button>
                      </div>
                      <div v-else class="cv-edit-actions">
                        <button @click="deleteCustomView" class="btn btn-ghost btn-danger-ghost">
                          <i class="fa-solid fa-trash mr-6"></i> Supprimer
                        </button>
                        <button @click="cancelEditView" class="btn btn-ghost">
                          <i class="fa-solid fa-times mr-6"></i> Fermer
                        </button>
                        <button @click="updateCustomView" class="btn btn-primary">
                          <i class="fa-solid fa-check mr-6"></i> Mettre à jour
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Bouton sauvegarde global -->
              <div class="pt-16 border-top">
                <button @click="saveViews" class="btn btn-primary btn-lg" :disabled="loading">
                  <i class="fa-solid fa-save mr-8"></i>
                  Enregistrer mes vues
                </button>
              </div>

            </div>
          </div>

          <!-- POPUP CONFIRM DELETE -->
          <div v-if="confirmDeleteViewId" class="modal-overlay" @click.self="confirmDeleteViewId = null">
            <div class="modal-box">
              <div class="modal-header">
                <i class="fa-solid fa-triangle-exclamation text-amber mr-8"></i>
                Supprimer cette vue ?
              </div>
              <div class="modal-body">
                La vue <strong>"{{ viewToDeleteLabel }}"</strong> sera définitivement supprimée.
              </div>
              <div class="modal-footer">
                <button class="btn btn-ghost" @click="confirmDeleteViewId = null">Annuler</button>
                <button class="btn btn-danger" @click="confirmDeleteView">Supprimer</button>
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
const activeWidgetId = ref(null);
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
  { id: 'clarifications', label: 'Clarifications actives', defaultEnabled: true, defaultWidth: '1/3' },
  { id: 'suggestions', label: 'Suggestions actives', defaultEnabled: true, defaultWidth: '1/3' },
  { id: 'objections', label: 'Objections actives', defaultEnabled: true, defaultWidth: '1/3' },
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

// -- Custom Views state --
const defaultCustomViewForm = () => ({
  label: '',
  icon: 'fa-solid fa-filter',
  filters: { state: '', role: '', circle: '', category: '', sort: '', search: '' }
});
const customViewForm = ref(defaultCustomViewForm());
const editingViewId = ref(null);
const activeViewId = ref(null);
const confirmDeleteViewId = ref(null);

const viewToDeleteLabel = computed(() => {
  const v = userViews.value.find(x => x.id === confirmDeleteViewId.value);
  return v ? v.label : '';
});

const circles = ref([]);
const categories = ref([]);
const availableIcons = [
  'fa-solid fa-filter', 'fa-solid fa-star', 'fa-solid fa-tag', 'fa-solid fa-circle-nodes',
  'fa-solid fa-folder', 'fa-solid fa-bolt', 'fa-solid fa-clock', 'fa-solid fa-check-double',
  'fa-solid fa-user', 'fa-solid fa-comments', 'fa-solid fa-heart',
  'fa-solid fa-fire', 'fa-solid fa-flag', 'fa-solid fa-chart-bar', 'fa-solid fa-pen'
];

const fetchMetaData = async () => {
  try {
    const [cRes, catRes] = await Promise.all([
      axios.get('/api/v1/circles'),
      axios.get('/api/v1/categories')
    ]);
    circles.value = cRes.data.data || cRes.data.circles || [];
    categories.value = catRes.data.data || catRes.data.categories || [];
  } catch (e) {
    console.error("Settings fetch meta error", e);
  }
};

const addCustomView = () => {
  if (!customViewForm.value.label) return alert('Veuillez donner un nom à votre vue.');
  
  // Filter out empty filters
  const filters = {};
  Object.entries(customViewForm.value.filters).forEach(([k, v]) => { if (v) filters[k] = v; });

  const newView = {
    id: 'custom-' + Date.now(),
    label: customViewForm.value.label,
    icon: customViewForm.value.icon,
    enabled: false,  // starts disabled, user sees it in the list
    width: 'full',
    filters
  };
  
  userViews.value.push(newView);
  customViewForm.value = defaultCustomViewForm();
  autoSaveViews();
};

const editCustomView = (v) => {
  editingViewId.value = v.id;
  const filters = v.filters || {};
  customViewForm.value = {
    label: v.label || '',
    icon: v.icon || 'fa-solid fa-filter',
    filters: {
      state: filters.state || '',
      role: filters.role || '',
      circle: filters.circle || '',
      category: filters.category || '',
      sort: filters.sort || '',
      search: filters.search || ''
    }
  };
};

const cancelEditView = () => {
  editingViewId.value = null;
  customViewForm.value = defaultCustomViewForm();
};

const updateCustomView = () => {
  const idx = userViews.value.findIndex(x => x.id === editingViewId.value);
  if (idx === -1) return;
  const filters = {};
  Object.entries(customViewForm.value.filters).forEach(([k, v]) => { if (v) filters[k] = v; });
  userViews.value[idx] = {
    ...userViews.value[idx],
    label: customViewForm.value.label,
    icon: customViewForm.value.icon,
    filters
  };
  editingViewId.value = null;
  customViewForm.value = defaultCustomViewForm();
  autoSaveViews();
};

const deleteCustomView = () => {
  if (!editingViewId.value) return;
  confirmDeleteViewId.value = editingViewId.value;
};

const confirmDeleteView = () => {
  userViews.value = userViews.value.filter(v => v.id !== confirmDeleteViewId.value);
  if (editingViewId.value === confirmDeleteViewId.value) {
    editingViewId.value = null;
    customViewForm.value = defaultCustomViewForm();
  }
  confirmDeleteViewId.value = null;
  autoSaveViews();
};

const removeCustomView = (id) => {
  userViews.value = userViews.value.filter(v => v.id !== id);
};

const cvFilterLabel = (key, val) => {
  const map = { state: 'État', role: 'Rôle', circle: 'Cercle', category: 'Catégorie', sort: 'Tri', search: 'Texte', status: 'Statut' };
  
  let label = val;
  if (key === 'category') {
    const cat = categories.value.find(c => String(c.id) === String(val));
    if (cat) label = cat.name;
  } else if (key === 'circle') {
    const circ = circles.value.find(c => String(c.id) === String(val));
    if (circ) label = circ.name;
  }
  
  return `${map[key] || key}: ${label}`;
};

const handleViewToggle = (v) => {
  if (!v.enabled) {
    // Move to end of list
    const idx = userViews.value.indexOf(v);
    userViews.value.splice(idx, 1);
    userViews.value.push(v);
  } else {
    // Move to end of ENABLED section (before first disabled)
    const idx = userViews.value.indexOf(v);
    userViews.value.splice(idx, 1);
    const firstDisabled = userViews.value.findIndex(x => !x.enabled);
    if (firstDisabled === -1) userViews.value.push(v);
    else userViews.value.splice(firstDisabled, 0, v);

    // WOW effect + scroll
    activeViewId.value = v.id;
    setTimeout(() => {
      const el = document.getElementById('cv-tile-' + v.id);
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => { activeViewId.value = null; }, 2200);
    }, 80);
  }
  autoSaveViews();
};

const autoSaveViews = async () => {
  try {
    await axios.put('/api/v1/auth/me', { custom_views: userViews.value });
    await authStore.fetchUser();
  } catch (e) {
    console.error('AutoSave views error', e);
  }
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
    // Re-sync order with stored base before saving
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

const lastActiveOrder = ref([]);

const handleWidgetToggle = (w) => {
    if (!w.enabled) {
        // Find current position in list to remember it
        const idx = userWidgets.value.indexOf(w);
        // Move to the end of the list
        userWidgets.value.splice(idx, 1);
        userWidgets.value.push(w);
        activeWidgetId.value = null;
    } else {
        // When reactivating, we move it back to the end of the "active" section
        const idx = userWidgets.value.indexOf(w);
        userWidgets.value.splice(idx, 1);
        
        // Find the index of the first disabled widget
        const firstDisabledIdx = userWidgets.value.findIndex(x => !x.enabled);
        if (firstDisabledIdx === -1) {
            userWidgets.value.push(w);
        } else {
            userWidgets.value.splice(firstDisabledIdx, 0, w);
        }

        // WOW EFFECT
        activeWidgetId.value = w.id;
        
        // Scroll to the element
        setTimeout(() => {
            const el = document.getElementById('widget-config-' + w.id);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            // Clear effect after animation
            setTimeout(() => {
                activeWidgetId.value = null;
            }, 2000);
        }, 100);
    }
};

const handleDragEnd = () => {
    // No specific logic needed, draggable already updated the array
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

const initializeViews = (savedViews) => {
  const views = JSON.parse(JSON.stringify(savedViews || []));
  
  viewOptions.forEach(opt => {
    if (!views.find(v => v.id === opt.id)) {
      views.push({
        id: opt.id,
        label: opt.label,
        icon: opt.icon,
        enabled: true,
        width: 'full',
        filters: opt.filters,
        isDefault: true
      });
    }
  });
  
  return views;
};

onMounted(() => {
    if (authStore.user) {
        profileForm.value.name = authStore.user.name;
        profileForm.value.email = authStore.user.email;
        userViews.value = initializeViews(authStore.user.custom_views);
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
        userViews.value = initializeViews(u.custom_views);
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
.widgets-config-list { display: flex; flex-wrap: wrap; gap: 12px; }
.widget-config-row-container { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.widget-config-row-container.is-disabled { order: 999; }
.w-config-quarter { width: calc(25% - 9px); }
.w-config-third { width: calc(33.33% - 8px); }
.w-config-half { width: calc(50% - 6px); }
.w-config-full { width: 100%; }

@media (max-width: 800px) {
  .w-config-quarter, .w-config-third, .w-config-half { width: 100%; }
}

.widget-config-row { 
  display: flex; align-items: flex-start; gap: 16px; padding: 12px 16px; 
  background: white; border: 1px solid var(--gray-200); border-radius: 16px; transition: border 0.2s, background 0.2s, box-shadow 0.2s;
  height: 100%; position: relative; cursor: grab;
}
.widget-config-row:active { cursor: grabbing; }
.widget-config-row:hover { border-color: var(--blue-400); background: var(--gray-50); box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
.widget-config-row.disabled { opacity: 0.5; background: var(--gray-50); cursor: default; }

.widget-ghost {
  opacity: 0.3 !important;
  background: var(--blue-50) !important;
  border: 2px dashed var(--blue-400) !important;
  border-radius: 16px !important;
}

.widget-dragging {
  opacity: 1 !important;
  transform: scale(1.02);
  box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
  z-index: 9999 !important;
}

/* Sidebar à gauche */
.widget-config-sidebar { display: flex; flex-direction: column; align-items: center; gap: 10px; width: 44px; flex-shrink: 0; }
.widget-config-icon { 
  width: 44px; height: 44px; border-radius: 12px; background: white; border: 1px solid var(--gray-200);
  display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--indigo-600);
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.widget-config-actions-vertical { display: flex; flex-direction: column; align-items: center; gap: 8px; width: 100%; }

.ww-select-compact { 
  width: 100%; padding: 4px 2px; font-size: 10px; font-weight: 800; border-radius: 6px; 
  border: 1px solid var(--gray-200); background: white; color: var(--gray-700); cursor: pointer; text-align: center;
}
.ww-select-compact:focus { border-color: var(--blue-400); outline: none; }

/* Contenu à droite */
.widget-config-content { flex: 1; min-width: 0; padding-top: 4px; padding-bottom: 24px; }
.widget-config-label { font-size: 14px; font-weight: 800; color: var(--gray-800); }
.widget-config-desc { font-size: 11px; color: var(--gray-500); margin-top: 4px; line-height: 1.4; }

.widget-config-toggle-br { position: absolute; bottom: 12px; right: 12px; z-index: 5; }

/* Wow Effect Activation */
.just-activated .widget-config-row {
  animation: success-glow 1.5s ease-out;
  border-color: var(--teal-500) !important;
  background: var(--teal-50) !important;
  z-index: 10;
}

@keyframes success-glow {
  0% { box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.6); transform: scale(1.03); }
  50% { box-shadow: 0 0 40px 20px rgba(20, 184, 166, 0); transform: scale(1); }
  100% { box-shadow: 0 0 0 0 rgba(20, 184, 166, 0); }
}

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

/* ============================================================
   CUSTOM VIEWS - Tiles (draggable)
   ============================================================ */
.config-label-hint {
  font-size: 11px; font-weight: 400; color: var(--gray-400);
  text-transform: none; letter-spacing: 0; margin-left: 8px;
}

.cv-tiles-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 16px;
}

.cv-tile-card {
  flex-direction: column;
  align-items: stretch;
}

.cv-tile {
  position: relative;
  display: flex;
  background: white; border: 1px solid var(--gray-200);
  border-radius: 14px; overflow: hidden;
  transition: all 0.25s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.cv-tile:hover { border-color: var(--blue-300); box-shadow: 0 4px 16px rgba(59,130,246,0.08); }

.cv-tile--disabled { opacity: 0.55; background: var(--gray-50); }

/* WOW reactivation glow */
.cv-tile--activated { animation: cv-wow 1.8s ease-out; border-color: var(--teal-400) !important; z-index: 10; }
@keyframes cv-wow {
  0%   { box-shadow: 0 0 0 0 rgba(20,184,166,0.7); transform: scale(1.025); background: var(--teal-50); }
  40%  { box-shadow: 0 0 32px 16px rgba(20,184,166,0.15); }
  100% { box-shadow: 0 1px 4px rgba(0,0,0,0.04); transform: scale(1); background: white; }
}

/* Drag states */
.cv-tile-ghost  { opacity: 0.35; border: 2px dashed var(--blue-400); background: var(--blue-50); }
.cv-tile-dragging { box-shadow: 0 12px 40px rgba(0,0,0,0.18); transform: scale(1.02); z-index: 100; }

/* Drag zone (entire tile except buttons) */
.cv-tile-drag-zone {
  display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1;
  padding: 24px 16px 16px; cursor: grab; min-width: 0; text-align: center;
}
.cv-tile-drag-zone:active { cursor: grabbing; }

.cv-tile-icon {
  width: 44px; height: 44px; border-radius: 12px;
  background: var(--blue-50); color: var(--blue-600);
  display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.cv-favicon { width: 28px; height: 28px; object-fit: contain; border-radius: 4px; }

/* Content */
.cv-tile-label { font-size: 14px; font-weight: 700; color: var(--gray-800); }
.cv-tile-desc  { font-size: 11px; color: var(--gray-500); margin-top: 2px; }
.cv-tile-filters { display: flex; flex-wrap: wrap; justify-content: center; gap: 4px; margin-top: 4px; }
.cv-filter-tag {
  font-size: 10px; font-weight: 600; padding: 2px 7px;
  background: var(--blue-50); color: var(--blue-700);
  border-radius: 6px; border: 1px solid var(--blue-100);
}

/* Footer */
.cv-tile-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 16px; border-top: 1px solid var(--gray-100);
  background: var(--gray-50);
}
.cv-size-select { width: 80px; }
.cv-tile-switch { margin-right: 0; }

/* Edit button */
.cv-btn-edit-abs {
  position: absolute; top: 12px; right: 12px; z-index: 2;
  flex-shrink: 0; width: 28px; height: 28px; border-radius: 8px;
  border: 1px solid var(--gray-200); background: white; cursor: pointer;
  color: var(--gray-500); font-size: 11px;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.18s;
}
.cv-btn-edit-abs:hover { border-color: var(--blue-400); color: var(--blue-600); background: var(--blue-50); }

/* ============================================================
   CUSTOM VIEWS - Creator / Editor card
   ============================================================ */
.cv-creator-card {
  border: 2px dashed var(--gray-300); border-radius: 16px;
  overflow: hidden; background: var(--gray-50);
}
.cv-creator-header {
  padding: 12px 20px; background: var(--gray-100);
  font-size: 13px; font-weight: 700; color: var(--gray-700);
  border-bottom: 1px solid var(--gray-200);
}
.cv-creator-body { padding: 20px; }

.cv-form-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; }
.cv-form-group { display: flex; flex-direction: column; gap: 5px; min-width: 120px; flex: 1; }

.cv-icon-row { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }

/* Sexy Inputs */
.cv-input-sexy {
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  padding: 10px 14px;
  background: white;
  transition: all 0.2s;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
}
.cv-input-sexy:hover {
  border-color: var(--gray-400);
}
.cv-input-sexy:focus {
  border-color: var(--blue-500);
  box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
  outline: none;
}

.cv-creator-actions { display: flex; justify-content: flex-end; }
.cv-edit-actions { display: flex; gap: 10px; align-items: center; }

/* Danger ghost btn */
.btn-danger-ghost { color: var(--red-600) !important; border-color: var(--red-200) !important; }
.btn-danger-ghost:hover { background: var(--red-50) !important; border-color: var(--red-400) !important; }

/* ============================================================
   MODAL CONFIRM
   ============================================================ */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: center;
  z-index: 9999; backdrop-filter: blur(3px);
}
.modal-box {
  background: white; border-radius: 18px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  width: 400px; max-width: 92vw; overflow: hidden;
}
.modal-header {
  padding: 20px 24px; font-size: 16px; font-weight: 700; color: var(--gray-800);
  border-bottom: 1px solid var(--gray-100);
}
.modal-body  { padding: 20px 24px; font-size: 14px; color: var(--gray-600); line-height: 1.6; }
.modal-footer {
  padding: 16px 24px; border-top: 1px solid var(--gray-100);
  display: flex; justify-content: flex-end; gap: 10px;
}

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

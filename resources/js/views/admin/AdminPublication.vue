<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">API</div>
            <div class="hero-subtitle">Gérez les données exposées publiquement et configurez l'accès API pour les intégrations tierces.</div>
          </div>
          <div class="hero-action">
             <button class="btn btn-primary btn-lg shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-cloud-arrow-up mr-8"></i> {{ saving ? 'Enregistrement...' : 'Enregistrer les paramètres' }}
             </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-48">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement des paramètres...</p>
      </div>

      <div v-else class="config-layout mt-32">
        <!-- NAVIGATION GAUCHE -->
        <aside class="config-nav">
          <div class="nav-group-title">API</div>
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

          <!-- SECTION CLÉ API -->
          <div v-if="activeSection === 'api_key'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-key"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Clé d'API</div>
                <div class="pc-header-sub">Clé d'authentification requise pour accéder aux routes publiques</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <div class="alert alert-info mb-24">
                <i class="fa-solid fa-circle-info"></i>
                <p>Pour utiliser l'API publique (<code>/api/v1/public/decisions</code>), le client doit envoyer cette clé via l'en-tête HTTP <code>X-API-Key</code> ou le paramètre <code>?api_key=</code>.</p>
              </div>

              <div class="form-group">
                <label class="config-label">Clé secrète actuelle</label>
                <div style="display: flex; gap: 16px; align-items: center;">
                  <input type="text" v-model="config.public_api_key" class="input input-lg" readonly
                    style="font-family: monospace; letter-spacing: 2px; background: var(--gray-50);"
                    :placeholder="config.public_api_key ? '' : 'Aucune clé active'">
                  <button class="btn btn-outline" @click="generateApiKey">
                    <i class="fa-solid fa-rotate-right mr-8"></i> {{ config.public_api_key ? 'Regénérer' : 'Générer une clé' }}
                  </button>
                  <button v-if="config.public_api_key" class="btn btn-ghost text-red-500" @click="revokeApiKey">
                    <i class="fa-solid fa-trash-can mr-8"></i> Révoquer
                  </button>
                </div>
                <p class="help-text">La regénération révoque immédiatement l'ancienne clé pour tous les clients.</p>
                <div class="config-key">variable : <code>public_api_key</code></div>
              </div>
            </div>
          </div>
          
          <!-- SECTION AUTORISATION ACCES -->
          <div v-if="activeSection === 'api_access'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-shield-halved"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Autorisation d'accès</div>
                <div class="pc-header-sub">Définissez les périmètres de données accessibles via l'API</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <p class="help-text mb-24">Ces paramètres contrôlent quelles données sont retournées par l'API publique. Les décisions privées restent inaccessibles par défaut.</p>

              <!-- CERCLES -->
              <div class="form-group mb-32">
                <label class="config-label">Cercles autorisés</label>
                <p class="help-text mb-16">Seules les décisions rattachées à ces cercles (et marquées publiques) seront exposées.</p>
                <div class="selection-chips">
                  <label v-for="circle in circles" :key="circle.id" class="chip-item">
                    <input type="checkbox" :value="circle.id" v-model="config.api_circles">
                    <div class="chip-content">
                      <i class="fa-solid fa-users-gear"></i>
                      <span>{{ circle.name }}</span>
                    </div>
                  </label>
                  <div v-if="circles.length === 0" class="text-muted p-16 text-xs italic">Aucun cercle trouvé.</div>
                </div>
                <div class="config-key">variable : <code>api_circles</code></div>
              </div>

              <!-- CATEGORIES -->
              <div class="form-group mb-32">
                <label class="config-label">Catégories autorisées</label>
                <p class="help-text mb-16">Seules les décisions rattachées à ces catégories pourront être exposées via l'API.</p>
                <div class="selection-chips">
                  <label v-for="cat in categories" :key="cat.id" class="chip-item">
                    <input type="checkbox" :value="cat.id" v-model="config.api_categories">
                    <div class="chip-content">
                      <i class="fa-solid fa-tag"></i>
                      <span>{{ cat.name }}</span>
                    </div>
                  </label>
                  <div v-if="categories.length === 0" class="text-muted p-16 text-xs italic">Aucune catégorie trouvée.</div>
                </div>
                <div class="config-key">variable : <code>api_categories</code></div>
              </div>

              <!-- STATUTS -->
              <div class="form-group">
                <label class="config-label">Statuts exposés</label>
                <p class="help-text mb-16">Définissez quels statuts de décisions sont consultables en externe.</p>
                <div class="selection-chips">
                  <label v-for="status in availableStatuses" :key="status.value" class="chip-item">
                    <input type="checkbox" :value="status.value" v-model="config.api_statuses">
                    <div class="chip-badge" 
                         :style="{ 
                           color: status.defaultPrimary,
                           backgroundColor: status.defaultSecondary
                         }">
                      <i class="fa-solid fa-circle-check"></i>
                      <span>{{ status.label }}</span>
                    </div>
                  </label>
                </div>
                <div class="config-key">variable : <code>api_statuses</code></div>
              </div>

            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Autorisations
              </button>
            </div>
          </div>

          <!-- SECTION FILTRES API -->
          <div v-if="activeSection === 'api_filters'" class="premium-card animate-fade-in">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-sliders"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Filtres de l'API</div>
                <div class="pc-header-sub">Filtres dynamiques autorisés dans les requêtes de l'API</div>
              </div>
            </div>
            <div class="pc-body p-24">
              <p class="help-text mb-24">Activez les paramètres de filtrage que les clients externes pourront utiliser sur l'endpoint public.</p>
              <div class="checkbox-list inline-list">
                <label v-for="filter in availableFilters" :key="filter.value" class="checkbox-item">
                  <input type="checkbox" :value="filter.value" v-model="config.api_filters">
                  <span>{{ filter.label }} (<code>?{{ filter.value }}=...</code>)</span>
                </label>
              </div>
              <div class="config-key mt-16">variable : <code>api_filters</code></div>
            </div>
            <div class="pc-footer bg-gray-50 border-top p-16 flex justify-end">
              <button class="btn btn-primary shadow-blue" @click="saveConfig" :disabled="saving">
                <i class="fa-solid fa-check mr-8"></i> Enregistrer Filtres
              </button>
            </div>
          </div>

          <!-- SECTION SNIPPETS -->
          <div v-if="activeSection === 'snippet'" class="animate-fade-in" style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- MES SNIPPETS -->
            <div class="premium-card">
              <div class="pc-header pc-header-blue">
                <div class="pc-header-icon"><i class="fa-solid fa-list-check"></i></div>
                <div class="pc-header-content">
                  <div class="pc-header-title">Mes snippets</div>
                  <div class="pc-header-sub">Gérez vos widgets existants</div>
                </div>
              </div>
              <div class="pc-body p-0">
                <div v-if="config.saved_snippets && config.saved_snippets.length > 0">
                    <div v-for="snip in config.saved_snippets" :key="snip.title" class="snippet-row">
                        <div class="snippet-row-title">{{ snip.title }}</div>
                        <span class="badge badge-sm" :class="snip.type === 'single' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'">
                          {{ snip.type === 'single' ? 'Unique' : 'Liste' }}
                        </span>
                        <span class="badge badge-sm bg-gray-50 border text-gray-500"><i class="fa-solid fa-palette mr-4"></i>{{ predefinedPalettes.find(p=>p.id===snip.theme)?.label || snip.theme }}</span>
                        <span class="snippet-row-filters">
                            <template v-if="snip.type === 'single'">ID: <code>{{ snip.decision_id?.substring(0, 12) }}…</code></template>
                            <template v-else>
                                <span v-if="snip.status">{{ availableStatuses.find(s=>s.value===snip.status)?.label || snip.status }}</span>
                                <span v-if="snip.circle_id">Cercle</span>
                                <span v-if="snip.category_id">Cat.</span>
                                <span v-if="snip.period">{{ snip.period }}</span>
                                <span v-if="!snip.circle_id && !snip.category_id && !snip.status && !snip.period" class="italic">Tous</span>
                            </template>
                        </span>
                        <div class="snippet-row-actions">
                            <button class="btn btn-xs btn-icon" @click="editSnippet(snip)" title="Éditer le snippet"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-xs btn-icon text-blue-600" @click="snippetToRender = snip; showSnippetRender = true" title="Aperçu du rendu"><i class="fa-solid fa-desktop"></i></button>
                            <button class="btn btn-xs btn-icon" @click="snippetToPreview = snip; showSnippetPreview = true" title="Aperçu du code"><i class="fa-solid fa-eye"></i></button>
                            <button class="btn btn-xs btn-icon text-green-600" @click="copySnippetCodeDirect(snip)" title="Copier le code"><i class="fa-solid fa-copy"></i></button>
                            <button class="btn btn-xs btn-icon text-red-500" @click="confirmDeleteSnippet(snip)" title="Supprimer le snippet"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center p-32 text-muted text-sm italic">
                  Aucun snippet enregistré pour le moment.
                </div>
              </div>
            </div>

            <!-- GENERATOR -->
            <div class="premium-card">
              <div class="pc-header pc-header-blue">
                <div class="pc-header-icon"><i class="fa-solid fa-code"></i></div>
                <div class="pc-header-content">
                  <div class="pc-header-title">Générateur de snippet</div>
                  <div class="pc-header-sub">Créez ou modifiez un extrait de code</div>
                </div>
              </div>
              <div class="pc-body p-24">
                  <div class="grid-layout">
                      <!-- Config Form -->
                      <div class="col-side" style="min-width: 350px;">
                          <div class="form-group mb-16">
                              <label class="label">Titre du snippet</label>
                              <input type="text" v-model="snippetConfig.title" class="input w-full" placeholder="Ex: Widget Accueil">
                          </div>

                          <div class="grid-2 gap-16 mb-20">
                              <div class="form-group">
                                  <label class="label">Type de Widget</label>
                                  <div class="flex gap-0 mt-8">
                                      <button class="btn btn-sm flex-1 btn-toggle-left justify-center" :class="snippetConfig.type === 'single' ? 'btn-blue' : 'btn-outline'" @click="snippetConfig.type = 'single'">Décision Unique</button>
                                      <button class="btn btn-sm flex-1 btn-toggle-right justify-center" :class="snippetConfig.type === 'list' ? 'btn-blue' : 'btn-outline'" @click="snippetConfig.type = 'list'">Liste de Décisions</button>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label class="label">Thème Visuel</label>
                                  <button class="btn btn-outline btn-sm flex justify-between items-center w-full mt-8" @click.prevent="showThemePopin = true">
                                    <span><i class="fa-solid fa-palette mr-8"></i>{{ currentTheme.label }}</span>
                                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                                  </button>
                              </div>
                          </div>

                          <!-- SINGLE DECISION -->
                          <div v-if="snippetConfig.type === 'single'" class="mb-20 p-16 bg-gray-50 rounded-xl border">
                              <div class="grid-2 gap-16 mb-16">
                                  <div class="form-group">
                                      <label class="label text-xs">Rechercher une décision</label>
                                      <div class="search-ajax-wrapper">
                                        <div class="search-ajax-input-wrap">
                                          <i class="fa-solid fa-magnifying-glass search-ajax-icon"></i>
                                          <input type="text" v-model="searchQuery" @input="handleSearchDecision" class="input w-full search-ajax-input" placeholder="Titre, contenu...">
                                          <i v-if="isSearching" class="fa-solid fa-spinner fa-spin search-ajax-spinner"></i>
                                        </div>
                                        <div v-if="(isSearching || searchGroups.titles?.length || searchGroups.content?.length) && searchQuery.length >= 2" class="search-ajax-dropdown">
                                          <div v-if="isSearching" class="search-ajax-loading"><i class="fa-solid fa-circle-notch fa-spin"></i> Recherche en cours...</div>
                                          <template v-else>
                                            <div v-if="searchGroups.titles?.length" class="search-ajax-group">
                                                <label>Titres</label>
                                                <div v-for="dec in searchGroups.titles" :key="'t'+dec.id" class="search-ajax-item" @mousedown.prevent="selectDecision(dec)">
                                                  {{ dec.title }}
                                                </div>
                                            </div>
                                            <div v-if="searchGroups.content?.length" class="search-ajax-group">
                                                <label>Contenu</label>
                                                <div v-for="dec in searchGroups.content" :key="'c'+dec.id" class="search-ajax-item" @mousedown.prevent="selectDecision(dec)">
                                                  {{ dec.title }} <small>(match contenu)</small>
                                                </div>
                                            </div>
                                            <div v-if="!searchGroups.titles?.length && !searchGroups.content?.length" class="search-ajax-empty">Aucun résultat trouvé</div>
                                          </template>
                                        </div>
                                      </div>
                                  </div>
                                  <div class="form-group">
                                      <label class="label text-xs">ID de la décision (UUID)</label>
                                      <input type="text" v-model="snippetConfig.decision_id" class="input w-full" placeholder="Ex: 123e4567-...">
                                  </div>
                              </div>
                              
                              <div class="border-top pt-12">
                                <label class="label text-xs mb-8">Détails à afficher</label>
                                <div class="detail-toggles">
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_clarifications"> <span>Clarifications</span></label>
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_reactions"> <span>Réactions</span></label>
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_objections"> <span>Objections</span></label>
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_suggestions"> <span>Suggestions</span></label>
                                </div>
                              </div>
                          </div>

                          <!-- LIST DECISION -->
                          <div v-if="snippetConfig.type === 'list'" class="mb-20 p-16 bg-gray-50 rounded-xl border">
                              <div class="list-filters-row" style="align-items: flex-end;">
                                  <div class="form-group flex-1">
                                      <label class="label text-xs">Phase</label>
                                      <select v-model="snippetConfig.status" class="input w-full select-sm">
                                          <option value="">Toutes</option>
                                          <option v-for="s in availableStatuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                                      </select>
                                  </div>
                                  <div class="form-group flex-1">
                                      <label class="label text-xs">Cercle</label>
                                      <select v-model="snippetConfig.circle_id" class="input w-full select-sm">
                                          <option value="">Tous</option>
                                          <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
                                      </select>
                                  </div>
                                  <div class="form-group flex-1">
                                      <label class="label text-xs">Catégorie</label>
                                      <select v-model="snippetConfig.category_id" class="input w-full select-sm">
                                          <option value="">Toutes</option>
                                          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                      </select>
                                  </div>
                                  <div class="form-group flex-1">
                                      <label class="label text-xs">Auteur</label>
                                      <input type="text" v-model="snippetConfig.author_id" class="input w-full input-sm" placeholder="ID…">
                                  </div>
                                  <div class="form-group flex-1">
                                      <label class="label text-xs">Période</label>
                                      <select v-model="snippetConfig.period" class="input w-full select-sm">
                                          <option value="">Toutes</option>
                                          <option value="today">Aujourd'hui</option>
                                          <option value="week">Semaine</option>
                                          <option value="month">Mois</option>
                                      </select>
                                  </div>
                                  <div class="form-group flex-1">
                                      <label class="label text-xs">Détails</label>
                                      <button class="btn btn-sm w-full justify-center" :class="snippetConfig.show_detail ? 'btn-blue' : 'btn-outline'" @click.prevent="snippetConfig.show_detail = !snippetConfig.show_detail">Vue détail</button>
                                  </div>
                                  <div class="form-group">
                                      <label class="label text-xs">&nbsp;</label>
                                      <button class="btn btn-sm btn-outline w-full justify-center" @click.prevent="resetListFilters" title="Réinitialiser les filtres"><i class="fa-solid fa-rotate-left"></i></button>
                                  </div>
                              </div>
                              
                              <div v-if="snippetConfig.show_detail" class="border-top pt-12 mt-12">
                                <div class="detail-toggles">
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_clarifications"> <span>Clarifications</span></label>
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_reactions"> <span>Réactions</span></label>
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_objections"> <span>Objections</span></label>
                                  <label class="detail-toggle-item"><input type="checkbox" v-model="snippetConfig.show_suggestions"> <span>Suggestions</span></label>
                                </div>
                              </div>
                          </div>

                          <div class="mt-24">
                              <button class="btn btn-primary shadow-blue w-full" @click="saveSnippet">
                                <i class="fa-solid fa-floppy-disk mr-8"></i> Sauvegarder ce snippet
                              </button>
                          </div>
                      </div>

                      <!-- Preview Area & Code -->
                      <div class="col-main flex flex-col mt-32 md:mt-0">
                          <!-- Aperçu -->
                          <div class="preview-container flex-1" :class="currentTheme.id === 'dark' || currentTheme.id === 'midnight' || currentTheme.id === 'forest_dark' ? 'dark' : ''" :key="previewKey">
                              <div class="preview-header">
                                  <div class="preview-dot" :style="{ background: currentTheme.colors[0] }"></div>
                                  <div class="preview-dot" :style="{ background: currentTheme.colors[3] }"></div>
                                  <div class="preview-dot" :style="{ background: currentTheme.colors[2], opacity: 0.3 }"></div>
                                  <span class="ml-12 text-xs opacity-50">Aperçu — {{ snippetConfig.type === 'single' ? 'Décision Unique' : 'Liste' }} — {{ currentTheme.label }}</span>
                              </div>
                              <div class="preview-content" :style="{ background: currentTheme.colors[1] }">
                                  <!-- Single preview -->
                                  <div v-if="snippetConfig.type === 'single'" class="widget-placeholder" :style="{ borderColor: currentTheme.colors[0] + '40' }">
                                      <div class="flex items-center gap-12 mb-16">
                                          <div class="w-40 h-40 rounded flex items-center justify-center" :style="{ backgroundColor: currentTheme.colors[3], color: currentTheme.colors[0] }">
                                              <i class="fa-solid fa-gavel"></i>
                                          </div>
                                          <div class="flex-1">
                                              <div class="h-12 rounded w-3/4 mb-8" :style="{ backgroundColor: currentTheme.colors[3] }"></div>
                                              <div class="h-8 rounded w-1/2" :style="{ backgroundColor: currentTheme.colors[3], opacity: 0.5 }"></div>
                                          </div>
                                      </div>
                                      <div class="space-y-8">
                                        <div class="h-8 rounded w-full" :style="{ backgroundColor: currentTheme.colors[3], opacity: 0.3 }"></div>
                                        <div class="h-8 rounded w-5/6" :style="{ backgroundColor: currentTheme.colors[3], opacity: 0.3 }"></div>
                                      </div>
                                      <div v-if="snippetConfig.show_clarifications || snippetConfig.show_reactions || snippetConfig.show_objections || snippetConfig.show_suggestions" class="mt-16 pt-12 border-top space-y-6">
                                          <div v-if="snippetConfig.show_clarifications" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: currentTheme.colors[0] }"></div><div class="h-6 rounded flex-1" :style="{ backgroundColor: currentTheme.colors[3] }"></div></div>
                                          <div v-if="snippetConfig.show_reactions" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: currentTheme.colors[0], opacity: 0.7 }"></div><div class="h-6 rounded w-3/4" :style="{ backgroundColor: currentTheme.colors[3] }"></div></div>
                                          <div v-if="snippetConfig.show_objections" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: currentTheme.colors[0], opacity: 0.5 }"></div><div class="h-6 rounded w-1/2" :style="{ backgroundColor: currentTheme.colors[3] }"></div></div>
                                          <div v-if="snippetConfig.show_suggestions" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: currentTheme.colors[0], opacity: 0.3 }"></div><div class="h-6 rounded w-2/3" :style="{ backgroundColor: currentTheme.colors[3] }"></div></div>
                                      </div>
                                      <div class="mt-16 pt-12 border-top flex justify-between items-center">
                                          <div class="h-16 rounded w-24" :style="{ backgroundColor: currentTheme.colors[3] }"></div>
                                          <div class="text-[10px] font-bold uppercase" :style="{ color: currentTheme.colors[0] }">Voir sur DAZO</div>
                                      </div>
                                  </div>
                                  <!-- List preview -->
                                  <div v-else class="widget-placeholder" style="max-width:100%;width:100%;" :style="{ borderColor: currentTheme.colors[0] + '40' }">
                                      <div class="flex justify-between items-center mb-16 border-bottom pb-12">
                                        <h4 class="font-bold text-sm" :style="{ color: currentTheme.colors[2] }">Dernières décisions</h4>
                                        <div class="flex gap-4">
                                          <div v-if="snippetConfig.status" class="px-6 py-2 rounded text-[9px] font-bold" :style="{ backgroundColor: currentTheme.colors[3], color: currentTheme.colors[2] }">{{ availableStatuses.find(s=>s.value===snippetConfig.status)?.label || 'Phase' }}</div>
                                          <div v-if="snippetConfig.category_id" class="px-6 py-2 rounded text-[9px] font-bold" :style="{ backgroundColor: currentTheme.colors[3], color: currentTheme.colors[2] }">Cat.</div>
                                        </div>
                                      </div>
                                      <div v-for="i in 3" :key="i" class="flex gap-12 py-10 border-bottom">
                                        <div class="w-8 h-8 rounded-full flex-shrink-0 mt-4" :style="{ backgroundColor: currentTheme.colors[0] }"></div>
                                        <div class="flex-1">
                                            <div class="h-10 rounded w-full mb-6" :style="{ backgroundColor: currentTheme.colors[3] }"></div>
                                            <div class="h-6 rounded w-1/2" :style="{ backgroundColor: currentTheme.colors[3], opacity: 0.5 }"></div>
                                            <div v-if="snippetConfig.show_detail && (snippetConfig.show_clarifications || snippetConfig.show_reactions)" class="flex gap-6 mt-6">
                                                <div v-if="snippetConfig.show_clarifications" class="h-4 w-16 rounded" :style="{ backgroundColor: currentTheme.colors[0], opacity: 0.4 }"></div>
                                                <div v-if="snippetConfig.show_reactions" class="h-4 w-12 rounded" :style="{ backgroundColor: currentTheme.colors[0], opacity: 0.3 }"></div>
                                            </div>
                                        </div>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Espace Code -->
                          <div class="mt-24 pt-20 border-top">
                              <label class="label mb-8">Code à copier</label>
                              <div class="snippet-code-box">
                                  <pre><code>{{ generateSnippetCode(snippetConfig) }}</code></pre>
                                  <button class="btn btn-sm btn-white copy-btn" @click="copySnippet">
                                      <i class="fa-solid fa-copy"></i>
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- POPIN PREVIEW SNIPPET (CODE) -->
    <div class="popin-overlay" v-if="showSnippetPreview && snippetToPreview" @click.self="showSnippetPreview = false; snippetToPreview = null">
      <div class="popin-container p-24" style="max-width: 600px;">
        <h3 class="font-bold text-lg text-gray-800 mb-16"><i class="fa-solid fa-eye text-blue-500 mr-8"></i> Aperçu du code</h3>
        <p class="text-sm text-gray-600 mb-16">Code généré pour le snippet "<strong>{{ snippetToPreview.title }}</strong>".</p>
        <div class="snippet-code-box mb-24">
            <pre><code>{{ generateSnippetCode(snippetToPreview) }}</code></pre>
            <button class="btn btn-sm btn-white copy-btn" @click="copySnippetCodeDirect(snippetToPreview)">
                <i class="fa-solid fa-copy"></i>
            </button>
        </div>
        <div class="flex justify-end">
          <button class="btn btn-primary shadow-blue" @click="showSnippetPreview = false; snippetToPreview = null">Fermer</button>
        </div>
      </div>
    </div>

    <!-- POPIN RENDER SNIPPET (VISUAL) -->
    <div class="popin-overlay" v-if="showSnippetRender && snippetToRender" @click.self="showSnippetRender = false; snippetToRender = null">
      <div class="popin-container" style="max-width: 500px; display: flex; flex-direction: column; overflow: hidden; background: transparent; box-shadow: none;">
        <!-- Re-use the preview skeleton component dynamically for this snippet -->
        <div class="preview-container" :class="getTheme(snippetToRender.theme).id === 'dark' || getTheme(snippetToRender.theme).id === 'midnight' || getTheme(snippetToRender.theme).id === 'forest_dark' ? 'dark' : ''">
            <div class="preview-header">
                <div class="preview-dot" :style="{ background: getTheme(snippetToRender.theme).colors[0] }"></div>
                <div class="preview-dot" :style="{ background: getTheme(snippetToRender.theme).colors[3] }"></div>
                <div class="preview-dot" :style="{ background: getTheme(snippetToRender.theme).colors[2], opacity: 0.3 }"></div>
                <span class="ml-12 text-xs opacity-50">Rendu du widget — {{ snippetToRender.title }}</span>
                <button class="ml-auto text-gray-400 hover:text-gray-800" @click="showSnippetRender = false; snippetToRender = null"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="preview-content" :style="{ background: getTheme(snippetToRender.theme).colors[1] }">
                <!-- Single preview -->
                <div v-if="snippetToRender.type === 'single'" class="widget-placeholder" :style="{ borderColor: getTheme(snippetToRender.theme).colors[0] + '40' }">
                    <div class="flex items-center gap-12 mb-16">
                        <div class="w-40 h-40 rounded flex items-center justify-center" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3], color: getTheme(snippetToRender.theme).colors[0] }">
                            <i class="fa-solid fa-gavel"></i>
                        </div>
                        <div class="flex-1">
                            <div class="h-12 rounded w-3/4 mb-8" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3] }"></div>
                            <div class="h-8 rounded w-1/2" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3], opacity: 0.5 }"></div>
                        </div>
                    </div>
                    <div class="space-y-8">
                      <div class="h-8 rounded w-full" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3], opacity: 0.3 }"></div>
                      <div class="h-8 rounded w-5/6" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3], opacity: 0.3 }"></div>
                    </div>
                    <div v-if="snippetToRender.show_clarifications || snippetToRender.show_reactions || snippetToRender.show_objections || snippetToRender.show_suggestions" class="mt-16 pt-12 border-top space-y-6">
                        <div v-if="snippetToRender.show_clarifications" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[0] }"></div><div class="h-6 rounded flex-1" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3] }"></div></div>
                        <div v-if="snippetToRender.show_reactions" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[0], opacity: 0.7 }"></div><div class="h-6 rounded w-3/4" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3] }"></div></div>
                        <div v-if="snippetToRender.show_objections" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[0], opacity: 0.5 }"></div><div class="h-6 rounded w-1/2" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3] }"></div></div>
                        <div v-if="snippetToRender.show_suggestions" class="flex items-center gap-8"><div class="w-4 h-4 rounded-full" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[0], opacity: 0.3 }"></div><div class="h-6 rounded w-2/3" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3] }"></div></div>
                    </div>
                    <div class="mt-16 pt-12 border-top flex justify-between items-center">
                        <div class="h-16 rounded w-24" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3] }"></div>
                        <div class="text-[10px] font-bold uppercase" :style="{ color: getTheme(snippetToRender.theme).colors[0] }">Voir sur DAZO</div>
                    </div>
                </div>
                <!-- List preview -->
                <div v-else class="widget-placeholder" style="max-width:100%;width:100%;" :style="{ borderColor: getTheme(snippetToRender.theme).colors[0] + '40' }">
                    <div class="flex justify-between items-center mb-16 border-bottom pb-12">
                      <h4 class="font-bold text-sm" :style="{ color: getTheme(snippetToRender.theme).colors[2] }">Dernières décisions</h4>
                      <div class="flex gap-4">
                        <div v-if="snippetToRender.status" class="px-6 py-2 rounded text-[9px] font-bold" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3], color: getTheme(snippetToRender.theme).colors[2] }">{{ availableStatuses.find(s=>s.value===snippetToRender.status)?.label || 'Phase' }}</div>
                        <div v-if="snippetToRender.category_id" class="px-6 py-2 rounded text-[9px] font-bold" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3], color: getTheme(snippetToRender.theme).colors[2] }">Cat.</div>
                      </div>
                    </div>
                    <div v-for="i in 3" :key="i" class="flex gap-12 py-10 border-bottom">
                      <div class="w-8 h-8 rounded-full flex-shrink-0 mt-4" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[0] }"></div>
                      <div class="flex-1">
                          <div class="h-10 rounded w-full mb-6" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3] }"></div>
                          <div class="h-6 rounded w-1/2" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[3], opacity: 0.5 }"></div>
                          <div v-if="snippetToRender.show_detail && (snippetToRender.show_clarifications || snippetToRender.show_reactions)" class="flex gap-6 mt-6">
                              <div v-if="snippetToRender.show_clarifications" class="h-4 w-16 rounded" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[0], opacity: 0.4 }"></div>
                              <div v-if="snippetToRender.show_reactions" class="h-4 w-12 rounded" :style="{ backgroundColor: getTheme(snippetToRender.theme).colors[0], opacity: 0.3 }"></div>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>

    <!-- POPIN DELETE SNIPPET -->
    <div class="popin-overlay" v-if="snippetToDelete" @click.self="snippetToDelete = null">
      <div class="popin-container p-24">
        <h3 class="font-bold text-lg text-gray-800 mb-8"><i class="fa-solid fa-triangle-exclamation text-red-500 mr-8"></i> Confirmer la suppression</h3>
        <p class="text-gray-600 mb-24">Êtes-vous sûr de vouloir supprimer le snippet "<strong>{{ snippetToDelete.title }}</strong>" ? Cette action est irréversible.</p>
        <div class="flex justify-end gap-12">
          <button class="btn btn-outline" @click="snippetToDelete = null">Annuler</button>
          <button class="btn btn-primary bg-red-500 border-red-500 shadow-red" @click="deleteSnippet">Confirmer la suppression</button>
        </div>
      </div>
    </div>

    <!-- POPIN THEME SELECTION -->
    <div class="popin-overlay" v-if="showThemePopin" @click.self="showThemePopin = false">
      <div class="popin-container p-24" style="max-width: 800px; max-height: 80vh; display: flex; flex-direction: column;">
        <h3 class="font-bold text-lg text-gray-800 mb-16"><i class="fa-solid fa-palette text-blue-500 mr-8"></i> Sélectionner un thème</h3>
        <div class="overflow-y-auto flex-1 pr-8">
            <div class="grid-3 gap-16">
              <div v-for="theme in predefinedPalettes" :key="theme.id" 
                   class="theme-card cursor-pointer border rounded-xl overflow-hidden hover:shadow-md transition-all"
                   :class="{'ring-2 ring-blue-500': snippetConfig.theme === theme.id}"
                   @click="selectTheme(theme.id)">
                <div class="flex h-48">
                  <div v-for="color in theme.colors" :key="color" class="flex-1" :style="{ backgroundColor: color }"></div>
                </div>
                <div class="p-12 text-center text-sm font-bold bg-gray-50 border-top">
                  {{ theme.label }}
                </div>
              </div>
            </div>
        </div>
        <div class="mt-20 flex justify-end border-top pt-16">
          <button class="btn btn-outline" @click="showThemePopin = false">Fermer</button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const config = ref({
  public_api_key: '',
  api_circles: [],
  api_categories: [],
  api_statuses: [],
  api_filters: [],
  saved_snippets: []
});

const circles = ref([]);
const categories = ref([]);
const loading = ref(true);
const saving = ref(false);
const activeSection = ref('snippet');
const baseUrl = window.location.origin;

const snippetConfig = ref({
    title: '',
    type: 'single',
    decision_id: '',
    circle_id: '',
    category_id: '',
    status: '',
    author_id: '',
    period: '',
    limit: 5,
    theme: 'default',
    show_meta: true,
    show_detail: false,
    show_clarifications: true,
    show_reactions: true,
    show_objections: true,
    show_suggestions: true,
    filters: []
});


// Popins & Search states
const showSnippetPreview = ref(false);
const showSnippetRender = ref(false);
const showThemePopin = ref(false);
const snippetToDelete = ref(null);
const snippetToPreview = ref(null);
const snippetToRender = ref(null);
const searchQuery = ref('');
const searchGroups = ref({ titles: [], content: [] });
const isSearching = ref(false);

const predefinedPalettes = [
  { id: 'default', label: 'Défaut (Bleu)', colors: ['#3b82f6', '#f8fafc', '#1e293b', '#e2e8f0'] },
  { id: 'red', label: 'Rouge Rubis', colors: ['#e11d48', '#fff1f2', '#881337', '#fecdd3'] },
  { id: 'yellow', label: 'Jaune Solaire', colors: ['#eab308', '#fefce8', '#713f12', '#fef08a'] },
  { id: 'green', label: 'Vert Forêt', colors: ['#16a34a', '#f0fdf4', '#14532d', '#bbf7d0'] },
  { id: 'purple', label: 'Violet Améthyste', colors: ['#9333ea', '#faf5ff', '#581c87', '#e9d5ff'] },
  { id: 'orange', label: 'Orange Corail', colors: ['#f97316', '#fff7ed', '#7c2d12', '#fed7aa'] },
  { id: 'teal', label: 'Bleu Canard', colors: ['#0d9488', '#f0fdfa', '#134e4a', '#99f6e4'] },
  { id: 'rose', label: 'Rose Poudré', colors: ['#f43f5e', '#fff1f2', '#881337', '#fecdd3'] },
  { id: 'indigo', label: 'Indigo Profond', colors: ['#4f46e5', '#eef2ff', '#312e81', '#c7d2fe'] },
  { id: 'gray', label: 'Monochrome Gris', colors: ['#4b5563', '#f9fafb', '#111827', '#e5e7eb'] },
  { id: 'dark', label: 'Mode Sombre', colors: ['#1f2937', '#111827', '#f3f4f6', '#374151'] },
  { id: 'midnight', label: 'Nuit Bleue', colors: ['#1e3a8a', '#0f172a', '#e2e8f0', '#1e293b'] },
  { id: 'forest_dark', label: 'Forêt Sombre', colors: ['#064e3b', '#022c22', '#d1fae5', '#065f46'] },
  { id: 'high_contrast', label: 'Contraste Élevé', colors: ['#000000', '#ffffff', '#000000', '#000000'] }
];

const currentTheme = computed(() => {
    return predefinedPalettes.find(p => p.id === snippetConfig.value.theme) || predefinedPalettes[0];
});

const previewKey = computed(() => {
    const s = snippetConfig.value;
    return `${s.type}-${s.theme}-${s.decision_id}-${s.status}-${s.circle_id}-${s.category_id}-${s.show_detail}-${s.show_clarifications}-${s.show_reactions}-${s.show_objections}-${s.show_suggestions}`;
});

const copySnippet = () => {
    const code = document.querySelector('.snippet-code-box pre code').innerText;
    navigator.clipboard.writeText(code);
    alert('Code copié dans le presse-papier !');
};

const sections = [
  { id: 'snippet', label: 'Générateur de snippet', icon: 'fa-solid fa-code' },
  { id: 'api_key', label: 'Clé API', icon: 'fa-solid fa-key' },
  { id: 'api_access', label: 'Autorisation d\'accès', icon: 'fa-solid fa-shield-halved' },
  { id: 'api_filters', label: 'Filtres API', icon: 'fa-solid fa-sliders' },
];

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

const availableFilters = [
  { value: 'category', label: 'Par Catégorie' },
  { value: 'circle', label: 'Par Cercle' },
  { value: 'status', label: 'Par Statut' },
  { value: 'author', label: 'Par Auteur' },
  { value: 'search', label: 'Recherche texte (titre)' },
];

const mapConfig = (data) => ({
  public_api_key: data.public_api_key || '',
  api_circles: Array.isArray(data.api_circles) ? data.api_circles : [],
  api_categories: Array.isArray(data.api_categories) ? data.api_categories : [],
  api_statuses: Array.isArray(data.api_statuses) ? data.api_statuses : [],
  api_filters: Array.isArray(data.api_filters) ? data.api_filters : [],
  saved_snippets: Array.isArray(data.saved_snippets) ? data.saved_snippets : []
});

const generateSnippetCode = (snip) => {
    const baseUrl = window.location.origin;
    const apiKey = config.value.public_api_key || 'VOTRE_CLE_API';
    let dataAttrs = `data-type="${snip.type}" data-theme="${snip.theme}" data-api-key="${apiKey}"`;
    
    if (snip.type === 'single') {
        dataAttrs += ` data-id="${snip.decision_id || ''}"`;
    } else {
        if (snip.status) dataAttrs += ` data-status="${snip.status}"`;
        if (snip.circle_id) dataAttrs += ` data-circle-id="${snip.circle_id}"`;
        if (snip.category_id) dataAttrs += ` data-category-id="${snip.category_id}"`;
    }
    
    if (snip.show_detail) {
        dataAttrs += ` data-show-detail="true"`;
        if (snip.show_clarifications) dataAttrs += ` data-show-clarifications="true"`;
        if (snip.show_reactions) dataAttrs += ` data-show-reactions="true"`;
        if (snip.show_objections) dataAttrs += ` data-show-objections="true"`;
        if (snip.show_suggestions) dataAttrs += ` data-show-suggestions="true"`;
    }

    return `<script src="${baseUrl}/widgets/loader.js"><\/script>\n<div class="dazo-widget" ${dataAttrs}></div>`;
};

const copySnippetCodeDirect = (snip) => {
    navigator.clipboard.writeText(generateSnippetCode(snip));
    alert('Code copié dans le presse-papier !');
};

const editSnippet = (snip) => {
    snippetConfig.value = { ...snip, filters: snip.filters || [], show_detail: snip.show_detail || false };
    activeSection.value = 'snippet';
};

const resetListFilters = () => {
    snippetConfig.value.status = '';
    snippetConfig.value.circle_id = '';
    snippetConfig.value.category_id = '';
    snippetConfig.value.author_id = '';
    snippetConfig.value.period = '';
};

const saveSnippet = async () => {
    if (!snippetConfig.value.title) {
        alert("Veuillez donner un titre au snippet.");
        return;
    }
    const idx = config.value.saved_snippets.findIndex(s => s.title === snippetConfig.value.title);
    if (idx !== -1) {
        config.value.saved_snippets[idx] = { ...snippetConfig.value };
    } else {
        config.value.saved_snippets.push({ ...snippetConfig.value });
    }
    await saveConfig();
};

const confirmDeleteSnippet = (snip) => {
    snippetToDelete.value = snip;
};

const deleteSnippet = async () => {
    if (!snippetToDelete.value) return;
    config.value.saved_snippets = config.value.saved_snippets.filter(s => s !== snippetToDelete.value);
    snippetToDelete.value = null;
    await saveConfig();
};

const selectTheme = (themeId) => {
    snippetConfig.value.theme = themeId;
    showThemePopin.value = false;
};

let searchTimeout;
const getTheme = (themeId) => {
    return predefinedPalettes.find(p => p.id === themeId) || predefinedPalettes[0];
};

const handleSearchDecision = () => {
    clearTimeout(searchTimeout);
    if (searchQuery.value.length < 2) {
        searchGroups.value = { titles: [], content: [] };
        return;
    }
    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const { data } = await axios.get(`/api/v1/front/decisions/suggestions?q=${encodeURIComponent(searchQuery.value)}`);
            const res = data.results || {};
            searchGroups.value = {
                titles: res.titles || [],
                content: res.content || []
            };
        } catch (e) {
            console.error(e);
            searchGroups.value = { titles: [], content: [] };
        } finally {
            isSearching.value = false;
        }
    }, 300);
};

const selectDecision = (dec) => {
    snippetConfig.value.decision_id = dec.id;
    searchQuery.value = dec.title;
    searchGroups.value = { titles: [], content: [] };
};

const generateApiKey = async () => {
  if (!confirm("Voulez-vous générer une nouvelle clé API ? L'ancienne sera immédiatement révoquée.")) return;
  try {
    const { data } = await axios.post('/api/v1/admin/config/api-key');
    config.value = mapConfig(data.config);
    alert('Nouvelle clé générée : ' + data.api_key);
  } catch (e) {
    alert('Erreur lors de la génération de la clé.');
  }
};

const revokeApiKey = async () => {
  if (!confirm('Voulez-vous révoquer la clé API actuelle ? Tout accès externe sera coupé.')) return;
  try {
    const { data } = await axios.delete('/api/v1/admin/config/api-key');
    config.value = mapConfig(data.config);
    alert('Clé révoquée avec succès.');
  } catch (e) {
    alert('Erreur lors de la révocation.');
  }
};

onMounted(async () => {
  try {
    const [configRes, circlesRes, categoriesRes] = await Promise.all([
      axios.get('/api/v1/admin/config'),
      axios.get('/api/v1/admin/circles?per_page=100'),
      axios.get('/api/v1/admin/categories?per_page=100'),
    ]);
    const data = configRes.data.config || configRes.data || {};
    config.value = mapConfig(data);
    circles.value = circlesRes.data.data || circlesRes.data.circles || [];
    categories.value = categoriesRes.data.data || categoriesRes.data.categories || [];
  } catch (e) {
    console.error('Fetch error', e);
  } finally {
    loading.value = false;
  }
});

const saveConfig = async () => {
  saving.value = true;
  try {
    await axios.put('/api/v1/admin/config', { config: config.value });
    alert('Paramètres de publication enregistrés avec succès.');
  } catch (e) {
    alert('Erreur lors de la sauvegarde.');
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
@import "../../../css/admin-config.css";

/* ── Snippet Row (Mes snippets) ── */
.snippet-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  border-bottom: 1px solid var(--gray-100);
  transition: background 0.15s;
}
.snippet-row:last-child { border-bottom: none; }
.snippet-row:hover { background: var(--gray-50); }
.snippet-row-title {
  font-weight: 700;
  font-size: 13px;
  color: var(--gray-800);
  min-width: 120px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.snippet-row-filters {
  flex: 1;
  font-size: 11px;
  color: var(--gray-500);
  display: flex;
  gap: 8px;
  align-items: center;
  overflow: hidden;
}
.snippet-row-filters code {
  font-family: var(--font-mono);
  font-size: 10px;
  background: var(--gray-100);
  padding: 2px 6px;
  border-radius: 4px;
}
.snippet-row-filters span {
  background: var(--gray-100);
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
  white-space: nowrap;
}
.snippet-row-actions {
  display: flex;
  gap: 4px;
  flex-shrink: 0;
}

/* ── Icon Buttons ── */
.btn-icon {
  width: 32px;
  height: 32px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--gray-200);
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s;
  color: var(--gray-500);
}
.btn-icon:hover { background: var(--gray-50); border-color: var(--gray-300); color: var(--gray-700); }
.btn-icon.text-blue-600 { color: var(--blue-600); }
.btn-icon.text-blue-600:hover { background: var(--blue-50); border-color: var(--blue-200); }
.btn-icon.text-red-500 { color: #ef4444; }
.btn-icon.text-red-500:hover { background: #fef2f2; border-color: #fecaca; }

.btn-icon-only {
  width: 36px;
  height: 36px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ── Toggle Buttons (Type Widget) ── */
.btn-toggle-left { border-radius: 8px 0 0 8px; }
.btn-toggle-right { border-radius: 0 8px 8px 0; margin-left: -1px; }

/* ── Search Ajax (front-style) ── */
.search-ajax-wrapper { position: relative; }
.search-ajax-input-wrap { position: relative; }
.search-ajax-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  font-size: 13px;
  pointer-events: none;
}
.search-ajax-input { padding-left: 36px !important; }
.search-ajax-spinner {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
}
.search-ajax-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 6px;
  background: var(--gray-50);
  border: 1px solid var(--gray-200);
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.12);
  max-height: 350px;
  overflow-y: auto;
  z-index: 100;
  padding: 12px;
  animation: dropIn 0.15s ease;
}
@keyframes dropIn {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}
.search-ajax-group { margin-bottom: 12px; }
.search-ajax-group:last-child { margin-bottom: 0; }
.search-ajax-group label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--gray-400);
  margin-bottom: 6px;
  padding-left: 8px;
}
.search-ajax-item {
  padding: 8px 12px;
  font-size: 13px;
  color: var(--gray-700);
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.15s;
}
.search-ajax-item:hover {
  background: white;
  color: var(--blue-600);
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.search-ajax-item small { color: var(--gray-400); margin-left: 4px; font-size: 11px; }
.search-ajax-loading, .search-ajax-empty {
  text-align: center;
  padding: 20px;
  font-size: 13px;
  color: var(--gray-500);
}

/* ── Detail Toggles (1/4 grid) ── */
.detail-toggles {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}
.detail-toggle-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.15s;
  border: 1px solid var(--gray-200);
  background: white;
}
.detail-toggle-item:hover { background: var(--blue-50); border-color: var(--blue-300); }
.detail-toggle-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--blue-600); flex-shrink: 0; }
.detail-toggle-item span { font-size: 12px; font-weight: 600; color: var(--gray-700); white-space: nowrap; }

/* ── List Filters Row ── */
.list-filters-row {
  display: flex;
  gap: 10px;
  align-items: flex-start;
}

/* ── Toggle Switch ── */
.toggle-switch {
  width: 36px;
  height: 20px;
  appearance: none;
  -webkit-appearance: none;
  background: var(--gray-300);
  border-radius: 20px;
  cursor: pointer;
  position: relative;
  transition: background 0.2s;
  flex-shrink: 0;
}
.toggle-switch::after {
  content: '';
  position: absolute;
  top: 2px;
  left: 2px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: white;
  transition: transform 0.2s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.toggle-switch:checked { background: var(--blue-600); }
.toggle-switch:checked::after { transform: translateX(16px); }

/* ── Existing overrides ── */
.checkbox-list {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 8px;
  padding: 8px 0;
}
.checkbox-list.inline-list {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  padding: 16px;
}
.checkbox-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 16px;
  cursor: pointer;
  border-radius: 8px;
  transition: background 0.15s;
  border: 1px solid var(--gray-200);
  background: var(--gray-50);
}
.checkbox-item:hover { background: var(--blue-50); border-color: var(--blue-300); }
.checkbox-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--blue-600); }
.checkbox-item span { font-size: 13px; font-weight: 600; color: var(--gray-700); }
.btn-outline { background: white; border: 1px solid var(--gray-300); color: var(--gray-700); }
.btn-outline:hover { background: var(--gray-50); border-color: var(--blue-400); }
.text-red-500 { color: #ef4444; }
.alert-info { background: var(--blue-50); border: 1px solid var(--blue-100); color: var(--blue-800); }

/* ── Snippet Code Box ── */
.snippet-code-box {
  background: var(--gray-900);
  border-radius: 12px;
  padding: 16px;
  position: relative;
  border: 1px solid rgba(255,255,255,0.1);
  margin-top: 8px;
}
.snippet-code-box pre {
  margin: 0;
  color: #a5d6ff;
  font-family: var(--font-mono);
  font-size: 11px;
  line-height: 1.6;
  overflow-x: auto;
}
.snippet-code-box .copy-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  opacity: 0.6;
  transition: opacity 0.2s;
}
.snippet-code-box .copy-btn:hover { opacity: 1; }

/* ── Preview Container ── */
.preview-container {
  background: #f1f5f9;
  border-radius: 16px;
  border: 1px solid var(--gray-200);
  overflow: hidden;
  height: 100%;
  min-height: 440px;
  display: flex;
  flex-direction: column;
}
.preview-container.dark { background: #0f172a; border-color: #1e293b; }

.preview-header {
  background: white;
  border-bottom: 1px solid var(--gray-200);
  padding: 12px 16px;
  display: flex;
  align-items: center;
}
.preview-container.dark .preview-header { background: #1e293b; border-color: #334155; color: rgba(255,255,255,0.5); }

.preview-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #e2e8f0;
  margin-right: 6px;
}
.preview-container.dark .preview-dot { background: #475569; }

.preview-content {
  flex: 1;
  padding: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.widget-placeholder {
  width: 100%;
  max-width: 350px;
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  border: 1px solid var(--gray-100);
}
.preview-container.dark .widget-placeholder { background: #1e293b; border-color: #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
.preview-container.dark .border-top { border-color: #334155; }
.preview-container.dark .border-bottom { border-color: #334155; }

.space-y-8 > * + * { margin-top: 8px; }
.space-y-6 > * + * { margin-top: 6px; }

/* ── Popin Overlay ── */
.popin-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(2px);
  padding: 24px;
}

.popin-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
  width: 100%;
  max-width: 480px;
  animation: popIn 0.25s ease-out forwards;
}

@keyframes popIn {
  0% { opacity: 0; transform: translateY(20px) scale(0.95); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

/* Badge sizing */
.badge-sm { font-size: 11px; padding: 2px 8px; }
</style>

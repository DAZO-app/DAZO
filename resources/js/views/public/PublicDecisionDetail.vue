<template>
  <div class="public-front dazo-public-detail">
    <div class="public-container-wide pb-48 pt-32" @touchstart="onTouchStart" @touchend="onTouchEnd">

      <!-- ── Barre de navigation sticky (sans phase) ── -->
      <div class="filters-actions-bar dazo-public-detail-navbar">
        
        <div class="popin-wrapper">
          <router-link to="/front" class="action-btn dazo-public-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="btn-label">Retour</span>
          </router-link>
        </div>

        <div class="popin-wrapper">
          <button class="action-btn" @click="openSharePopin">
            <i class="fa-solid fa-share-nodes"></i>
            <span class="btn-label">Partager <template v-if="decision?.share_count > 0">({{ decision.share_count }})</template></span>
          </button>
        </div>

        <div class="popin-wrapper">
          <button class="action-btn" @click="printDecision">
            <i class="fa-solid fa-print"></i>
            <span class="btn-label">Imprimer</span>
          </button>
        </div>

        <!-- Flèches navigation -->
        <div class="popin-wrapper dazo-public-nav-arrows" v-if="decision">
          <div class="flex items-center gap-8 w-full">
            <button
              class="action-btn"
              :disabled="!nav.prev"
              @click="navigate(nav.prev)"
            >
              <i class="fa-solid fa-chevron-left"></i>
              <span class="btn-label">Précédent</span>
            </button>
            <button
              class="action-btn"
              :disabled="!nav.next"
              @click="navigate(nav.next)"
            >
              <i class="fa-solid fa-chevron-right"></i>
              <span class="btn-label">Suivant</span>
            </button>
          </div>
        </div>
      </div>

      <!-- ── Chargement ── -->
      <div v-if="loading" class="text-center py-48 text-muted dazo-public-loading">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement de la décision...</p>
      </div>

      <!-- ── Introuvable ── -->
      <div v-else-if="!decision" class="empty-state dazo-public-empty">
        <div class="empty-icon"><i class="fa-solid fa-file-circle-xmark"></i></div>
        <h3>Décision introuvable</h3>
        <p class="text-muted">La décision demandée n'existe pas ou n'est pas publique.</p>
        <router-link to="/front" class="btn btn-primary mt-16">Retour à la liste</router-link>
      </div>

      <!-- ── Carte principale ── -->
      <div v-else class="decision-detail-card dazo-public-card-detail">

        <!-- En-tête de la tuile -->
        <div class="card-header-bar detail-card-header">
          <div class="header-left">
            <span
              v-if="decision.circle"
              class="meta-item circle-tag clickable dazo-public-card-circle"
              @click="goFilter('circle', String(decision.circle.id))"
              title="Filtrer par ce cercle"
            ><i class="fa-solid fa-circle-nodes"></i> {{ decision.circle.name }}</span>
          </div>

          <div class="header-right">
            <span
              class="status-badge clickable dazo-public-badge"
              :class="'status-' + decision.status"
              @click="goFilter('status', decision.status)"
              title="Filtrer par ce statut"
            >{{ getStatusLabel(decision.status) }}</span>
          </div>
        </div>

        <div class="detail-header-content dazo-public-detail-header">
          <!-- Titre & Hashtags -->
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
            <h1 class="article-title dazo-public-detail-title" style="flex: 1; min-width: 250px;">{{ decision.title }}</h1>
            
            <div class="category-tags dazo-public-detail-tags" v-if="decision.categories?.length > 0" style="margin-top: 6px;">
              <span
                v-for="cat in decision.categories"
                :key="cat.id"
                class="hashtag-link dazo-public-detail-tag"
                @click="goFilter('category', String(cat.id))"
                title="Filtrer par cette catégorie"
              >#{{ cat.name }}</span>
            </div>
          </div>

          <!-- Auteurs -->
          <div class="decision-authors-line inline mt-16 dazo-public-card-authors" v-if="getAuthor(decision) || getAnimator(decision)">
            Proposé par : 
            <span class="author-link" v-if="getAuthor(decision)" @click.prevent="goFilter('author', String(getAuthor(decision).id))">
              {{ getAuthor(decision).name }}
            </span>
            <span v-else class="author-link">N/A</span>, 
            animé par : 
            <span class="author-link" v-if="getAnimator(decision)" @click.prevent="goFilter('animator', String(getAnimator(decision).id))">
              {{ getAnimator(decision).name }}
            </span>
            <span v-else class="author-link">Non assigné</span>.
          </div>

          <!-- Métadonnées -->
          <div class="decision-metadata-row mt-16">
            <div class="decision-metadata-line" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
              <div class="popin-container" v-click-outside="() => activePopin = null">
                <span 
                  class="meta-item version-tag dazo-public-version-tag clickable"
                  :class="{ active: activePopin === 'versions' }"
                  @click.stop="togglePopin('versions')"
                  title="Historique des versions"
                >
                  v{{ decision.current_version?.version_number || 1 }}
                </span>

                <!-- Popin Historique des versions (Premium Design) -->
                <transition name="popin-wow">
                  <div v-if="activePopin === 'versions'" class="popin-center-overlay" @click="activePopin = null">
                    <div class="popin-versions-premium" @click.stop>
                      <div class="premium-popin-header">
                        <div class="header-icon-wrapper">
                          <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div class="header-text">
                          <h3>Historique</h3>
                          <span>Retrouvez toutes les révisions</span>
                        </div>
                        <button class="close-popin-btn" @click="activePopin = null">
                          <i class="fa-solid fa-xmark"></i>
                        </button>
                      </div>

                      <div class="premium-versions-list">
                        <div 
                          v-for="v in sortedVersions" 
                          :key="v.id" 
                          class="premium-version-item"
                          :class="{ current: v.id === decision.current_version?.id }"
                          @click="selectVersion(v)"
                        >
                          <div class="item-visual">
                            <div class="version-number-badge">v{{ v.version_number }}</div>
                          </div>
                          
                          <div class="item-content">
                            <div class="item-title">Révision #{{ v.version_number }}</div>
                            <div class="item-meta">{{ formatDate(v.published_at || v.created_at) }}</div>
                          </div>

                          <div class="item-status">
                            <i v-if="v.id === decision.current_version?.id" class="fa-solid fa-circle-check"></i>
                            <i v-else class="fa-solid fa-chevron-right"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </transition>
              </div>

              <!-- Bouton Rollback (Retour à la version actuelle) -->
              <span 
                v-if="isOldVersion" 
                class="meta-item rollback-tag clickable"
                @click="resetToLatest"
                title="Retourner à la version actuelle"
              >
                <i class="fa-solid fa-rotate-left"></i>
              </span>

              <span class="date dazo-public-date"><i class="fa-regular fa-calendar"></i> Créé le {{ formatDate(decision.created_at) }}</span>
              <span class="date dazo-public-date" v-if="decision.updated_at && decision.updated_at !== decision.created_at">
                <i class="fa-regular fa-calendar-check"></i> Modifié le {{ formatDate(decision.updated_at) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Corps du contenu -->
        <div class="detail-body dazo-public-detail-body">
          <div class="article-abstract dazo-public-abstract" v-if="decision.current_version?.abstract">
            {{ decision.current_version.abstract }}
          </div>
          <div class="article-html-content dazo-public-content" v-if="decision.current_version?.description" v-html="decision.current_version.description"></div>
          <div class="article-html-content dazo-public-content" v-else-if="decision.current_version?.content" v-html="decision.current_version.content"></div>

          <!-- Échanges publics (clarifications / réactions / objections / suggestions) -->
          <div v-if="feedbacksByType.length > 0" class="exchanges-section mt-48 dazo-public-exchanges">
            <div v-for="group in feedbacksByType" :key="group.type" class="exchange-group dazo-public-exchange-group">
              <h3 class="exchange-title dazo-public-exchange-title">
                <i :class="group.icon"></i> {{ group.label }}
                <span class="exchange-count">{{ group.items.length }}</span>
              </h3>
              <div class="exchange-list">
                <div v-for="fb in group.items" :key="fb.id" class="exchange-item dazo-public-exchange-item" :class="'exchange-' + group.type">
                  <div class="exchange-header">
                    <span class="exchange-author">
                      <i :class="getAuthorRoleIcon(fb)"></i> {{ fb.author?.name || 'Anonyme' }}
                    </span>
                    <span class="exchange-date">{{ formatDate(fb.created_at) }}</span>
                  </div>
                  <div class="exchange-content">{{ fb.content }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pièces jointes -->
          <div class="attachments-section mt-48 dazo-public-attachments" v-if="decision.current_version?.attachments?.length > 0">
            <h3 class="attachments-title dazo-public-attachments-title">
              <i class="fa-solid fa-paperclip"></i> Pièces jointes ({{ decision.current_version.attachments.length }})
            </h3>
            <div class="attachments-list">
              <div
                v-for="(att, idx) in decision.current_version.attachments"
                :key="att.id"
                class="attachment-card dazo-public-attachment-card"
                @click="openAttachment(idx)"
              >
                <div class="attachment-icon">
                  <i class="fa-regular fa-file-pdf" v-if="att.mime_type?.includes('pdf')"></i>
                  <i class="fa-regular fa-image" v-else-if="att.mime_type?.includes('image')"></i>
                  <i class="fa-regular fa-file-word" v-else-if="att.mime_type?.includes('word')"></i>
                  <i class="fa-regular fa-file" v-else></i>
                </div>
                <div class="attachment-info">
                  <div class="attachment-name">{{ att.filename }}</div>
                  <div class="attachment-size">{{ formatSize(att.size_bytes) }}</div>
                </div>
                <div class="attachment-action"><i class="fa-solid fa-expand"></i></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pied de tuile : navigation précédente / suivante -->
        <div class="card-nav-footer dazo-public-detail-footer">
          <span
            class="nav-footer-link dazo-public-footer-nav"
            :class="{ disabled: !nav.prev }"
            @click="nav.prev && navigate(nav.prev)"
          ><i class="fa-solid fa-chevron-left"></i> Précédente</span>

          <span class="nav-footer-counter dazo-public-footer-counter" v-if="nav.index >= 0">{{ nav.index + 1 }} / {{ nav.total }}</span>
          <span v-else></span>

          <span
            class="nav-footer-link dazo-public-footer-nav"
            :class="{ disabled: !nav.next }"
            @click="nav.next && navigate(nav.next)"
          >Suivante <i class="fa-solid fa-chevron-right"></i></span>
        </div>

      </div>
    </div>

    <!-- Share Popin -->
    <SharePopin
      v-if="showSharePopin"
      :url="shareUrl"
      :title="decision?.title || ''"
      :description="decision?.current_version?.content?.substring(0, 150) + '...'"
      :share-count="decision?.share_count || 0"
      @close="showSharePopin = false"
      @shared="handleShared"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import PhotoSwipe from 'photoswipe';
import 'photoswipe/dist/photoswipe.css';
import { usePublicFrontStore } from '../../stores/publicFront';
import SharePopin from '../../components/public/SharePopin.vue';

// Directive v-click-outside
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value();
      }
    };
    document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent);
  },
};

const route   = useRoute();
const router  = useRouter();
const store   = usePublicFrontStore();

const decision = ref(null);
const loading  = ref(true);
const activePopin = ref(null);
const originalVersionId = ref(null);
const showSharePopin = ref(false);

const shareUrl = computed(() => window.location.href);

// ── Navigation ──────────────────────────────────────────
const nav = computed(() => {
  if (!decision.value || store.decisions.length === 0) return { prev: null, next: null, index: -1, total: 0 };
  return store.getNeighbours(decision.value.id);
});

const navigate = (id) => { if (id) router.push({ name: 'PublicDecision', params: { id } }); };

// ── Filter + redirect to listing ─────────────────────────
const goFilter = (key, value) => {
  store.filters[key] = value;
  store.fetchDecisions(1);
  router.push({ name: 'PublicFront' });
};

// ── Actions ──────────────────────────────────────────────
const printDecision = () => {
  window.print();
};

const openSharePopin = () => {
  showSharePopin.value = true;
};

const handleShared = async () => {
  if (!decision.value) return;
  try {
    const { data } = await axios.post(`/api/v1/front/decisions/${decision.value.id}/share`);
    decision.value.share_count = data.share_count;
  } catch (e) {
    console.error('Error incrementing share count', e);
  }
};

const togglePopin = (name) => {
  activePopin.value = activePopin.value === name ? null : name;
};

// ── Versions ──────────────────────────────────────────────
const sortedVersions = computed(() => {
  if (!decision.value?.versions) return [];
  return [...decision.value.versions].sort((a, b) => b.version_number - a.version_number);
});

const isOldVersion = computed(() => {
  if (!decision.value || !originalVersionId.value) return false;
  return decision.value.current_version?.id !== originalVersionId.value;
});

const selectVersion = (v) => {
  if (!decision.value) return;
  
  // Ensure we find the full version object from the versions list (which now has attachments/feedbacks)
  const fullVersion = decision.value.versions.find(ver => ver.id === v.id) || v;
  
  decision.value.current_version = fullVersion;
  activePopin.value = null;
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const resetToLatest = () => {
  if (!decision.value || !originalVersionId.value) return;
  const latest = decision.value.versions.find(v => v.id === originalVersionId.value);
  if (latest) selectVersion(latest);
};


// ── Feedbacks regroupés par type ──────────────────────────
const FEEDBACK_META = {
  clarification: { label: 'Clarifications',  icon: 'fa-solid fa-circle-question',  type: 'clarification' },
  reaction:      { label: 'Réactions',        icon: 'fa-solid fa-comment-dots',      type: 'reaction' },
  objection:     { label: 'Objections',       icon: 'fa-solid fa-hand',              type: 'objection' },
  suggestion:    { label: 'Suggestions',      icon: 'fa-solid fa-lightbulb',         type: 'suggestion' },
};

const feedbacksByType = computed(() => {
  const feedbacks = decision.value?.current_version?.feedbacks ?? [];
  const groups = {};
  for (const fb of feedbacks) {
    const type = typeof fb.type === 'object' ? fb.type.value : fb.type;
    if (!groups[type]) groups[type] = [];
    groups[type].push(fb);
  }
  return Object.entries(groups)
    .filter(([, items]) => items.length > 0)
    .map(([type, items]) => ({ ...FEEDBACK_META[type] ?? { label: type, icon: 'fa-solid fa-comment', type }, items }));
});

// ── Swipe (mobile) ──────────────────────────────────────
let touchStartX = 0;
const onTouchStart = (e) => { touchStartX = e.changedTouches[0].screenX; };
const onTouchEnd   = (e) => {
  const diff = touchStartX - e.changedTouches[0].screenX;
  if (Math.abs(diff) < 60) return;
  if (diff > 0 && nav.value.next) navigate(nav.value.next);
  else if (diff < 0 && nav.value.prev) navigate(nav.value.prev);
};

// ── Fetch ────────────────────────────────────────────────
const getAuthor = (d) => {
  if (!d) return null;
  return d.author?.user || null;
};

const getAnimator = (d) => {
  if (!d) return null;
  return d.current_animator?.user || null;
};

const fetchDecision = async () => {
  loading.value = true;
  decision.value = null;
  try {
    const { data } = await axios.get(`/api/v1/front/decisions/${route.params.id}`);
    decision.value = data.decision.data || data.decision;
    originalVersionId.value = decision.value.current_version?.id;
  } catch (e) {
    console.error('decision detail fetch error', e);
  } finally {
    loading.value = false;
  }
};

watch(() => route.params.id, () => { fetchDecision(); window.scrollTo(0, 0); });

let lightbox = null;

onMounted(async () => {
  if (!store.metaLoaded) await store.fetchMeta();
  if (store.decisions.length === 0) await store.fetchDecisions();
  fetchDecision();

  lightbox = new PhotoSwipeLightbox({
    pswpModule: PhotoSwipe,
    bgOpacity: 0.95,
    showHideAnimationType: 'zoom',
  });
  lightbox.init();
});

const openAttachment = (index) => {
  if (!lightbox || !decision.value?.current_version?.attachments) return;

  const items = decision.value.current_version.attachments.map(a => {
    const isImg = a.mime_type?.includes('image');
    const url = `/api/v1/attachments/${a.id}/download`;
    
    if (isImg) {
      return { src: url, w: 1200, h: 800, alt: a.filename };
    } else if (a.mime_type?.includes('pdf')) {
      return {
        html: `<div class="pswp-pdf-slide"><iframe src="${url}#view=FitH" style="width:100%;height:100%;border:none;"></iframe></div>`
      };
    } else {
      return {
        html: `<div class="pswp-doc-slide">
          <i class="fa-solid fa-file-lines" style="font-size:60px;margin-bottom:20px;"></i>
          <div style="font-size:18px;margin-bottom:24px;">${a.filename}</div>
          <a href="${url}" target="_blank" class="btn btn-primary" onclick="event.stopPropagation()">Ouvrir le document</a>
        </div>`
      };
    }
  });

  lightbox.options.dataSource = items;
  lightbox.loadAndOpen(index);
};

// ── Helpers ──────────────────────────────────────────────
const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' }) : '';
const formatSize = (bytes) => {
  if (!bytes) return '0 B';
  const k = 1024, sizes = ['B','KB','MB','GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / k ** i).toFixed(1)) + ' ' + sizes[i];
};
const getStatusLabel = (s) => ({ draft:'Brouillon', clarification:'Clarification', reaction:'Réaction', objection:'Objection', adopted:'Adoptée', abandoned:'Abandonnée' }[s] || s);

// Icones de rôle identiques au backoffice
const ROLE_ICONS = {
  author:      'fa-solid fa-bullhorn',       // Porteur
  animator:    'fa-solid fa-user-tie',        // Animateur
  participant: 'fa-solid fa-user-group',      // Participant
  observer:    'fa-solid fa-eye',
};
const getAuthorRoleIcon = (fb) => {
  const role = fb.author?.role || fb.author_role;
  return ROLE_ICONS[role] || 'fa-solid fa-user-group';
};
</script>

<style scoped>
/* ── Nav bar ── */
.filters-actions-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  padding: 12px 20px;
  border-radius: 16px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
  position: sticky;
  top: 0;
  z-index: 100;
  margin-bottom: 32px;
  flex-wrap: nowrap;
  overflow-x: auto;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none;  /* IE and Edge */
}
.filters-actions-bar::-webkit-scrollbar {
  display: none; /* Chrome, Safari and Opera */
}

@supports (backdrop-filter: blur(10px)) {
  .filters-actions-bar {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
  }
}

.popin-wrapper {
  position: relative;
  flex: 1;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 10px 18px;
  border: 1px solid var(--gray-200);
  background: white;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-700);
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  width: 100%;
  text-decoration: none;
  box-sizing: border-box;
  white-space: nowrap;
}

.action-btn:hover {
  background: var(--gray-50);
  border-color: var(--gray-300);
}

.action-btn .btn-label {
  display: inline-block;
  margin-left: 2px;
}

/* ── Card ── */
.decision-detail-card {
  background: white; border-radius: 16px;
  border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); overflow: hidden;
}

/* ── Header ── */
.card-header-bar {
  background: linear-gradient(135deg, var(--blue-700) 0%, var(--blue-900) 100%);
  color: white;
  padding: 16px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  position: relative;
  overflow: hidden;
}

.card-header-bar::after {
  content: ""; position: absolute; top: -50%; right: -10%; width: 250px; height: 250px;
  background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%; pointer-events: none;
}

.header-left { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; position: relative; z-index: 1; }
.header-right { flex-shrink: 0; position: relative; z-index: 1; }

.card-header-bar .circle-tag {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
}

/* ── Authors ── */
.decision-authors-line {
  font-size: 13px;
  color: var(--gray-600);
}
.author-link {
  font-weight: 600;
  color: var(--blue-600);
  cursor: pointer;
  text-decoration: underline dotted;
  transition: color 0.2s;
}
.author-link:hover {
  color: var(--blue-800);
}

.detail-header-content {
  padding: 24px 40px 16px;
}

.date {
  font-size: 13px; color: var(--gray-500); font-weight: 500;
  display: inline-flex; align-items: center; gap: 6px;
}
.version-tag {
  border: 1px solid var(--gray-200);
  color: var(--gray-500);
  background: white;
  font-family: var(--font-mono);
}

.article-title { font-size: 30px; font-weight: 800; letter-spacing: -0.03em; color: var(--gray-900); line-height: 1.2; margin: 0; }

.category-tags { display: flex; gap: 8px; flex-wrap: wrap; }
.hashtag-link {
  font-size: 12px; font-weight: 600; color: var(--blue-600);
  cursor: pointer; transition: all 0.2s; padding: 2px 6px; border-radius: 4px;
}
.hashtag-link:hover { color: var(--blue-800); background: var(--blue-50); }

.meta-item { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; }
.circle-tag { background: var(--gray-100); color: var(--gray-600); }
.version-tag {
  border: 1px solid var(--gray-200);
  color: var(--gray-500);
  background: white;
  font-family: var(--font-mono);
}
.version-tag.active {
  background: var(--blue-50);
  border-color: var(--blue-300);
  color: var(--blue-700);
}

.rollback-tag {
  background: var(--amber-50);
  color: var(--amber-700);
  border: 1px solid var(--amber-200);
}
.rollback-tag:hover {
  background: var(--amber-100);
}

.clickable { cursor: pointer; transition: all 0.2s; }
.circle-tag.clickable:hover { background: var(--blue-50); color: var(--blue-700); }

/* ── Popin WOW Effects ── */
.popin-container {
  position: relative;
  display: inline-flex;
}

.popin-versions {
  position: absolute;
  top: calc(100% + 12px);
  left: 0;
  width: 260px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.12);
  border: 1px solid var(--gray-200);
  z-index: 1000;
  padding: 8px 0;
  transform-origin: top left;
}

.popin-center-overlay {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(4px);
}

.popin-center-overlay {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(15, 23, 42, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2500;
  backdrop-filter: blur(8px);
}

.popin-versions-premium {
  width: 400px;
  background: white;
  border-radius: 24px;
  box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.4);
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.8);
}

.premium-popin-header {
  padding: 24px;
  background: linear-gradient(to right, var(--blue-600), var(--blue-800));
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  color: white;
}

.header-icon-wrapper {
  width: 44px;
  height: 44px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.header-text h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.header-text span {
  font-size: 12px;
  opacity: 0.8;
  font-weight: 500;
}

.close-popin-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  color: white;
  opacity: 0.5;
  cursor: pointer;
  padding: 8px;
  transition: all 0.2s;
}

.close-popin-btn:hover {
  opacity: 1;
  transform: rotate(90deg);
}

.premium-versions-list {
  padding: 12px;
  max-height: 450px;
  overflow-y: auto;
  background: #f8fafc;
}

.premium-version-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  background: white;
  margin-bottom: 8px;
  border: 1px solid transparent;
}

.premium-version-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  border-color: var(--blue-200);
}

.premium-version-item.current {
  background: var(--blue-50);
  border-color: var(--blue-200);
}

.item-visual {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.version-number-badge {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-mono);
  font-weight: 800;
  font-size: 12px;
  color: var(--gray-600);
  transition: all 0.2s;
}

.premium-version-item.current .version-number-badge {
  background: var(--blue-600);
  color: white;
}

.item-content {
  flex: 1;
}

.item-title {
  font-weight: 700;
  color: var(--gray-800);
  font-size: 14px;
}

.item-meta {
  font-size: 11px;
  color: var(--gray-500);
  margin-top: 2px;
}

.item-status {
  color: var(--gray-300);
  font-size: 16px;
  transition: all 0.2s;
}

.premium-version-item.current .item-status {
  color: var(--blue-600);
}

.premium-version-item:hover .item-status {
  transform: translateX(3px);
  color: var(--blue-400);
}

/* WOW Transition */
.popin-wow-enter-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.popin-wow-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.popin-wow-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(-10px);
}
.popin-wow-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* ── Status badges ── */
.status-badge {
  display: inline-block; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.05em; padding: 5px 12px; border-radius: 20px;
}
.status-badge.clickable:hover { opacity: 0.75; }
.status-draft        { background: var(--gray-50);   color: var(--gray-600);   border: 1px solid var(--gray-200); }
.status-clarification{ background: var(--blue-50);   color: var(--blue-800);   border: 1px solid var(--blue-200); }
.status-reaction     { background: var(--blue-50);   color: var(--blue-800);   border: 1px solid var(--blue-200); }
.status-objection    { background: var(--amber-50);  color: var(--amber-600);  border: 1px solid var(--amber-100); }
.status-revision     { background: var(--purple-50); color: var(--purple-600); border: 1px solid var(--purple-100); }
.status-adopted      { background: var(--teal-50);   color: var(--teal-600);   border: 1px solid var(--teal-100); }
.status-abandoned    { background: var(--red-50);    color: var(--red-600);    border: 1px solid var(--red-100); }

/* ── Body ── */
.detail-body { padding: 36px 40px 48px; }
.article-abstract {
  font-size: 18px; line-height: 1.7; color: var(--gray-600); font-weight: 500;
  margin-bottom: 32px; padding-left: 20px; border-left: 4px solid var(--blue-400);
}
.article-html-content { font-size: 16px; line-height: 1.8; color: var(--gray-800); }
.article-html-content :deep(p) { margin-bottom: 1.5em; }
.article-html-content :deep(h2) { font-size: 24px; font-weight: 800; margin: 2em 0 1em; color: var(--gray-900); }
.article-html-content :deep(h3) { font-size: 20px; font-weight: 700; margin: 1.5em 0 1em; }
.article-html-content :deep(ul), .article-html-content :deep(ol) { margin-bottom: 1.5em; padding-left: 1.5em; }
.article-html-content :deep(li) { margin-bottom: 0.5em; }
.article-html-content :deep(blockquote) { border-left: 3px solid var(--gray-300); padding-left: 1em; color: var(--gray-600); font-style: italic; margin-bottom: 1.5em; }

/* ── Exchanges ── */
.exchanges-section { border-top: 1px solid var(--gray-200); padding-top: 36px; }
.exchange-group { margin-bottom: 32px; }
.exchange-title {
  font-size: 16px; font-weight: 700; color: var(--gray-800);
  margin-bottom: 16px; display: flex; align-items: center; gap: 10px;
}
.exchange-count {
  background: var(--gray-100); color: var(--gray-500);
  font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 10px;
}
.exchange-list { display: flex; flex-direction: column; gap: 12px; }
.exchange-item {
  padding: 16px 20px; border-radius: 12px; border: 1px solid var(--gray-200);
}
.exchange-clarification { border-left: 3px solid var(--blue-400); background: var(--blue-50); }
.exchange-reaction      { border-left: 3px solid #f59e0b;         background: var(--amber-50); }
.exchange-objection     { border-left: 3px solid var(--red-600);  background: var(--red-50); }
.exchange-suggestion    { border-left: 3px solid var(--teal-500); background: var(--teal-50); }
.exchange-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.exchange-author { font-size: 13px; font-weight: 600; color: var(--gray-700); display: flex; align-items: center; gap: 6px; }
.exchange-date   { font-size: 12px; color: var(--gray-400); }
.exchange-content { font-size: 14px; color: var(--gray-700); line-height: 1.6; }

/* ── Attachments ── */
.attachments-section { border-top: 1px solid var(--gray-200); padding-top: 32px; }
.attachments-title { font-size: 17px; font-weight: 700; color: var(--gray-800); margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
.attachments-list { display: flex; flex-direction: column; gap: 12px; }
.attachment-card { display: flex; align-items: center; padding: 14px 16px; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.2s; }
.attachment-card:hover { border-color: var(--blue-300); background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.attachment-icon { width: 38px; height: 38px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--blue-500); margin-right: 14px; flex-shrink: 0; border: 1px solid var(--gray-200); }
.attachment-info { flex: 1; min-width: 0; }
.attachment-name { font-size: 14px; font-weight: 600; color: var(--gray-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
.attachment-size { font-size: 12px; color: var(--gray-500); }
.attachment-action { width: 30px; height: 30px; border-radius: 50%; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center; opacity: 0; transition: all 0.2s; flex-shrink: 0; }
.attachment-card:hover .attachment-action { opacity: 1; }

/* ── Card nav footer ── */
.card-nav-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 40px;
  border-top: 1px solid var(--gray-100);
  background: var(--gray-50);
}
.nav-footer-link {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 600; color: var(--gray-500);
  cursor: pointer; transition: all 0.2s; user-select: none;
}
.nav-footer-link:hover:not(.disabled) { color: var(--blue-600); }
.nav-footer-link.disabled { opacity: 0.35; cursor: not-allowed; }
.nav-footer-counter { font-size: 13px; font-weight: 600; color: var(--gray-400); }

/* ── Swipe indicator ── */
.swipe-indicator { font-size: 12px; color: var(--gray-400); font-weight: 600; display: flex; align-items: center; gap: 6px; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 64px 20px; background: white; border-radius: 16px; border: 1px dashed var(--gray-300); }
.empty-icon { font-size: 48px; color: var(--gray-300); margin-bottom: 16px; }
.empty-state h3 { font-size: 18px; font-weight: 700; color: var(--gray-800); margin-bottom: 8px; }

/* ── Responsive ── */
@media (max-width: 768px) {
  .filters-actions-bar {
    gap: 8px;
    padding: 10px;
    top: 0;
    border-radius: 0 0 16px 16px;
    margin-bottom: 24px;
  }
  
  .popin-wrapper {
    flex: 1;
  }
  
  .action-btn {
    padding: 10px 5px;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
    flex: 1;
    height: auto;
  }
  
  .action-btn .btn-label {
    font-size: 10px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }

  .dazo-public-nav-arrows {
    flex: 2; /* Donne plus de place aux flèches */
  }

  .dazo-public-nav-arrows .flex {
    gap: 8px;
  }

  .desktop-only { display: none !important; }
  .mobile-only  { display: flex !important; }

  .detail-header-content { padding: 20px; }
  .detail-body { padding: 20px 20px 36px; }
  .card-nav-footer { padding: 12px 20px; }
  .article-title { font-size: 22px; }
  .article-abstract { font-size: 15px; }
  .article-html-content { font-size: 15px; }
}

@media (max-width: 480px) {
  .action-btn .btn-label {
    display: none;
  }
  .action-btn {
    padding: 12px 5px;
  }
}
</style>

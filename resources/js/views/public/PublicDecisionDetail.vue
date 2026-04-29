<template>
  <div class="public-front">
    <div class="public-container-wide pb-48 pt-32" @touchstart="onTouchStart" @touchend="onTouchEnd">

      <!-- ── Barre de navigation sticky (sans phase) ── -->
      <div class="filters-bar nav-bar">
        <router-link to="/public" class="btn btn-secondary btn-sm back-btn">
          <i class="fa-solid fa-arrow-left"></i>
          <span class="back-label">Retour</span>
        </router-link>

        <div class="nav-center"></div>

        <!-- Flèches navigation desktop -->
        <div class="nav-arrows desktop-only" v-if="decision">
          <button
            class="btn btn-secondary btn-sm nav-arrow"
            :disabled="!nav.prev"
            @click="navigate(nav.prev)"
            title="Décision précédente"
          ><i class="fa-solid fa-chevron-left"></i></button>
          <span class="nav-counter" v-if="nav.index >= 0">{{ nav.index + 1 }}&nbsp;/&nbsp;{{ nav.total }}</span>
          <button
            class="btn btn-secondary btn-sm nav-arrow"
            :disabled="!nav.next"
            @click="navigate(nav.next)"
            title="Décision suivante"
          ><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>

      <!-- ── Chargement ── -->
      <div v-if="loading" class="text-center py-48 text-muted">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Chargement de la décision...</p>
      </div>

      <!-- ── Introuvable ── -->
      <div v-else-if="!decision" class="empty-state">
        <div class="empty-icon"><i class="fa-solid fa-file-circle-xmark"></i></div>
        <h3>Décision introuvable</h3>
        <p class="text-muted">La décision demandée n'existe pas ou n'est pas publique.</p>
        <router-link to="/public" class="btn btn-primary mt-16">Retour à la liste</router-link>
      </div>

      <!-- ── Carte principale ── -->
      <div v-else class="decision-detail-card">

        <!-- En-tête de la tuile -->
        <div class="detail-header">
          <!-- Ligne : cercle + date + version  |  phase (haut droite) -->
          <div class="detail-meta-row">
            <div class="detail-meta-left">
              <span
                v-if="decision.circle"
                class="meta-item circle-tag clickable"
                @click="goFilter('circle', String(decision.circle.id))"
                title="Filtrer par ce cercle"
              ><i class="fa-solid fa-circle-nodes"></i> {{ decision.circle.name }}</span>
              <span class="meta-item version-tag">v{{ decision.current_version?.version_number || 1 }}</span>
              <span class="date"><i class="fa-regular fa-calendar"></i> {{ formatDate(decision.created_at) }}</span>
            </div>
            <span
              class="status-badge clickable"
              :class="'status-' + decision.status"
              @click="goFilter('status', decision.status)"
              title="Filtrer par ce statut"
            >{{ getStatusLabel(decision.status) }}</span>
          </div>

          <!-- Titre -->
          <h1 class="article-title">{{ decision.title }}</h1>

          <!-- Catégories en hashtag -->
          <div class="category-tags mt-16" v-if="decision.categories?.length > 0">
            <span
              v-for="cat in decision.categories"
              :key="cat.id"
              class="hashtag-link"
              @click="goFilter('category', String(cat.id))"
              title="Filtrer par cette catégorie"
            >#{{ cat.name }}</span>
          </div>
        </div>

        <!-- Corps du contenu -->
        <div class="detail-body">
          <div class="article-abstract" v-if="decision.current_version?.abstract">
            {{ decision.current_version.abstract }}
          </div>
          <div class="article-html-content" v-if="decision.current_version?.description" v-html="decision.current_version.description"></div>
          <div class="article-html-content" v-else-if="decision.current_version?.content" v-html="decision.current_version.content"></div>

          <!-- Échanges publics (clarifications / réactions / objections / suggestions) -->
          <div v-if="feedbacksByType.length > 0" class="exchanges-section mt-48">
            <div v-for="group in feedbacksByType" :key="group.type" class="exchange-group">
              <h3 class="exchange-title">
                <i :class="group.icon"></i> {{ group.label }}
                <span class="exchange-count">{{ group.items.length }}</span>
              </h3>
              <div class="exchange-list">
                <div v-for="fb in group.items" :key="fb.id" class="exchange-item" :class="'exchange-' + group.type">
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
          <div class="attachments-section mt-48" v-if="decision.current_version?.attachments?.length > 0">
            <h3 class="attachments-title">
              <i class="fa-solid fa-paperclip"></i> Pièces jointes ({{ decision.current_version.attachments.length }})
            </h3>
            <div class="attachments-list">
              <a
                v-for="att in decision.current_version.attachments"
                :key="att.id"
                :href="'/api/v1/attachments/' + att.id + '/download'"
                target="_blank"
                class="attachment-card"
              >
                <div class="attachment-icon">
                  <i class="fa-regular fa-file-pdf" v-if="att.file_type?.includes('pdf')"></i>
                  <i class="fa-regular fa-image" v-else-if="att.file_type?.includes('image')"></i>
                  <i class="fa-regular fa-file-word" v-else-if="att.file_type?.includes('word')"></i>
                  <i class="fa-regular fa-file" v-else></i>
                </div>
                <div class="attachment-info">
                  <div class="attachment-name">{{ att.original_name }}</div>
                  <div class="attachment-size">{{ formatSize(att.file_size) }}</div>
                </div>
                <div class="attachment-action"><i class="fa-solid fa-download"></i></div>
              </a>
            </div>
          </div>
        </div>

        <!-- Pied de tuile : navigation précédente / suivante -->
        <div class="card-nav-footer">
          <span
            class="nav-footer-link"
            :class="{ disabled: !nav.prev }"
            @click="nav.prev && navigate(nav.prev)"
          ><i class="fa-solid fa-chevron-left"></i> Précédente</span>

          <span class="nav-footer-counter" v-if="nav.index >= 0">{{ nav.index + 1 }} / {{ nav.total }}</span>
          <span v-else></span>

          <span
            class="nav-footer-link"
            :class="{ disabled: !nav.next }"
            @click="nav.next && navigate(nav.next)"
          >Suivante <i class="fa-solid fa-chevron-right"></i></span>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { usePublicFrontStore } from '../../stores/publicFront';

const route   = useRoute();
const router  = useRouter();
const store   = usePublicFrontStore();

const decision = ref(null);
const loading  = ref(true);

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
const fetchDecision = async () => {
  loading.value = true;
  decision.value = null;
  try {
    const { data } = await axios.get(`/api/v1/front/decisions/${route.params.id}`);
    decision.value = data.decision;
  } catch (e) {
    console.error('decision detail fetch error', e);
  } finally {
    loading.value = false;
  }
};

watch(() => route.params.id, () => { fetchDecision(); window.scrollTo(0, 0); });

onMounted(async () => {
  if (!store.metaLoaded) await store.fetchMeta();
  if (store.decisions.length === 0) await store.fetchDecisions();
  fetchDecision();
});

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
.filters-bar {
  display: flex; gap: 16px; margin-bottom: 32px;
  background: white; padding: 12px 20px; border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--gray-200);
  position: sticky; top: 106px; z-index: 90; align-items: center;
}
.nav-bar { justify-content: space-between; flex-wrap: nowrap; }
.back-btn { display: inline-flex; align-items: center; gap: 8px; font-weight: 600; font-size: 13px; }
.nav-center { flex: 1; overflow: hidden; padding: 0 16px; }
.nav-title { font-size: 13px; font-weight: 600; color: var(--gray-600); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }
.nav-arrows { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.nav-arrow { width: 34px; height: 34px; padding: 0 !important; display: flex; align-items: center; justify-content: center; }
.nav-counter { font-size: 13px; font-weight: 600; color: var(--gray-500); min-width: 44px; text-align: center; }

.desktop-only { display: flex; }
.mobile-only  { display: none; }

/* ── Card ── */
.decision-detail-card {
  background: white; border-radius: 16px;
  border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); overflow: hidden;
}

/* ── Header ── */
.detail-header {
  padding: 32px 40px 28px;
  border-bottom: 1px solid var(--gray-100);
}

.detail-meta-row {
  display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px;
}
.detail-meta-left { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }

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
.clickable { cursor: pointer; transition: all 0.2s; }
.circle-tag.clickable:hover { background: var(--blue-50); color: var(--blue-700); }

/* ── Status badges ── */
.status-badge {
  display: inline-block; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.05em; padding: 5px 12px; border-radius: 20px;
}
.status-badge.clickable:hover { opacity: 0.75; }
.status-draft        { background: var(--gray-100);  color: var(--gray-600); }
.status-clarification{ background: var(--blue-100);  color: var(--blue-700); }
.status-reaction     { background: var(--amber-100); color: var(--amber-700); }
.status-objection    { background: var(--red-100);   color: var(--red-700); }
.status-adopted      { background: var(--teal-100);  color: var(--teal-700); }
.status-abandoned    { background: var(--gray-200);  color: var(--gray-700); }

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
  .desktop-only { display: none !important; }
  .mobile-only  { display: flex !important; }
  .nav-bar { display: none !important; }
  .detail-header { padding: 20px; }
  .detail-body { padding: 20px 20px 36px; }
  .card-nav-footer { padding: 12px 20px; }
  .article-title { font-size: 22px; }
  .article-abstract { font-size: 15px; }
  .article-html-content { font-size: 15px; }
  .nav-bar { gap: 8px; padding: 10px 12px; }
  .back-label { display: none; }
}
</style>

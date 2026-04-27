<template>
  <!-- Backdrop fixe – pas de Teleport pour éviter le conflit avec le CSS scoped -->
  <div class="dpm-backdrop" @click.self="emit('close')">
    <div class="dpm-modal" role="dialog" aria-modal="true" aria-labelledby="dpm-title">

      <!-- En-tête -->
      <div class="dpm-header">
        <div class="dpm-header-icon">
          <i class="fa-solid fa-file-pdf"></i>
        </div>
        <div>
          <h2 id="dpm-title" class="dpm-title">Exporter en PDF</h2>
          <p class="dpm-subtitle">Choisissez les sections à inclure dans le document</p>
        </div>
        <button class="dpm-close" @click="emit('close')" title="Fermer">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Corps : options -->
      <div class="dpm-body">
        <div v-if="loading" class="dpm-loading">
          <i class="fa-solid fa-spinner fa-spin"></i> Chargement des données…
        </div>
        <template v-else>
          <p class="dpm-section-label">Contenu à inclure</p>
          <div class="dpm-options">
            <label class="dpm-option" :class="{ active: options.versions }">
              <div class="dpm-option-check">
                <input type="checkbox" v-model="options.versions">
              </div>
              <div class="dpm-option-body">
                <div class="dpm-option-title"><i class="fa-solid fa-layer-group mr-6 text-indigo-500"></i> Toutes les versions</div>
                <div class="dpm-option-desc">Historique des propositions successives</div>
              </div>
              <div class="dpm-option-count">{{ allVersions.length }}</div>
            </label>

            <div v-if="!options.versions" class="dpm-option" style="cursor: default; margin-top: -4px; margin-bottom: 8px; background: #f8fafc; border-color: #e2e8f0; padding-top: 16px; padding-bottom: 16px;">
              <div class="dpm-option-body" style="width: 100%;">
                <div class="dpm-option-title" style="margin-bottom: 10px; font-size: 13px;"><i class="fa-solid fa-clock-rotate-left mr-6 text-indigo-500"></i> Sélectionner une version spécifique</div>
                <select v-model="selectedVersionId" class="dpm-select">
                  <option v-for="v in allVersions" :key="v.id" :value="v.id">Version {{ v.version_number }} ({{ formatDate(v.created_at) }})</option>
                </select>
              </div>
            </div>

            <label class="dpm-option" :class="{ active: options.clarifications }">
              <div class="dpm-option-check">
                <input type="checkbox" v-model="options.clarifications">
              </div>
              <div class="dpm-option-body">
                <div class="dpm-option-title"><i class="fa-solid fa-comments mr-6 text-amber-500"></i> Clarifications</div>
                <div class="dpm-option-desc">Questions et demandes de précision</div>
              </div>
              <div class="dpm-option-count">{{ clarifications.length }}</div>
            </label>

            <label class="dpm-option" :class="{ active: options.reactions }">
              <div class="dpm-option-check">
                <input type="checkbox" v-model="options.reactions">
              </div>
              <div class="dpm-option-body">
                <div class="dpm-option-title"><i class="fa-solid fa-face-smile mr-6 text-blue-500"></i> Réactions</div>
                <div class="dpm-option-desc">Avis et retours des participants</div>
              </div>
              <div class="dpm-option-count">{{ reactions.length }}</div>
            </label>

            <label class="dpm-option" :class="{ active: options.objections }">
              <div class="dpm-option-check">
                <input type="checkbox" v-model="options.objections">
              </div>
              <div class="dpm-option-body">
                <div class="dpm-option-title"><i class="fa-solid fa-hand mr-6 text-red-500"></i> Objections</div>
                <div class="dpm-option-desc">Objections et suggestions émises</div>
              </div>
              <div class="dpm-option-count">{{ objections.length }}</div>
            </label>
          </div>
        </template>
      </div>

      <!-- Pied -->
      <div class="dpm-footer">
        <button class="btn btn-outline" @click="emit('close')">Annuler</button>
        <button class="btn btn-primary" :disabled="loading || nothingSelected" @click="doPrint">
          <i class="fa-solid fa-print mr-8"></i> Générer le PDF
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  decision: { type: Object, required: true },
  currentVersion: { type: Object, default: null },
});
const emit = defineEmits(['close']);

const loading = ref(true);
const allVersions = ref([]);
const feedbacks = ref([]);           // version courante (pour les compteurs)
const feedbacksByVersion = ref({}); // map: version_id -> feedbacks[]

const options = reactive({
  versions: true,
  clarifications: true,
  reactions: true,
  objections: true,
});

const selectedVersionId = ref(null);

const activeFeedbacks = computed(() => {
  if (options.versions) {
    return Object.values(feedbacksByVersion.value).flat();
  } else if (selectedVersionId.value) {
    return feedbacksByVersion.value[selectedVersionId.value] || [];
  }
  return [];
});

const clarifications = computed(() => {
  return activeFeedbacks.value.filter(f => (f.type?.value || f.type) === 'clarification');
});
const reactions = computed(() => {
  return activeFeedbacks.value.filter(f => (f.type?.value || f.type) === 'reaction');
});
const objections = computed(() => {
  return activeFeedbacks.value.filter(f => ['objection', 'suggestion'].includes(f.type?.value || f.type));
});

const nothingSelected = computed(() => !Object.values(options).some(Boolean));

const getVal = (t) => (typeof t === 'object' && t !== null) ? t.value : t;

const statusLabels = {
  draft: 'Brouillon', clarification: 'Clarification', reaction: 'Réaction',
  objection: 'Objection', revision: 'Révision', adopted: 'Adoptée',
  adopted_override: 'Adoptée (forçage)', abandoned: 'Abandonnée',
};

const feedbackTypeLabel = (type) => {
  const v = getVal(type);
  return { clarification: 'Clarification', reaction: 'Réaction', objection: 'Objection', suggestion: 'Suggestion' }[v] || v;
};

const feedbackStatusLabel = (status) => {
  const v = getVal(status);
  return { submitted: 'Soumis', treated: 'Traité', withdrawn: 'Retiré', acknowledged: 'Pris en compte', rejected: 'Écarté' }[v] || v;
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
const formatDateTime = (d) => d ? new Date(d).toLocaleString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';

onMounted(async () => {
  try {
    const versionsRes = await axios.get(`/api/v1/decisions/${props.decision.id}/versions`);
    allVersions.value = versionsRes.data.versions || [];

    // Charger les feedbacks de toutes les versions en parallèle
    const feedbackResults = await Promise.all(
      allVersions.value.map(v =>
        axios.get(`/api/v1/decisions/${props.decision.id}/feedback`, {
          params: { version_id: v.id }
        }).then(r => ({ version_id: v.id, feedbacks: r.data.feedbacks || [] }))
      )
    );

    const byVersion = {};
    feedbackResults.forEach(({ version_id, feedbacks: fbs }) => {
      byVersion[version_id] = fbs;
    });
    feedbacksByVersion.value = byVersion;

    // Aussi conserver les feedbacks de la version courante pour usage rapide
    const current = allVersions.value.find(v => v.is_current) || allVersions.value[0];
    selectedVersionId.value = current ? current.id : null;
    feedbacks.value = current ? (byVersion[current.id] || []) : feedbackResults[0]?.feedbacks || [];
  } catch (e) {
    console.error('Print data fetch error', e);
  } finally {
    loading.value = false;
  }
});

const stripHtml = (html) => {
  if (!html) return '';
  const tmp = document.createElement('div');
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || '';
};

const buildPrintHtml = async () => {
  const d = props.decision;
  const statusLabel = statusLabels[getVal(d.status)] || getVal(d.status);

  // Charger et encoder le logo en base64 pour l'embarquer dans le PDF
  let logoImg = '';
  try {
    const resp = await fetch('/images/dazo-logo.svg');
    const svgText = await resp.text();
    const b64 = btoa(unescape(encodeURIComponent(svgText)));
    logoImg = `<img class="doc-logo" src="data:image/svg+xml;base64,${b64}" alt="DAZO">`;
  } catch (e) {
    logoImg = '<span style="font-size:14pt;font-weight:900;color:#4f46e5;letter-spacing:-1pt;">DAZO</span>';
  }

  let html = `
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>${d.title} — DAZO</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10pt; color: #1a1a2e; background: #fff; padding: 24mm 20mm; line-height: 1.55; }
    
    /* Header */
    .doc-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 14pt; border-bottom: 2px solid #4f46e5; margin-bottom: 20pt; gap: 16pt; }
    .doc-header-left { display: flex; align-items: center; gap: 14pt; flex: 1; }
    .doc-logo { height: 72pt; width: auto; flex-shrink: 0; }
    .doc-title { font-size: 16pt; font-weight: 700; color: #1a1a2e; margin-bottom: 3pt; }
    .doc-meta { font-size: 8pt; color: #64748b; line-height: 1.7; }
    .doc-badge { display: inline-block; padding: 3pt 9pt; border-radius: 4pt; font-size: 8pt; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5pt; }
    .badge-adopted { background: #d1fae5; color: #065f46; }
    .badge-objection { background: #fef3c7; color: #92400e; }
    .badge-clarification { background: #e0e7ff; color: #3730a3; }
    .badge-reaction { background: #dbeafe; color: #1e40af; }
    .badge-other { background: #f1f5f9; color: #475569; }
    
    /* Sections */
    h2 { font-size: 11pt; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 0.8pt; padding-bottom: 5pt; border-bottom: 1px solid #e2e8f0; margin-bottom: 10pt; margin-top: 18pt; }
    h3 { font-size: 10pt; font-weight: 600; color: #334155; margin-bottom: 4pt; margin-top: 10pt; }
    
    /* Version block */
    .version-block { border: 1px solid #e2e8f0; border-radius: 4pt; margin-bottom: 12pt; page-break-inside: avoid; }
    .version-header { background: #f8fafc; padding: 6pt 10pt; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .version-label { font-weight: 700; font-size: 9pt; color: #334155; }
    .version-body { padding: 10pt; font-size: 9.5pt; color: #334155; line-height: 1.65; white-space: pre-wrap; }
    .version-meta { font-size: 8pt; color: #64748b; }
    
    /* Feedback block */
    .fb-block { border-left: 3px solid #e2e8f0; padding: 6pt 10pt; margin-bottom: 8pt; page-break-inside: avoid; }
    .fb-block.type-clarification { border-color: #f59e0b; }
    .fb-block.type-reaction { border-color: #3b82f6; }
    .fb-block.type-objection { border-color: #ef4444; }
    .fb-block.type-suggestion { border-color: #8b5cf6; }
    .fb-meta { font-size: 8pt; color: #94a3b8; margin-bottom: 3pt; }
    .fb-author { font-weight: 600; color: #334155; font-size: 9pt; }
    .fb-content { font-size: 9.5pt; color: #1e293b; margin-top: 4pt; }
    .fb-status { display: inline-block; padding: 1pt 6pt; border-radius: 2pt; font-size: 7.5pt; font-weight: 600; background: #f1f5f9; color: #475569; margin-left: 6pt; }
    .fb-status.status-treated, .fb-status.status-acknowledged, .fb-status.status-withdrawn { background: #d1fae5; color: #065f46; }
    .fb-status.status-rejected { background: #fee2e2; color: #991b1b; }
    
    /* Reply thread */
    .fb-replies { margin-top: 6pt; padding-left: 12pt; border-left: 1px dashed #cbd5e1; }
    .fb-reply { margin-bottom: 4pt; }
    .fb-reply-author { font-size: 8pt; font-weight: 600; color: #475569; }
    .fb-reply-content { font-size: 8.5pt; color: #334155; }
    
    /* Footer */
    .doc-footer { margin-top: 24pt; padding-top: 8pt; border-top: 1px solid #e2e8f0; font-size: 7.5pt; color: #94a3b8; display: flex; justify-content: space-between; }
    
    @media print {
      body { padding: 0; }
      @page { margin: 18mm 15mm; }
      .version-block, .fb-block { page-break-inside: avoid; }
    }
  </style>
</head>
<body>
  <div class="doc-header">
    <div class="doc-header-left">
      ${logoImg}
      <div>
        <div class="doc-title">${d.title}</div>
        <div class="doc-meta">
          Cercle : <strong>${d.circle?.name || '—'}</strong> &nbsp;·&nbsp;
          Porteur : <strong>${d.participants?.find(p => p.role?.value === 'author' || p.role === 'author')?.user?.name || '—'}</strong><br>
          Créée le ${formatDate(d.created_at)} &nbsp;·&nbsp; Mise à jour le ${formatDate(d.updated_at)}
        </div>
      </div>
    </div>
    <div style="flex-shrink:0">
      <span class="doc-badge badge-${getVal(d.status)?.includes('adopt') ? 'adopted' : (getVal(d.status) || 'other')}">${statusLabel}</span>
    </div>
  </div>`;

  // ── Versions ──
  let versionsToPrint = [];
  if (options.versions) {
    versionsToPrint = [...allVersions.value].reverse();
  } else if (selectedVersionId.value) {
    const v = allVersions.value.find(v => v.id === selectedVersionId.value);
    if (v) versionsToPrint = [v];
  }

  if (versionsToPrint.length > 0) {
    html += `<h2>${options.versions ? 'Versions de la proposition' : 'Contenu de la proposition'}</h2>`;
    versionsToPrint.forEach(v => {
      // Pour une version unique, on peut imprimer tout le contenu sans le tronquer
      const content = options.versions ? stripHtml(v.content) : v.content;
      const formattedContent = options.versions 
        ? `${content.substring(0, 2000)}${content.length > 2000 ? '\n[...]' : ''}`
        : content;

      html += `
      <div class="version-block">
        <div class="version-header">
          <span class="version-label">Version ${v.version_number}</span>
          <span class="version-meta">Par ${v.author?.name || '—'} · ${formatDateTime(v.created_at)}</span>
        </div>
        <div class="version-body" ${!options.versions ? 'style="white-space: normal;"' : ''}>${formattedContent}</div>
      </div>`;
    });
  }

  // ── Helper : render a group of feedbacks ──
  const renderFeedbackGroup = (items, typeClass) => {
    if (!items.length) {
      return `<p style="font-size:8.5pt;color:#94a3b8;font-style:italic;">Aucun élément pour l'ensemble des versions.</p>`;
    }
    return items.map(fb => {
      const statusVal = getVal(fb.status);
      const content = fb.content || '';
      const joinsText = (fb.joins && fb.joins.length > 0) 
        ? `<div style="font-size: 8.5pt; color: #64748b; font-style: italic; margin-top: 4pt;">Rejoint par (${fb.joins.length}) : ${fb.joins.map(j => j.user?.name).join(', ')}</div>` 
        : '';
      const replies = (fb.messages || []).map(m => `
        <div class="fb-reply">
          <span class="fb-reply-author">${m.author?.name || '—'}</span>
          <span style="font-size:7.5pt;color:#94a3b8"> · ${formatDateTime(m.created_at)}</span><br>
          <span class="fb-reply-content">${m.content}</span>
        </div>`).join('');
      return `
      <div class="fb-block type-${typeClass}">
        <div class="fb-meta">
          <span class="fb-author">${fb.author?.name || '—'}</span>
          <span class="fb-status status-${statusVal}">${feedbackStatusLabel(fb.status)}</span>
          &nbsp;·&nbsp; ${formatDateTime(fb.created_at)}
          <span style="float:right;font-size:7pt;color:#94a3b8">V${fb._versionNumber || ''}</span>
        </div>
        <div class="fb-content">${content}</div>
        ${joinsText}
        ${replies ? `<div class="fb-replies">${replies}</div>` : ''}
      </div>`;
    }).join('');
  };

  // Associer le numéro de version à chaque feedback pour l'affichage
  const sortedVersions = [...allVersions.value].sort((a, b) => a.version_number - b.version_number);
  const versionsForFeedbacks = options.versions ? sortedVersions : sortedVersions.filter(v => v.id === selectedVersionId.value);
  
  const taggedFeedbacks = {}; // type -> feedbacks[] tagués
  ['clarification', 'reaction', 'objection', 'suggestion'].forEach(t => { taggedFeedbacks[t] = []; });

  versionsForFeedbacks.forEach(v => {
    const vFbs = feedbacksByVersion.value[v.id] || [];
    vFbs.forEach(fb => {
      const t = getVal(fb.type);
      if (taggedFeedbacks[t]) {
        taggedFeedbacks[t].push({ ...fb, _versionNumber: v.version_number });
      }
    });
  });

  if (options.clarifications && taggedFeedbacks['clarification'].length > 0) {
    html += `<h2>Clarifications</h2>`;
    html += renderFeedbackGroup(taggedFeedbacks['clarification'], 'clarification');
  }

  if (options.reactions && taggedFeedbacks['reaction'].length > 0) {
    html += `<h2>Réactions</h2>`;
    html += renderFeedbackGroup(taggedFeedbacks['reaction'], 'reaction');
  }

  if (options.objections) {
    if (taggedFeedbacks['objection'] && taggedFeedbacks['objection'].length > 0) {
      html += `<h2>Objections</h2>`;
      html += renderFeedbackGroup(taggedFeedbacks['objection'], 'objection');
    }
    if (taggedFeedbacks['suggestion'] && taggedFeedbacks['suggestion'].length > 0) {
      html += `<h2>Suggestions</h2>`;
      html += renderFeedbackGroup(taggedFeedbacks['suggestion'], 'suggestion');
    }
  }

  html += `
  <div class="doc-footer">
    <span>Exporté depuis DAZO · ${formatDateTime(new Date())}</span>
    <span>${d.title}</span>
  </div>
</body>
</html>`;

  return html;
};

const doPrint = async () => {
  const html = await buildPrintHtml();
  const win = window.open('', '_blank', 'width=900,height=700');
  if (!win) {
    alert('Veuillez autoriser les pop-ups pour générer le PDF.');
    return;
  }
  win.document.write(html);
  win.document.close();
  win.focus();
  setTimeout(() => {
    win.print();
    // win.close(); // on laisse l'utilisateur fermer après avoir imprimé
  }, 600);
};
</script>

<style scoped>
/* Backdrop */
.dpm-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}

/* Modal */
.dpm-modal {
  background: white;
  border-radius: 20px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
  width: 100%;
  max-width: 520px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: dpm-in 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes dpm-in {
  from { opacity: 0; transform: scale(0.92) translateY(12px); }
  to   { opacity: 1; transform: scale(1)   translateY(0); }
}

/* Header */
.dpm-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 24px 24px 20px;
  border-bottom: 1px solid var(--gray-100, #f1f5f9);
}

.dpm-header-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
  flex-shrink: 0;
}

.dpm-title {
  font-size: 17px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.dpm-subtitle {
  font-size: 13px;
  color: #64748b;
  margin-top: 2px;
}

.dpm-close {
  margin-left: auto;
  background: none;
  border: 1px solid var(--gray-200, #e2e8f0);
  border-radius: 8px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #64748b;
  transition: all 0.15s;
  flex-shrink: 0;
}
.dpm-close:hover { background: #f1f5f9; color: #0f172a; }

/* Body */
.dpm-body {
  padding: 20px 24px;
  flex: 1;
}

.dpm-loading {
  text-align: center;
  padding: 24px;
  color: #64748b;
  font-size: 14px;
}

.dpm-section-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #94a3b8;
  margin-bottom: 12px;
}

/* Options */
.dpm-options { display: flex; flex-direction: column; gap: 10px; }

.dpm-option {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 16px;
  border: 1.5px solid var(--gray-200, #e2e8f0);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.15s;
  user-select: none;
}
.dpm-option:hover { border-color: #a5b4fc; background: #f8f7ff; }
.dpm-option.active { border-color: #4f46e5; background: #eef2ff; }

.dpm-option-check input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #4f46e5;
  cursor: pointer;
}

.dpm-option-body { flex: 1; }
.dpm-option-title { font-size: 14px; font-weight: 600; color: #1e293b; }
.dpm-option-desc { font-size: 12px; color: #64748b; margin-top: 2px; }

.dpm-option-count {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  background: #f1f5f9;
  border-radius: 20px;
  padding: 2px 10px;
  min-width: 28px;
  text-align: center;
}
.dpm-option.active .dpm-option-count { background: #c7d2fe; color: #3730a3; }

/* Select */
.dpm-select {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid var(--gray-300, #cbd5e1);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #1e293b;
  background-color: white;
  outline: none;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 10px center;
  background-repeat: no-repeat;
  background-size: 20px 20px;
  transition: all 0.15s;
}
.dpm-select:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Footer */
.dpm-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 16px 24px;
  border-top: 1px solid var(--gray-100, #f1f5f9);
  background: #fafafa;
}
</style>

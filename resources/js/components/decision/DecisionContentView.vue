<template>
  <div>
    <!-- Vue standard (Clarification, Réaction, Objection, Adoptée) -->
    <div v-if="!isRevision && !isDraft" class="premium-card mb-16">
      <div class="pc-header pc-header-blue">
        <div class="pc-header-icon"><i :class="viewingVersionId ? 'fa-solid fa-clock-rotate-left' : 'fa-solid fa-file-lines'"></i></div>
        <div class="pc-header-content">
          <div class="pc-header-title">{{ viewingVersionId ? 'Version ' + historicalVersionNumber : 'Contenu de la décision' }}</div>
          <div class="pc-header-sub">{{ viewingVersionId ? 'Version historique' : 'Version actuelle de la proposition' }}</div>
        </div>
      </div>
      <div class="card-body">
        <div v-if="displayContent" class="decision-prose" v-html="displayContent"></div>
        <div v-else class="text-muted text-sm">Aucun contenu disponible pour cette version.</div>
      </div>
    </div>

    <!-- Panel des Pièces jointes (si pas en édition) -->
    <AttachmentPanel
      :attachments="displayAttachments"
      :editable="false"
      :can-reload="canReloadAttachments"
      :version-id="viewingVersionId || currentVersionId || ''"
    />
    
    <!-- Vue contextuelle pendant une révision -->
    <div v-if="isRevision && !isDraft" class="premium-card mb-16">
      <div class="pc-header pc-header-blue" style="opacity: 0.9;">
        <div class="pc-header-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <div class="pc-header-content">
          <div class="pc-header-title">{{ isAuthorOrAnimator ? 'Proposition à réviser' : 'Proposition actuelle' }}</div>
          <div class="pc-header-sub">Version {{ currentVersionNumber }} en cours de révision</div>
        </div>
      </div>
      <div class="card-body" style="background: var(--gray-50); border-radius: 0 0 var(--radius-lg) var(--radius-lg);">
        <div v-if="currentContent" class="decision-prose" v-html="currentContent"></div>
        <div v-else class="text-muted text-sm">Aucun contenu disponible pour cette version.</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AttachmentPanel from '../AttachmentPanel.vue';

defineProps({
  isDraft: Boolean,
  isRevision: Boolean,
  isAuthorOrAnimator: Boolean,
  canReloadAttachments: Boolean,
  viewingVersionId: { type: [String, Number], default: null },
  historicalVersionNumber: { type: [String, Number], default: '' },
  currentVersionNumber: { type: [String, Number], default: '' },
  currentVersionId: { type: [String, Number], default: '' },
  displayContent: { type: String, default: '' },
  currentContent: { type: String, default: '' },
  displayAttachments: { type: Array, default: () => [] }
});
</script>

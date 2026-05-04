<template>
  <div>
    <!-- DRAFT MODE -->
    <div v-if="isDraft" class="premium-card mb-16">
      <div class="pc-header pc-header-amber">
        <div class="pc-header-icon"><i class="fa-solid fa-file-pen"></i></div>
        <div class="pc-header-content">
          <div class="pc-header-title">Mode brouillon</div>
          <div class="pc-header-sub">Édition active</div>
        </div>
      </div>

      <div class="card-body">
        <form @submit.prevent="$emit('save-draft')">
          <div class="form-group">
            <label class="label">Titre</label>
            <input v-model="form.title" class="input" required>
          </div>

          <div class="form-group">
            <label class="label">Contenu de la proposition</label>
            <RichTextEditor
              v-model="form.content"
              placeholder="Rédigez la décision avec mise en forme, liens et listes…"
            />
          </div>

          <div class="mb-16">
            <AttachmentPanel
              :attachments="currentVersion?.attachments || []"
              :editable="true"
              :version-id="currentVersion?.id || ''"
              :hide-header="true"
              @changed="$emit('refresh')"
            />
          </div>

          <div class="draft-actions flex justify-between items-center mt-24">
            <button type="button" class="btn btn-danger" :disabled="deletingDraft" @click="$emit('delete-draft')">
              <i class="fa-solid fa-trash-can mr-8"></i>
              {{ deletingDraft ? 'Suppression…' : 'Supprimer' }}
            </button>
            <button type="submit" class="btn btn-primary" :disabled="savingDraft">
              <i class="fa-solid fa-floppy-disk mr-8"></i>
              {{ savingDraft ? 'Enregistrement…' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- REVISION MODE -->
    <div v-if="isRevision && isAuthorOrAnimator" class="premium-card mb-16">
      <div class="pc-header pc-header-light-blue">
        <div class="pc-header-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="pc-header-content">
          <div class="pc-header-title">Révision de la proposition</div>
          <div class="pc-header-sub">Préparez la nouvelle version suite aux retours</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-group">
          <label class="label">Contenu de la nouvelle proposition</label>
          <RichTextEditor
            v-model="form.content"
            placeholder="Appliquez les modifications nécessaires ici..."
          />
        </div>

        <div class="mb-16">
          <AttachmentPanel
            :attachments="revisionAttachments"
            :editable="true"
            version-id=""
            :hide-header="true"
            :can-reload="false"
            @uploaded="(file) => $emit('upload-revision-file', file)"
            @removed="(file) => $emit('remove-revision-file', file)"
          />
        </div>


      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import RichTextEditor from '../RichTextEditor.vue';
import AttachmentPanel from '../AttachmentPanel.vue';

const props = defineProps({
  isDraft: Boolean,
  isRevision: Boolean,
  isAuthorOrAnimator: Boolean,
  form: Object,
  currentVersion: Object,
  savingDraft: Boolean,
  deletingDraft: Boolean,
  publishing: Boolean,
  reusedAttachmentIds: { type: Array, default: () => [] },
  revisionAttachments: { type: Array, default: () => [] }
});

const allAttachmentsReused = computed(() => {
  if (!props.currentVersion?.attachments) return false;
  return props.currentVersion.attachments.every(a => props.reusedAttachmentIds.includes(a.id));
});

defineEmits([
  'save-draft', 'delete-draft', 'refresh', 'toggle-all-attachments',
  'toggle-attachment', 'upload-revision-file', 'remove-revision-file',
  'save-revision', 'publish-revision'
]);
</script>

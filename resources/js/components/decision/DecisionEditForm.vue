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

          <div class="form-group">
            <label class="label">Exclure des membres de ce processus</label>
            <div class="checkbox-panel">
              <label
                v-for="member in excludableMembers"
                :key="member.user_id"
                class="checkbox-row"
              >
                <input v-model="form.excluded_members" type="checkbox" :value="member.user_id">
                <span>{{ member.user?.name }}</span>
              </label>
            </div>
          </div>

          <div class="mb-16">
            <AttachmentPanel
              :attachments="currentVersion?.attachments || []"
              :editable="true"
              :version-id="currentVersion?.id || ''"
              @changed="$emit('refresh')"
            />
          </div>

          <div class="draft-actions">
            <button type="button" class="btn btn-danger" :disabled="deletingDraft" @click="$emit('delete-draft')">
              {{ deletingDraft ? 'Suppression…' : 'Supprimer le brouillon' }}
            </button>
            <button type="submit" class="btn btn-primary" :disabled="savingDraft">
              {{ savingDraft ? 'Enregistrement…' : 'Enregistrer le brouillon' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- REVISION MODE -->
    <div v-if="isRevision && isAuthorOrAnimator" class="premium-card mb-16">
      <div class="pc-header pc-header-amber">
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

        <div v-if="currentVersion?.attachments?.length > 0" class="mb-16 p-12 bg-gray-50 border-radius-sm">
          <div class="flex items-center justify-between mb-8">
            <label class="label mb-0" style="font-size: 13px;">Conserver des pièces jointes de la version précédente</label>
            <button type="button" class="btn btn-link btn-sm" @click="$emit('toggle-all-attachments')" style="font-size: 12px;">
              {{ allAttachmentsReused ? 'Tout décocher' : 'Tout cocher' }}
            </button>
          </div>
          <div class="flex flex-wrap gap-12">
            <div v-for="att in currentVersion.attachments" :key="att.id" class="flex items-center gap-8">
              <input 
                type="checkbox" 
                :id="'prev-att-' + att.id"
                :checked="reusedAttachmentIds.includes(att.id)"
                @change="$emit('toggle-attachment', att)"
                class="checkbox-sm"
              >
              <label :for="'prev-att-' + att.id" class="text-sm cursor-pointer" style="margin-bottom: 0;">{{ att.filename }}</label>
            </div>
          </div>
        </div>

        <div class="mb-16">
          <AttachmentPanel
            :attachments="revisionAttachments"
            :editable="true"
            version-id=""
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
  excludableMembers: Array,
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

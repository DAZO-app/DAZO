<template>
  <div v-if="editable || displayAttachments.length" class="premium-card">
    <div class="pc-header pc-header-blue">
      <div class="pc-header-icon">📎</div>
      <div class="pc-header-content">
        <div class="pc-header-title">Pièces jointes</div>
        <div class="pc-header-sub">{{ displayAttachments.length }} fichier(s) associé(s)</div>
      </div>
      <button v-if="editable" class="btn btn-secondary btn-sm" type="button" @click="browseFiles" style="margin-left:auto; position:relative; z-index:1;">
        Ajouter
      </button>
    </div>

    <div class="card-body">
      <input
        v-if="editable"
        ref="fileInputRef"
        type="file"
        class="hidden-input"
        multiple
        @change="handleFileSelection"
      >

      <div
        v-if="editable"
        class="drop-zone"
        :class="{ active: dragActive }"
        @dragover.prevent="dragActive = true"
        @dragleave.prevent="dragActive = false"
        @drop.prevent="handleDrop"
      >
        <div class="drop-zone-icon">📎</div>
        <div class="drop-zone-title">Glissez-déposez vos fichiers ici</div>
        <div class="drop-zone-subtitle">ou utilisez le bouton Ajouter pour joindre un document</div>
      </div>

      <div v-if="displayAttachments.length" class="attachments-grid" :class="{ 'mt-16': editable }">
        <article v-for="attachment in displayAttachments" :key="attachment.localKey" class="attachment-card">
          <div class="attachment-preview">
            <img
              v-if="isImage(attachment.mime_type) && attachment.url"
              :src="attachment.url"
              :alt="attachment.filename"
              class="attachment-image"
            >
            <div v-else class="attachment-icon">{{ fileGlyph(attachment.mime_type) }}</div>
          </div>

          <div class="attachment-content">
            <div class="attachment-name" :title="attachment.filename">{{ attachment.filename }}</div>
            <div class="attachment-meta">
              <span>{{ formatSize(attachment.size_bytes) }}</span>
              <span v-if="attachment.mime_type">{{ attachment.mime_type }}</span>
            </div>

            <div v-if="attachment.uploading" class="upload-track">
              <div class="upload-bar" :style="{ width: `${attachment.progress}%` }"></div>
            </div>

            <div v-else class="attachment-actions">
              <a
                v-if="attachment.url"
                class="btn btn-ghost btn-sm"
                :href="attachment.url"
                target="_blank"
                rel="noopener noreferrer"
              >
                Ouvrir
              </a>
              <a
                v-if="attachment.url"
                class="btn btn-ghost btn-sm"
                :href="attachment.url"
                :download="attachment.filename"
              >
                Télécharger
              </a>
              <button
                v-if="editable && attachment.id"
                type="button"
                class="btn btn-danger btn-sm"
                @click="removeAttachment(attachment)"
              >
                Supprimer
              </button>
            </div>
          </div>
        </article>
      </div>

      <div v-else class="empty-state" :class="{ 'mt-16': editable }">
        Aucune pièce jointe disponible pour cette version.
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  attachments: {
    type: Array,
    default: () => [],
  },
  editable: {
    type: Boolean,
    default: false,
  },
  versionId: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['changed', 'uploaded', 'removed']);

const fileInputRef = ref(null);
const dragActive = ref(false);
const localAttachments = ref([]);

const hydrateAttachments = () => {
  localAttachments.value = (props.attachments || []).map((attachment) => ({
    ...attachment,
    localKey: attachment.id || `${attachment.filename}-${attachment.s3_path}`,
    url: attachment.s3_path ? `/storage/${attachment.s3_path}` : attachment.url,
    uploading: false,
    progress: 100,
  }));
};

watch(() => props.attachments, hydrateAttachments, { immediate: true, deep: true });

const displayAttachments = computed(() => localAttachments.value);

const browseFiles = () => {
  fileInputRef.value?.click();
};

const handleFileSelection = (event) => {
  const files = Array.from(event.target.files || []);
  uploadFiles(files);
  event.target.value = '';
};

const handleDrop = (event) => {
  dragActive.value = false;
  uploadFiles(Array.from(event.dataTransfer.files || []));
};

const uploadFiles = async (files) => {
  if (!props.editable) {
    return;
  }

  for (const file of files) {
    const tempKey = `${file.name}-${file.size}-${Date.now()}-${Math.random()}`;
    const localItem = {
      id: null,
      localKey: tempKey,
      filename: file.name,
      size_bytes: file.size,
      mime_type: file.type,
      url: '',
      uploading: true,
      progress: 0,
    };

    localAttachments.value.unshift(localItem);

    const formData = new FormData();
    formData.append('file', file);
    if (props.versionId) {
      formData.append('decision_version_id', props.versionId);
    }

    try {
      const { data } = await axios.post('/api/v1/attachments', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
          if (!progressEvent.total) {
            return;
          }

          localItem.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        },
      });

      Object.assign(localItem, data.attachment, {
        localKey: data.attachment.id,
        url: data.url,
        uploading: false,
        progress: 100,
      });

      emit('uploaded', data.attachment);
      emit('changed');
    } catch (error) {
      localAttachments.value = localAttachments.value.filter((attachment) => attachment.localKey !== tempKey);
      const apiErrors = error.response?.data?.errors;
      const firstError = apiErrors
        ? Object.values(apiErrors).flat().find(Boolean)
        : null;
      window.alert(firstError || error.response?.data?.message || 'Erreur lors de l’ajout de la pièce jointe.');
    }
  }
};

const removeAttachment = async (attachment) => {
  if (!attachment.id) {
    return;
  }

  if (!window.confirm(`Supprimer "${attachment.filename}" ?`)) {
    return;
  }

  try {
    await axios.delete(`/api/v1/attachments/${attachment.id}`);
    localAttachments.value = localAttachments.value.filter((item) => item.localKey !== attachment.localKey);
    emit('removed', attachment.id);
    emit('changed');
  } catch (error) {
    window.alert(error.response?.data?.message || 'Suppression impossible pour le moment.');
  }
};

const formatSize = (bytes) => {
  if (!bytes) return '0 B';
  const units = ['B', 'KB', 'MB', 'GB'];
  const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
  return `${(bytes / 1024 ** index).toFixed(index === 0 ? 0 : 1)} ${units[index]}`;
};

const isImage = (mimeType) => typeof mimeType === 'string' && mimeType.startsWith('image/');

const fileGlyph = (mimeType) => {
  if (isImage(mimeType)) return '🖼';
  if (mimeType === 'application/pdf') return '📕';
  if (typeof mimeType === 'string' && mimeType.includes('spreadsheet')) return '📊';
  if (typeof mimeType === 'string' && mimeType.includes('word')) return '📝';
  return '📄';
};
</script>

<style scoped>
.hidden-input {
  display: none;
}

.drop-zone {
  border: 1px dashed var(--gray-300);
  border-radius: var(--radius-md);
  padding: 20px;
  background: var(--gray-50);
  text-align: center;
  transition: all 0.15s ease;
}

.drop-zone.active {
  border-color: var(--blue-600);
  background: var(--blue-50);
}

.drop-zone-icon {
  font-size: 24px;
  margin-bottom: 8px;
}

.drop-zone-title {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-800);
}

.drop-zone-subtitle {
  font-size: 12px;
  color: var(--gray-500);
  margin-top: 4px;
}

.attachments-grid {
  display: grid;
  gap: 12px;
}

.attachment-card {
  display: grid;
  grid-template-columns: 88px minmax(0, 1fr);
  gap: 12px;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  padding: 12px;
  background: white;
}

.attachment-preview {
  width: 88px;
  height: 88px;
  border-radius: var(--radius-md);
  background: var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.attachment-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.attachment-icon {
  font-size: 28px;
}

.attachment-content {
  min-width: 0;
}

.attachment-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-900);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.attachment-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 4px;
  font-size: 11px;
  color: var(--gray-500);
}

.attachment-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.upload-track {
  width: 100%;
  height: 6px;
  border-radius: 999px;
  background: var(--gray-100);
  overflow: hidden;
  margin-top: 12px;
}

.upload-bar {
  height: 100%;
  background: var(--blue-600);
}

.empty-state {
  font-size: 12px;
  color: var(--gray-500);
}

.mt-16 {
  margin-top: 16px;
}
</style>

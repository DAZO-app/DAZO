<template>
  <div class="premium-card">
    <div class="pc-header pc-header-blue">
      <div class="pc-header-icon"><i class="fa-solid fa-paperclip"></i></div>
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
        <div class="drop-zone-icon"><i class="fa-solid fa-paperclip"></i></div>
        <div class="drop-zone-title">Glissez-déposez vos fichiers ici</div>
        <div class="drop-zone-subtitle">ou</div>
        <button type="button" class="btn btn-secondary btn-sm mt-8" @click="browseFiles">
          <i class="fa-solid fa-folder-open mr-4"></i> Parcourir
        </button>
      </div>

      <div v-if="displayAttachments.length" class="attachments-grid" :class="{ 'mt-16': editable }">
        <article v-for="(attachment, index) in displayAttachments" :key="attachment.localKey" class="attachment-card">
          <div 
            class="attachment-preview" 
            :data-url="attachment.url"
            :data-pswp-src="attachment.url"
            data-pswp-width="1200"
            data-pswp-height="800"
            @click="runLightbox(index)"
            style="cursor: pointer; display: flex;"
          >
            <img
              v-if="isImage(attachment.mime_type) && attachment.url"
              :src="attachment.url"
              :alt="attachment.filename"
              class="attachment-image"
            >
            <div v-else class="attachment-icon"><i :class="fileGlyph(attachment.mime_type)"></i></div>
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
                class="btn btn-secondary btn-sm"
                :href="attachment.url"
                target="_blank"
                rel="noopener noreferrer"
                @click.prevent="runLightbox(index)"
              >
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Ouvrir
              </a>
              <a
                v-if="attachment.url"
                class="btn btn-secondary btn-sm"
                :href="attachment.url"
                :download="attachment.filename"
              >
                <i class="fa-solid fa-download"></i> Télécharger
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
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import PhotoSwipe from 'photoswipe';
import 'photoswipe/dist/photoswipe.css';

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
    url: attachment.id ? `/api/v1/attachments/${attachment.id}/download` : attachment.url,
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

  // Max size check (client-side) - Default 100MB if not specified
  const MAX_SIZE = 100 * 1024 * 1024; 

  for (const file of files) {
    if (file.size > MAX_SIZE) {
        window.alert(`Le fichier "${file.name}" est trop volumineux (max 100 Mo).`);
        continue;
    }
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

const isImage = (mimeType) => {
  const mime = (typeof mimeType === 'object' && mimeType !== null) ? mimeType.value : mimeType;
  return typeof mime === 'string' && mime.startsWith('image/');
};

const fileGlyph = (mimeType) => {
  const mime = (typeof mimeType === 'object' && mimeType !== null) ? mimeType.value : mimeType;
  if (isImage(mime)) return 'fa-solid fa-file-image';
  if (mime === 'application/pdf') return 'fa-solid fa-file-pdf';
  if (typeof mime === 'string' && mime.includes('spreadsheet')) return 'fa-solid fa-file-excel';
  if (typeof mime === 'string' && (mime.includes('word') || mime.includes('officedocument.wordprocessingml'))) return 'fa-solid fa-file-word';
  return 'fa-solid fa-file';
};

let lightbox = null;

onMounted(() => {
  console.log('📦 [DAZO] AttachmentPanel Mounted. Attachments:', props.attachments);
  lightbox = new PhotoSwipeLightbox({
    pswpModule: PhotoSwipe,
    bgOpacity: 0.9,
    showHideAnimationType: 'fade',
  });
  lightbox.init();
});

onBeforeUnmount(() => {
  if (lightbox) {
    lightbox.destroy();
    lightbox = null;
  }
});

const runLightbox = (index) => {
  if (!lightbox) return;

  const items = displayAttachments.value.map(a => {
    const isImg = isImage(a.mime_type);
    const url = a.url || '';
    
    if (isImg && url) {
      return {
        src: url,
        type: 'image',
        width: 1200, 
        height: 800,
        alt: a.filename
      };
    } else if (a.mime_type === 'application/pdf' && url) {
      return {
        html: `
          <div class="pswp__pdf-container">
            <iframe src="${url}#view=FitH" class="pswp__pdf-iframe"></iframe>
            <div class="pswp__pdf-fallback">
              <a href="${url}" target="_blank" class="pswp__doc-btn">Ouvrir le PDF en plein écran</a>
            </div>
          </div>
        `
      };
    } else {
      const icon = fileGlyph(a.mime_type);
      return {
        html: `
          <div class="pswp__doc-slide">
            <div class="pswp__doc-icon"><i class="${icon}"></i></div>
            <div class="pswp__doc-name">${a.filename || 'Document'}</div>
            <a href="${url}" target="_blank" class="pswp__doc-btn" onclick="event.stopPropagation()">Ouvrir le document</a>
          </div>
        `
      };
    }
  });

  try {
    lightbox.options.dataSource = items;
    lightbox.loadAndOpen(index);
  } catch (err) {
    console.error('PhotoSwipe Error:', err);
  }
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

<style>
/* Global styles for PhotoSwipe document slides */
.pswp__doc-slide {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  padding: 20px;
  text-align: center;
}
.pswp__doc-icon {
  font-size: 80px;
  margin-bottom: 20px;
}
.pswp__doc-name {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 30px;
  max-width: 400px;
}
.pswp__doc-btn {
  background: white;
  color: black;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: opacity 0.2s;
}
.pswp__doc-btn:hover {
  opacity: 0.9;
}
</style>

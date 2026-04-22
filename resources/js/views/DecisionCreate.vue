<template>
  <main class="main">
    <div class="page-body">
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Nouvelle proposition</div>
            <div class="hero-subtitle">Remplissez les détails pour lancer un nouveau cycle de décision</div>
          </div>
          <div class="hero-action gap-12 flex">
            <button class="btn btn-white" @click="$router.back()">Annuler</button>
            <button class="btn btn-secondary" @click="submit" :disabled="submitting">
              <i class="fa-solid fa-paper-plane mr-8"></i> {{ submitting ? 'Création...' : 'Créer la décision' }}
            </button>
          </div>
        </div>
      </div>
      <div v-if="error" class="alert alert-error mb-16">{{ error }}</div>

      <div class="grid-layout">
        <div class="col-main">
          <div class="premium-card mb-16">
            <div class="pc-header pc-header-blue">
              <div class="pc-header-icon"><i class="fa-solid fa-pen-nib"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Contenu de la décision</div>
                <div class="pc-header-sub">Titre et description détaillée</div>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="label">Titre de la proposition *</label>
                <input v-model="form.title" class="input input-lg" placeholder="Ex: Adopter le télétravail hybride" required />
              </div>

              <div class="form-group">
                <label class="label">Contenu détaillé *</label>
                <div id="quill-editor" style="height: 300px; background: white;"></div>
              </div>
            </div>
          </div>

          <!-- Zone de pièces jointes -->
          <div class="premium-card">
            <div class="pc-header pc-header-indigo">
              <div class="pc-header-icon"><i class="fa-solid fa-paperclip"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Pièces jointes</div>
                <div class="pc-header-sub">Documents et ressources liées</div>
              </div>
            </div>
            <div class="card-body">
              <div 
                class="drop-zone" 
                @dragover.prevent="dragActive = true" 
                @dragleave.prevent="dragActive = false" 
                @drop.prevent="handleDrop"
                :class="{ active: dragActive }"
              >
                <div class="drop-zone-content">
                  <i class="fa-solid fa-folder-open text-2xl mb-8"></i>
                  <p class="mb-8">Glissez-déposez vos fichiers ici</p>
                  <p class="text-xs text-muted mb-12">ou</p>
                  <button type="button" class="btn btn-secondary btn-sm" @click="$refs.fileInputRef.click()">
                    <i class="fa-solid fa-search mr-4"></i> Parcourir
                  </button>
                  <input ref="fileInputRef" type="file" multiple class="hidden" @change="handleFileSelect">
                  <p class="text-xs text-muted mt-12">Images, PDF, Documents (Max 10Mo par fichier)</p>
                </div>
              </div>

                <div v-if="attachments.length" class="attachments-list mt-16">
                    <div v-for="(a, idx) in attachments" :key="idx" class="attachment-mini">
                        <div
                           class="attachment-preview-mini" 
                           :data-url="a.url"
                           :data-pswp-src="a.url"
                           data-pswp-width="1200"
                           data-pswp-height="800"
                           @click.stop="runLightbox(idx)" 
                           style="cursor: pointer; display: flex;"
                        >
                           <img v-if="isImage(a.mime_type) && a.url" :src="a.url" class="mini-img" />
                           <i v-else :class="fileGlyph(a.mime_type)" class="text-lg"></i>
                        </div>
                        <div class="attachment-info" @click.stop.prevent="runLightbox(idx)" style="cursor: pointer;">
                            <div class="attachment-name">{{ a.filename }}</div>
                            <div class="attachment-size">{{ formatSize(a.size_bytes) }}</div>
                        </div>
                        <div v-if="a.uploading" class="upload-progress-bar">
                             <div class="progress" :style="{ width: a.progress + '%' }"></div>
                        </div>
                        <button v-else class="btn btn-ghost btn-icon btn-sm" @click.stop="removeAttachment(idx)"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
            </div>
          </div>
        </div>

        <div class="col-side">
          <div class="premium-card mb-16">
            <div class="pc-header pc-header-amber">
              <div class="pc-header-icon"><i class="fa-solid fa-sliders"></i></div>
              <div class="pc-header-content">
                <div class="pc-header-title">Paramètres de la décision</div>
                <div class="pc-header-sub">Configuration du processus</div>
              </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="label">Cercle *</label>
                    <select v-model="form.circle_id" class="select" required>
                    <option value="" disabled>Sélectionner un cercle...</option>
                    <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>

                <div class="form-group" v-if="circleMembers.length > 0">
                    <label class="label">Animateur (Optionnel)</label>
                    <select v-model="form.animator_id" class="select">
                    <option value="">Auteur (vous)</option>
                    <option v-for="m in circleMembers" :key="m.user.id" :value="m.user.id">
                        {{ m.user.name }}
                    </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label">Catégorie</label>
                    <select v-model="form.category_id" class="select">
                        <option value="">Aucune</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>


            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import PhotoSwipe from 'photoswipe';
import 'photoswipe/dist/photoswipe.css';

const router = useRouter();
const route = useRoute();
const circles = ref([]);
const categories = ref([]);
const models = ref([]);
const circleMembers = ref([]);
const attachments = ref([]);
const dragActive = ref(false);
const error = ref('');
const submitting = ref(false);

const form = ref({
  circle_id: '',
  animator_id: '',
  title: '',
  category_id: '',
  model_id: '',
});

let quill = null;

onMounted(async () => {
    if (route.query.circle_id) {
        form.value.circle_id = route.query.circle_id;
    }

    // Initialisation Quill (on attend que le script chargé via CDN soit prêt)
    const initQuill = () => {
        if (typeof Quill === 'undefined') {
            setTimeout(initQuill, 100);
            return;
        }
        quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{'list': 'ordered'}, {'list': 'bullet'}],
                    ['link', 'clean']
                ]
            },
            placeholder: 'Décrivez votre proposition en détail...'
        });
    };
    initQuill();

  try {
    const [circleRes, modelRes, catRes] = await Promise.all([
      axios.get('/api/v1/circles'),
      axios.get('/api/v1/models'),
      axios.get('/api/v1/categories'),
    ]);
    circles.value = circleRes.data.circles || [];
    models.value = modelRes.data.data || modelRes.data || [];
    categories.value = catRes.data.categories || [];
  } catch (e) { /* silent */ }
});

watch(() => form.value.circle_id, async (newId) => {
  if (newId) {
    try {
      const { data } = await axios.get(`/api/v1/circles/${newId}/members`);
      circleMembers.value = data.members || [];
    } catch (e) { circleMembers.value = []; }
  } else { circleMembers.value = []; }
});

const handleFileSelect = (e) => {
    uploadFiles(Array.from(e.target.files));
};

const handleDrop = (e) => {
    dragActive.value = false;
    uploadFiles(Array.from(e.dataTransfer.files));
};

const uploadFiles = (files) => {
    files.forEach(file => {
        const item = { filename: file.name, size_bytes: file.size, uploading: true, progress: 0, id: null };
        attachments.value.push(item);
        
        const formData = new FormData();
        formData.append('file', file);
        
        axios.post('/api/v1/attachments', formData, {
            onUploadProgress: (progressEvent) => {
                item.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            }
        }).then(res => {
            item.uploading = false;
            item.id = res.data.attachment.id;
            item.url = res.data.url;
            item.mime_type = res.data.attachment.mime_type;
        }).catch(() => {
            item.uploading = false;
            item.filename += " (Erreur)";
        });
    });
};

const removeAttachment = (idx) => {
    const item = attachments.value[idx];
    if (item.id) {
        axios.delete(`/api/v1/attachments/${item.id}`);
    }
    attachments.value.splice(idx, 1);
};

const formatSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
};

const submit = async () => {
    if (!form.value.circle_id || !form.value.title) {
        error.value = "Veuillez remplir les champs obligatoires (*)";
        return;
    }

    const content = quill.root.innerHTML;
    if (content === '<p><br></p>') {
        error.value = "Le contenu de la proposition est obligatoire.";
        return;
    }

    submitting.value = true;
    error.value = '';

    try {
        const { data } = await axios.post(`/api/v1/circles/${form.value.circle_id}/decisions`, {
            title: form.value.title,
            content: content,
            animator_id: form.value.animator_id || undefined,
            category_id: form.value.category_id || undefined,
            model_id: form.value.model_id || undefined,
        });

        const decision = data.decision;
        const versionId = decision.current_version.id;

        // Liaison des pièces jointes
        const attIds = attachments.value.filter(a => a.id).map(a => a.id);
        if (attIds.length) {
            await axios.post(`/api/v1/decisions/versions/${versionId}/attachments/link`, {
                attachment_ids: attIds
            });
        }

        router.push({ name: 'DecisionDetail', params: { id: decision.id } });
    } catch (e) {
        error.value = e.response?.data?.message || 'Erreur lors de la création.';
    } finally {
        submitting.value = false;
    }
};

const isImage = (mimeType) => typeof mimeType === 'string' && mimeType.startsWith('image/');

const fileGlyph = (mimeType) => {
  if (isImage(mimeType)) return 'fa-solid fa-file-image';
  if (mimeType === 'application/pdf') return 'fa-solid fa-file-pdf';
  if (typeof mimeType === 'string' && mimeType.includes('spreadsheet')) return 'fa-solid fa-file-excel';
  if (typeof mimeType === 'string' && (mimeType.includes('word') || mimeType.includes('officedocument.wordprocessingml'))) return 'fa-solid fa-file-word';
  return 'fa-solid fa-file';
};

let lightbox = null;

onMounted(() => {
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
  if (attachments.value[index].uploading || !lightbox) {
    console.warn('LightBox not ready or file uploading');
    return;
  }
  
  console.log('Opening PhotoSwipe in DecisionCreate at index:', index);

  try {
    const items = attachments.value.filter(a => !a.uploading && a.id).map(a => {
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

    const filteredIndex = attachments.value.slice(0, index).filter(a => !a.uploading && a.id).length;

    lightbox.options.dataSource = items;
    lightbox.loadAndOpen(filteredIndex);
  } catch (err) {
    console.error('PhotoSwipe loadAndOpen error:', err);
  }
};
</script>

<style scoped>
.grid-layout { display: flex; flex-direction: column; gap: 20px; }
@media (min-width: 900px) {
  .grid-layout { flex-direction: row; }
  .col-main { flex: 2; }
  .col-side { flex: 1; min-width: 300px; }
}

.input-lg { font-size: 18px; font-weight: 600; padding: 12px 16px; }

.drop-zone {
    border: 2px dashed var(--gray-200); border-radius: var(--radius-lg); padding: 30px; text-align: center;
    transition: all 0.2s; background: var(--gray-50);
}
.drop-zone.active { border-color: var(--blue-500); background: var(--blue-50); }
.drop-zone-content { display: flex; flex-direction: column; align-items: center; color: var(--gray-500); font-size: 13px; }
.hidden { display: none; }

.attachment-mini {
    display: flex; align-items: center; gap: 12px; padding: 10px; background: white; border: 1px solid var(--gray-100);
    border-radius: var(--radius-md); margin-bottom: 8px; position: relative;
}
.attachment-info { flex: 1; min-width: 0; }
.attachment-name { font-size: 12px; font-weight: 500; color: var(--gray-700); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.attachment-size { font-size: 10px; color: var(--gray-400); }

.upload-progress-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: var(--gray-100); overflow: hidden; }
.progress { height: 100%; background: var(--blue-600); transition: width 0.2s; }

.attachment-preview-mini {
    width: 32px; height: 32px; border-radius: 4px; background: var(--gray-50);
    display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.mini-img { width: 100%; height: 100%; object-fit: cover; }
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

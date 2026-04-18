<template>
  <main class="main">
    <div class="page-header justify-between">
      <div>
        <div class="page-title">Nouvelle proposition</div>
        <div class="page-subtitle">Remplissez les détails pour lancer un nouveau cycle de décision</div>
      </div>
      <div class="page-actions">
        <button class="btn btn-ghost" @click="$router.back()">Annuler</button>
        <button class="btn btn-primary" @click="submit" :disabled="submitting">
          {{ submitting ? 'Création...' : 'Créer la décision' }}
        </button>
      </div>
    </div>

    <div class="page-body">
      <div v-if="error" class="alert alert-error mb-16">{{ error }}</div>

      <div class="grid-layout">
        <div class="col-main">
          <div class="card mb-16">
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
          <div class="card">
            <div class="card-header">
              <span class="card-title">Pièces jointes</span>
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
                  <span class="text-2xl mb-8">📁</span>
                  <p>Glissez-déposez vos fichiers ici ou <label class="text-blue-600 cursor-pointer">parcourez<input type="file" multiple class="hidden" @change="handleFileSelect"></label></p>
                  <p class="text-xs text-muted mt-4">Images, PDF, Documents (Max 10Mo par fichier)</p>
                </div>
              </div>

                <div v-if="attachments.length" class="attachments-list mt-16">
                    <div v-for="(a, idx) in attachments" :key="idx" class="attachment-mini">
                        <span class="text-lg">📄</span>
                        <div class="attachment-info">
                            <div class="attachment-name">{{ a.filename }}</div>
                            <div class="attachment-size">{{ formatSize(a.size_bytes) }}</div>
                        </div>
                        <div v-if="a.uploading" class="upload-progress-bar">
                             <div class="progress" :style="{ width: a.progress + '%' }"></div>
                        </div>
                        <button v-else class="btn btn-ghost btn-icon btn-sm" @click="removeAttachment(idx)">✕</button>
                    </div>
                </div>
            </div>
          </div>
        </div>

        <div class="col-side">
          <div class="card mb-16">
            <div class="card-header"><span class="card-title">Paramètres</span></div>
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

                <div class="form-group">
                    <label class="label">Modèle de décision</label>
                    <select v-model="form.model_id" class="select">
                        <option value="">Par défaut</option>
                        <option v-for="m in models" :key="m.id" :value="m.id">{{ m.name }}</option>
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
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';

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
</style>

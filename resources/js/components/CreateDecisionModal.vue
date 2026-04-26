<template>
  <div v-if="visible" class="modal-overlay" @click.self="close">
    <div class="modal-card">
      <div class="modal-header">
        <span class="modal-title">Nouvelle décision</span>
        <button class="btn btn-ghost btn-icon" @click="close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="submit">
          <div v-if="error" class="alert alert-error mb-16">{{ error }}</div>

          <div class="form-group">
            <label class="label">Cercle *</label>
            <select v-model="form.circle_id" class="select" required>
              <option value="" disabled>Sélectionner un cercle...</option>
              <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <div class="form-group" v-if="circleMembers.length > 0">
            <label class="label">Animateur de la décision (Optionnel)</label>
            <select v-model="form.animator_id" class="select">
              <option value="">Aucun (Auteur par défaut)</option>
              <option v-for="m in circleMembers" :key="m.user.id" :value="m.user.id">
                {{ m.user.name }} ({{ m.role }})
              </option>
            </select>
            <!-- Recherche hors cercle -->
            <div class="mt-8" style="position:relative;">
              <input
                v-model="externalSearch"
                @input="searchExternalUsers"
                class="input input-sm mt-4"
                placeholder="Inviter quelqu'un hors du cercle (nom ou email)..."
              />
              <div v-if="externalResults.length" class="ext-dropdown">
                <div
                  v-for="u in externalResults"
                  :key="u.id"
                  class="ext-result"
                  @click="selectExternal(u)"
                >
                  <span class="font-semibold text-xs">{{ u.name }}</span>
                  <span class="text-xs text-muted ml-8">{{ u.email }}</span>
                </div>
              </div>
            </div>
            <div v-if="selectedExternalUser" class="mt-8 text-xs" style="background:var(--blue-50); border:1px solid var(--blue-200); padding:6px 10px; border-radius:6px;">
              <i class="fa-solid fa-circle-check" style="color:var(--teal-600)"></i> Animateur externe sélectionné : <strong>{{ selectedExternalUser.name }}</strong>
              <button type="button" class="btn-ghost btn-xs" style="margin-left:8px;" @click="clearExternal"><i class="fa-solid fa-xmark"></i></button>
            </div>
          </div>

          <div class="form-group">
            <label class="label">Titre *</label>
            <input v-model="form.title" class="input" placeholder="Ex: Adopter le télétravail hybride" required>
          </div>

          <div class="form-group">
            <label class="label">Contenu de la proposition *</label>
            <textarea v-model="form.content" class="textarea" rows="5" placeholder="Décrivez votre proposition en détail..." required></textarea>
          </div>

            <div class="form-group" style="flex:1">
              <label class="label">Catégories</label>
              <div class="categories-selector mt-8">
                 <div class="category-chips">
                    <div 
                      v-for="cat in categories" 
                      :key="cat.id"
                      class="category-chip"
                      :class="{ active: form.category_ids.includes(cat.id) }"
                      @click="toggleCategory(cat.id)"
                      :style="form.category_ids.includes(cat.id) ? { borderColor: cat.color_hex, background: cat.color_hex + '15', color: cat.color_hex } : {}"
                    >
                       <i :class="cat.icon || 'fa-solid fa-tag'" class="mr-6"></i>
                       {{ cat.name }}
                    </div>
                 </div>
              </div>
            </div>
            <div class="form-group" style="flex:1">
              <label class="label">Modèle</label>
              <select v-model="form.model_id" class="select">
                <option value="">Par défaut</option>
                <option v-for="m in models" :key="m.id" :value="m.id">{{ m.name }}</option>
              </select>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" @click="close">Annuler</button>
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              {{ submitting ? 'Création...' : 'Créer la décision' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const props = defineProps({ visible: Boolean });
const emit = defineEmits(['close', 'created']);
const router = useRouter();

const circles = ref([]);
const categories = ref([]);
const models = ref([]);
const circleMembers = ref([]);
const externalSearch = ref('');
const externalResults = ref([]);
const selectedExternalUser = ref(null);
const error = ref('');
const submitting = ref(false);

const form = ref({
  circle_id: '',
  animator_id: '',
  title: '',
  content: '',
  category_ids: [],
  model_id: '',
});

const toggleCategory = (id) => {
  const index = form.value.category_ids.indexOf(id);
  if (index === -1) {
    form.value.category_ids.push(id);
  } else {
    form.value.category_ids.splice(index, 1);
  }
};

let extSearchTimeout = null;

watch(() => form.value.circle_id, async (newId) => {
  if (newId) {
    try {
      const { data } = await axios.get(`/api/v1/circles/${newId}/members`);
      circleMembers.value = data.members || [];
      // form.animator_id = ''; // optional reset
    } catch (e) {
      circleMembers.value = [];
    }
  } else {
    circleMembers.value = [];
  }
});

onMounted(async () => {
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

const close = () => emit('close');

const searchExternalUsers = () => {
  clearTimeout(extSearchTimeout);
  if (externalSearch.value.length < 2) { externalResults.value = []; return; }
  extSearchTimeout = setTimeout(async () => {
    try {
      const { data } = await axios.get('/api/v1/users/search', { params: { q: externalSearch.value } });
      externalResults.value = data.users || [];
    } catch (e) {
      externalResults.value = [];
    }
  }, 300);
};

const selectExternal = (user) => {
  selectedExternalUser.value = user;
  form.value.animator_id = user.id;
  externalResults.value = [];
  externalSearch.value = '';
};

const clearExternal = () => {
  selectedExternalUser.value = null;
  form.value.animator_id = '';
};

const submit = async () => {
  submitting.value = true;
  error.value = '';
  try {
    const { data } = await axios.post(`/api/v1/circles/${form.value.circle_id}/decisions`, {
      title: form.value.title,
      content: form.value.content,
      animator_id: form.value.animator_id || undefined,
      category_ids: form.value.category_ids,
      model_id: form.value.model_id || undefined,
    });
    emit('created', data.decision);
    close();
    router.push({ name: 'DecisionDetail', params: { id: data.decision.id } });
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors de la création.';
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px;
}
.modal-card {
  background: white; border-radius: var(--radius-lg); width: 100%; max-width: 560px; box-shadow: var(--shadow-lg); overflow: hidden;
  animation: modalIn 0.2s ease;
}
@keyframes modalIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.modal-header {
  display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--gray-200);
}
.modal-title { font-size: 15px; font-weight: 600; color: var(--gray-900); }
.modal-body { padding: 20px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding-top: 16px; border-top: 1px solid var(--gray-100); margin-top: 8px; }
.form-row { display: flex; gap: 12px; }
@media (max-width: 500px) { .form-row { flex-direction: column; } }

.ext-dropdown {
  position: absolute; top: calc(100% + 2px); left: 0; right: 0; z-index: 200;
  background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-md);
  box-shadow: var(--shadow-md); max-height: 180px; overflow-y: auto;
}
.ext-result {
  padding: 8px 12px; cursor: pointer; border-bottom: 1px solid var(--gray-100); transition: background 0.1s;
}
.ext-result:last-child { border-bottom: none; }
.ext-result:hover { background: var(--blue-50); }
.mt-4 { margin-top: 4px; }
.mt-8 { margin-top: 8px; }
.ml-8 { margin-left: 8px; }
.btn-xs { padding: 2px 8px; font-size: 11px; cursor: pointer; }
</style>

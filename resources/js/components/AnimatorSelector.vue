<template>
  <div class="animator-selector">
    <!-- Affichage actuel + Bouton édition -->
    <div v-if="!editing" class="animator-display">
      <span class="text-xs text-muted">
        <span class="font-semibold text-gray-700">Animateur :</span>
        {{ currentAnimatorName || 'Non défini (Auteur par défaut)' }}
      </span>
      <button v-if="canEdit" class="btn btn-ghost btn-xs ml-8" @click="startEditing" title="Changer l'animateur"><i class="fa-solid fa-pen"></i></button>
    </div>

    <!-- Mode édition -->
    <div v-else class="animator-edit-panel card" style="padding:16px; margin-top:8px;">
      <div class="text-xs font-semibold text-gray-700 mb-12">Choisir ou rechercher un animateur</div>

      <!-- Membres du cercle -->
      <div class="mb-12">
        <label class="label">Membres du cercle</label>
        <select v-model="selectedId" class="select">
          <option value="">— Aucun / Auteur par défaut —</option>
          <option v-for="m in circleMembers" :key="m.user.id" :value="m.user.id">
            {{ m.user.name }} ({{ m.role }})
          </option>
        </select>
      </div>

      <!-- Recherche externe -->
      <div class="mb-12">
        <label class="label">Ou rechercher dans toute la plateforme</label>
        <div style="position:relative;">
          <input
            v-model="searchQuery"
            @input="searchUsers"
            class="input"
            placeholder="Nom ou email..."
            autocomplete="off"
          />
          <div v-if="searchResults.length" class="search-dropdown">
            <div
              v-for="u in searchResults"
              :key="u.id"
              class="search-result-item"
              @click="selectExternal(u)"
            >
              <div class="font-semibold text-xs">{{ u.name }}</div>
              <div class="text-xs text-muted">{{ u.email }}</div>
            </div>
          </div>
        </div>
        <div v-if="selectedExternalUser" class="mt-8 p-8 bg-blue-50 border border-blue-200 rounded text-xs">
          <i class="fa-solid fa-circle-check" style="color:var(--teal-600)"></i> Sélectionné hors cercle : <strong>{{ selectedExternalUser.name }}</strong>
          <button class="btn-ghost btn-xs ml-8" @click="clearExternal"><i class="fa-solid fa-xmark"></i></button>
        </div>
      </div>

      <!-- Actions -->
      <div style="display:flex; gap:8px; justify-content:flex-end;">
        <button class="btn btn-ghost btn-sm" @click="cancel">Annuler</button>
        <button class="btn btn-primary btn-sm" @click="save" :disabled="saving">
          {{ saving ? 'Enregistrement...' : 'Valider' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  decision: { type: Object, required: true },
  canEdit: { type: Boolean, default: false },
});
const emit = defineEmits(['updated']);

const editing = ref(false);
const saving = ref(false);
const circleMembers = ref([]);
const searchQuery = ref('');
const searchResults = ref([]);
const selectedId = ref('');
const selectedExternalUser = ref(null);

let searchTimeout = null;

const currentAnimatorName = computed(() => {
  const a = props.decision.participants?.find(p => p.role === 'animator');
  return a?.user?.name || null;
});

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/v1/circles/${props.decision.circle_id}/members`);
    circleMembers.value = data.members || [];
  } catch (e) {
    circleMembers.value = [];
  }
});

const startEditing = () => {
  // Pré-sélectionner l'animateur courant si présent
  const a = props.decision.participants?.find(p => p.role === 'animator');
  selectedId.value = a?.user_id || '';
  selectedExternalUser.value = null;
  searchQuery.value = '';
  searchResults.value = [];
  editing.value = true;
};

const cancel = () => {
  editing.value = false;
};

const searchUsers = () => {
  clearTimeout(searchTimeout);
  if (searchQuery.value.length < 2) { searchResults.value = []; return; }
  searchTimeout = setTimeout(async () => {
    try {
      const { data } = await axios.get('/api/v1/users/search', { params: { q: searchQuery.value } });
      searchResults.value = data.users || [];
    } catch (e) {
      searchResults.value = [];
    }
  }, 300);
};

const selectExternal = (user) => {
  selectedExternalUser.value = user;
  selectedId.value = user.id;
  searchResults.value = [];
  searchQuery.value = '';
};

const clearExternal = () => {
  selectedExternalUser.value = null;
  selectedId.value = '';
};

const save = async () => {
  saving.value = true;
  try {
    const animatorId = selectedExternalUser.value ? selectedExternalUser.value.id : selectedId.value;
    await axios.put(`/api/v1/decisions/${props.decision.id}/animator`, {
      animator_id: animatorId || null,
    });
    editing.value = false;
    emit('updated');
  } catch (e) {
    alert(e.response?.data?.message || "Erreur lors de la mise à jour de l'animateur.");
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
.animator-selector { position: relative; }
.animator-display { display: flex; align-items: center; flex-wrap: wrap; }
.ml-8 { margin-left: 8px; }
.mb-12 { margin-bottom: 12px; }
.mt-8 { margin-top: 8px; }
.p-8 { padding: 8px; }
.rounded { border-radius: var(--radius-md); }

.search-dropdown {
  position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 100;
  background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-md);
  box-shadow: var(--shadow-md); max-height: 200px; overflow-y: auto;
}
.search-result-item {
  padding: 8px 12px; cursor: pointer; border-bottom: 1px solid var(--gray-100);
  transition: background 0.1s;
}
.search-result-item:last-child { border-bottom: none; }
.search-result-item:hover { background: var(--blue-50); }

.btn-xs { padding: 2px 8px; font-size: 11px; }
.bg-blue-50 { background: var(--blue-50); }
.border-blue-200 { border-color: var(--blue-200); }
</style>

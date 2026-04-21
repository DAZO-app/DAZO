<template>
  <div v-if="visible" class="modal-overlay" @click.self="close">
    <div class="modal-card">
      <div class="modal-header">
        <span class="modal-title">Ajouter des membres</span>
        <button class="btn btn-ghost btn-icon" @click="close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-group" v-if="!circleId">
          <label class="label">Cercle de destination</label>
          <select v-model="selectedCircleId" class="select">
            <option value="">Sélectionner un cercle...</option>
            <option v-for="c in circles" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <div class="form-group" style="position:relative;">
            <label class="label">Rechercher des utilisateurs (Nom ou Email)</label>
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input 
                    v-model="searchQuery" 
                    @input="handleSearch"
                    class="input pl-32" 
                    placeholder="Ex: Jean Dupont ou jean@clic.fr..."
                >
            </div>
            
            <div v-if="searchResults.length" class="ajax-results">
                <div 
                    v-for="u in searchResults" 
                    :key="u.id" 
                    class="ajax-item"
                    :class="{ selected: isSelected(u.id) }"
                    @click="toggleSelection(u)"
                >
                    <div class="flex items-center gap-12">
                        <div class="avatar-mini">{{ u.name.charAt(0).toUpperCase() }}</div>
                        <div class="min-w-0">
                            <div class="text-strong text-xs truncate">{{ u.name }}</div>
                            <div class="text-xs text-muted truncate">{{ u.email }}</div>
                        </div>
                    </div>
                    <div class="check-box">
                        <i v-if="isSelected(u.id)" class="fa-solid fa-check"></i>
                    </div>
                </div>
            </div>
            <div v-else-if="searchQuery.length >= 2 && !loadingSearch" class="ajax-no-results">
                Aucun utilisateur trouvé.
            </div>
        </div>

        <div v-if="selectedUsers.length" class="selected-wrap mt-16">
            <div class="label mb-8">Utilisateurs sélectionnés ({{ selectedUsers.length }})</div>
            <div class="selected-tags">
                <div v-for="u in selectedUsers" :key="u.id" class="user-tag">
                    <span>{{ u.name }}</span>
                    <i class="fa-solid fa-xmark" @click="removeUser(u.id)"></i>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" @click="close">Annuler</button>
        <button 
            class="btn btn-primary" 
            :disabled="!selectedCircleId || !selectedUsers.length || submitting"
            @click="submit"
        >
            {{ submitting ? 'Ajout en cours...' : 'Confirmer l\'ajout' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { useCircleStore } from '../stores/circle';

const props = defineProps({
  visible: Boolean,
  circleId: { type: [String, Number], default: null }
});

const emit = defineEmits(['close', 'added']);
const circleStore = useCircleStore();

const circles = ref([]);
const selectedCircleId = ref(props.circleId || '');
const searchQuery = ref('');
const searchResults = ref([]);
const selectedUsers = ref([]);
const loadingSearch = ref(false);
const submitting = ref(false);

let searchTimeout = null;

watch(() => props.visible, (isVis) => {
    if (isVis) {
        selectedCircleId.value = props.circleId || '';
        searchQuery.value = '';
        searchResults.value = [];
        selectedUsers.value = [];
    }
});

onMounted(async () => {
    if (!circleStore.circles.length) {
        await circleStore.fetchCircles();
    }
    circles.value = circleStore.circles;
});

const handleSearch = () => {
    clearTimeout(searchTimeout);
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }

    loadingSearch.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const { data } = await axios.get('/api/v1/users/search', { params: { q: searchQuery.value } });
            searchResults.value = data.users || [];
        } catch (e) {
            searchResults.value = [];
        } finally {
            loadingSearch.value = false;
        }
    }, 300);
};

const isSelected = (id) => selectedUsers.value.some(u => u.id === id);

const toggleSelection = (user) => {
    if (isSelected(user.id)) {
        removeUser(user.id);
    } else {
        selectedUsers.value.push(user);
    }
};

const removeUser = (id) => {
    selectedUsers.value = selectedUsers.value.filter(u => u.id !== id);
};

const close = () => emit('close');

const submit = async () => {
    if (!selectedCircleId.value || !selectedUsers.length) return;
    
    submitting.value = true;
    try {
        await circleStore.addMembersToCircle(
            selectedCircleId.value, 
            selectedUsers.value.map(u => u.id)
        );
        emit('added');
        close();
    } catch (e) {
        alert(e.response?.data?.message || 'Erreur lors de l\'ajout des membres.');
    } finally {
        submitting.value = false;
    }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 16px;
}
.modal-card {
  background: white; border-radius: var(--radius-lg); width: 100%; max-width: 500px; box-shadow: var(--shadow-lg); overflow: hidden;
  animation: modalIn 0.2s ease;
}
@keyframes modalIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--gray-200); }
.modal-title { font-size: 15px; font-weight: 600; color: var(--gray-900); }
.modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; padding: 16px 20px; border-top: 1px solid var(--gray-100); }

.search-input-wrap { position: relative; }
.pl-32 { padding-left: 36px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--gray-400); font-size: 14px; }

.ajax-results {
    margin-top: 4px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); max-height: 200px; overflow-y: auto;
    background: white; box-shadow: var(--shadow-sm);
}
.ajax-item {
    display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; cursor: pointer; border-bottom: 1px solid var(--gray-50); transition: all 0.1s;
}
.ajax-item:last-child { border-bottom: none; }
.ajax-item:hover { background: var(--gray-50); }
.ajax-item.selected { background: var(--blue-50); }

.ajax-no-results { padding: 12px; text-align: center; font-size: 12px; color: var(--gray-500); }

.avatar-mini {
    width: 32px; height: 32px; border-radius: 50%; background: var(--blue-100); color: var(--blue-800);
    display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;
}

.check-box {
    width: 20px; height: 20px; border: 2px solid var(--gray-300); border-radius: 4px;
    display: flex; align-items: center; justify-content: center; color: white;
}
.selected .check-box { background: var(--teal-500); border-color: var(--teal-500); }

.selected-tags { display: flex; flex-wrap: wrap; gap: 8px; }
.user-tag {
    display: flex; align-items: center; gap: 6px; padding: 4px 10px; background: var(--blue-50);
    color: var(--blue-800); border: 1px solid var(--blue-200); border-radius: var(--radius-full); font-size: 12px; font-weight: 500;
}
.user-tag i { cursor: pointer; color: var(--blue-400); }
.user-tag i:hover { color: var(--red-600); }

.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>

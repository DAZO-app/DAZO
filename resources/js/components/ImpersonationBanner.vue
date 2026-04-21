<template>
  <div v-if="isVisible" class="impersonation-banner" :class="{ 'banner-admin': !isImpersonating }">
    <div class="impersonation-content">
      <span class="impersonation-icon">
        <i v-if="isImpersonating" class="fa-solid fa-user-secret"></i>
        <i v-else class="fa-solid fa-bolt"></i>
      </span>
      <span class="impersonation-text">
        <template v-if="isImpersonating">
          <strong>Mode Impersonation.</strong> Vous naviguez en tant que <strong>{{ user?.name ?? 'un utilisateur' }}</strong>.
        </template>
        <template v-else>
          <strong>Administration Rapide :</strong> Sélectionnez un utilisateur pour simuler son compte.
        </template>
      </span>
    </div>
    
    <div class="impersonation-actions">
      <select v-model="selectedUser" @change="switchImpersonation" class="impersonation-select" :disabled="isLoading">
        <option value="" disabled>{{ isImpersonating ? "Changer d'utilisateur..." : "Simuler un utilisateur..." }}</option>
        <option v-for="u in sortedUsers" :key="u.id" :value="u.id" :disabled="u.id === user?.id">
          {{ u.name }}
        </option>
      </select>

      <button v-if="isImpersonating" @click="stopImpersonating" class="btn btn-sm impersonation-btn" :disabled="isLoading">
        {{ isLoading ? '...' : 'Revenir à mon compte' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();

const isImpersonating = computed(() => authStore.isImpersonating);
const isSuperAdmin = computed(() => authStore.isSuperAdmin);
const isVisible = computed(() => isImpersonating.value || isSuperAdmin.value);

const user = computed(() => authStore.user);
const isLoading = ref(false);

const usersList = ref([]);
const selectedUser = ref('');

const sortedUsers = computed(() => {
  return [...usersList.value].sort((a, b) => a.name.localeCompare(b.name));
});

const loadUsersList = async () => {
  // We need a token to load users. 
  // If impersonating, use originalToken. If not, the current token (SuperAdmin) works automatically.
  const headers = {};
  if (isImpersonating.value && authStore.originalToken) {
    headers.Authorization = `Bearer ${authStore.originalToken}`;
  }

  try {
    const { data } = await axios.get('/api/v1/admin/users', { headers });
    usersList.value = data.users.filter(u => u.is_active);
  } catch (e) {
    console.error("Erreur chargement utilisateurs (Banner):", e);
  }
};

onMounted(() => {
  if (isVisible.value) loadUsersList();
});

watch(isVisible, (newVal) => {
  if (newVal) loadUsersList();
});

const switchImpersonation = async () => {
  if (!selectedUser.value) return;
  const targetId = selectedUser.value;
  selectedUser.value = '';
  
  isLoading.value = true;
  try {
    // Direct switch using authStore.impersonate (now handles switching correctly)
    await authStore.impersonate(targetId);
    router.push({ name: 'Dashboard' });
  } catch (e) {
    console.error(e);
    alert('Erreur lors du changement.');
  } finally {
    isLoading.value = false;
  }
};

const stopImpersonating = async () => {
  isLoading.value = true;
  try {
    await authStore.stopImpersonating();
    router.push({ name: 'AdminUsers' });
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.impersonation-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: var(--amber-600);
  background-image: repeating-linear-gradient(
    45deg, transparent, transparent 10px, rgba(0, 0, 0, 0.05) 10px, rgba(0, 0, 0, 0.05) 20px
  );
  color: white;
  padding: 6px 16px;
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px rgba(0,0,0,0.15);
}

.banner-admin {
  background-color: var(--blue-800);
  background-image: none;
  padding: 4px 16px; /* Slightly thinner when idle */
}

.impersonation-content { display: flex; align-items: center; gap: 12px; }
.impersonation-icon { font-size: 18px; opacity: 0.9; }
.impersonation-text { font-size: 12px; }

.impersonation-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.impersonation-select {
  font-size: 12px;
  padding: 3px 8px;
  border-radius: 4px;
  border: none;
  background: rgba(255, 255, 255, 0.9);
  color: var(--gray-900);
  outline: none;
  font-family: inherit;
  cursor: pointer;
  min-width: 180px;
}
.impersonation-select:hover {
  background: white;
}

.impersonation-btn {
  background: white;
  color: var(--gray-900);
  border: none;
  font-weight: 600;
  font-size: 11px;
  padding: 4px 10px;
}
.impersonation-btn:hover { background: var(--gray-100); }

@media (max-width: 768px) {
  .impersonation-banner { flex-direction: column; gap: 8px; padding: 10px; }
  .impersonation-select { width: 100%; min-width: unset; }
}
</style>

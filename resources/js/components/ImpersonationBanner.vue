<template>
  <div v-if="isVisible && !authStore.bannerHidden" class="impersonation-banner">
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

      <button @click="authStore.setBannerHidden(true)" class="btn btn-sm impersonation-btn" title="Masquer cette barre">
        Masquer
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const authStore = useAuthStore();

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
    await authStore.impersonate(targetId);
    // Full redirect to ensure all stores and layout are fresh
    window.location.href = '/';
  } catch (e) {
    console.error(e);
    const msg = e.response?.data?.message || 'Erreur lors du changement d’utilisateur.';
    alert(msg);
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
  background-color: #f97316; /* Orange 500/600 */
  background-image: repeating-linear-gradient(
    45deg, 
    transparent, 
    transparent 10px, 
    rgba(0, 0, 0, 0.08) 10px, 
    rgba(0, 0, 0, 0.08) 20px
  );
  color: white;
  padding: 8px 16px;
  position: sticky;
  top: 0;
  z-index: 9999;
  width: 100%;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  border-bottom: 2px solid rgba(0,0,0,0.1);
}

.impersonation-content { display: flex; align-items: center; gap: 12px; }
.impersonation-icon { font-size: 18px; opacity: 0.9; }
.impersonation-text { font-size: 12px; letter-spacing: 0.01em; }

.impersonation-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.impersonation-select {
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid rgba(0,0,0,0.1);
  background: white;
  color: var(--gray-900);
  outline: none;
  font-family: inherit;
  cursor: pointer;
  min-width: 200px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.impersonation-select:hover {
  border-color: rgba(0,0,0,0.2);
}

.impersonation-btn {
  background: rgba(0,0,0,0.15);
  color: white;
  border: 1px solid rgba(255,255,255,0.3);
  font-weight: 600;
  font-size: 11px;
  padding: 4px 12px;
  border-radius: 6px;
  transition: all 0.2s;
  cursor: pointer;
}
.impersonation-btn:hover { 
  background: rgba(0,0,0,0.25);
  border-color: rgba(255,255,255,0.5);
}

@media (max-width: 768px) {
  .impersonation-banner { flex-direction: column; gap: 8px; padding: 12px; text-align: center; }
  .impersonation-select { width: 100%; min-width: unset; }
}
</style>

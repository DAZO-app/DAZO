<template>
  <div v-if="isImpersonating" class="impersonation-banner">
    <div class="impersonation-content">
      <span class="impersonation-icon">🎭</span>
      <span class="impersonation-text">
        <strong>Mode Impersonation.</strong> Vous naviguez en tant que <strong>{{ user?.name ?? 'un utilisateur' }}</strong>.
      </span>
    </div>
    
    <div class="impersonation-actions">
      <select v-model="selectedUser" @change="switchImpersonation" class="impersonation-select" :disabled="isLoading">
        <option value="" disabled>Changer d'utilisateur...</option>
        <option v-for="u in usersList" :key="u.id" :value="u.id" :disabled="u.id === user?.id">
          {{ u.name }}
        </option>
      </select>

      <button @click="stopImpersonating" class="btn btn-sm impersonation-btn" :disabled="isLoading">
        {{ isLoading ? '...' : 'Revenir à l\'administration' }}
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
const user = computed(() => authStore.user);
const isLoading = ref(false);

const usersList = ref([]);
const selectedUser = ref('');

const loadUsersList = async () => {
  if (authStore.originalToken) {
    try {
      const { data } = await axios.get('/api/v1/admin/users', {
        headers: { Authorization: `Bearer ${authStore.originalToken}` }
      });
      usersList.value = data.users.filter(u => u.is_active);
    } catch (e) {
      console.error("Erreur chargement utilisateurs (Impersonation):", e);
    }
  }
};

onMounted(() => {
  if (isImpersonating.value) loadUsersList();
});

watch(isImpersonating, (newVal) => {
  if (newVal) loadUsersList();
});

const switchImpersonation = async () => {
  if (!selectedUser.value) return;
  const targetId = selectedUser.value;
  selectedUser.value = '';
  
  isLoading.value = true;
  try {
    // Stop gives back admin token
    await authStore.stopImpersonating();
    // Impersonate uses admin token
    await authStore.impersonate(targetId);
    // Force route to dashboard to reset view state
    router.push({ name: 'Dashboard' });
  } catch (e) {
    console.error(e);
    alert('Erreur lors du changement. Token expiré ?');
    await authStore.stopImpersonating();
  } finally {
    if(document.querySelector('.impersonation-banner')) {
        isLoading.value = false;
    }
  }
};

const stopImpersonating = async () => {
  isLoading.value = true;
  try {
    await authStore.stopImpersonating();
    router.push({ name: 'AdminUsers' });
  } finally {
    if(document.querySelector('.impersonation-banner')) {
        isLoading.value = false;
    }
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
  padding: 8px 16px;
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
}
.impersonation-content { display: flex; align-items: center; gap: 12px; }
.impersonation-icon { font-size: 20px; }
.impersonation-text { font-size: 13px; }

.impersonation-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.impersonation-select {
  font-size: 13px;
  padding: 4px 8px;
  border-radius: 4px;
  border: none;
  background: rgba(255, 255, 255, 0.9);
  color: var(--gray-900);
  outline: none;
  font-family: inherit;
  cursor: pointer;
  max-width: 200px;
}
.impersonation-select:hover {
  background: white;
}

.impersonation-btn {
  background: white;
  color: var(--gray-900);
  border: none;
  font-weight: 600;
}
.impersonation-btn:hover { background: var(--gray-100); }
</style>

<template>
  <main class="main">
    <div v-if="loading" class="p-24 text-center text-muted">Chargement...</div>
    <template v-else-if="circle">
      <div class="page-header">
        <div>
          <div class="page-title">{{ circle.name }}</div>
          <div class="page-subtitle">{{ circle.description }}</div>
        </div>
        <div class="page-actions">
          <span class="badge mr-8" :class="typeBadge(circle.type)">{{ circle.type }}</span>
          <button class="btn btn-primary btn-sm" @click="$router.push({ name: 'DecisionCreate', query: { circle_id: circle.id } })">
             + Nouvelle décision
          </button>
        </div>
      </div>

      <div class="page-body">
        <div class="card mb-16">
          <div class="card-header">
            <span class="card-title">Décisions dans ce cercle</span>
          </div>
          <div class="card-body" style="padding:0">
            <div v-if="decisions.length === 0" class="text-sm text-muted text-center" style="padding:16px 0;">Aucune décision dans ce cercle.</div>
            <div v-for="d in decisions" :key="d.id" class="decision-mini" @click="$router.push({ name: 'DecisionDetail', params: { id: d.id } })">
              <div class="decision-status-strip" :class="'strip-' + d.status"></div>
              <div class="role-bg-mini" :class="'role-' + getMyRole(d)" :title="getMyRole(d)">
                {{ getRolePicto(getMyRole(d)) }}
              </div>
              <div class="decision-title" style="flex:1">{{ d.title }}</div>
              <span class="badge" :class="statusBadge(d.status)">{{ d.status?.toUpperCase() }}</span>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">Membres ({{ members.length }})</span>
          </div>
          <div class="card-body">
            <div v-if="members.length === 0" class="text-sm text-muted text-center" style="padding:16px 0;">Aucun membre.</div>
            <div v-for="m in members" :key="m.id" class="member-row">
              <div class="avatar av-blue" style="width:28px;height:28px;font-size:10px;">
                {{ m.user?.name?.split(' ').map(p => p[0]).join('').toUpperCase().slice(0,2) }}
              </div>
              <div class="member-info">
                <div class="member-name">{{ m.user?.name }}</div>
                <div class="member-email">{{ m.user?.email }}</div>
              </div>
              <span class="badge badge-sm" :class="roleBadge(m.role)">{{ m.role }}</span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const route = useRoute();
const authStore = useAuthStore();
const circle = ref(null);
const members = ref([]);
const decisions = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/v1/circles/${route.params.id}`);
    circle.value = data.circle;
    members.value = data.circle?.members || [];
    const decRes = await axios.get(`/api/v1/circles/${route.params.id}/decisions`);
    decisions.value = decRes.data.decisions || [];
  } catch (e) { /* */ } finally { loading.value = false; }
});

const typeBadge = (t) => ({ open: 'badge-teal', closed: 'badge-red', observer_open: 'badge-blue' }[t] || 'badge-gray');
const roleBadge = (r) => ({ animator: 'badge-amber', member: 'badge-blue', observer: 'badge-gray' }[r] || 'badge-gray');
const statusBadge = (s) => ({ draft: 'badge-gray', clarification: 'badge-blue', reaction: 'badge-blue', objection: 'badge-amber', adopted: 'badge-teal' }[s] || 'badge-gray');

const getMyRole = (decision) => {
    if (!authStore.user || !decision.participants) return 'participant';
    const p = decision.participants.find(p => p.user_id === authStore.user.id);
    return p ? p.role : 'participant';
};

const getRolePicto = (role) => {
    const map = { author: '💡', animator: '🎭', participant: '👥', observer: '👁️' };
    return map[role] || '👥';
};
</script>

<style scoped>
.p-24 { padding: 24px; }
.member-row { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--gray-100); }
.member-row:last-child { border-bottom: none; }
.member-info { flex: 1; min-width: 0; }
.member-name { font-size: 13px; font-weight: 500; color: var(--gray-900); }
.member-email { font-size: 11px; color: var(--gray-400); }
.avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; flex-shrink: 0; }
.av-blue { background: var(--blue-100); color: var(--blue-800); }

.decision-mini {
  display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px;
  cursor: pointer; transition: background 0.1s; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 18px;
}
.decision-mini:last-child { border-bottom: none; }
.decision-mini:hover { background: var(--blue-50); }
.decision-title { font-size: 13px; font-weight: 600; color: var(--gray-800); }

.decision-status-strip {
  position: absolute; left: 0; top: 0; bottom: 0;
  width: 4px; flex-shrink: 0;
}
.strip-draft        { background: var(--gray-300); }
.strip-clarification { background: var(--amber-400); }
.strip-reaction     { background: var(--blue-400); }
.strip-objection    { background: var(--red-500); }
.strip-revision     { background: var(--orange-400); }
.strip-adopted      { background: var(--teal-500); }

.role-bg-mini {
  width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 13px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1.5px solid transparent;
  flex-shrink: 0;
}
.role-author { border-color: var(--blue-500); background: var(--blue-50); }
.role-animator { border-color: var(--amber-500); background: var(--amber-50); }
.role-participant { border-color: var(--teal-500); background: var(--teal-50); }
.role-observer { border-color: var(--gray-400); background: var(--gray-50); }
</style>

<template>
  <main class="main">
    <div v-if="loading" class="p-24 text-center text-muted">Chargement...</div>
    <template v-else-if="circle">
      <div class="page-body">
        <!-- HERO HEADER -->
        <div class="hero-card">
          <div class="hero-flex">
            <div>
              <div class="hero-title">
                {{ circle.name }} 
                <span class="text-xs opacity-60 font-normal">({{ members.length }} membres)</span>
              </div>
              <div class="hero-subtitle">{{ circle.description }}</div>
            </div>
            <div class="hero-action">
              <span class="badge mr-8" :class="typeBadge(circle.type)" style="background:rgba(255,255,255,0.2); color:white; border:none; vertical-align: middle;">{{ circle.type }}</span>
              <template v-if="canManage">
                 <button class="btn btn-ghost btn-sm text-white" @click="handleEdit" title="Modifier le cercle">
                   <i class="fa-solid fa-pen"></i>
                 </button>
                 <button class="btn btn-ghost btn-sm text-red" @click="handleDelete" title="Supprimer le cercle" style="color:var(--red-400)">
                   <i class="fa-solid fa-trash"></i>
                 </button>
              </template>
            </div>
          </div>
        </div>

        <div class="card mb-32">
          <div class="card-header card-header-sexy justify-between cursor-pointer" @click="decisionsExpanded = !decisionsExpanded">
            <span class="card-title">
              <i class="fa-solid fa-chevron-right mr-8 transition-transform" :class="{ 'rotate-90': decisionsExpanded }"></i>
              Décisions dans ce cercle
            </span>
            <button class="btn btn-primary btn-sm" @click.stop="$router.push({ name: 'DecisionCreate', query: { circle_id: circle.id } })">
               <i class="fa-solid fa-plus"></i> <span class="hide-mobile">Nouvelle décision</span>
            </button>
          </div>
          <div v-show="decisionsExpanded" class="card-body" style="padding:0">
            <EmptyState v-if="decisions.length === 0" message="Aucune décision dans ce cercle." />
            <DecisionListItem
              v-for="d in decisions" :key="d.id" :decision="d"
              @click="$router.push({ name: 'DecisionDetail', params: { id: d.id } })"
              @filter-circle="() => {}"
              @filter-category="$router.push({ name: 'DecisionList', query: { category: $event } })"
            />
          </div>
        </div>

        <div class="card">
          <div class="card-header card-header-sexy justify-between cursor-pointer" @click="membersExpanded = !membersExpanded">
            <span class="card-title">
              <i class="fa-solid fa-chevron-right mr-8 transition-transform" :class="{ 'rotate-90': membersExpanded }"></i>
              Membres
            </span>
            <button v-if="canManage" class="btn btn-secondary btn-sm" @click.stop="showAddMember = true">
              <i class="fa-solid fa-user-plus"></i> <span class="hide-mobile">Ajouter des membres</span>
            </button>
          </div>
          <div v-show="membersExpanded" class="card-body">
            <EmptyState v-if="members.length === 0" message="Aucun membre." />
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

    <AddMemberModal 
      v-if="circle"
      :visible="showAddMember" 
      :circle-id="circle.id"
      @close="showAddMember = false"
      @added="fetchData"
    />
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import DecisionListItem from '../components/DecisionListItem.vue';
import AddMemberModal from '../components/AddMemberModal.vue';
import EmptyState from '../components/EmptyState.vue';
import axios from 'axios';

const route = useRoute();
const authStore = useAuthStore();
const router = useRouter();
const circle = ref(null);
const members = ref([]);
const decisions = ref([]);
const loading = ref(true);
const showAddMember = ref(false);
const decisionsExpanded = ref(false);
const membersExpanded = ref(true);

const myRole = computed(() => {
  const m = members.value.find(m => m.user_id === authStore.user?.id);
  return m?.role || 'observer';
});

const canManage = computed(() => {
  return myRole.value === 'animator' || authStore.user?.role === 'admin' || authStore.user?.role === 'superadmin';
});

const fetchData = async () => {
  try {
    const { data } = await axios.get(`/api/v1/circles/${route.params.id}`);
    circle.value = data.data || data.circle;
    members.value = circle.value?.members || [];
    const decRes = await axios.get(`/api/v1/circles/${route.params.id}/decisions`);
    decisions.value = decRes.data.data || decRes.data.decisions || [];
  } catch (e) { /* */ } finally { loading.value = false; }
};

onMounted(fetchData);

const typeBadge = (t) => ({ open: 'badge-teal', closed: 'badge-red', observer_open: 'badge-blue' }[t] || 'badge-gray');
const roleBadge = (r) => ({ animator: 'badge-amber', member: 'badge-blue', observer: 'badge-gray' }[r] || 'badge-gray');
const statusBadge = (s) => ({ draft: 'badge-gray', clarification: 'badge-blue', reaction: 'badge-blue', objection: 'badge-amber', adopted: 'badge-teal' }[s] || 'badge-gray');

const handleEdit = () => {
    router.push({ name: 'AdminCircles' }); // For now redirect to admin view or we could open a modal
};

const handleDelete = async () => {
    if (confirm('Voulez-vous vraiment supprimer ce cercle ?')) {
        try {
            await axios.delete(`/api/v1/circles/${circle.value.id}`);
            router.push({ name: 'CircleList' });
        } catch (e) {
            alert('Erreur lors de la suppression.');
        }
    }
};
</script>

<style scoped>
.p-24 { padding: 24px; }
.member-row { display: flex; align-items: center; gap: 16px; padding: 14px 0; border-bottom: 1px solid var(--gray-100); }
.member-row:last-child { border-bottom: none; }
.member-info { flex: 1; min-width: 0; }
.member-name { font-size: 14px; font-weight: 600; color: var(--gray-900); margin-bottom: 2px; }
.member-email { font-size: 11px; color: var(--gray-500); }
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
.mb-32 { margin-bottom: 32px; }
.cursor-pointer { cursor: pointer; }
.transition-transform { transition: transform 0.2s ease; }
.rotate-90 { transform: rotate(90deg); }
.mr-8 { margin-right: 8px; }

@media (max-width: 600px) {
  .hide-mobile { display: none; }
}
</style>

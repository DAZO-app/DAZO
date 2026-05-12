<template>
  <main class="main">
    <div v-if="loading" class="p-24 text-center text-muted">Chargement...</div>
    <template v-else-if="circle">
      <div class="page-body">
        <!-- HERO HEADER -->
        <div class="hero-card relative">
          <div class="hero-flex">
            <div>
              <div class="hero-title mb-4">{{ circle.name }}</div>
              <div class="hero-subtitle">{{ circle.description || 'Cercle de décision digital.' }}</div>
              
              <div v-if="circle.parent" class="mt-16">
                <router-link :to="{ name: 'CircleDetail', params: { id: circle.parent_id } }" class="btn btn-white btn-outline btn-sm rounded-full bg-white/10 text-white border-white/20 hover:bg-white/20">
                  <i class="fa-solid fa-arrow-left mr-8"></i>
                  Cercle parent : <span class="font-bold ml-4">{{ circle.parent.name }}</span>
                </router-link>
              </div>
            </div>
            <div class="hero-action absolute" style="top: 20px; right: 20px;">
              <span class="badge" :class="circleTypeBadge(circle.type)" style="background:rgba(255,255,255,0.2); color:white; border:none; vertical-align: middle; min-width: 80px; justify-content: center;">{{ circleTypeLabel(circle.type) }}</span>
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

        <div class="card mb-32">
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

        <div v-if="!circle.is_sub_circle" class="card mb-32">
          <div class="card-header card-header-sexy justify-between cursor-pointer" @click="subcirclesExpanded = !subcirclesExpanded">
            <span class="card-title">
              <i class="fa-solid fa-chevron-right mr-8 transition-transform" :class="{ 'rotate-90': subcirclesExpanded }"></i>
              Cercles liés ({{ (circle.active_children?.length || 0) + (circle.archived_children?.length || 0) }})
            </span>
          </div>
          <div v-show="subcirclesExpanded" class="card-body" style="padding:16px; background:var(--gray-50)">
            <EmptyState v-if="(circle.active_children?.length || 0) + (circle.archived_children?.length || 0) === 0" message="Aucun cercle lié." />
            
            <div v-if="circle.active_children?.length" class="circles-grid mb-16">
              <div v-for="sub in circle.active_children" :key="sub.id" class="premium-card clickable-card relative" @click="$router.push({ name: 'CircleDetail', params: { id: sub.id } })">
                  <div class="pc-header pc-header-indigo relative" style="min-height: 70px;">
                      <div class="pc-header-icon"><i class="fa-solid fa-circle-nodes"></i></div>
                      <div class="pc-header-content">
                          <div class="pc-header-title text-xl">{{ sub.name }}</div>
                          <div class="text-xs font-normal italic opacity-80" style="margin-top: -2px">Lié à {{ circle.name }}</div>
                      </div>
                      <div class="pc-badge-wrap absolute" style="top: 10px; right: 10px;">
                        <span class="badge" :class="circleTypeBadge(sub.type)" style="font-size:10px; min-width:80px; justify-content: center; height: 22px;">{{ circleTypeLabel(sub.type) }}</span>
                      </div>
                  </div>
                  <div class="pc-body p-20 flex flex-col justify-between h-full">
                      <div class="circle-desc text-sm mb-20 line-clamp-2" style="color: var(--gray-900); font-weight: 500;">
                          {{ sub.description || 'Sous-cercle de décision digital.' }}
                      </div>
                      <div class="flex justify-between items-end">
                          <div class="flex items-center" style="gap: 24px;">
                              <div class="text-xs text-gray-400 flex items-center">
                                  <i class="fa-solid fa-users mr-8" style="font-size: 12px; width: 14px; text-align: center;"></i>
                                  {{ sub.members?.length || 0 }} membres
                              </div>
                          </div>
                          <button class="btn btn-primary btn-sm rounded-full px-16">
                              Explorer <i class="fa-solid fa-arrow-right ml-6"></i>
                          </button>
                      </div>
                  </div>
              </div>
            </div>

            <div v-if="circle.archived_children?.length">
              <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-8">Sous-cercles archivés</div>
              <div class="circles-grid" style="opacity:0.6">
                <div v-for="sub in circle.archived_children" :key="sub.id" class="premium-card clickable-card" @click="$router.push({ name: 'CircleDetail', params: { id: sub.id } })">
                    <div class="pc-header pc-header-blue" style="filter: grayscale(1);">
                        <div class="pc-header-icon"><i class="fa-solid fa-archive"></i></div>
                        <div class="pc-header-content">
                            <div class="pc-header-title">{{ sub.name }}</div>
                        </div>
                        <div class="pc-badge-wrap">
                          <span class="badge badge-gray" style="font-size:10px;">Archivé</span>
                        </div>
                    </div>
                </div>
              </div>
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
const subcirclesExpanded = ref(true);

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

// Watch for route changes (e.g. navigating from parent to sub-circle)
import { watch } from 'vue';
watch(() => route.params.id, (newId) => {
  if (newId) fetchData();
});

import { circleTypeLabel, circleTypeBadge } from '../utils/circleHelpers';
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
.circles-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
@media (min-width: 1000px) {
  .circles-grid { grid-template-columns: 1fr 1fr; }
}
@media (min-width: 1400px) {
  .circles-grid { grid-template-columns: repeat(3, 1fr); }
}
.clickable-card { cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; }
.clickable-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.pc-badge-wrap { margin-left: auto; z-index: 2; }

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

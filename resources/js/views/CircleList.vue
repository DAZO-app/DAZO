<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-title">Participez à la décision digitale</div>
        <div class="hero-subtitle">Rejoignez vos cercles de confiance et gérez vos communautés.</div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else-if="error" class="alert alert-error mb-16">{{ error }}</div>

      <div v-else class="circles-grid">
        <div v-for="circle in circles" :key="circle.id" class="premium-card clickable-card" @click="goTo(circle.id)">
            <div class="pc-header pc-header-light-blue">
                <div class="pc-header-icon"><i class="fa-solid fa-circle-nodes"></i></div>
                <div class="pc-header-content">
                    <div class="pc-header-title">{{ circle.name }} <span class="text-xs opacity-60 font-normal">({{ circle.members_count || 0 }} membres)</span></div>
                    <div class="pc-header-sub">{{ circle.description || 'Cercle de décision' }}</div>
                </div>
                <div class="pc-badge-wrap">
                  <span class="badge" :class="typeBadge(circle.type)">{{ circle.type }}</span>
                </div>
            </div>
            
            <div class="pc-body card-padding">
                <div class="flex justify-between items-center">
                    <div class="member-stack-wrap">
                        <div class="text-strong text-xs mb-8" style="color:var(--gray-500)">MEMBRES</div>
                        <div class="member-stack">
                            <div 
                                v-for="m in (circle.members || []).slice(0, 5)" 
                                :key="m.id" 
                                class="stack-avatar"
                                :title="m.user?.name"
                            >
                                {{ m.user?.name?.[0].toUpperCase() }}
                            </div>
                            <div v-if="circle.members_count > 5" class="stack-more">
                                +{{ (circle.members_count || 0) - (circle.members || []).slice(0,5).length }}
                            </div>
                            <div v-if="circle.members_count === 0" class="text-xs text-muted">
                                Aucun membre
                            </div>
                        </div>
                    </div>
                    <div class="circle-enter">
                        <span>Explorer</span> <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="circles.length === 0" class="text-center text-muted py-24">
          Vous n'êtes membre d'aucun cercle.
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCircleStore } from '../stores/circle';

const store = useCircleStore();
const router = useRouter();

const circles = computed(() => store.circles);
const loading = computed(() => store.loading);
const error = computed(() => store.error);

onMounted(() => store.fetchCircles());

const goTo = (id) => router.push({ name: 'CircleDetail', params: { id } });

const typeBadge = (type) => {
  const map = { open: 'badge-teal', closed: 'badge-red', observer_open: 'badge-blue' };
  return map[type] || 'badge-gray';
};
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.circles-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
@media (min-width: 900px) {
  .circles-grid { grid-template-columns: 1fr 1fr; }
}

.clickable-card { cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; }
.clickable-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }

.card-padding { padding: 20px; }

.pc-badge-wrap { margin-left: auto; z-index: 2; }

.member-stack { display: flex; align-items: center; }
.stack-avatar {
  width: 32px; height: 32px; border-radius: 50%; border: 2.5px solid white;
  background: var(--blue-100); color: var(--blue-800);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; margin-left: -10px;
  box-shadow: var(--shadow-sm);
}
.stack-avatar:first-child { margin-left: 0; }
.stack-more {
  width: 32px; height: 32px; border-radius: 50%; border: 2.5px solid white;
  background: var(--gray-100); color: var(--gray-600);
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; font-weight: 700; margin-left: -10px;
}

.circle-enter {
  display: flex; align-items: center; gap: 8px; color: var(--blue-600);
  font-weight: 600; font-size: 13px;
}

@media (max-width: 600px) {
  .hide-mobile { display: none; }
}
</style>

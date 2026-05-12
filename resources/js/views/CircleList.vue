<template>
  <main class="main">
    <div class="page-body">
      <!-- HERO HEADER -->
      <div class="hero-card">
        <div class="hero-flex flex-wrap items-center justify-between gap-16">
          <div style="flex: 1; min-width: 300px;">
            <div class="hero-title">Participez à la décision digitale</div>
            <div class="hero-subtitle">Rejoignez vos cercles de confiance et gérez vos communautés.</div>
          </div>
          <div class="hero-filter-wrap" style="min-width: 280px;">
            <select v-model="selectedParentId" class="select-premium-glass">
              <option value="">Toutes les organisations</option>
              <option v-for="c in topLevelCircles" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-24">Chargement...</div>
      <div v-else-if="error" class="alert alert-error mb-16">{{ error }}</div>

      <div v-else>
        <!-- TOP LEVEL CIRCLES -->
        <div class="circles-grid">
          <div v-for="circle in filteredTopLevel" :key="circle.id" class="premium-card clickable-card relative" @click="goTo(circle.id)">
              <div class="pc-header pc-header-blue relative" style="min-height: 70px;">
                  <div class="pc-header-icon"><i class="fa-solid fa-circle-nodes"></i></div>
                  <div class="pc-header-content">
                      <div class="pc-header-title text-xl">{{ circle.name }}</div>
                  </div>
                  <div class="pc-badge-wrap absolute" style="top: 10px; right: 10px;">
                    <span class="badge" :class="circleTypeBadge(circle.type)" style="font-size:10px; min-width:80px; justify-content: center; height: 22px;">
                      {{ circleTypeLabel(circle.type) }}
                    </span>
                  </div>
              </div>
              
              <div class="pc-body p-20 flex flex-col justify-between h-full">
                  <div class="circle-desc text-sm mb-20 line-clamp-2" style="color: var(--gray-900); font-weight: 500;">
                      {{ circle.description || 'Cercle de décision digital pour collaborer et décider ensemble.' }}
                  </div>
                  
                  <div class="flex justify-between items-end">
                      <div class="flex items-center" style="gap: 24px;">
                          <div class="text-xs text-gray-400 flex items-center">
                              <i class="fa-solid fa-users mr-8" style="font-size: 12px; width: 14px; text-align: center;"></i>
                              {{ circle.members_count || 0 }} membres
                          </div>
                          <div class="text-xs text-gray-400 flex items-center">
                              <i class="fa-solid fa-circle-nodes mr-8" style="font-size: 12px; width: 14px; text-align: center;"></i>
                              {{ circle.children_count || 0 }} cercles liés
                          </div>
                      </div>
                      
                      <button class="btn btn-primary btn-sm rounded-full px-16">
                          Explorer <i class="fa-solid fa-arrow-right ml-6"></i>
                      </button>
                  </div>
              </div>
          </div>
        </div>

        <!-- SUB CIRCLES -->
        <div v-if="filteredSubCircles.length > 0">
          <div class="section-divider mt-48 mb-24">
            <span class="divider-text">Sous-cercles liés</span>
          </div>
          <div class="circles-grid">
            <div v-for="circle in filteredSubCircles" :key="circle.id" class="premium-card clickable-card relative" @click="goTo(circle.id)">
                <div class="pc-header pc-header-indigo relative" style="min-height: 70px;">
                    <div class="pc-header-icon"><i class="fa-solid fa-circle-nodes"></i></div>
                    <div class="pc-header-content">
                        <div class="pc-header-title text-xl">{{ circle.name }}</div>
                        <div v-if="circle.parent" class="text-xs font-normal italic opacity-80" style="margin-top: -2px">
                          Lié à {{ circle.parent.name }}
                        </div>
                    </div>
                    <div class="pc-badge-wrap absolute" style="top: 10px; right: 10px;">
                      <span class="badge" :class="circleTypeBadge(circle.type)" style="font-size:10px; min-width:80px; justify-content: center; height: 22px;">
                        {{ circleTypeLabel(circle.type) }}
                      </span>
                    </div>
                </div>
                
                <div class="pc-body p-20 flex flex-col justify-between h-full">
                    <div class="circle-desc text-sm mb-20 line-clamp-2" style="color: var(--gray-900); font-weight: 500;">
                        {{ circle.description || 'Cercle de décision digital pour collaborer et décider ensemble.' }}
                    </div>
                    
                    <div class="flex justify-between items-end">
                        <div class="flex items-center" style="gap: 24px;">
                            <div class="text-xs text-gray-400 flex items-center">
                                <i class="fa-solid fa-users mr-8" style="font-size: 12px; width: 14px; text-align: center;"></i>
                                {{ circle.members_count || 0 }} membres
                            </div>
                        </div>
                        
                        <button class="btn btn-primary btn-sm rounded-full px-16">
                            Explorer <i class="fa-solid fa-arrow-right ml-6"></i>
                        </button>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="circles.length === 0" class="text-center text-muted py-24">
        Vous n'êtes membre d'aucun cercle.
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCircleStore } from '../stores/circle';

const store = useCircleStore();
const router = useRouter();

const circles = computed(() => store.circles);
const loading = computed(() => store.loading);
const error = computed(() => store.error);

const topLevelCircles = computed(() => circles.value.filter(c => !c.parent_id));
const subCircles = computed(() => circles.value.filter(c => c.parent_id));

const selectedParentId = ref('');
const filteredTopLevel = computed(() => {
  if (!selectedParentId.value) return topLevelCircles.value;
  return topLevelCircles.value.filter(c => c.id === selectedParentId.value);
});
const filteredSubCircles = computed(() => {
  if (!selectedParentId.value) return subCircles.value;
  return subCircles.value.filter(c => c.parent_id === selectedParentId.value);
});

onMounted(() => store.fetchCircles());

const goTo = (id) => router.push({ name: 'CircleDetail', params: { id } });

import { circleTypeLabel, circleTypeBadge } from '../utils/circleHelpers';
</script>

<style scoped>
.py-24 { padding: 24px 0; }
.circles-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
@media (min-width: 1000px) {
  .circles-grid { grid-template-columns: 1fr 1fr; }
}
@media (min-width: 1400px) {
  .circles-grid { grid-template-columns: repeat(3, 1fr); }
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

.section-divider {
  display: flex;
  align-items: center;
  gap: 16px;
}

@media (max-width: 1100px) {
  .hero-filter-wrap {
    width: 100%;
    margin-top: 8px;
  }
}

.section-divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: var(--gray-200);
}
.divider-text {
  font-size: 14px;
  font-weight: 700;
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.select-premium-glass {
  width: 100%;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  padding: 10px 16px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  outline: none;
  transition: all 0.2s;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 16px;
}
.select-premium-glass:hover {
  background-color: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}
.select-premium-glass option {
  background: #1e3a8a; /* Dark blue for consistency */
  color: white;
}
</style>

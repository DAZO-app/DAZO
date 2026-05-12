<template>
  <main class="main">
    <div class="page-body">
      <div class="hero-card">
        <div class="hero-flex">
          <div>
            <div class="hero-title">Base de données</div>
            <div class="hero-subtitle">Statistiques en temps réel et structure des données.</div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center text-muted py-48">
        <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-16"></i>
        <p>Analyse de la base de données...</p>
      </div>

      <div v-else class="animate-fade-in">
        <div class="grid-3 gap-24 mt-32">
          <div class="stat-card stat-card-blue">
             <div class="stat-icon"><i class="fa-solid fa-server"></i></div>
             <div class="stat-content">
                <div class="stat-label">Moteur</div>
                <div class="stat-value">{{ dbInfo.engine }}</div>
             </div>
          </div>
          <div class="stat-card stat-card-teal">
             <div class="stat-icon"><i class="fa-solid fa-weight-hanging"></i></div>
             <div class="stat-content">
                <div class="stat-label">Taille Totale</div>
                <div class="stat-value">{{ dbInfo.total_size }}</div>
             </div>
          </div>
          <div class="stat-card stat-card-amber">
             <div class="stat-icon"><i class="fa-solid fa-link"></i></div>
             <div class="stat-content">
                <div class="stat-label">Base Connectée</div>
                <div class="stat-value">{{ dbInfo.connection.database }}</div>
             </div>
          </div>
        </div>

        <div class="premium-card mt-32">
          <div class="pc-header pc-header-blue">
            <div class="pc-header-icon"><i class="fa-solid fa-table"></i></div>
            <div class="pc-header-content">
               <div class="pc-header-title">Liste des Tables</div>
               <div class="pc-header-sub">Volume et nombre d'entrées par table</div>
            </div>
          </div>
          <div class="pc-body">
            <div class="table-responsive">
              <table class="db-table">
                <thead>
                  <tr>
                    <th>Table</th>
                    <th>Lignes (Est.)</th>
                    <th>Taille</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="table in dbInfo.tables" :key="table.name">
                    <td class="font-bold font-mono text-sm">{{ table.name }}</td>
                    <td>{{ formatNumber(table.rows) }}</td>
                    <td><span class="badge badge-gray">{{ table.size }}</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const dbInfo = ref({
    engine: '',
    total_size: '',
    connection: {},
    tables: []
});

const fetchData = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/admin/tools/database');
        dbInfo.value = data;
    } catch (e) {
        console.error("Erreur lors du chargement des stats BDD", e);
    } finally {
        loading.value = false;
    }
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('fr-FR').format(num);
};

onMounted(fetchData);
</script>

<style scoped>
.stat-card {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 24px;
  border-radius: var(--radius-xl);
  color: white;
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(255,255,255,0.1);
  transition: transform 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-4px);
}
.stat-card::before {
  content: "";
  position: absolute;
  top: -50%;
  right: -20%;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%;
}

.stat-card-blue {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}
.stat-card-teal {
  background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
}
.stat-card-amber {
  background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
}

.stat-icon {
  width: 56px;
  height: 56px;
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(4px);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.stat-label {
  font-size: 11px;
  text-transform: uppercase;
  font-weight: 800;
  letter-spacing: 0.05em;
  opacity: 0.8;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 800;
  white-space: nowrap;
}

.db-table { width: 100%; border-collapse: collapse; }
.db-table th { text-align: left; padding: 12px 20px; font-size: 11px; text-transform: uppercase; color: var(--gray-500); background: var(--gray-50); border-bottom: 1px solid var(--gray-100); }
.db-table td { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
.db-table tr:hover { background: var(--gray-50); }
.badge-gray { background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
</style>

import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export const usePublicFrontStore = defineStore('publicFront', () => {
  const decisions   = ref([]);
  const loading     = ref(false);
  const filters     = ref({ search: '', status: '', category: '', circle: '', author: '', sort: 'created_desc' });
  const pagination  = ref({ current_page: 1, last_page: 1 });

  // Metadata from /front/meta
  const meta = ref({
    allowed_filters: [],
    circles: [],
    categories: [],
    statuses: [],
    authors: [],
  });
  const metaLoaded = ref(false);

  // ── Fetch meta ──────────────────────────────────────
  const fetchMeta = async () => {
    if (metaLoaded.value) return;
    try {
      const { data } = await axios.get('/api/v1/front/meta');
      meta.value = data;
      metaLoaded.value = true;
    } catch (e) {
      console.warn('publicFront: meta fetch failed', e);
    }
  };

  // ── Fetch decisions ──────────────────────────────────
  const fetchDecisions = async (page = 1) => {
    loading.value = true;
    try {
      const params = { page, ...filters.value };
      Object.keys(params).forEach(k => { if (!params[k] && params[k] !== 0) delete params[k]; });
      const { data } = await axios.get('/api/v1/front/decisions', { params });
      decisions.value = data.data;
      pagination.value = { current_page: data.current_page, last_page: data.last_page };
    } catch (e) {
      console.error('publicFront: fetch error', e);
    } finally {
      loading.value = false;
    }
  };

  // ── Navigation helpers ───────────────────────────────
  const decisionIds = () => decisions.value.map(d => d.id);

  const getNeighbours = (currentId) => {
    const ids = decisionIds();
    const idx = ids.indexOf(Number(currentId)) !== -1 ? ids.indexOf(Number(currentId)) : ids.indexOf(String(currentId));
    return {
      prev:  idx > 0             ? ids[idx - 1] : null,
      next:  idx < ids.length - 1 ? ids[idx + 1] : null,
      index: idx,
      total: ids.length,
    };
  };

  // ── Filter helpers ───────────────────────────────────
  const applyFilter = (key, value) => {
    filters.value[key] = value;
    fetchDecisions(1);
  };

  const isFilterAllowed = (key) => meta.value.allowed_filters.includes(key);

  // ── Navigate to list with filter ─────────────────────
  const goToListWithFilter = (key, value) => {
    filters.value[key] = String(value);
    // Router access needs to be done from the component, return flag
    fetchDecisions(1);
  };

  return {
    decisions, loading, filters, pagination,
    meta, metaLoaded,
    fetchMeta, fetchDecisions,
    getNeighbours, applyFilter, isFilterAllowed, goToListWithFilter,
  };
});

<template>
  <GlobalAlert />
  <router-view></router-view>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import GlobalAlert from './components/GlobalAlert.vue';
import { useConfigStore } from './stores/config';

const configStore = useConfigStore();

const predefinedPalettes = [
  { id: 'default', label: 'Défaut (Bleu)', colors: ['#5C66C5', '#f0f2fd', '#1a2060', '#c5caee'] },
  { id: 'red', label: 'Rouge Rubis', colors: ['#e11d48', '#fff1f2', '#881337', '#fecdd3'] },
  { id: 'yellow', label: 'Jaune Solaire', colors: ['#eab308', '#fefce8', '#713f12', '#fef08a'] },
  { id: 'green', label: 'Vert Forêt', colors: ['#16a34a', '#f0fdf4', '#14532d', '#bbf7d0'] },
  { id: 'purple', label: 'Violet Améthyste', colors: ['#9333ea', '#faf5ff', '#581c87', '#e9d5ff'] },
  { id: 'orange', label: 'Orange Corail', colors: ['#f97316', '#fff7ed', '#7c2d12', '#fed7aa'] },
  { id: 'teal', label: 'Bleu Canard', colors: ['#0d9488', '#f0fdfa', '#134e4a', '#99f6e4'] },
  { id: 'rose', label: 'Rose Poudré', colors: ['#f43f5e', '#fff1f2', '#881337', '#fecdd3'] },
  { id: 'indigo', label: 'Indigo Profond', colors: ['#4f46e5', '#eef2ff', '#312e81', '#c7d2fe'] },
  { id: 'gray', label: 'Monochrome Gris', colors: ['#4b5563', '#f9fafb', '#111827', '#e5e7eb'] },
  { id: 'dark', label: 'Mode Sombre', colors: ['#3b82f6', '#1f2937', '#111827', '#374151'] },
  { id: 'midnight', label: 'Nuit Bleue', colors: ['#3b82f6', '#1e293b', '#0f172a', '#475569'] },
  { id: 'forest_dark', label: 'Forêt Sombre', colors: ['#10b981', '#064e3b', '#022c22', '#065f46'] },
  { id: 'high_contrast', label: 'Contraste Élevé', colors: ['#000000', '#ffffff', '#000000', '#e5e7eb'] }
];

const applyTheme = () => {
  const mode = configStore.config.theme_public_palette_mode || 'default';
  const root = document.documentElement;

  // Reset base variables
  root.style.removeProperty('--gray-50');
  root.style.removeProperty('--gray-900');
  root.style.removeProperty('--white');
  document.body.style.background = '';
  document.body.style.color = '';

  if (mode === 'custom') {
    if (configStore.config.theme_public_primary) {
      root.style.setProperty('--blue-600', configStore.config.theme_public_primary);
      root.style.setProperty('--blue-700', configStore.config.theme_public_primary);
    }
    if (configStore.config.theme_public_secondary) {
      root.style.setProperty('--blue-800', configStore.config.theme_public_secondary);
      root.style.setProperty('--blue-900', configStore.config.theme_public_secondary);
    }
    if (configStore.config.theme_public_bg_main) {
      root.style.setProperty('--gray-50', configStore.config.theme_public_bg_main);
    }
    if (configStore.config.theme_public_text_main) {
      root.style.setProperty('--gray-900', configStore.config.theme_public_text_main);
      document.body.style.color = configStore.config.theme_public_text_main;
    }
    if (configStore.config.theme_public_bg_alt) {
      root.style.setProperty('--white', configStore.config.theme_public_bg_alt);
      document.body.style.background = configStore.config.theme_public_bg_alt;
    }
  } else {
    const palette = predefinedPalettes.find(p => p.id === mode);
    if (palette) {
      const [cPrimary, cBg, cDark, cLight] = palette.colors;

      root.style.setProperty('--blue-600', cPrimary);
      root.style.setProperty('--blue-700', cPrimary);
      root.style.setProperty('--blue-400', cLight);
      root.style.setProperty('--blue-200', cLight);
      root.style.setProperty('--blue-50', cBg);
      
      root.style.setProperty('--blue-800', cDark);
      root.style.setProperty('--blue-900', cDark);

      // Simple dark mode heuristics based on palette id
      if (mode.includes('dark') || mode === 'midnight') {
        root.style.setProperty('--gray-50', cBg); // card backgrounds
        root.style.setProperty('--white', cDark); // alternative backgrounds
        root.style.setProperty('--gray-900', '#f8fafc'); // text
        document.body.style.background = cBg;
        document.body.style.color = '#f8fafc';
      }
    }
  }
};

watch(() => configStore.config, () => {
  applyTheme();
}, { deep: true });

onMounted(() => {
  // If config is already loaded
  if (Object.keys(configStore.config).length > 0) {
    applyTheme();
  }
});
</script>

<style>
/* Scoped styles ou globaux qui ne sont pas dans dazo-theme.css */
</style>

<template>
  <div 
    class="floating-window"
    :style="windowStyle"
    @mousedown="bringToFront"
  >
    <!-- Header Draggable -->
    <div 
      class="fw-header"
      @mousedown="startDrag"
    >
      <div class="fw-title truncate pr-4" :title="title">
        <i class="fa-solid fa-paperclip mr-8 text-indigo-200"></i>
        {{ title }}
      </div>
      
      <div class="fw-actions flex items-center gap-8">
        <!-- Bouton Télécharger -->
        <!-- Bouton Télécharger -->
        <a :href="url" target="_blank" download class="fw-btn text-white opacity-70 hover:opacity-100" title="Télécharger / Ouvrir dans un nouvel onglet" @mousedown.stop>
          <i class="fa-solid fa-download"></i>
        </a>
        <!-- Bouton Maximiser -->
        <button class="fw-btn text-white opacity-70 hover:opacity-100" @mousedown.stop @click="toggleMaximize" :title="isMaximized ? 'Restaurer' : 'Agrandir'">
          <i class="fa-solid" :class="isMaximized ? 'fa-compress' : 'fa-expand'"></i>
        </button>
        <!-- Bouton Fermer -->
        <button class="fw-btn text-white opacity-70 hover:opacity-100" @mousedown.stop @click="$emit('close', id)" title="Fermer">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>
    </div>

    <!-- Contenu -->
    <div class="fw-content">
      <!-- Si c'est une image -->
      <img v-if="isImage" :src="url" :alt="title" class="fw-image" @dragstart.prevent />
      
      <!-- Si c'est un PDF ou autre document -->
      <iframe v-else :src="iframeSrc" class="fw-iframe" frameborder="0"></iframe>
      
      <!-- Message pour les documents qui pourraient ne pas s'afficher -->
      <div v-if="!isImage && !isPdf" class="fw-fallback-msg">
        <p class="text-sm text-gray-500 mb-8"><i class="fa-solid fa-circle-info mr-4"></i> Si le document ne s'affiche pas, vous pouvez le télécharger.</p>
        <a :href="url" target="_blank" download class="btn btn-sm btn-primary">Télécharger le document</a>
      </div>
    </div>

    <!-- Poignée de redimensionnement -->
    <div class="fw-resizer" @mousedown="startResize"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  id: { type: [String, Number], required: true },
  title: { type: String, default: 'Document' },
  url: { type: String, required: true },
  initialX: { type: Number, default: 100 },
  initialY: { type: Number, default: 100 },
  initialWidth: { type: Number, default: 600 },
  initialHeight: { type: Number, default: 450 },
  zIndex: { type: Number, default: 100 },
});

const emit = defineEmits(['close', 'focus']);

// État de position et taille
const x = ref(props.initialX);
const y = ref(props.initialY);
const width = ref(props.initialWidth);
const height = ref(props.initialHeight);

// État de déplacement/redimensionnement
const isDragging = ref(false);
const isResizing = ref(false);
const startX = ref(0);
const startY = ref(0);
const startWidth = ref(0);
const startHeight = ref(0);
const initialMouseX = ref(0);
const initialMouseY = ref(0);

const extension = computed(() => {
  if (!props.url) return '';
  const parts = props.url.split('?')[0].split('.');
  return parts.length > 1 ? parts.pop().toLowerCase() : '';
});

const isImage = computed(() => {
  return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension.value);
});

const isPdf = computed(() => {
  return extension.value === 'pdf';
});

// Par défaut, on utilise l'URL. Si c'est public, on pourrait ajouter un viewer Google/Office,
// mais en local on met juste l'URL. Le navigateur gère le PDF, et force le download pour Office.
const iframeSrc = computed(() => {
  return props.url;
});

const isMaximized = ref(false);

const toggleMaximize = () => {
  isMaximized.value = !isMaximized.value;
  bringToFront();
};

const windowStyle = computed(() => {
  if (isMaximized.value) {
    return {
      top: 0,
      left: 0,
      width: '100%',
      height: '100%',
      zIndex: props.zIndex,
      transform: 'none',
      borderRadius: '0',
    };
  }
  return {
    transform: `translate(${x.value}px, ${y.value}px)`,
    width: `${width.value}px`,
    height: `${height.value}px`,
    zIndex: props.zIndex,
  };
});

// Actions
const bringToFront = () => {
  emit('focus', props.id);
};

// Dragging Logic
const startDrag = (e) => {
  if (isMaximized.value) return;
  bringToFront();
  isDragging.value = true;
  initialMouseX.value = e.clientX;
  initialMouseY.value = e.clientY;
  startX.value = x.value;
  startY.value = y.value;
  
  // Prevent iframe from capturing mouse events during drag
  document.body.classList.add('fw-dragging');
  
  window.addEventListener('mousemove', handleDrag);
  window.addEventListener('mouseup', stopDrag);
};

const handleDrag = (e) => {
  if (!isDragging.value) return;
  const dx = e.clientX - initialMouseX.value;
  const dy = e.clientY - initialMouseY.value;
  x.value = startX.value + dx;
  y.value = startY.value + dy;
};

const stopDrag = () => {
  isDragging.value = false;
  document.body.classList.remove('fw-dragging');
  window.removeEventListener('mousemove', handleDrag);
  window.removeEventListener('mouseup', stopDrag);
};

// Resizing Logic
const startResize = (e) => {
  if (isMaximized.value) return;
  bringToFront();
  isResizing.value = true;
  initialMouseX.value = e.clientX;
  initialMouseY.value = e.clientY;
  startWidth.value = width.value;
  startHeight.value = height.value;
  
  // Prevent iframe from capturing mouse events during resize
  document.body.classList.add('fw-dragging');
  
  window.addEventListener('mousemove', handleResize);
  window.addEventListener('mouseup', stopResize);
  e.preventDefault(); // prevent text selection
};

const handleResize = (e) => {
  if (!isResizing.value) return;
  const dx = e.clientX - initialMouseX.value;
  const dy = e.clientY - initialMouseY.value;
  
  // Min width/height constraints
  width.value = Math.max(300, startWidth.value + dx);
  height.value = Math.max(200, startHeight.value + dy);
};

const stopResize = () => {
  isResizing.value = false;
  document.body.classList.remove('fw-dragging');
  window.removeEventListener('mousemove', handleResize);
  window.removeEventListener('mouseup', stopResize);
};

// Méthode publique pour centrer la fenêtre (utilisée par le parent)
const centerWindow = () => {
  const vW = window.innerWidth;
  const vH = window.innerHeight;
  x.value = Math.max(0, (vW - width.value) / 2);
  y.value = Math.max(0, (vH - height.value) / 2);
};

defineExpose({ centerWindow });

onBeforeUnmount(() => {
  stopDrag();
  stopResize();
});
</script>

<style scoped>
.floating-window {
  position: fixed;
  top: 0;
  left: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  will-change: transform, width, height;
}

.fw-header {
  height: 40px;
  background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  cursor: grab;
  user-select: none;
  flex-shrink: 0;
}

.fw-header:active {
  cursor: grabbing;
}

.fw-title {
  font-weight: 600;
  font-size: 13px;
  letter-spacing: 0.5px;
}

.fw-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: opacity 0.2s;
}

.fw-content {
  flex: 1;
  position: relative;
  overflow: auto;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
}

.fw-image {
  max-width: 100%;
  height: auto;
  margin: auto;
  display: block;
}

.fw-iframe {
  width: 100%;
  height: 100%;
  flex: 1;
  background: white;
}

.fw-fallback-msg {
  padding: 16px;
  text-align: center;
  background: white;
  border-top: 1px solid #e2e8f0;
}

.fw-resizer {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 16px;
  height: 16px;
  cursor: nwse-resize;
  background: linear-gradient(135deg, transparent 50%, rgba(0,0,0,0.1) 50%);
  border-bottom-right-radius: 8px;
}
</style>

<style>
/* Classe globale injectée sur le body pendant le drag/resize pour éviter les interférences iframe */
body.fw-dragging iframe {
  pointer-events: none !important;
}
</style>

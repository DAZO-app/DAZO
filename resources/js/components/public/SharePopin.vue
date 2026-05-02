<template>
  <div class="popin-center-overlay" @click="$emit('close')">
    <div class="popin-share-premium" @click.stop>
      <div class="premium-share-header">
        <div class="header-icon-wrapper">
          <i class="fa-solid fa-share-nodes"></i>
        </div>
        <div class="header-text">
          <h3>Partager la décision</h3>
          <span>Diffusez cette proposition</span>
        </div>
        <button class="close-popin-btn" @click="$emit('close')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="premium-share-content">
        <!-- Stats Row -->
        <div class="share-stats">
          <div class="stat-item">
            <div class="stat-value">{{ shareCount }}</div>
            <div class="stat-label">Partages</div>
          </div>
        </div>

        <div class="share-info-preview">
          <div class="preview-title">{{ title }}</div>
          <div class="preview-desc" v-if="description">{{ description }}</div>
        </div>

        <div class="social-grid">
          <ShareNetwork
            v-for="network in networks"
            :key="network.network"
            :network="network.network"
            :url="shareUrl"
            :title="title"
            :description="description"
            class="social-button"
            @open="onShare"
          >
            <div class="social-icon" :style="{ backgroundColor: network.color }">
              <i :class="network.icon"></i>
            </div>
            <span>{{ network.name }}</span>
          </ShareNetwork>
        </div>

        <div class="divider">
          <span>OU</span>
        </div>

        <div class="direct-actions">
          <div class="input-copy-group">
            <input type="text" readonly :value="shareUrl" class="share-input">
            <button class="btn-copy" @click="copyLink">
              <i class="fa-solid" :class="copied ? 'fa-check text-green' : 'fa-copy'"></i>
              {{ copied ? 'Copié !' : 'Copier' }}
            </button>
          </div>
          
          <a :href="emailLink" class="btn-email" @click="onShare">
            <i class="fa-solid fa-envelope mr-8"></i> Envoyer par email
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  url: { type: String, required: true },
  title: { type: String, required: true },
  description: { type: String, default: '' },
  shareCount: { type: Number, default: 0 }
});

const emit = defineEmits(['close', 'shared']);

const copied = ref(false);

const shareUrl = computed(() => props.url || window.location.href);

const networks = [
  { network: 'facebook', name: 'Facebook', icon: 'fa-brands fa-facebook-f', color: '#1877f2' },
  { network: 'twitter', name: 'X / Twitter', icon: 'fa-brands fa-x-twitter', color: '#000000' },
  { network: 'linkedin', name: 'LinkedIn', icon: 'fa-brands fa-linkedin-in', color: '#0a66c2' },
  { network: 'whatsapp', name: 'WhatsApp', icon: 'fa-brands fa-whatsapp', color: '#25d366' },
];

const emailLink = computed(() => {
  return `mailto:?subject=${encodeURIComponent(props.title)}&body=${encodeURIComponent(props.description + '\n\n' + shareUrl.value)}`;
});

const onShare = () => {
  emit('shared');
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(shareUrl.value);
    copied.value = true;
    emit('shared');
    setTimeout(() => { copied.value = false; }, 2000);
  } catch (err) {
    console.error('Failed to copy!', err);
  }
};
</script>

<style scoped>
.popin-center-overlay {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(15, 23, 42, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2500;
  backdrop-filter: blur(8px);
}

.popin-share-premium {
  width: 420px;
  background: white;
  border-radius: 24px;
  box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.4);
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.8);
}

.premium-share-header {
  padding: 24px;
  background: linear-gradient(to right, var(--indigo-600), var(--indigo-800));
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  color: white;
}

.header-icon-wrapper {
  width: 44px;
  height: 44px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.header-text h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.header-text span {
  font-size: 12px;
  opacity: 0.8;
  font-weight: 500;
}

.close-popin-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  color: white;
  opacity: 0.5;
  cursor: pointer;
  padding: 8px;
}

.premium-share-content {
  padding: 24px;
  background: #f8fafc;
}

.share-stats {
  display: flex;
  justify-content: center;
  margin-bottom: 24px;
}

.stat-item {
  text-align: center;
  background: white;
  padding: 12px 24px;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  border: 1px solid var(--gray-100);
}

.stat-value {
  font-size: 24px;
  font-weight: 900;
  color: var(--indigo-600);
}

.stat-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--gray-400);
  letter-spacing: 0.05em;
}

.share-info-preview {
  background: white;
  padding: 16px;
  border-radius: 16px;
  margin-bottom: 24px;
  border: 1px solid var(--gray-100);
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.preview-title {
  font-weight: 700;
  font-size: 14px;
  color: var(--gray-900);
  margin-bottom: 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.preview-desc {
  font-size: 12px;
  color: var(--gray-500);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.social-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 24px;
}

.social-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: transform 0.2s;
}

.social-button:hover {
  transform: translateY(-4px);
}

.social-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
  box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}

.social-button span {
  font-size: 11px;
  font-weight: 600;
  color: var(--gray-600);
}

.divider {
  display: flex;
  align-items: center;
  margin: 24px 0;
  color: var(--gray-300);
}

.divider::before, .divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: var(--gray-200);
}

.divider span {
  padding: 0 12px;
  font-size: 10px;
  font-weight: 800;
}

.input-copy-group {
  display: flex;
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 12px;
}

.share-input {
  flex: 1;
  border: none;
  background: none;
  padding: 10px 14px;
  font-size: 13px;
  color: var(--gray-600);
  outline: none;
}

.btn-copy {
  background: var(--gray-50);
  border: none;
  border-left: 1px solid var(--gray-200);
  padding: 0 16px;
  font-size: 12px;
  font-weight: 700;
  color: var(--indigo-600);
  cursor: pointer;
  transition: background 0.2s;
}

.btn-copy:hover {
  background: var(--indigo-50);
}

.btn-email {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 12px;
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 12px;
  color: var(--gray-700);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-email:hover {
  border-color: var(--indigo-300);
  background: var(--indigo-50);
  color: var(--indigo-700);
}

.text-green { color: #10b981; }
</style>

<template>
  <div class="recaptcha-wrapper" v-if="siteKey">
    <div ref="recaptchaContainer"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  siteKey: {
    type: String,
    required: false,
    default: ''
  }
});

const emit = defineEmits(['verify', 'expire', 'error']);

const recaptchaContainer = ref(null);
let widgetId = null;

const renderRecaptcha = () => {
  if (window.grecaptcha && window.grecaptcha.render && recaptchaContainer.value && props.siteKey) {
    // Prevent double rendering
    if (recaptchaContainer.value.innerHTML !== '') return;
    
    try {
      widgetId = window.grecaptcha.render(recaptchaContainer.value, {
        sitekey: props.siteKey,
        callback: (response) => emit('verify', response),
        'expired-callback': () => emit('expire'),
        'error-callback': () => emit('error')
      });
    } catch (e) {
      console.error('Error rendering reCAPTCHA', e);
    }
  }
};

const loadRecaptchaScript = () => {
  if (!props.siteKey) return;

  if (window.grecaptcha) {
    renderRecaptcha();
    return;
  }

  if (!document.getElementById('recaptcha-script')) {
    const script = document.createElement('script');
    script.id = 'recaptcha-script';
    script.src = 'https://www.google.com/recaptcha/api.js?render=explicit';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);

    // Poll until loaded
    const checkInterval = setInterval(() => {
      if (window.grecaptcha && window.grecaptcha.render) {
        clearInterval(checkInterval);
        renderRecaptcha();
      }
    }, 100);
  } else {
    // Script is loading
    const checkInterval = setInterval(() => {
      if (window.grecaptcha && window.grecaptcha.render) {
        clearInterval(checkInterval);
        renderRecaptcha();
      }
    }, 100);
  }
};

onMounted(() => {
  loadRecaptchaScript();
});

watch(() => props.siteKey, (newVal) => {
  if (newVal) {
    loadRecaptchaScript();
  }
});

onUnmounted(() => {
  if (widgetId !== null && window.grecaptcha) {
    window.grecaptcha.reset(widgetId);
  }
});
</script>

<style scoped>
.recaptcha-wrapper {
  margin-bottom: 16px;
  display: flex;
  justify-content: center;
}
</style>

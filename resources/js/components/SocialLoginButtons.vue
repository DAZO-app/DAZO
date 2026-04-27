<template>
  <div class="social-login-section">
    <div class="social-divider">
      <span class="social-divider-line"></span>
      <span class="social-divider-text">{{ label }}</span>
      <span class="social-divider-line"></span>
    </div>

    <div class="social-buttons-grid">
      <button
        v-for="provider in enabledProviders"
        :key="provider.key"
        class="social-btn"
        :class="'social-btn--' + provider.key"
        :disabled="loadingProvider === provider.key"
        @click="handleSocialLogin(provider.key)"
      >
        <span class="social-btn-icon" v-html="provider.icon"></span>
        <span class="social-btn-label">{{ provider.label }}</span>
        <i v-if="loadingProvider === provider.key" class="fa-solid fa-spinner fa-spin social-btn-spinner"></i>
      </button>
    </div>

    <div v-if="error" class="alert alert-error mt-12" style="font-size: 12px;">
      <i class="fa-solid fa-circle-exclamation"></i>
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  label: {
    type: String,
    default: 'ou continuer avec'
  },
  invitationToken: {
    type: String,
    default: null
  }
});

const loadingProvider = ref(null);
const error = ref('');

const allProviders = [
  {
    key: 'google',
    label: 'Google',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>'
  },
  {
    key: 'github',
    label: 'GitHub',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>'
  },
  {
    key: 'microsoft',
    label: 'Microsoft',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><rect fill="#F25022" x="1" y="1" width="10" height="10"/><rect fill="#7FBA00" x="13" y="1" width="10" height="10"/><rect fill="#00A4EF" x="1" y="13" width="10" height="10"/><rect fill="#FFB900" x="13" y="13" width="10" height="10"/></svg>'
  },
  {
    key: 'facebook',
    label: 'Facebook',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
  },
  {
    key: 'linkedin-openid',
    label: 'LinkedIn',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><path fill="#0A66C2" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>'
  },
  {
    key: 'gitlab',
    label: 'GitLab',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><path fill="#E24329" d="m12 22.167 4.05-12.457H7.95z"/><path fill="#FC6D26" d="M12 22.167 7.95 9.71H1.514z"/><path fill="#FCA326" d="M1.514 9.71.047 14.223a1 1 0 0 0 .363 1.118L12 22.167z"/><path fill="#E24329" d="M1.514 9.71h6.436L5.263 1.667a.5.5 0 0 0-.952 0z"/><path fill="#FC6D26" d="M12 22.167 16.05 9.71h6.436z"/><path fill="#FCA326" d="m22.486 9.71 1.467 4.513a1 1 0 0 1-.363 1.118L12 22.167z"/><path fill="#E24329" d="M22.486 9.71H16.05l2.687-8.043a.5.5 0 0 1 .952 0z"/></svg>'
  },
  {
    key: 'twitter',
    label: 'X',
    icon: '<svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
  },
  {
    key: 'apple',
    label: 'Apple',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>'
  },
  {
    key: 'franceconnect',
    label: 'FranceConnect',
    icon: '<svg viewBox="0 0 24 24" width="18" height="18"><rect fill="#000091" width="8" height="24"/><rect fill="#fff" x="8" width="8" height="24"/><rect fill="#E1000F" x="16" width="8" height="24"/></svg>'
  },
];

// Only show providers that have been configured (have a client_id in env)
// For now, show all — the backend will return an error if not configured
const enabledProviders = computed(() => allProviders);

const handleSocialLogin = async (providerKey) => {
  loadingProvider.value = providerKey;
  error.value = '';

  try {
    const params = {};
    if (props.invitationToken) {
      params.invitation_token = props.invitationToken;
    }

    const { data } = await axios.get(`/api/v1/auth/social/${providerKey}/redirect`, { params });
    
    // Redirect the browser to the OAuth provider
    window.location.href = data.url;
  } catch (e) {
    error.value = e.response?.data?.message || 'Impossible de se connecter avec ce fournisseur.';
    loadingProvider.value = null;
  }
};
</script>

<style scoped>
.social-login-section {
  margin-top: 24px;
}

.social-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.social-divider-line {
  flex: 1;
  height: 1px;
  background: var(--gray-200);
}

.social-divider-text {
  font-size: 11px;
  color: var(--gray-400);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  white-space: nowrap;
}

.social-buttons-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 12px;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  background: white;
  color: var(--gray-700);
  font-size: 12px;
  font-weight: 500;
  font-family: var(--font-sans);
  cursor: pointer;
  transition: all 0.15s;
  line-height: 1;
}

.social-btn:hover:not(:disabled) {
  border-color: var(--gray-300);
  background: var(--gray-50);
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  transform: translateY(-1px);
}

.social-btn:active:not(:disabled) {
  transform: translateY(0);
}

.social-btn:disabled {
  opacity: 0.7;
  cursor: wait;
}

.social-btn-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

.social-btn-label {
  flex: 1;
  text-align: left;
}

.social-btn-spinner {
  font-size: 10px;
  color: var(--gray-400);
}

/* Provider-specific hover colors */
.social-btn--google:hover:not(:disabled) { border-color: #4285F4; }
.social-btn--github:hover:not(:disabled) { border-color: #333; }
.social-btn--microsoft:hover:not(:disabled) { border-color: #00A4EF; }
.social-btn--facebook:hover:not(:disabled) { border-color: #1877F2; }
.social-btn--linkedin-openid:hover:not(:disabled) { border-color: #0A66C2; }
.social-btn--gitlab:hover:not(:disabled) { border-color: #FC6D26; }
.social-btn--twitter:hover:not(:disabled) { border-color: #1DA1F2; }
.social-btn--apple:hover:not(:disabled) { border-color: #000; }

/* FranceConnect: special full-width styling */
.social-btn--franceconnect {
  grid-column: 1 / -1;
  background: #000091;
  color: white;
  border-color: #000091;
  font-weight: 700;
  padding: 12px 16px;
  font-size: 13px;
}

.social-btn--franceconnect:hover:not(:disabled) {
  background: #0000b8;
  border-color: #0000b8;
  color: white;
}

.social-btn--franceconnect .social-btn-icon svg rect:nth-child(2) {
  fill: #fff;
}

@media (max-width: 360px) {
  .social-buttons-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<template>
  <div v-if="isOpen" class="modal-backdrop" @click.self="close">
    <div class="modal-content premium-card shadow-2xl">
      <div class="pc-header pc-header-blue">
        <div class="pc-header-icon"><i class="fa-solid fa-envelope"></i></div>
        <div class="pc-header-title">Contacter un administrateur</div>
        <button class="pc-header-action" @click="close">
          <i class="fa-solid fa-times"></i>
        </button>
      </div>

      <div class="pc-body p-24">
        <div v-if="loadingAdmins" class="text-center py-24">
          <i class="fa-solid fa-spinner fa-spin text-blue-500"></i>
          <p class="text-xs text-muted mt-8">Chargement des administrateurs...</p>
        </div>

        <form v-else @submit.prevent="submit">
          <div class="form-group mb-20">
            <label class="label mb-8">Administrateur destinataire</label>
            <select v-model="form.admin_id" class="input" required>
              <option value="" disabled>Choisir un administrateur...</option>
              <option v-for="admin in admins" :key="admin.id" :value="admin.id">
                {{ admin.name }} ({{ admin.email }})
              </option>
            </select>
          </div>

          <div class="form-group mb-20">
            <label class="label mb-8">Sujet</label>
            <input v-model="form.subject" class="input" placeholder="Ex: Problème d'accès, Question sur les cercles..." required>
          </div>

          <div class="form-group mb-24">
            <label class="label mb-8">Votre message</label>
            <textarea 
              v-model="form.message" 
              class="input min-h-[120px] py-12" 
              placeholder="Décrivez votre demande en détail..."
              required
            ></textarea>
          </div>

          <div class="flex justify-end gap-12">
            <button type="button" class="btn btn-ghost" @click="close" :disabled="sending">Annuler</button>
            <button type="submit" class="btn btn-primary px-24" :disabled="sending">
              <i v-if="sending" class="fa-solid fa-spinner fa-spin mr-8"></i>
              <i v-else class="fa-solid fa-paper-plane mr-8"></i>
              Envoyer le message
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close', 'success']);

const admins = ref([]);
const loadingAdmins = ref(false);
const sending = ref(false);

const form = ref({
  admin_id: '',
  subject: '',
  message: ''
});

const close = () => {
  if (!sending.value) {
    emit('close');
  }
};

const fetchAdmins = async () => {
  loadingAdmins.value = true;
  try {
    const { data } = await axios.get('/api/v1/users/admins');
    admins.value = data.admins;
    if (admins.value.length > 0) {
      form.value.admin_id = admins.value[0].id;
    }
  } catch (err) {
    console.error('Erreur chargement admins', err);
  } finally {
    loadingAdmins.value = false;
  }
};

const submit = async () => {
  sending.value = true;
  try {
    await axios.post('/api/v1/contact/admin', form.value);
    alert('Votre message a été envoyé avec succès.');
    form.value.subject = '';
    form.value.message = '';
    emit('success');
    close();
  } catch (err) {
    alert(err.response?.data?.message || "Erreur lors de l'envoi.");
  } finally {
    sending.value = false;
  }
};

watch(() => props.isOpen, (newVal) => {
  if (newVal && admins.value.length === 0) {
    fetchAdmins();
  }
});
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  border: none;
}

.label { font-size: 13px; font-weight: 700; color: var(--gray-700); }
.min-h-\[120px\] { min-height: 120px; }
.px-24 { padding-left: 24px; padding-right: 24px; }
.py-12 { padding-top: 12px; padding-bottom: 12px; }
</style>

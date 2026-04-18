<template>
  <div class="rich-editor-shell">
    <div ref="editorRef"></div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Rédigez votre contenu...',
  },
  readOnly: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'ready']);

const editorRef = ref(null);
let quill = null;
let syncFromParent = false;
let initTimer = null;

const normalizeHtml = (value) => {
  if (!value || value === '<p><br></p>') {
    return '';
  }

  return value;
};

const applyValue = (value) => {
  if (!quill) {
    return;
  }

  const nextValue = normalizeHtml(value);
  const currentValue = normalizeHtml(quill.root.innerHTML);

  if (currentValue === nextValue) {
    return;
  }

  syncFromParent = true;
  if (!nextValue) {
    quill.setText('');
  } else {
    quill.clipboard.dangerouslyPasteHTML(nextValue);
  }
  syncFromParent = false;
};

const initQuill = () => {
  if (typeof window === 'undefined' || typeof window.Quill === 'undefined' || !editorRef.value) {
    initTimer = window.setTimeout(initQuill, 100);
    return;
  }

  quill = new window.Quill(editorRef.value, {
    theme: 'snow',
    readOnly: props.readOnly,
    placeholder: props.placeholder,
    modules: {
      toolbar: props.readOnly ? false : [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['blockquote', 'code-block'],
        ['link'],
        ['clean'],
      ],
    },
  });

  applyValue(props.modelValue);

  quill.on('text-change', () => {
    if (syncFromParent) {
      return;
    }

    emit('update:modelValue', normalizeHtml(quill.root.innerHTML));
  });

  emit('ready', quill);
};

onMounted(() => {
  initQuill();
});

onBeforeUnmount(() => {
  if (initTimer) {
    window.clearTimeout(initTimer);
  }
  quill = null;
});

watch(() => props.modelValue, (value) => {
  applyValue(value);
});

watch(() => props.readOnly, (value) => {
  if (quill) {
    quill.enable(!value);
  }
});
</script>

<style scoped>
.rich-editor-shell :deep(.ql-toolbar) {
  border-top-left-radius: var(--radius-md);
  border-top-right-radius: var(--radius-md);
  border-color: var(--gray-300);
}

.rich-editor-shell :deep(.ql-container) {
  border-bottom-left-radius: var(--radius-md);
  border-bottom-right-radius: var(--radius-md);
  border-color: var(--gray-300);
  min-height: 280px;
  font-family: var(--font-sans);
  font-size: 14px;
}

.rich-editor-shell :deep(.ql-editor) {
  min-height: 280px;
  color: var(--gray-900);
  line-height: 1.65;
}
</style>

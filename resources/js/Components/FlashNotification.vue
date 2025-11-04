<template>
  <teleport to="body">
    <transition name="toast">
      <div
        v-if="visible"
        :class="[
          'fixed top-4 right-4 z-50 max-w-md w-full shadow-lg rounded-lg overflow-hidden',
          typeClasses
        ]"
      >
        <div class="p-4 flex items-start gap-3">
          <!-- Icon -->
          <div class="flex-shrink-0">
            <svg
              v-if="type === 'success'"
              class="w-6 h-6"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else-if="type === 'error'"
              class="w-6 h-6"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else-if="type === 'warning'"
              class="w-6 h-6"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else
              class="w-6 h-6"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              />
            </svg>
          </div>

          <!-- Message -->
          <div class="flex-1 pt-0.5">
            <p class="text-sm font-medium">{{ message }}</p>
          </div>

          <!-- Close Button -->
          <button
            @click="close"
            class="flex-shrink-0 ml-4 inline-flex text-current hover:opacity-75 transition-opacity"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>

        <!-- Progress Bar -->
        <div class="h-1 bg-black bg-opacity-10">
          <div
            class="h-full bg-current transition-all duration-100 ease-linear"
            :style="{ width: progressWidth + '%' }"
          />
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
  message: String,
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  duration: {
    type: Number,
    default: 5000
  }
});

const emit = defineEmits(['close']);

const visible = ref(false);
const progressWidth = ref(100);
let progressInterval = null;
let hideTimeout = null;

const typeClasses = computed(() => {
  const classes = {
    success: 'bg-green-50 text-green-800 border-l-4 border-green-500',
    error: 'bg-red-50 text-red-800 border-l-4 border-red-500',
    warning: 'bg-yellow-50 text-yellow-800 border-l-4 border-yellow-500',
    info: 'bg-blue-50 text-blue-800 border-l-4 border-blue-500'
  };
  return classes[props.type];
});

const close = () => {
  visible.value = false;
  clearInterval(progressInterval);
  clearTimeout(hideTimeout);
  setTimeout(() => emit('close'), 300);
};

const startProgress = () => {
  const step = 100 / (props.duration / 100);
  progressInterval = setInterval(() => {
    progressWidth.value -= step;
    if (progressWidth.value <= 0) {
      close();
    }
  }, 100);

  hideTimeout = setTimeout(() => {
    close();
  }, props.duration);
};

onMounted(() => {
  visible.value = true;
  startProgress();
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
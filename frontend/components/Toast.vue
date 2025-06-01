<template>
  <div
    v-show="visible"
    class="toast fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center z-50"
  >
    <i class="fas fa-check-circle mr-2"></i>
    <span>{{ message }}</span>
  </div>
</template>

<script setup>
import { ref } from "vue";

const visible = ref(false);
const message = ref("");

const show = (msg) => {
  message.value = msg;
  visible.value = true;
  setTimeout(() => {
    visible.value = false;
  }, 3000);
};

defineExpose({ show });
</script>

<style scoped>
.toast {
  animation: slideIn 0.5s forwards, fadeOut 0.5s 2.5s forwards;
}

@keyframes slideIn {
  from {
    transform: translateY(100px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes fadeOut {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}
</style>

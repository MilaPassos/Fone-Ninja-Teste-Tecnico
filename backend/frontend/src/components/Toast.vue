<template>
  <div class="fixed top-4 right-4 space-y-2 z-50">
    <transition-group name="toast" tag="div">
      <div
        v-for="(toast, index) in toasts"
        :key="toast.id"
        class="bg-gray-800 text-white px-4 py-2 rounded shadow-lg"
        :class="{
          'bg-green-500': toast.tipo === 'success',
          'bg-red-500': toast.tipo === 'error',
          'bg-blue-500': toast.tipo === 'info'
        }"
      >
        {{ toast.mensagem }}
      </div>
    </transition-group>
  </div>
</template>

<script>
export default {
  data() {
    return {
      toasts: [],
      contador: 0,
    };
  },
  methods: {
    show(mensagem, tipo = 'info', duracao = 3000) {
      const id = this.contador++;
      this.toasts.push({ id, mensagem, tipo });

      setTimeout(() => {
        this.toasts = this.toasts.filter(t => t.id !== id);
      }, duracao);
    },
  },
};
</script>

<style>
.toast-enter-active, .toast-leave-active {
  transition: all 0.3s;
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

<template>
  <div v-if="visivel" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-md w-full max-w-md p-6 space-y-4">
      <h2 class="text-xl font-bold">{{ titulo }}</h2>
      <p>{{ mensagem }}</p>

      <div class="flex justify-end gap-2 mt-4">
        <button @click="$emit('fechar')" 
                class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100">
          Cancelar
        </button>
        <button @click="confirmar" 
                class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600">
          Confirmar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    visivel: Boolean,
    titulo: { type: String, default: "Confirmação" },
    mensagem: { type: String, default: "Deseja realmente executar esta ação?" }
  },
  methods: {
    confirmar() {
      this.$emit("confirmou");
      this.$emit("fechar");
    }
  }
};
</script>

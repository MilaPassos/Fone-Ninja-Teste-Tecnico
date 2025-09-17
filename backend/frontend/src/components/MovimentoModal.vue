<template>
    <div v-if="visivel" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-md w-full max-w-2xl p-6 space-y-4">
            <h2 class="text-xl font-bold">
                {{ tipo === "compra" ? "Registrar Compra" : "Registrar Venda" }}
            </h2>

            <div v-if="tipo === 'compra'" class="space-y-1">
                <label class="block font-semibold text-sm">Fornecedor</label>
                <input v-model="form.fornecedor" type="text" class="w-full border border-gray-300 rounded p-2" />
            </div>

            <div v-if="tipo === 'venda'" class="space-y-1">
                <label class="block font-semibold text-sm">Cliente</label>
                <input v-model="form.cliente" type="text" class="w-full border border-gray-300 rounded p-2" />
            </div>

            <div v-for="(p, index) in form.produtos" :key="index" class="rounded space-y-3">
                <label class="block font-semibold text-lg">
                    Produto {{ index + 1 }}
                </label>

                <select v-model="p.id" class="w-full border border-gray-300 rounded p-2">
                    <option disabled value="">Selecione um produto</option>
                    <option v-for="produto in produtos" :key="produto.id" :value="produto.id">
                        {{ produto.nome }}
                    </option>
                </select>

                <div class="flex gap-2">
                    <div class="flex-1 space-y-1">
                        <label class="block font-semibold text-sm">Quantidade</label>
                        <input v-model.number="p.quantidade" type="number" min="1"
                            class="w-full border border-gray-300 rounded p-2" />
                    </div>
                    <div class="flex-1 space-y-1">
                        <label class="block font-semibold text-sm">Preço Unitário</label>
                        <input v-model.number="p.preco_unitario" type="number" min="0.01" step="0.01"
                            class="w-full border border-gray-300 rounded p-2" />
                    </div>
                </div>

                <button v-if="form.produtos.length > 1" @click="removerProduto(index)"
                    class="px-3 py-1 rounded text-gray-700 hover:text-black border border-gray-300">
                    Remover
                </button>
            </div>

            <button @click="adicionarProduto"
                class="px-3 py-1 rounded text-gray-700 hover:text-black border border-gray-300">
                + Adicionar Produto
            </button>

            <div class="flex justify-end gap-2 mt-2">
                <button @click="$emit('fechar')"
                    class="px-3 py-1 rounded text-gray-700 hover:text-black border border-gray-300">
                    Cancelar
                </button>
                <button @click="salvar" class="px-3 py-1 rounded text-white bg-green-500 hover:bg-green-600">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</template>


<script>
import api from "../api";

export default {
  props: {
    visivel: Boolean,
    tipo: String,
    produtos: Array
  },
  data() {
    return {
      form: {
        fornecedor: "",
        cliente: "",
        produtos: [{ id: "", quantidade: 1, preco_unitario: 0 }]
      }
    };
  },
  methods: {
    adicionarProduto() {
      this.form.produtos.push({ id: "", quantidade: 1, preco_unitario: 0 });
    },
    removerProduto(index) {
      this.form.produtos.splice(index, 1);
    },
    async salvar() {
      if (this.tipo === "compra" && !this.form.fornecedor.trim()) {
        this.$emit("erro", "O campo Fornecedor é obrigatório.");
        return;
      }
      if (this.tipo === "venda" && !this.form.cliente.trim()) {
        this.$emit("erro", "O campo Cliente é obrigatório.");
        return;
      }

      for (let i = 0; i < this.form.produtos.length; i++) {
        const p = this.form.produtos[i];
        if (!p.id) {
          this.$emit("erro", `Selecione um produto na linha ${i + 1}.`);
          return;
        }
        if (!p.quantidade || p.quantidade <= 0) {
          this.$emit("erro", `Quantidade inválida na linha ${i + 1}.`);
          return;
        }
        if (!p.preco_unitario || p.preco_unitario <= 0) {
          this.$emit("erro", `Preço unitário inválido na linha ${i + 1}.`);
          return;
        }
      }

      try {
        const token = localStorage.getItem("token");
        const url = this.tipo === "compra" ? "/compras" : "/vendas";

        const payload = { ...this.form };
        if (this.tipo === "compra") delete payload.cliente;
        if (this.tipo === "venda") delete payload.fornecedor;

        await api.post(url, payload, {
          headers: { Authorization: `Bearer ${token}` }
        });

        this.$emit("salvou");
        this.$emit("fechar");
      } catch (err) {
        this.$emit("erro", err.response?.data?.message || err.message);
      }
    }
  }
};
</script>



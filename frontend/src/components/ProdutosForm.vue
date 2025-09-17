<template>
  <form @submit.prevent="salvarProduto" class="mb-6 p-4 text-black rounded shadow bg-white space-y-4">

    <p class="text-2xl">Adicionar novo produto</p>

    <div class="flex items-end justify-between gap-4">
      <div class="flex gap-4">
        <div>
          <label class="block font-semibold mb-1">Nome</label>
          <input v-model="produto.nome" type="text" placeholder="Capinha"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg" required />
        </div>

        <div>
          <label class="block font-semibold mb-1">Preço de venda</label>
          <input v-model.number="produto.preco_venda" type="number" min="0" step="0.01"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg" required />
        </div>

      </div>
      <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
        {{ produtoEditando ? 'Atualizar' : 'Cadastrar' }}
      </button>

    </div>
  </form>

</template>

<script>
import api from "../api.js";

export default {
  props: {
    produtoEditando: Object
  },
  data() {
    return {
      produto: {
        nome: '',
        preco_venda: 50.00,
      }
    }
  },
  watch: {
    produtoEditando: {
      immediate: true,
      handler(val) {
        if (val) {
          this.produto = { ...val }; 
        } else {
          this.produto = { nome: '', preco_venda: 50.00 };
        }
      }
    }
  },
  methods: {
    async salvarProduto() {
      if (this.produto.nome.length < 3) {
        alert("O nome do produto deve ter pelo menos 3 caracteres.");
        return;
      }

      if (this.produto.preco_venda < 0) {
        alert("O preço de venda não pode ser negativo.");
        return;
      }

      try {
        const token = localStorage.getItem("token");

        if (this.produtoEditando) {

          const res = await api.put(`/produtos/${this.produto.id}`, this.produto, {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.$emit("produto-editado", res.data);

        } else {

          const res = await api.post("/produtos", this.produto, {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.$emit("produto-cadastrado", res.data);

        }

        this.produto = { nome: '', preco_venda: 50.00 };

      } catch (err) {
        alert("Erro ao salvar produto: " + (err.response?.data?.message || err.message));
      }
    }
  }
}
</script>

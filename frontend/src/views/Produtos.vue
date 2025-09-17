<template>
  <div class="p-8 max-w-5xl text-black mx-auto">
    <h1 class="text-3xl font-semibold mb-6">Meus Produtos</h1>

    <div class="flex gap-2 mb-6">
      <button
        @click="abrirMovimento('compra')"
        class="p-2 bg-black text-white rounded hover:bg-green-600 flex items-center gap-2"
      >
        <PlusIcon class="w-6 h-6 text-white" />
        Nova Compra
      </button>

      <button
        @click="abrirMovimento('venda')"
        class="p-2 bg-white text-black rounded hover:bg-green-600 border border-gray-200 flex items-center gap-2"
      >
      <PlusIcon class="w-6 h-6" />
        Nova Venda
      </button>
    </div>

    <!-- Modal de Compra/Venda -->
    <MovimentoModal
      :visivel="modalAberto"
      :tipo="tipoMovimento"
      :produtos="produtos"
      @fechar="modalAberto = false"
      @salvou="handleSucessoMovimento"
    />

    <!-- Formulário e Lista -->
    <ProdutosForm
      :produtoEditando="produtoEditando"
      @produto-cadastrado="handleProdutoCadastrado"
      @produto-editado="handleProdutoEditado"
    />
    <ProdutosList
      :produtos="produtos"
      @excluir="excluirProduto"
    />

    <!-- Toast -->
    <Toast ref="toast" />
  </div>
</template>

<script>
import ProdutosForm from "../components/ProdutosForm.vue";
import ProdutosList from "../components/ProdutosList.vue";
import MovimentoModal from "../components/MovimentoModal.vue";
import Toast from "../components/Toast.vue";
import api from "../api.js";
import { PlusIcon } from "@heroicons/vue/24/solid";

export default {
  components: { 
    ProdutosForm, 
    ProdutosList, 
    MovimentoModal, 
    Toast,
    PlusIcon,
  },
  data() {
    return {
      produtos: [],
      produtoEditando: null,
      modalAberto: false,
      tipoMovimento: "compra",
    };
  },
  async created() {
    await this.buscarProdutos();
  },
  methods: {
    async buscarProdutos() {
      try {
        const token = localStorage.getItem("token");
        const res = await api.get("/produtos", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.produtos = res.data;
      } catch (err) {
        this.$refs.toast.show("Erro ao buscar produtos", "error");
      }
    },
    adicionarProduto(produto) {
      this.produtos.push(produto);
      this.$refs.toast.show("Produto cadastrado com sucesso!", "success");
      this.atualizarProdutos();
    },

    async excluirProduto(id) {
      try {
        const token = localStorage.getItem("token");
        await api.delete(`/produtos/${id}`, { headers: { Authorization: `Bearer ${token}` } });
        this.produtos = this.produtos.filter((p) => p.id !== id);
        this.$refs.toast.show("Produto excluído com sucesso!", "success");
        this.atualizarProdutos();
      } catch (err) {
        this.$refs.toast.show("Erro ao excluir produto", "error");
      }
    },
    abrirMovimento(tipo) {
      this.tipoMovimento = tipo;
      this.modalAberto = true;
    },
    async atualizarProdutos() {
      this.modalAberto = false;
      await this.buscarProdutos();
    },
    handleSucessoMovimento() {
      this.atualizarProdutos();
      this.$refs.toast.show(`${this.tipoMovimento === "compra" ? "Compra" : "Venda"} registrada com sucesso!`, "success");
    },
    handleProdutoCadastrado(produto) {
      this.adicionarProduto(produto);
      this.atualizarProdutos();
    },
  },
};
</script>

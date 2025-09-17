<template>
  <div class="p-8 max-w-5xl text-black mx-auto">
    <h1 class="text-3xl font-semibold mb-6">Histórico Geral</h1>

    <div v-if="historico.length" class="space-y-3">
      <div
        v-for="mov in historico"
        :key="mov.tipo + '-' + mov.id"
        class="border border-gray-300 rounded shadow bg-white p-4 flex flex-col gap-2"
      >
        <div class="flex justify-between items-start">
          <div class="space-y-2">
            <p><strong>[{{ mov.tipo.toUpperCase() }}]</strong></p>
            <p v-if="mov.tipo === 'compra'" class="text-green-600">Fornecedor: {{ mov.fornecedor }}</p>
            <p v-else class="text-red-600">Cliente: {{ mov.cliente }}</p>

            <!-- Lista de produtos do movimento -->
            <div v-for="p in mov.produtos" :key="p.id" class="pl-2 border-l border-gray-300 space-y-1">
              <p class="font-semibold">Produto: {{ buscarNomeProduto(p.id) }}</p>
              <p>{{ p.quantidade }} x R$ {{ Number(p.preco_unitario).toFixed(2) }}</p>
            </div>

            <p class="text-sm text-gray-500">{{ new Date(mov.created_at).toLocaleString() }}</p>
          </div>

          <button
            @click="abrirModalExcluir(mov)"
            class="px-3 py-1 rounded text-white bg-red-500 hover:bg-red-600"
          >
            Excluir
          </button>
        </div>
      </div>
    </div>

    <p v-else class="text-gray-600">Nenhum movimento registrado ainda.</p>

    <ConfirmModal
      :visivel="modalExcluirAberto"
      titulo="Excluir Movimento"
      :mensagem="`Tem certeza que deseja excluir este ${movimentoParaExcluir?.tipo}?`"
      @confirmou="excluirMovimentoConfirmado"
      @fechar="modalExcluirAberto = false"
    />

    <Toast ref="toast" />
  </div>
</template>

<script>
import api from "../api.js";
import ConfirmModal from "../components/ConfirmModal.vue";
import Toast from "../components/Toast.vue";

export default {
  components: { ConfirmModal, Toast },
  data() {
    return {
      historico: [],
      produtos: [],
      modalExcluirAberto: false,
      movimentoParaExcluir: null,
      toast: null, 
    };
  },
  async created() {
    await this.buscarHistorico();
  },
  mounted() {
    this.toast = this.$refs.toast;
  },
  methods: {
    async buscarHistorico() {
      try {
        const token = localStorage.getItem("token");

        const [comprasRes, vendasRes, produtosRes] = await Promise.all([
          api.get("/compras", { headers: { Authorization: `Bearer ${token}` } }),
          api.get("/vendas", { headers: { Authorization: `Bearer ${token}` } }),
          api.get("/produtos", { headers: { Authorization: `Bearer ${token}` } }),
        ]);

        this.produtos = produtosRes.data;

        const compras = comprasRes.data.map(c => ({
          id: c.id,
          tipo: "compra",
          fornecedor: c.fornecedor,
          created_at: c.created_at,
          produtos: c.produtos
        }));

        const vendas = vendasRes.data.map(v => ({
          id: v.id,
          tipo: "venda",
          cliente: v.cliente,
          created_at: v.created_at,
          produtos: v.produtos
        }));

        this.historico = [...compras, ...vendas].sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        );
      } catch (err) {
        this.toast?.show(err.response?.data?.message || err.message, "error");
      }
    },
    abrirModalExcluir(mov) {
      this.movimentoParaExcluir = mov;
      this.modalExcluirAberto = true;
    },
    async excluirMovimentoConfirmado() {
      if (!this.movimentoParaExcluir) return;

      try {
        const token = localStorage.getItem("token");
        const url = this.movimentoParaExcluir.tipo === "compra"
          ? `/compras/${this.movimentoParaExcluir.id}`
          : `/vendas/${this.movimentoParaExcluir.id}`;

        await api.delete(url, { headers: { Authorization: `Bearer ${token}` } });

        await this.buscarHistorico();
        this.toast?.show(`${this.movimentoParaExcluir.tipo} excluída com sucesso!`, "success");
      } catch (err) {
        this.toast?.show(err.response?.data?.message || err.message, "error");
      } finally {
        this.modalExcluirAberto = false;
        this.movimentoParaExcluir = null;
      }
    },
    buscarNomeProduto(id) {
      const prod = this.produtos.find(p => p.id === id);
      return prod ? prod.nome : "Desconhecido";
    },
  },
};
</script>

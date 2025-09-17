<template>
  <div class="text-black bg-white rounded mt-6 pt-1 shadow">
    <p class="text-2xl m-4">Lista de Produtos</p>


    <div v-for="produto in produtos" :key="produto.id" class="border-t border-gray-300 shadow-sm">
      <div class="flex justify-between items-center p-4">
    
        <div>
          <p class="font-semibold">{{ produto.nome }}</p>
          <p>Preço de venda: R$ {{ Number(produto.preco_venda).toFixed(2) }}</p>
          <p>Estoque: {{ produto.estoque }}</p>
        </div>
    
        <div class="flex gap-2 text-gray-700">
          <button @click="abrirModalExcluir(produto)" class="p-2 rounded" title="Excluir produto">
            <TrashIcon class="w-5 h-5" />
          </button>

          <button @click="toggleMovimentos(produto.id)" class="p-2 rounded-xl bg-gray-200 hover:bg-gray-300"
            title="Histórico">
            <ChevronDownIcon v-if="isAberto(produto.id)" class="w-5 h-5" />
            <ChevronRightIcon v-else class="w-5 h-5" />
          </button>
        </div>
      </div>
  
      <div v-if="isAberto(produto.id)" class="p-4 border-t border-gray-400 bg-gray-50">
        <p class="font-semibold mb-2">Histórico:</p>
        <ul class="mb-2 space-y-1">
          <li v-for="mov in historicoProduto(produto.id)" :key="mov.tipo + mov.id"
            :class="mov.tipo === 'compra' ? 'text-green-600' : 'text-red-600'">
            <strong>[{{ mov.tipo.toUpperCase() }}]</strong>
            {{ mov.quantidade }} x R$ {{ Number(mov.preco_unitario).toFixed(2) }}
            - {{ mov.tipo === 'compra' ? mov.fornecedor : mov.cliente }}
            <span class="text-gray-500 text-sm">
              ({{ new Date(mov.created_at).toLocaleString() }})
            </span>
          </li>
        </ul>

        <p><strong>Custo médio:</strong> R$ {{ calcularCustoMedio(produto.id).toFixed(2) }}</p>
        <p><strong>Lucro total:</strong> R$ {{ calcularLucroTotal(produto.id).toFixed(2) }}</p>
      </div>
    </div>


    <ConfirmModal :visivel="modalExcluirAberto" titulo="Excluir Produto"
      :mensagem="`Tem certeza que deseja excluir o produto ${produtoParaExcluir?.nome}?`"
      @confirmou="excluirProdutoConfirmado" @fechar="modalExcluirAberto = false" />
  </div>
</template>

<script>
import {
  ShoppingCartIcon,
  BanknotesIcon,
  PencilIcon,
  TrashIcon,
  ChevronDownIcon,
  ChevronRightIcon
} from "@heroicons/vue/24/solid";

import api from "../api.js";
import ConfirmModal from "./ConfirmModal.vue";

export default {
  props: { produtos: Array },
  components: {
    ShoppingCartIcon,
    BanknotesIcon,
    PencilIcon,
    TrashIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    ConfirmModal
  },
  data() {
    return {
      abertos: [],
      todasCompras: [],
      todasVendas: [],
      modalExcluirAberto: false,
      produtoParaExcluir: null,
    };
  },
  async created() {
    await this.buscarMovimentos();
  },
  methods: {
    isAberto(id) {
      return this.abertos.includes(id);
    },
    toggleMovimentos(id) {
      if (this.isAberto(id)) this.abertos = this.abertos.filter(x => x !== id);
      else this.abertos.push(id);
    },
    abrirModalExcluir(produto) {
      this.produtoParaExcluir = produto;
      this.modalExcluirAberto = true;
    },
    async excluirProdutoConfirmado() {
      if (!this.produtoParaExcluir) return;

      const id = this.produtoParaExcluir.id;
      this.$emit("excluir", id); 
      this.modalExcluirAberto = false;
      this.produtoParaExcluir = null;
    },

    async buscarMovimentos() {
      try {
        const token = localStorage.getItem("token");
        const comprasRes = await api.get("/compras", { headers: { Authorization: `Bearer ${token}` } });
        const vendasRes = await api.get("/vendas", { headers: { Authorization: `Bearer ${token}` } });
        this.todasCompras = comprasRes.data;
        this.todasVendas = vendasRes.data;
      } catch (err) {
        console.log("Erro ao buscar movimentos: " + (err.response?.data?.message || err.message));
      }
    },
    historicoProduto(produtoId) {
      const compras = this.todasCompras
        .filter(c => c.produtos.some(p => p.id === produtoId))
        .map(c => {
          const p = c.produtos.find(p => p.id === produtoId);
          return { ...p, fornecedor: c.fornecedor, created_at: c.created_at, id: c.id, tipo: "compra" };
        });
      const vendas = this.todasVendas
        .filter(v => v.produtos.some(p => p.id === produtoId))
        .map(v => {
          const p = v.produtos.find(p => p.id === produtoId);
          return { ...p, cliente: v.cliente, created_at: v.created_at, id: v.id, tipo: "venda" };
        });
      return [...compras, ...vendas].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    },
    calcularCustoMedio(produtoId) {
      const compras = this.historicoProduto(produtoId).filter(m => m.tipo === "compra");
      if (!compras.length) return 0;
      const total = compras.reduce((acc, c) => acc + c.quantidade * c.preco_unitario, 0);
      const qtd = compras.reduce((acc, c) => acc + c.quantidade, 0);
      return total / qtd;
    },
    calcularLucroTotal(produtoId) {
      const vendas = this.historicoProduto(produtoId).filter(m => m.tipo === "venda");
      const custoMedio = this.calcularCustoMedio(produtoId);
      return vendas.reduce((acc, v) => acc + (v.preco_unitario - custoMedio) * v.quantidade, 0);
    }
  }
};
</script>

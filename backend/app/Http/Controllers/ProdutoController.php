<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::all(['id', 'nome', 'preco_venda', 'estoque']);
        return response()->json($produtos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|min:3',
            'preco_venda' => 'required|numeric|min:0.01',
        ]);

        $produto = Produto::create([
            'nome' => $request->nome,
            'preco_venda' => $request->preco_venda,
            'estoque' => 0,
        ]);

        return response()->json($produto, 201);
    }

    public function destroy($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto excluído com sucesso'], 200);
    }
}

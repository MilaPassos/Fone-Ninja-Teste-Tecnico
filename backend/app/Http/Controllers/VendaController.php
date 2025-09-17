<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venda;
use App\Models\Produto;

class VendaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'required|string|min:3',
            'produtos' => 'required|array|min:1',
            'produtos.*.id' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
            'produtos.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        $vendaProdutos = [];
        $total = 0;
        $lucro = 0;

        foreach ($request->produtos as $item) {
            $produto = Produto::findOrFail($item['id']);

            if ($item['quantidade'] > $produto->estoque) {
                return response()->json(['error' => "Estoque insuficiente para {$produto->nome}"], 400);
            }

            $produto->estoque -= $item['quantidade'];
            $produto->save();

            $vendaProdutos[] = [
                'id' => $produto->id,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
            ];

            $total += $item['quantidade'] * $item['preco_unitario'];
            $lucro += ($item['preco_unitario'] - $produto->custo_medio) * $item['quantidade'];
        }

        $venda = Venda::create([
            'cliente' => $request->cliente,
            'produtos' => $vendaProdutos,
            'total' => $total,
            'lucro' => $lucro,
        ]);

        return response()->json($venda, 201);
    }

    public function index()
    {
        return response()->json(Venda::all());
    }

    
    public function destroy($id)
    {
        $venda = Venda::findOrFail($id);

        foreach ($venda->produtos as $item) {
            $produto = Produto::find($item['id']);
            if ($produto) {
                $produto->estoque += $item['quantidade'];
                $produto->save();
            }
        }

        $venda->delete();

        return response()->json(['message' => 'Venda excluída e estoque revertido.']);
    }
}

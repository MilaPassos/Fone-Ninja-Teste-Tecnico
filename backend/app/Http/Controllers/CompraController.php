<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Produto;

class CompraController extends Controller
{
    // Registrar compra
    public function store(Request $request)
    {
        $request->validate([
            'fornecedor' => 'required|string|min:3',
            'produtos' => 'required|array|min:1',
            'produtos.*.id' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
            'produtos.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        $compraProdutos = [];

        foreach ($request->produtos as $item) {
            $produto = Produto::findOrFail($item['id']);

            $novoEstoque = $produto->estoque + $item['quantidade'];

            $produto->estoque = $novoEstoque;
            $produto->save();

            $compraProdutos[] = [
                'id' => $produto->id,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
            ];
        }

        $compra = Compra::create([
            'fornecedor' => $request->fornecedor,
            'produtos' => $compraProdutos,
        ]);

        return response()->json($compra, 201);
    }

    public function index()
    {
        return response()->json(Compra::all());
    }

    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);

        foreach ($compra->produtos as $item) {
            $produto = Produto::find($item['id']); 
            if ($produto) {
                $produto->estoque -= $item['quantidade'];
                if ($produto->estoque < 0) {
                    $produto->estoque = 0;
                }
                $produto->save();
            }
        }

        $compra->delete();

        return response()->json(['message' => 'Compra excluída e estoque revertido.']);
    }
}

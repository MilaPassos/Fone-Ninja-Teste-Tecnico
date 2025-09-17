<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\AuthController;

// Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Produtos
Route::get('/produtos', [ProdutoController::class, 'index']);
Route::post('/produtos', [ProdutoController::class, 'store']);
Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy']);

// Compras
Route::get('/compras', [CompraController::class, 'index']);
Route::post('/compras', [CompraController::class, 'store']);
Route::delete('/compras/{id}', [CompraController::class, 'destroy']);

// Vendas
Route::get('/vendas', [VendaController::class, 'index']);
Route::post('/vendas', [VendaController::class, 'store']);
Route::delete('/vendas/{id}', [VendaController::class, 'destroy']);
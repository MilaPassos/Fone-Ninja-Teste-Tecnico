<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = ['fornecedor', 'produtos'];

    protected $casts = [
        'produtos' => 'array',
    ];
}

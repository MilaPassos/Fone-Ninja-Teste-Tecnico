<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $fillable = ['cliente', 'produtos', 'total', 'lucro'];

    protected $casts = [
        'produtos' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tablero extends Model
{
    protected $fillable = [
        'partida_id',
        'casilla_1',
        'casilla_2',
        'casilla_3',
        'casilla_4',
        'casilla_5',
        'casilla_6',
        'casilla_7',
        'casilla_8',
        'casilla_9',
    ];

    public function casillasFormateadas(): array
{
    $casillas = [];

    for ($i = 1; $i <= 9; $i++) {
        $campo = 'casilla_' . $i;
        $casillas[] = [
            'numero' => $i,
            'campo' => $campo,
            'valor' => $this->{$campo} ?? null,
        ];
    }

    return $casillas;
}

    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Partida extends Model
{
    protected $fillable = [
        'jugador_1_id',
        'jugador_2_id',
        'turno_actual_id',
        'ganador_id',
        'status',
    ];

    public function jugador1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_1_id');
    }

    public function jugador2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_2_id');
    }

    public function turnoActual(): BelongsTo
    {
        return $this->belongsTo(User::class, 'turno_actual_id');
    }

    public function ganador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ganador_id');
    }

    public function tablero(): HasOne
    {
        return $this->hasOne(Tablero::class);
    }

    public function historiales(): HasMany
    {
        return $this->hasMany(Historial::class);
    }
}

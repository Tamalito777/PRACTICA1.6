<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends Model
{
    protected $table = 'historiales';
    
    protected $fillable = [
        'partida_id',
        'user_id',
        'status',
    ];

    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
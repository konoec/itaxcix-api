<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TbConductor extends Model
{
    protected $table = 'tb_conductor';
    public $timestamps = false;

    protected $fillable = [
        'cond_usuario_id', 'cond_licencia', 'cond_estado_disponibilidad', 'cond_estado'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(TbUsuario::class, 'cond_usuario_id');
    }
}
<?php

namespace iTaxCix\models\travel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use iTaxCix\models\auth\TbUsuario;

class TbCalificacion extends Model
{
    protected $table = 'tb_calificacion';
    public $timestamps = false;

    protected $fillable = [
        'cali_viaje_id', 'cali_calificador_id', 'cali_calificado_id',
        'cali_puntaje', 'cali_comentario'
    ];

    // Relaciones
    public function viaje(): BelongsTo
    {
        return $this->belongsTo(TbViaje::class, 'cali_viaje_id');
    }

    public function calificador(): BelongsTo
    {
        return $this->belongsTo(TbUsuario::class, 'cali_calificador_id');
    }

    public function calificado(): BelongsTo
    {
        return $this->belongsTo(TbUsuario::class, 'cali_calificado_id');
    }
}
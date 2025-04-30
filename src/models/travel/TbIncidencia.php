<?php

namespace iTaxCix\models\travel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use iTaxCix\models\auth\TbUsuario;

class TbIncidencia extends Model
{
    protected $table = 'tb_incidencia';
    public $timestamps = false;

    protected $fillable = [
        'inci_viaje_id', 'inci_usuario_id', 'inci_tipo', 'inci_descripcion',
        'inci_fecha'
    ];

    // Relaciones
    public function viaje(): BelongsTo
    {
        return $this->belongsTo(TbViaje::class, 'inci_viaje_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(TbUsuario::class, 'inci_usuario_id');
    }
}
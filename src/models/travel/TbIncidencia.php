<?php

namespace iTaxCix\models\travel;

use Illuminate\Database\Eloquent\Model;
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
    public function viaje()
    {
        return $this->belongsTo(TbViaje::class, 'inci_viaje_id');
    }

    public function usuario()
    {
        return $this->belongsTo(TbUsuario::class, 'inci_usuario_id');
    }
}
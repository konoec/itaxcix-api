<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;
use iTaxCix\models\common\TbTipoConfirmacion;

class TbConfirmacion extends Model
{
    protected $table = 'tb_confirmacion';
    public $timestamps = false;

    protected $fillable = [
        'conf_usuario_id', 'conf_tipo_id', 'conf_codigo',
        'conf_fecha_generacion', 'conf_utilizado', 'conf_fecha_utilizacion'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(TbUsuario::class, 'conf_usuario_id');
    }

    public function tipoConfirmacion()
    {
        return $this->belongsTo(TbTipoConfirmacion::class, 'conf_tipo_id');
    }
}
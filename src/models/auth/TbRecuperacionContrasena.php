<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;

class TbRecuperacionContrasena extends Model
{
    protected $table = 'tb_recuperacion_contrasena';
    public $timestamps = false;

    protected $fillable = [
        'recu_usuario_id', 'recu_codigo', 'recu_fecha_generacion',
        'recu_utilizado', 'recu_fecha_utilizacion'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(TbUsuario::class, 'recu_usuario_id');
    }
}
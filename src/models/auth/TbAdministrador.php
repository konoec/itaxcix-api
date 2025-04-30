<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;

class TbAdministrador extends Model
{
    protected $table = 'tb_administrador';
    public $timestamps = false;

    protected $fillable = [
        'admi_usuario_id', 'admi_area', 'admi_estado_id'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(TbUsuario::class, 'admi_usuario_id');
    }
}
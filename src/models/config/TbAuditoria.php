<?php

namespace iTaxCix\models\config;

use Illuminate\Database\Eloquent\Model;

class TbAuditoria extends Model
{
    protected $table = 'tb_auditoria';
    public $timestamps = false;

    protected $fillable = [
        'audi_tabla_afectada', 'audi_operacion', 'audi_usuario_bd',
        'audi_usuario_sistema', 'audi_fecha', 'audi_dato_anterior',
        'audi_dato_nuevo'
    ];
}
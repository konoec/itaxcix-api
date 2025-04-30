<?php

namespace iTaxCix\models\config;

use Illuminate\Database\Eloquent\Model;

class TbConfiguracion extends Model
{
    protected $table = 'tb_configuracion';
    public $timestamps = false;

    protected $fillable = [
        'conf_clave', 'conf_valor'
    ];
}
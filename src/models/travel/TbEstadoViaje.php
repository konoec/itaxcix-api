<?php

namespace iTaxCix\models\travel;

use Illuminate\Database\Eloquent\Model;

class TbEstadoViaje extends Model
{
    protected $table = 'tb_estado_viaje';
    public $timestamps = false;
    protected $fillable = ['estv_nombre'];
}
<?php

namespace iTaxCix\models\infraction;

use Illuminate\Database\Eloquent\Model;

class TbEstadoInfraccion extends Model
{
    protected $table = 'tb_estado_infraccion';
    public $timestamps = false;
    protected $fillable = ['estin_nombre'];
}
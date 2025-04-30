<?php

namespace iTaxCix\models\infraction;

use Illuminate\Database\Eloquent\Model;

class TbGravedadInfraccion extends Model
{
    protected $table = 'tb_gravedad_infraccion';
    public $timestamps = false;
    protected $fillable = ['grav_nombre'];
}
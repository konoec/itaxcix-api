<?php

namespace iTaxCix\models\vehicle;

use Illuminate\Database\Eloquent\Model;

class TbClaseVehiculo extends Model
{
    protected $table = 'tb_clase_vehiculo';
    public $timestamps = false;
    protected $fillable = ['clase_nombre'];
}
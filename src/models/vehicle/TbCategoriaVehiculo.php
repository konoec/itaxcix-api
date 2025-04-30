<?php

namespace iTaxCix\models\vehicle;

use Illuminate\Database\Eloquent\Model;

class TbCategoriaVehiculo extends Model
{
    protected $table = 'tb_categoria_vehiculo';
    public $timestamps = false;
    protected $fillable = ['cate_nombre'];
}
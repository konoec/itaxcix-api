<?php

namespace iTaxCix\models\vehicle;

use Illuminate\Database\Eloquent\Model;

class TbTipoCombustible extends Model
{
    protected $table = 'tb_tipo_combustible';
    public $timestamps = false;
    protected $fillable = ['comb_nombre'];
}
<?php

namespace iTaxCix\models\common;

use Illuminate\Database\Eloquent\Model;

class TbTipoConfirmacion extends Model
{
    protected $table = 'tb_tipo_confirmacion';
    public $timestamps = false;
    protected $fillable = ['tcon_nombre'];
}
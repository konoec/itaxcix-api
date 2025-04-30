<?php

namespace iTaxCix\models\common;

use Illuminate\Database\Eloquent\Model;

class TbTipoDocumento extends Model
{
    // Especifica el nombre de la tabla si no sigue la convención de nombres
    protected $table = 'tb_tipo_documento';

    // Deshabilita las marcas de tiempo si no usas `created_at` y `updated_at`
    public $timestamps = false;

    // Define los campos que pueden ser llenados masivamente
    protected $fillable = ['tdoc_nombre'];
}
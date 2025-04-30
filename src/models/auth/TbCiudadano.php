<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TbCiudadano extends Model
{
    protected $table = 'tb_ciudadano';
    public $timestamps = false;

    protected $fillable = [
        'ciud_usuario_id', 'ciud_direccion', 'ciud_estado_id'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(TbUsuario::class, 'ciud_usuario_id');
    }
}
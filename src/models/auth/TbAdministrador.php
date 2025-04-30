<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TbAdministrador extends Model
{
    protected $table = 'tb_administrador';
    public $timestamps = false;

    protected $fillable = [
        'admi_usuario_id', 'admi_area', 'admi_estado_id'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(TbUsuario::class, 'admi_usuario_id');
    }
}
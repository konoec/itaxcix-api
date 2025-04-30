<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use iTaxCix\models\common\TbTipoDocumento;

class TbUsuario extends Model
{
    protected $table = 'tb_usuario';
    public $timestamps = false;

    protected $fillable = [
        'usua_alias', 'usua_nombre', 'usua_apellido', 'usua_tipo_documento_id',
        'usua_documento', 'usua_telefono', 'usua_correo', 'usua_imagen',
        'usua_clave', 'usua_estado_id'
    ];

    // Relaciones
    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TbTipoDocumento::class, 'usua_tipo_documento_id');
    }

    public function conductor(): HasOne
    {
        return $this->hasOne(TbConductor::class, 'cond_usuario_id');
    }

    public function ciudadano(): HasOne
    {
        return $this->hasOne(TbCiudadano::class, 'ciud_usuario_id');
    }

    public function administrador(): HasOne
    {
        return $this->hasOne(TbAdministrador::class, 'admi_usuario_id');
    }
}
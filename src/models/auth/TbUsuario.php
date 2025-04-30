<?php

namespace iTaxCix\models\auth;

use Illuminate\Database\Eloquent\Model;
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
    public function tipoDocumento()
    {
        return $this->belongsTo(TbTipoDocumento::class, 'usua_tipo_documento_id');
    }

    public function conductor()
    {
        return $this->hasOne(TbConductor::class, 'cond_usuario_id');
    }

    public function ciudadano()
    {
        return $this->hasOne(TbCiudadano::class, 'ciud_usuario_id');
    }

    public function administrador()
    {
        return $this->hasOne(TbAdministrador::class, 'admi_usuario_id');
    }
}
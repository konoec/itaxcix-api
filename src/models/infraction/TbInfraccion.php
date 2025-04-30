<?php

namespace iTaxCix\models\infraction;

use Illuminate\Database\Eloquent\Model;
use iTaxCix\models\auth\TbConductor;

class TbInfraccion extends Model
{
    protected $table = 'tb_infraccion';
    public $timestamps = false;

    protected $fillable = [
        'infr_conductor_id', 'infr_gravedad_id', 'infr_fecha',
        'infr_descripcion', 'infr_estado_id'
    ];

    // Relaciones
    public function conductor()
    {
        return $this->belongsTo(TbConductor::class, 'infr_conductor_id');
    }

    public function gravedadInfraccion()
    {
        return $this->belongsTo(TbGravedadInfraccion::class, 'infr_gravedad_id');
    }

    public function estadoInfraccion()
    {
        return $this->belongsTo(TbEstadoInfraccion::class, 'infr_estado_id');
    }
}
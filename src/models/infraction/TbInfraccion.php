<?php

namespace iTaxCix\models\infraction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    public function conductor(): BelongsTo
    {
        return $this->belongsTo(TbConductor::class, 'infr_conductor_id');
    }

    public function gravedadInfraccion(): BelongsTo
    {
        return $this->belongsTo(TbGravedadInfraccion::class, 'infr_gravedad_id');
    }

    public function estadoInfraccion(): BelongsTo
    {
        return $this->belongsTo(TbEstadoInfraccion::class, 'infr_estado_id');
    }
}
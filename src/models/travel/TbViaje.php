<?php

namespace iTaxCix\models\travel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use iTaxCix\models\auth\TbCiudadano;
use iTaxCix\models\auth\TbConductor;

class TbViaje extends Model
{
    protected $table = 'tb_viaje';
    public $timestamps = false;

    protected $fillable = [
        'viaj_ciudadano_id', 'viaj_conductor_id', 'viaj_origen', 'viaj_destino',
        'viaj_origen_lat', 'viaj_origen_lng', 'viaj_destino_lat', 'viaj_destino_lng',
        'viaj_fecha_inicio', 'viaj_fecha_fin', 'viaj_fecha_creacion', 'viaj_estado_id'
    ];

    // Relaciones
    public function ciudadano(): BelongsTo
    {
        return $this->belongsTo(TbCiudadano::class, 'viaj_ciudadano_id');
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(TbConductor::class, 'viaj_conductor_id');
    }

    public function estadoViaje(): BelongsTo
    {
        return $this->belongsTo(TbEstadoViaje::class, 'viaj_estado_id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(TbCalificacion::class, 'cali_viaje_id');
    }

    public function incidencias(): HasMany
    {
        return $this->hasMany(TbIncidencia::class, 'inci_viaje_id');
    }
}
<?php

namespace iTaxCix\models\vehicle;

use Illuminate\Database\Eloquent\Model;
use iTaxCix\models\auth\TbConductor;

class TbTarjetaCirculacion extends Model
{
    protected $table = 'tb_tarjeta_circulacion';
    public $timestamps = false;

    protected $fillable = [
        'tarj_vehiculo_id', 'tarj_conductor_id', 'tarj_codigo',
        'tarj_fecha_emision', 'tarj_fecha_caducidad', 'tarj_estado',
        'tarj_fecha_anulacion', 'tarj_ruc_empresa'
    ];

    // Relaciones
    public function vehiculo()
    {
        return $this->belongsTo(TbVehiculo::class, 'tarj_vehiculo_id');
    }

    public function conductor()
    {
        return $this->belongsTo(TbConductor::class, 'tarj_conductor_id');
    }
}
<?php

namespace iTaxCix\models\vehicle;

use Illuminate\Database\Eloquent\Model;

class TbVehiculo extends Model
{
    protected $table = 'tb_vehiculo';
    public $timestamps = false;

    protected $fillable = [
        'vehi_placa', 'vehi_serie', 'vehi_modelo', 'vehi_tipo',
        'vehi_numero_asientos', 'vehi_anio_fabricacion', 'vehi_tipo_combustible_id',
        'vehi_numero_pasajeros', 'vehi_marca', 'vehi_color', 'vehi_clase_id',
        'vehi_categoria_id', 'vehi_estado_id'
    ];

    // Relaciones
    public function tipoCombustible()
    {
        return $this->belongsTo(TbTipoCombustible::class, 'vehi_tipo_combustible_id');
    }

    public function clase()
    {
        return $this->belongsTo(TbClaseVehiculo::class, 'vehi_clase_id');
    }

    public function categoria()
    {
        return $this->belongsTo(TbCategoriaVehiculo::class, 'vehi_categoria_id');
    }
}
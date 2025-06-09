<?php

namespace itaxcix\Shared\Validators\generic;

class CoordinatesValidator
{
    public static function validateLatitude(float $latitude): array
    {
        if ($latitude < -90 || $latitude > 90) {
            return ['La latitud debe estar entre -90 y 90 grados.'];
        }
        return [];
    }

    public static function validateLongitude(float $longitude): array
    {
        if ($longitude < -180 || $longitude > 180) {
            return ['La longitud debe estar entre -180 y 180 grados.'];
        }
        return [];
    }
}
<?php

namespace itaxcix\Shared\Validators\useCases\Travel;

use itaxcix\Shared\Validators\generic\IdValidator;

class TravelIdValidator
{
    public function validate(array $data): array
    {
        if (!isset($data['travelId'])) {
            return ['El ID del viaje es requerido.'];
        }

        $idErrors = IdValidator::validate((int)$data['travelId']);
        return !empty($idErrors) ? $idErrors : [];
    }
}
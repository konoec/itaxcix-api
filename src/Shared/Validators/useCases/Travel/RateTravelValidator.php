<?php

namespace itaxcix\Shared\Validators\useCases\Travel;

use itaxcix\Shared\Validators\generic\IdValidator;

class RateTravelValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['travelId'])) {
            $errors['travelId'] = 'El ID del viaje es requerido.';
        } elseif (!is_int($data['travelId']) && !ctype_digit((string) $data['travelId'])) {
            $errors['travelId'] = 'El ID del viaje debe ser un número entero válido.';
        } else {
            $idErrors = IdValidator::validate((int) $data['travelId']);
            if (!empty($idErrors)) {
                $errors['travelId'] = reset($idErrors);
            }
        }

        if (!isset($data['score'])) {
            $errors['score'] = 'El puntaje es requerido.';
        } elseif (!is_int($data['score']) && !ctype_digit((string) $data['score'])) {
            $errors['score'] = 'El puntaje debe ser un número entero.';
        } else {
            $score = (int) $data['score'];
            if ($score < 1 || $score > 5) {
                $errors['score'] = 'El puntaje debe estar entre 1 y 5.';
            }
        }

        if (isset($data['comment']) && !is_null($data['comment']) && !is_string($data['comment'])) {
            $errors['comment'] = 'El comentario debe ser un texto.';
        }

        if (!isset($data['raterId'])) {
            $errors['raterId'] = 'El ID del usuario que califica es requerido.';
        } elseif (!is_int($data['raterId']) && !ctype_digit((string) $data['raterId'])) {
            $errors['raterId'] = 'El ID del usuario que califica debe ser un número entero válido.';
        } else {
            $idErrors = IdValidator::validate((int) $data['raterId']);
            if (!empty($idErrors)) {
                $errors['raterId'] = reset($idErrors);
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        return [];
    }
}

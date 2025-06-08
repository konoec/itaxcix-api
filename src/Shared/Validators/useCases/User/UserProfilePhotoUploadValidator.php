<?php

namespace itaxcix\Shared\Validators\useCases\User;

use itaxcix\Shared\Validators\generic\Base64ImageValidator;
use itaxcix\Shared\Validators\generic\IdValidator;

class UserProfilePhotoUploadValidator
{
    public function validate(array $data): array {
        $errors = [];

        // Validar personId
        if (!isset($data['userId'])) {
            $errors['userId'] = 'El ID de persona es requerido.';
        } else {
            $idErrors = IdValidator::validate((int)$data['userId']);
            if (!empty($idErrors)) {
                $errors['userId'] = reset($idErrors);
            }
        }

        // Validar imageBase64
        if (!isset($data['base64Image'])) {
            $errors['imageBase64'] = 'La imagen es requerida.';
        } else {
            $imageErrors = Base64ImageValidator::validate($data['base64Image']);
            if (!empty($imageErrors)) {
                $errors['base64Image'] = reset($imageErrors);
            }
        }

        // Devolver mensaje general si hay múltiples errores
        if (count($errors) > 1) {
            return ['Los datos proporcionados no son válidos.'];
        }

        return $errors;
    }
}
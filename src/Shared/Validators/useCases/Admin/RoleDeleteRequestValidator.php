<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

use itaxcix\Shared\Validators\generic\IdValidator;

class RoleDeleteRequestValidator
{
    public function validate(array $data): array {
        $errors = [];
        if (!isset($data['id'])) {
            $errors['id'] = 'El ID es requerido.';
        } else {
            $idErrors = IdValidator::validate((int)$data['id']);
            if (!empty($idErrors)) {
                $errors['id'] = reset($idErrors);
            }
        }
        if (count($errors) > 1) {
            return ['Los datos proporcionados no son válidos.'];
        }
        return $errors;
    }
}


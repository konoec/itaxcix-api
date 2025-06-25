<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class PermissionDeleteRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (!isset($data['id']) || !is_int($data['id']) || $data['id'] <= 0) {
            $errors['id'] = 'El ID del permiso es requerido y debe ser un entero positivo.';
        }
        return $errors;
    }
}


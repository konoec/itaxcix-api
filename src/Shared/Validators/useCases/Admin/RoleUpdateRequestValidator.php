<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class RoleUpdateRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['id'])) {
            $errors['id'] = 'El ID del rol es requerido.';
        }
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre del rol es requerido.';
        }
        return $errors;
    }
}


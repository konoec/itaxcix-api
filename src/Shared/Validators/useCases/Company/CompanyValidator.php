<?php

namespace itaxcix\Shared\Validators\useCases\Company;

class CompanyValidator {
    public function validate(array $data): array {
        $errors = [];

        if (!isset($data['ruc']) || trim($data['ruc']) === '') {
            $errors['ruc'] = 'El RUC es requerido.';
        } elseif (!preg_match('/^\d{11}$/', $data['ruc'])) {
            $errors['ruc'] = 'El RUC debe tener exactamente 11 dígitos.';
        }

        if (isset($data['name']) && strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre no puede tener más de 100 caracteres.';
        }

        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El campo activo debe ser verdadero o falso.';
        }

        return $errors;
    }
}

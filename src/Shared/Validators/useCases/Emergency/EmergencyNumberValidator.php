<?php

namespace itaxcix\Shared\Validators\useCases\Emergency;

class EmergencyNumberValidator {
    public function validate(array $data): array {
        $errors = [];
        if (!isset($data['number']) || trim($data['number']) === '') {
            $errors['number'] = 'El número de emergencia es requerido.';
        } elseif (!preg_match('/^\+?\d{7,15}$/', $data['number'])) {
            $errors['number'] = 'El número de emergencia no es válido.';
        }
        return $errors;
    }
}


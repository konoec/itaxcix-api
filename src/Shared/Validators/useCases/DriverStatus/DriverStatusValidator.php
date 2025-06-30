<?php

namespace itaxcix\Shared\Validators\useCases\DriverStatus;

class DriverStatusValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar nombre
        if (!isset($data['name']) || empty(trim($data['name']))) {
            $errors['name'] = 'El nombre es requerido.';
        } elseif (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres.';
        } elseif (strlen(trim($data['name'])) > 100) {
            $errors['name'] = 'El nombre no puede exceder 100 caracteres.';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\.]+$/u', trim($data['name']))) {
            $errors['name'] = 'El nombre solo puede contener letras, espacios, guiones y puntos.';
        }

        // Validar active (opcional, por defecto true)
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El estado activo debe ser verdadero o falso.';
        }

        return $errors;
    }

    public static function getPredefinedStatuses(): array
    {
        return [
            'disponibles' => [
                'Disponible',
                'En línea',
                'Activo'
            ],
            'ocupados' => [
                'En carrera',
                'Ocupado',
                'En viaje'
            ],
            'no_disponibles' => [
                'Fuera de línea',
                'Descanso',
                'No disponible',
                'Inactivo'
            ]
        ];
    }
}

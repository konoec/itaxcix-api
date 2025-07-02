<?php

namespace itaxcix\Shared\Validators\generic;

abstract class BaseValidator
{
    /**
     * Validar datos
     *
     * @param array $data Datos a validar
     * @return array Array de errores (vacío si no hay errores)
     */
    abstract public function validate(array $data): array;

    /**
     * Verificar si un valor está vacío
     *
     * @param mixed $value
     * @return bool
     */
    protected function isEmpty($value): bool
    {
        return empty($value) && $value !== '0' && $value !== 0;
    }

    /**
     * Verificar si un valor es un ID válido
     *
     * @param mixed $value
     * @return bool
     */
    protected function isValidId($value): bool
    {
        return is_numeric($value) && (int)$value > 0;
    }

    /**
     * Verificar si un valor es una cadena válida
     *
     * @param mixed $value
     * @param int $minLength
     * @param int $maxLength
     * @return bool
     */
    protected function isValidString($value, int $minLength = 1, int $maxLength = 255): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $length = strlen(trim($value));
        return $length >= $minLength && $length <= $maxLength;
    }

    /**
     * Verificar si un valor es un email válido
     *
     * @param mixed $value
     * @return bool
     */
    protected function isValidEmail($value): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar parámetros de paginación
     *
     * @param array $data
     * @return array
     */
    protected function validatePagination(array $data): array
    {
        $errors = [];

        // Validar page (opcional, por defecto 1)
        if (isset($data['page'])) {
            if (!is_numeric($data['page']) || (int)$data['page'] < 1) {
                $errors['page'] = 'La página debe ser un número entero mayor que 0';
            }
        }

        // Validar limit (opcional, por defecto 10)
        if (isset($data['limit'])) {
            if (!is_numeric($data['limit']) || (int)$data['limit'] < 1 || (int)$data['limit'] > 100) {
                $errors['limit'] = 'El límite debe ser un número entero entre 1 y 100';
            }
        }

        // Validar offset (opcional)
        if (isset($data['offset'])) {
            if (!is_numeric($data['offset']) || (int)$data['offset'] < 0) {
                $errors['offset'] = 'El offset debe ser un número entero mayor o igual a 0';
            }
        }

        return $errors;
    }
}

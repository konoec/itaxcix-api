<?php

namespace itaxcix\Shared\Validators\useCases\Profile;

use itaxcix\Shared\Validators\rules\contact\MobilePhoneRule;

class ChangePhoneRequestValidator
{
    private MobilePhoneRule $phoneRule;

    public function __construct()
    {
        $this->phoneRule = new MobilePhoneRule();
    }

    public function validate(array $data): array
    {
        $errors = [];

        // Validar que exista el teléfono
        if (!isset($data['phone'])) {
            return ['El número telefónico es requerido.'];
        }

        // Validar que sea string
        if (!is_string($data['phone'])) {
            return ['El número telefónico debe ser texto.'];
        }

        // Validar formato de teléfono
        $phoneErrors = $this->phoneRule->validate($data['phone']);
        if (!empty($phoneErrors)) {
            return $phoneErrors;
        }

        return [];
    }
}

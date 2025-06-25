<?php

namespace itaxcix\Shared\Validators\useCases\Profile;

use itaxcix\Shared\Validators\rules\contact\EmailRule;

class ChangeEmailRequestValidator
{
    private EmailRule $emailRule;

    public function __construct()
    {
        $this->emailRule = new EmailRule();
    }

    public function validate(array $data): array
    {
        $errors = [];

        // Validar que exista el correo
        if (!isset($data['email'])) {
            return ['El correo electrónico es requerido.'];
        }

        // Validar que sea string
        if (!is_string($data['email'])) {
            return ['El correo electrónico debe ser texto.'];
        }

        // Validar formato de correo
        $emailErrors = $this->emailRule->validate($data['email']);
        if (!empty($emailErrors)) {
            return $emailErrors;
        }

        return [];
    }
}

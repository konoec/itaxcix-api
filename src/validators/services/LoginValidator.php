<?php

namespace itaxcix\validators\services;

use Exception;
use itaxcix\models\entities\usuario\Usuario;

class LoginValidator {

    /**
     * Válida que el usuario exista y tenga credenciales correctas.
     *
     * @param Usuario|null $usuario
     * @param string $password
     * @return void
     * @throws Exception
     */
    public function validateCredentials(?Usuario $usuario, string $password): void {
        if (!$usuario) {
            throw new Exception("Credenciales inválidas.", 401);
        }

        if (!password_verify($password, $usuario->getClave())) {
            throw new Exception("Credenciales inválidas.", 401);
        }
    }

    /**
     * Válida que el usuario tenga al menos un rol asignado.
     *
     * @param array $roles
     * @return void
     * @throws Exception
     */
    public function validateRoles(array $roles): void {
        if (empty($roles)) {
            throw new Exception("El usuario no tiene roles asignados.", 403);
        }
    }
}
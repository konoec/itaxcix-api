<?php

namespace itaxcix\utils;

use Closure;
use Exception;

class ValidationHelper {
    /**
     * Maneja las excepciones lanzadas durante la validación y las convierte en un error genérico.
     *
     * @param Closure $callback Función que realiza la validación.
     * @param string $errorMessage Mensaje de error genérico a mostrar.
     * @param int $errorCode Código HTTP del error.
     * @throws Exception Error genérico con mensaje y código especificados.
     */
    public static function handle(
        Closure $callback,
        string $errorMessage = "Credenciales incorrectas.",
        int $errorCode = 401
    ): void {
        try {
            $callback();
        } catch (Exception $e) {
            throw new Exception($errorMessage, $errorCode);
        }
    }
}
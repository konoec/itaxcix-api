<?php

namespace itaxcix\Shared\Validators\generic;

class Base64ImageValidator {
    /**
     * Válida una imagen codificada en base64 y asegura que sea una imagen JPEG válida.
     *
     * @param string $value La cadena base64 a validar
     * @return array Arreglo de errores (vacío si no hay errores)
     */
    public static function validate(string $value): array {
        $errors = [];

        // Validar encabezado y extraer tipo MIME + datos codificados
        if (!preg_match('/^data:image\/(jpeg|jpg);base64,([a-zA-Z0-9+\/=]+)$/', $value, $matches)) {
            $errors[] = 'La imagen debe estar en formato JPEG codificada en base64 (data:image/jpeg;base64,...)';
            return $errors;
        }

        $mime = $matches[1];
        $base64Data = $matches[2];

        // Intentar decodificar el contenido base64
        $imageData = base64_decode($base64Data, true);
        if ($imageData === false) {
            $errors[] = 'El contenido base64 no pudo ser decodificado.';
            return $errors;
        }

        // Verificar que el contenido realmente sea una imagen válida
        $imageInfo = @getimagesizefromstring($imageData);
        if ($imageInfo === false || $imageInfo['mime'] !== 'image/jpeg') {
            $errors[] = 'El contenido no corresponde a una imagen JPEG válida.';
        }

        return $errors;
    }
}

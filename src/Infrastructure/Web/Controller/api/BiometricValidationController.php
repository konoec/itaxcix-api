<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\BiometricValidationRequestDTO;
use itaxcix\Shared\Validators\useCases\BiometricValidationValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BiometricValidationController extends AbstractController {
    public function validateBiometric(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar campos requeridos
            $validator = new BiometricValidationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // 3. Mapear al DTO de entrada
            $dto = new BiometricValidationRequestDTO(
                personId: (int) $data['personId'],
                imageBase64: (string) $data['imageBase64']
            );

            // 4. Llamar a lógica de validación (aquí iría el caso de uso)
            $result = $this->fakeBiometricValidationService($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Simulación de servicio de validación biométrica.
     */
    private function fakeBiometricValidationService(BiometricValidationRequestDTO $dto): array {
        // Aquí podría haber una validación real contra un sistema biométrico

        // Simular porcentaje de coincidencias
        $matchPercentage = rand(80, 100); // Entre 80% y 100%

        $valid = $matchPercentage >= 90;

        return [
            'personId' => $dto->personId,
            'valid' => $valid,
            'matchPercentage' => $matchPercentage . '%',
            'message' => $valid ? 'Identidad verificada' : 'No se pudo verificar la identidad'
        ];
    }
}
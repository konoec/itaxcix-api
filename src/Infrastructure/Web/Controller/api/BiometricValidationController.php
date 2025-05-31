<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\BiometricValidationUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\BiometricValidationRequestDTO;
use itaxcix\Shared\Validators\useCases\BiometricValidationValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BiometricValidationController extends AbstractController {

    private BiometricValidationUseCase $biometricValidationUseCase;

    public function __construct(BiometricValidationUseCase $biometricValidationUseCase) {
        $this->biometricValidationUseCase = $biometricValidationUseCase;
    }

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

            // 4. Lógica de validación
            $result = $this->biometricValidationUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
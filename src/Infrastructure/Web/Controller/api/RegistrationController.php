<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\RegistrationRequestDTO;
use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;
use itaxcix\Shared\Validators\useCases\RegistrationValidator;
use itaxcix\Shared\Validators\useCases\VerificationCodeValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegistrationController extends AbstractController {
    public function submitRegistrationData(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new RegistrationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new RegistrationRequestDTO(
                password: (string) $data['password'],
                contactTypeId: (int) $data['contactTypeId'],
                contactValue: (string) $data['contactValue'],
                personId: (int) $data['personId'],
                vehicleId: isset($data['vehicleId']) ? (int) $data['vehicleId'] : null
            );

            // 4. Simular lógica de registro (aquí iría el caso de uso)
            $result = $this->fakeRegistrationService($dto);

            // 5. Devolver resultado exitoso
            return $this->created($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Simulación de servicio de registro
     */
    private function fakeRegistrationService(RegistrationRequestDTO $dto): array
    {
        // Aquí iría la lógica real: guardar usuario, generar token, enviar código, etc.

        // Simulamos que se guardó correctamente
        return [
            'userId' => rand(1000, 9999), // ID simulado
            'message' => 'Usuario registrado. Código de verificación enviado.',
            'contactSentTo' => $dto->contactValue,
            'nextStep' => 'Verificar contacto'
        ];
    }

    // =========================================================================

    public function verifyContactCode(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar campos requeridos
            $validator = new VerificationCodeValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new VerificationCodeRequestDTO(
                userId: (int) $data['userId'],
                code: (string) $data['code']
            );

            // 4. Simular lógica de verificación (aquí iría el caso de uso)
            $result = $this->fakeVerificationService($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Simulación de servicio de verificación de código
     */
    private function fakeVerificationService(VerificationCodeRequestDTO $dto): array {
        // En una app real, esto validar contra una BD o sistema de autenticación

        return [
            'userId' => $dto->userId,
            'verified' => "",
            'message' => "" ? 'Código verificado correctamente' : 'Código inválido',
        ];
    }
}
<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\UserRegistrationUseCase;
use itaxcix\Core\UseCases\VerificationCodeUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\RegistrationRequestDTO;
use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;
use itaxcix\Shared\Validators\useCases\RegistrationValidator;
use itaxcix\Shared\Validators\useCases\VerificationCodeValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegistrationController extends AbstractController {
    private UserRegistrationUseCase $userRegistrationUseCase;
    private VerificationCodeUseCase $verificationCodeUseCase;
    public function __construct(UserRegistrationUseCase $userRegistrationUseCase, VerificationCodeUseCase $verificationCodeUseCase)
    {
        $this->userRegistrationUseCase = $userRegistrationUseCase;
        $this->verificationCodeUseCase = $verificationCodeUseCase;
    }

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

            // 4. Lógica de registro
            $result = $this->userRegistrationUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->created($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

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

            // 4. Lógica de verificación
            $result = $this->verificationCodeUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
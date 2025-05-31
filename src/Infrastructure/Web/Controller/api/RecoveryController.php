<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\ChangePasswordUseCase;
use itaxcix\Core\UseCases\StartPasswordRecoveryUseCase;
use itaxcix\Core\UseCases\VerifyRecoveryCodeUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\PasswordChangeRequestDTO;
use itaxcix\Shared\DTO\useCases\RecoveryStartRequestDTO;
use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;
use itaxcix\Shared\Validators\useCases\PasswordChangeValidator;
use itaxcix\Shared\Validators\useCases\RecoveryStartValidator;
use itaxcix\Shared\Validators\useCases\VerificationCodeValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RecoveryController extends AbstractController {
    private StartPasswordRecoveryUseCase $startPasswordRecoveryUseCase;
    private VerifyRecoveryCodeUseCase $verifyRecoveryCodeUseCase;
    private ChangePasswordUseCase $changePasswordUseCase;

    public function __construct(StartPasswordRecoveryUseCase $startPasswordRecoveryUseCase, ChangePasswordUseCase $changePasswordUseCase, VerifyRecoveryCodeUseCase $verificationCodeUseCase)
    {
        $this->startPasswordRecoveryUseCase = $startPasswordRecoveryUseCase;
        $this->changePasswordUseCase = $changePasswordUseCase;
        $this->verifyRecoveryCodeUseCase = $verificationCodeUseCase;
    }

    public function startPasswordRecovery(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new RecoveryStartValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new RecoveryStartRequestDTO(
                contactTypeId: (int) $data['contactTypeId'],
                contactValue: (string) $data['contactValue']
            );

            // 4. Simular lógica de inicio de recuperación
            $result = $this->startPasswordRecoveryUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function verifyRecoveryCode(ServerRequestInterface $request): ResponseInterface
    {
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

            // 4. Simular lógica de verificación de código
            $result = $this->verifyRecoveryCodeUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function changePassword(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener y validar body
            $data = $this->getJsonBody($request);
            $validator = new PasswordChangeValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->error(reset($errors), 400);
            }

            // 2. Mapear DTO
            $dto = new PasswordChangeRequestDTO(
                userId: (int) $data['userId'],
                newPassword: (string) $data['newPassword'],
                repeatPassword: (string) $data['repeatPassword']
            );

            // 3. Validar coincidencia con JWT
            $authUser = $request->getAttribute('user');
            if (!isset($authUser['userId']) || $dto->userId !== $authUser['userId']) {
                return $this->error('Usuario no autorizado', 403);
            }

            // 4. Ejecutar caso de uso de cambio de contraseña
            $result = $this->changePasswordUseCase->execute($dto);

            // 5. Responder con éxito
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
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
    // Simulamos una "base de datos" temporal para códigos de recuperación
    private array $recoveryCodes = [];

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
            $result = $this->fakeStartPasswordRecoveryService($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Simulación de servicio de inicio de recuperación de contraseña
     */
    private function fakeStartPasswordRecoveryService(RecoveryStartRequestDTO $dto): array {
        // Aquí iría la lógica real: buscar usuario, generar código, enviar por email/SMS

        // Generamos un código temporal
        $code = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardamos el código temporalmente (simulando base de datos)
        $userId = 1234; // Esto vendría de buscar por contacto
        $this->recoveryCodes[$userId] = [
            'code' => $code,
            'expiresAt' => time() + 300, // Expira en 5 minutos
        ];

        return [
            'userId' => $userId,
            'message' => 'Código de recuperación enviado',
            'contactSentTo' => $dto->contactValue,
            'codeSent' => $code, // En producción no devolver esto
            'nextStep' => 'Verificar código'
        ];
    }

    // =========================================================================

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
            $result = $this->fakeVerifyRecoveryCodeService($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Simulación de servicio de verificación de código de recuperación
     */
    private function fakeVerifyRecoveryCodeService(VerificationCodeRequestDTO $dto): array
    {
        // Verificamos si existe el código y no ha expirado
        if (!isset($this->recoveryCodes[$dto->userId])) {
            return [
                'valid' => false,
                'message' => 'No hay código pendiente para este usuario'
            ];
        }

        $record = $this->recoveryCodes[$dto->userId];

        if ($record['expiresAt'] < time()) {
            return [
                'valid' => false,
                'message' => 'El código ha expirado'
            ];
        }

        $valid = hash_equals($record['code'], $dto->code);

        return [
            'valid' => $valid,
            'message' => $valid ? 'Código verificado correctamente' : 'Código inválido'
        ];
    }

    // =========================================================================

    public function changePassword(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar campos requeridos
            $validator = new PasswordChangeValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new PasswordChangeRequestDTO(
                userId: (int) $data['userId'],
                newPassword: (string) $data['newPassword']
            );

            // 4. Simular lógica de cambio de contraseña
            $result = $this->fakeChangePasswordService($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Simulación de servicio de cambio de contraseña
     */
    private function fakeChangePasswordService(PasswordChangeRequestDTO $dto): array {
        // Aquí iría la lógica real: actualizar contraseña en la BD

        // Simulamos éxito
        unset($this->recoveryCodes[$dto->userId]); // Limpiamos el código usado

        return [
            'userId' => $dto->userId,
            'message' => 'Contraseña actualizada correctamente'
        ];
    }
}
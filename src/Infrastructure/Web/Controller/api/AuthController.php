<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\AuthLoginRequestDTO;
use itaxcix\Shared\DTO\useCases\AuthLoginResponseDTO;
use itaxcix\Shared\Validators\useCases\AuthLoginValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Random\RandomException;

class AuthController extends AbstractController {
    public function login(ServerRequestInterface $request): ResponseInterface {
        try {
            $data = $this->getJsonBody($request);

            $validator = new AuthLoginValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Sí pasa la validación, crear DTO
            $loginDto = new AuthLoginRequestDTO(
                username: $data['username'],
                password: $data['password']
            );

            // Continuar con autenticación...
            $user = $this->fakeAuthenticate($loginDto);

            if (!$user) {
                return $this->error('Credenciales inválidas', 401);
            }

            // Devolver respuesta exitosa
            $authResponse = new AuthLoginResponseDTO(
                token: bin2hex(random_bytes(50)),
                userId: $user['id'],
                username: $user['username'],
                roles: $user['roles'],
                availability: $user['availability'] ?? null
            );

            return $this->ok($authResponse);

        } catch (InvalidArgumentException|RandomException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    private function fakeAuthenticate(AuthLoginRequestDTO $dto): ?array
    {
        // Datos simulados de usuarios
        $users = [
            [
                'id' => 1,
                'username' => 'admin',
                'password' => password_hash('1234', PASSWORD_DEFAULT),
                'roles' => ['ROLE_ADMIN'],
                'availability' => true
            ],
            [
                'id' => 2,
                'username' => 'user',
                'password' => password_hash('abcd', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'availability' => false
            ]
        ];

        foreach ($users as $user) {
            if ($user['username'] === $dto->username && password_verify($dto->password, $user['password'])) {
                return $user;
            }
        }

        return null;
    }
}
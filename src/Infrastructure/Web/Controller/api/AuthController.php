<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\LoginUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\AuthLoginRequestDTO;
use itaxcix\Shared\Validators\useCases\AuthLoginValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends AbstractController
{
    private LoginUseCase $loginUseCase;

    public function __construct(LoginUseCase $loginUseCase)
    {
        $this->loginUseCase = $loginUseCase;
    }

    public function login(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $validator = new AuthLoginValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO de entrada
            $loginDto = new AuthLoginRequestDTO(
                documentValue: $data['documentValue'],
                password: $data['password'],
                web: $data['web'] ?? false
            );

            // Usar el UseCase
            $authResponse = $this->loginUseCase->execute($loginDto);

            if (!$authResponse) {
                return $this->error('Credenciales inválidas', 401);
            }

            return $this->ok($authResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
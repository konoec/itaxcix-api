<?php

namespace itaxcix\Infrastructure\Web\Controller\generic;

use InvalidArgumentException;
use itaxcix\Shared\DTO\generic\BaseResponseDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;

abstract class AbstractController {
    protected function jsonResponse(mixed $data, int $statusCode = 200): ResponseInterface {
        $baseResponse = new BaseResponseDTO(
            success: $statusCode < 400,
            message: match ($statusCode) {
                200 => 'OK',
                201 => 'Creado',
                204 => 'Sin contenido',
                400 => 'Petición inválida',
                401 => 'No autorizado',
                404 => 'No encontrado',
                default => 'Error interno del servidor'
            },
            data: $statusCode < 400 ? $data : null,
            error: $statusCode >= 400 ? ['message' => is_string($data) ? $data : ''] : null
        );

        $json = json_encode($baseResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return new Response($statusCode, [
            'Content-Type' => 'application/json'
        ], $json);
    }

    protected function ok(mixed $data = null): ResponseInterface {
        return $this->jsonResponse($data, 200);
    }

    protected function created(mixed $data = null): ResponseInterface {
        return $this->jsonResponse($data, 201);
    }

    protected function noContent(): ResponseInterface {
        return new Response(204);
    }

    protected function error(string $message, int $code = 500): ResponseInterface {
        return $this->jsonResponse($message, $code);
    }

    protected function getJsonBody(ServerRequestInterface $request): array {
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $contents = $request->getBody()->getContents();
            $data = json_decode($contents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }

            return $data;
        }

        throw new InvalidArgumentException('Se requiere Content-Type: application/json');
    }

    protected function validationError(array $errors): ResponseInterface {
        $errorMessage = !empty($errors) ? reset($errors) : 'Petición inválida';

        return $this->jsonResponse($errorMessage, 400);
    }

    protected function getHeader(ServerRequestInterface $request, string $headerName, ?string $default = null): ?string {
        return $request->hasHeader($headerName)
            ? $request->getHeaderLine($headerName)
            : $default;
    }
}
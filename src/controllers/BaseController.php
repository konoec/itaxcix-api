<?php

namespace itaxcix\controllers;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\StreamInterface;
use Exception;

abstract class BaseController {

    /**
     * Método para obtener el cuerpo de la solicitud en formato JSON.
     *
     * @param Request $request
     * @return array|string|int|float|bool|null
     * @throws Exception
     */
    protected function getJsonBody(Request $request): array|string|int|float|bool|null {
        $body = $request->getBody()->getContents();
        if (empty(trim($body))) {
            throw new Exception("Cuerpo de solicitud vacío", 400);
        }
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Formato JSON inválido: " . json_last_error_msg(), 400);
        }
        return $data;
    }

    /**
     * Método para obtener el cuerpo de la solicitud en formato JSON y asegurarse de que sea un objeto.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    protected function getJsonObject(Request $request): array {
        $data = $this->getJsonBody($request);
        if (!is_array($data)) {
            throw new Exception("Se esperaba un objeto JSON", 400);
        }
        return $data;
    }

    /**
     * Método para obtener el cuerpo de la solicitud en formato JSON y asegurarse de que sea un arreglo.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    protected function getJsonArray(Request $request): array {
        $data = $this->getJsonBody($request);
        if (!is_array($data)) {
            throw new Exception("Se esperaba un arreglo JSON", 400);
        }
        return $data;
    }

    /**
     * Método para enviar una respuesta JSON.
     *
     * @param Response $response
     * @param array $data
     * @param int $status
     * @return Response
     */
    protected function respondWithJson(Response $response, array $data, int $status = 200): Response {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        $stream = $this->createStream($payload);
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);
    }

    /**
     * Método para crear un stream a partir de un contenido.
     *
     * @param string $content
     * @return StreamInterface
     */
    private function createStream(string $content): StreamInterface {
        $stream = new Stream(fopen('php://memory', 'r+'));
        $stream->write($content);
        $stream->rewind();
        return $stream;
    }

    /**
     * Método para manejar errores y enviar una respuesta JSON con el error.
     *
     * @param Exception $e
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    protected function handleError(Exception $e, Request $request, Response $response): Response {
        $code = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() <= 599 ? $e->getCode() : 500;

        $errorResponse = [
            'error' => [
                'message' => $e->getMessage(),
                'code' => $code,
                'status' => $code,
                'timestamp' => date('c'),
                'path' => $request->getUri()->getPath(),
            ]
        ];

        return $this->respondWithJson($response, $errorResponse, $code);
    }
}
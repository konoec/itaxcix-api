<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Driver;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Driver\ToggleDriverStatusUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DriverStatusController extends AbstractController
{
    private ToggleDriverStatusUseCase $toggleDriverStatusUseCase;
    public function __construct(ToggleDriverStatusUseCase $toggleDriverStatusUseCase)
    {
        $this->toggleDriverStatusUseCase = $toggleDriverStatusUseCase;
    }
    public function toggleActiveStatus(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $driverId = (int) $request->getAttribute('id');

            if ($driverId <= 0) {
                return $this->error('ID de conductor inválido', 400);
            }

            $driverStatus = $this->toggleDriverStatusUseCase->execute($driverId);

            if ($driverStatus === null) {
                return $this->error('Conductor no encontrado o error al cambiar el estado', 404);
            }

            return $this->ok($driverStatus);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
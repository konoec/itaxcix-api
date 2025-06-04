<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admission;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Admission\GetDriverDetailsUseCase;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PendingDriversController extends AbstractController
{
    private GetDriverDetailsUseCase $getDriverDetailsUseCase;
    private GetPendingDriversUseCase $getPendingDriversUseCase;
    public function __construct(GetDriverDetailsUseCase $getDriverDetailsUseCase, GetPendingDriversUseCase $getPendingDriversUseCase)
    {
        $this->getDriverDetailsUseCase = $getDriverDetailsUseCase;
        $this->getPendingDriversUseCase = $getPendingDriversUseCase;
    }

    public function getAllPendingDrivers(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $pendingDriversResponse = $this->getPendingDriversUseCase->execute();

            if ($pendingDriversResponse === null) {
                return $this->error('No se encontraron conductores pendientes', 404);
            }

            return $this->ok($pendingDriversResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
    public function getDriverDetails(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $driverId = (int)$request->getAttribute('id');

            if ($driverId <= 0) {
                return $this->error('ID de conductor inválido', 400);
            }

            $driverDetailsResponse = $this->getDriverDetailsUseCase->execute($driverId);

            if ($driverDetailsResponse === null) {
                return $this->error('Conductor no encontrado', 404);
            }

            return $this->ok($driverDetailsResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
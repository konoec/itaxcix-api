<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Admission;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Admission\ApproveDriverAdmissionUseCase;
use itaxcix\Core\UseCases\Admission\RejectDriverAdmissionUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Admission\ApproveDriverRequestDto;
use itaxcix\Shared\DTO\useCases\Admission\RejectDriverRequestDto;
use itaxcix\Shared\Validators\useCases\Admission\ApproveDriverValidator;
use itaxcix\Shared\Validators\useCases\Admission\RejectDriverValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DriverApprovalController extends AbstractController
{
    private ApproveDriverAdmissionUseCase $approveDriverUseCase;
    private RejectDriverAdmissionUseCase $rejectDriverUseCase;

    public function __construct(ApproveDriverAdmissionUseCase $approveDriverUseCase, RejectDriverAdmissionUseCase $rejectDriverUseCase)
    {
        $this->approveDriverUseCase = $approveDriverUseCase;
        $this->rejectDriverUseCase = $rejectDriverUseCase;
    }

    public function approveDriver(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $validator = new ApproveDriverValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO de entrada
            $approveDriverDto = new ApproveDriverRequestDto(
                driverId: $data['driverId']
            );

            // Usar el UseCase
            $authResponse = $this->approveDriverUseCase->execute($approveDriverDto);

            if (!$authResponse) {
                return $this->error('No se pudo aprobar al conductor', 400);
            }

            return $this->ok($authResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
    public function rejectDriver(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            $validator = new RejectDriverValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO de entrada
            $rejectDriverDto = new RejectDriverRequestDto(
                driverId: $data['driverId']
            );

            // Usar el UseCase
            $authResponse = $this->rejectDriverUseCase->execute($rejectDriverDto);

            if (!$authResponse) {
                return $this->error('No se pudo rechazar al conductor', 400);
            }

            return $this->ok($authResponse);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
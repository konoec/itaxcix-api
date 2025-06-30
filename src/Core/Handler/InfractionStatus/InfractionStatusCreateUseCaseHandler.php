<?php

namespace itaxcix\Core\Handler\InfractionStatus;

use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusCreateUseCase;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusResponseDTO;

class InfractionStatusCreateUseCaseHandler
{
    private InfractionStatusCreateUseCase $useCase;

    public function __construct(InfractionStatusCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(InfractionStatusRequestDTO $request): InfractionStatusResponseDTO
    {
        return $this->useCase->execute($request);
    }
}


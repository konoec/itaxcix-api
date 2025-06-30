<?php

namespace itaxcix\Core\Handler\InfractionStatus;

use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusUpdateUseCase;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusResponseDTO;

class InfractionStatusUpdateUseCaseHandler
{
    private InfractionStatusUpdateUseCase $useCase;

    public function __construct(InfractionStatusUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, InfractionStatusRequestDTO $request): InfractionStatusResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}


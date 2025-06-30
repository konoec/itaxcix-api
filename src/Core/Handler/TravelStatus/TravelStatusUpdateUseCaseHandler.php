<?php

namespace itaxcix\Core\Handler\TravelStatus;

use itaxcix\Core\UseCases\TravelStatus\TravelStatusUpdateUseCase;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusResponseDTO;

class TravelStatusUpdateUseCaseHandler
{
    private TravelStatusUpdateUseCase $useCase;

    public function __construct(TravelStatusUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, TravelStatusRequestDTO $request): TravelStatusResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}


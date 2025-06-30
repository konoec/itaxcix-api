<?php

namespace itaxcix\Core\Handler\TravelStatus;

use itaxcix\Core\UseCases\TravelStatus\TravelStatusCreateUseCase;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusResponseDTO;

class TravelStatusCreateUseCaseHandler
{
    private TravelStatusCreateUseCase $useCase;

    public function __construct(TravelStatusCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(TravelStatusRequestDTO $request): TravelStatusResponseDTO
    {
        return $this->useCase->execute($request);
    }
}


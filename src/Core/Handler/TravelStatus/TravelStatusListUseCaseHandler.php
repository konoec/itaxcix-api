<?php

namespace itaxcix\Core\Handler\TravelStatus;

use itaxcix\Core\UseCases\TravelStatus\TravelStatusListUseCase;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusPaginationRequestDTO;

class TravelStatusListUseCaseHandler
{
    private TravelStatusListUseCase $useCase;

    public function __construct(TravelStatusListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(TravelStatusPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}


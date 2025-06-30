<?php

namespace itaxcix\Core\Handler\FuelType;

use itaxcix\Core\UseCases\FuelType\FuelTypeListUseCase;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypePaginationRequestDTO;

class FuelTypeListUseCaseHandler
{
    private FuelTypeListUseCase $useCase;

    public function __construct(FuelTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(FuelTypePaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

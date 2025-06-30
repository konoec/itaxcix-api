<?php

namespace itaxcix\Core\Handler\FuelType;

use itaxcix\Core\UseCases\FuelType\FuelTypeCreateUseCase;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeResponseDTO;

class FuelTypeCreateUseCaseHandler
{
    private FuelTypeCreateUseCase $useCase;

    public function __construct(FuelTypeCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(FuelTypeRequestDTO $request): FuelTypeResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

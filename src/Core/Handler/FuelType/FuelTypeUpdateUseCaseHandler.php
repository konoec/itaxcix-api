<?php

namespace itaxcix\Core\Handler\FuelType;

use itaxcix\Core\UseCases\FuelType\FuelTypeUpdateUseCase;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeResponseDTO;

class FuelTypeUpdateUseCaseHandler
{
    private FuelTypeUpdateUseCase $useCase;

    public function __construct(FuelTypeUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, FuelTypeRequestDTO $request): FuelTypeResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}

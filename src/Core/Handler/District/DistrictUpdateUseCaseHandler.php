<?php

namespace itaxcix\Core\Handler\District;

use itaxcix\Core\UseCases\District\DistrictUpdateUseCase;
use itaxcix\Shared\DTO\useCases\District\DistrictRequestDTO;
use itaxcix\Shared\DTO\useCases\District\DistrictResponseDTO;

class DistrictUpdateUseCaseHandler
{
    private DistrictUpdateUseCase $useCase;

    public function __construct(DistrictUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DistrictRequestDTO $dto): DistrictResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

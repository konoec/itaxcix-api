<?php

namespace itaxcix\Core\Handler\District;

use itaxcix\Core\UseCases\District\DistrictCreateUseCase;
use itaxcix\Shared\DTO\useCases\District\DistrictRequestDTO;
use itaxcix\Shared\DTO\useCases\District\DistrictResponseDTO;

class DistrictCreateUseCaseHandler
{
    private DistrictCreateUseCase $useCase;

    public function __construct(DistrictCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DistrictRequestDTO $dto): DistrictResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

<?php

namespace itaxcix\Core\Handler\District;

use itaxcix\Core\UseCases\District\DistrictListUseCase;
use itaxcix\Shared\DTO\useCases\District\DistrictPaginationRequestDTO;

class DistrictListUseCaseHandler
{
    private DistrictListUseCase $useCase;

    public function __construct(DistrictListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DistrictPaginationRequestDTO $dto): array
    {
        return $this->useCase->execute($dto);
    }
}

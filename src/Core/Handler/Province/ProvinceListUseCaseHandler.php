<?php

namespace itaxcix\Core\Handler\Province;

use itaxcix\Core\UseCases\Province\ProvinceListUseCase;
use itaxcix\Shared\DTO\useCases\Province\ProvincePaginationRequestDTO;

class ProvinceListUseCaseHandler
{
    private ProvinceListUseCase $useCase;

    public function __construct(ProvinceListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ProvincePaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

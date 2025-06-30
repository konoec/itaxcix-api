<?php

namespace itaxcix\Core\Handler\Brand;

use itaxcix\Core\UseCases\Brand\BrandListUseCase;
use itaxcix\Shared\DTO\useCases\Brand\BrandPaginationRequestDTO;

class BrandListUseCaseHandler
{
    private BrandListUseCase $useCase;

    public function __construct(BrandListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(BrandPaginationRequestDTO $dto): array
    {
        return $this->useCase->execute($dto);
    }
}

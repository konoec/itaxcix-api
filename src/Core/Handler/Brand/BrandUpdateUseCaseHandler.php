<?php

namespace itaxcix\Core\Handler\Brand;

use itaxcix\Core\UseCases\Brand\BrandUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Brand\BrandRequestDTO;
use itaxcix\Shared\DTO\useCases\Brand\BrandResponseDTO;

class BrandUpdateUseCaseHandler
{
    private BrandUpdateUseCase $useCase;

    public function __construct(BrandUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, BrandRequestDTO $dto): BrandResponseDTO
    {
        return $this->useCase->execute($id, $dto);
    }
}

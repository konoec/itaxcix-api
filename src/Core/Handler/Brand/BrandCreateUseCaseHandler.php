<?php

namespace itaxcix\Core\Handler\Brand;

use itaxcix\Core\UseCases\Brand\BrandCreateUseCase;
use itaxcix\Shared\DTO\useCases\Brand\BrandRequestDTO;
use itaxcix\Shared\DTO\useCases\Brand\BrandResponseDTO;

class BrandCreateUseCaseHandler
{
    private BrandCreateUseCase $useCase;

    public function __construct(BrandCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(BrandRequestDTO $dto): BrandResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}

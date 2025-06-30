<?php

namespace itaxcix\Core\Handler\Province;

use itaxcix\Core\UseCases\Province\ProvinceUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Province\ProvinceRequestDTO;
use itaxcix\Shared\DTO\useCases\Province\ProvinceResponseDTO;

class ProvinceUpdateUseCaseHandler
{
    private ProvinceUpdateUseCase $useCase;

    public function __construct(ProvinceUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, ProvinceRequestDTO $request): ProvinceResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}

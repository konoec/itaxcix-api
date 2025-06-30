<?php

namespace itaxcix\Core\Handler\Province;

use itaxcix\Core\UseCases\Province\ProvinceCreateUseCase;
use itaxcix\Shared\DTO\useCases\Province\ProvinceRequestDTO;
use itaxcix\Shared\DTO\useCases\Province\ProvinceResponseDTO;

class ProvinceCreateUseCaseHandler
{
    private ProvinceCreateUseCase $useCase;

    public function __construct(ProvinceCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ProvinceRequestDTO $request): ProvinceResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

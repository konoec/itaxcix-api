<?php

namespace itaxcix\Core\Handler\Color;

use itaxcix\Core\UseCases\Color\ColorCreateUseCase;
use itaxcix\Shared\DTO\useCases\Color\ColorRequestDTO;
use itaxcix\Shared\DTO\useCases\Color\ColorResponseDTO;

class ColorCreateUseCaseHandler
{
    private ColorCreateUseCase $useCase;

    public function __construct(ColorCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ColorRequestDTO $request): ColorResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

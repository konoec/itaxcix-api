<?php

namespace itaxcix\Core\Handler\Color;

use itaxcix\Core\UseCases\Color\ColorUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Color\ColorRequestDTO;
use itaxcix\Shared\DTO\useCases\Color\ColorResponseDTO;

class ColorUpdateUseCaseHandler
{
    private ColorUpdateUseCase $useCase;

    public function __construct(ColorUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, ColorRequestDTO $request): ColorResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}

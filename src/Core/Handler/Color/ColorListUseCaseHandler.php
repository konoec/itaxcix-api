<?php

namespace itaxcix\Core\Handler\Color;

use itaxcix\Core\UseCases\Color\ColorListUseCase;
use itaxcix\Shared\DTO\useCases\Color\ColorPaginationRequestDTO;

class ColorListUseCaseHandler
{
    private ColorListUseCase $useCase;

    public function __construct(ColorListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ColorPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

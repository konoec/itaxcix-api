<?php

namespace itaxcix\Core\Handler\Color;

use itaxcix\Core\UseCases\Color\ColorDeleteUseCase;

class ColorDeleteUseCaseHandler
{
    private ColorDeleteUseCase $useCase;

    public function __construct(ColorDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}

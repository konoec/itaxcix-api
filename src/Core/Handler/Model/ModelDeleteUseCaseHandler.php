<?php

namespace itaxcix\Core\Handler\Model;

use itaxcix\Core\UseCases\Model\ModelDeleteUseCase;

class ModelDeleteUseCaseHandler
{
    private ModelDeleteUseCase $useCase;

    public function __construct(ModelDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}

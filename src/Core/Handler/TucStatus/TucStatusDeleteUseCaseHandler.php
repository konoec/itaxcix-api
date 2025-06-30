<?php

namespace itaxcix\Core\Handler\TucStatus;

use itaxcix\Core\UseCases\TucStatus\TucStatusDeleteUseCase;

class TucStatusDeleteUseCaseHandler
{
    private TucStatusDeleteUseCase $useCase;

    public function __construct(TucStatusDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}


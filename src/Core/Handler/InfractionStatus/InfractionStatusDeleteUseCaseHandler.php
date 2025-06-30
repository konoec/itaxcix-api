<?php

namespace itaxcix\Core\Handler\InfractionStatus;

use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusDeleteUseCase;

class InfractionStatusDeleteUseCaseHandler
{
    private InfractionStatusDeleteUseCase $useCase;

    public function __construct(InfractionStatusDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}


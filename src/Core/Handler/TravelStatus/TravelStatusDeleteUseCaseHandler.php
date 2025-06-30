<?php

namespace itaxcix\Core\Handler\TravelStatus;

use itaxcix\Core\UseCases\TravelStatus\TravelStatusDeleteUseCase;

class TravelStatusDeleteUseCaseHandler
{
    private TravelStatusDeleteUseCase $useCase;

    public function __construct(TravelStatusDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}


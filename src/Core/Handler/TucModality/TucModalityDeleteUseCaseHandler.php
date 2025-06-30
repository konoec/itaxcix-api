<?php

namespace itaxcix\Core\Handler\TucModality;

use itaxcix\Core\UseCases\TucModality\TucModalityDeleteUseCase;

class TucModalityDeleteUseCaseHandler
{
    private TucModalityDeleteUseCase $useCase;

    public function __construct(TucModalityDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}


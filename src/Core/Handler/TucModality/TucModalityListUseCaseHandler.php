<?php

namespace itaxcix\Core\Handler\TucModality;

use itaxcix\Core\UseCases\TucModality\TucModalityListUseCase;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityPaginationRequestDTO;

class TucModalityListUseCaseHandler
{
    private TucModalityListUseCase $useCase;

    public function __construct(TucModalityListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(TucModalityPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}


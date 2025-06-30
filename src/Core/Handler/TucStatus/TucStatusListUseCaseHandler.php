<?php

namespace itaxcix\Core\Handler\TucStatus;

use itaxcix\Core\UseCases\TucStatus\TucStatusListUseCase;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusPaginationRequestDTO;

class TucStatusListUseCaseHandler
{
    private TucStatusListUseCase $useCase;

    public function __construct(TucStatusListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(TucStatusPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}


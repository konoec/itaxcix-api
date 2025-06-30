<?php

namespace itaxcix\Core\Handler\TucStatus;

use itaxcix\Core\UseCases\TucStatus\TucStatusCreateUseCase;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusResponseDTO;

class TucStatusCreateUseCaseHandler
{
    private TucStatusCreateUseCase $useCase;

    public function __construct(TucStatusCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(TucStatusRequestDTO $request): TucStatusResponseDTO
    {
        return $this->useCase->execute($request);
    }
}


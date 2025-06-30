<?php

namespace itaxcix\Core\Handler\TucStatus;

use itaxcix\Core\UseCases\TucStatus\TucStatusUpdateUseCase;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusResponseDTO;

class TucStatusUpdateUseCaseHandler
{
    private TucStatusUpdateUseCase $useCase;

    public function __construct(TucStatusUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, TucStatusRequestDTO $request): TucStatusResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}


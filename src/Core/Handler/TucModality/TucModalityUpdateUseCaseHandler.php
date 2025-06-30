<?php

namespace itaxcix\Core\Handler\TucModality;

use itaxcix\Core\UseCases\TucModality\TucModalityUpdateUseCase;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityRequestDTO;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityResponseDTO;

class TucModalityUpdateUseCaseHandler
{
    private TucModalityUpdateUseCase $useCase;

    public function __construct(TucModalityUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, TucModalityRequestDTO $request): TucModalityResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}


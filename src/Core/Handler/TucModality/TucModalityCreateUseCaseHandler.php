<?php

namespace itaxcix\Core\Handler\TucModality;

use itaxcix\Core\UseCases\TucModality\TucModalityCreateUseCase;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityRequestDTO;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityResponseDTO;

class TucModalityCreateUseCaseHandler
{
    private TucModalityCreateUseCase $useCase;

    public function __construct(TucModalityCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(TucModalityRequestDTO $request): TucModalityResponseDTO
    {
        return $this->useCase->execute($request);
    }
}


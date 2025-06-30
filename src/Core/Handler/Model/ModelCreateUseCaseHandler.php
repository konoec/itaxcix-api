<?php

namespace itaxcix\Core\Handler\Model;

use itaxcix\Core\UseCases\Model\ModelCreateUseCase;
use itaxcix\Shared\DTO\useCases\Model\ModelRequestDTO;
use itaxcix\Shared\DTO\useCases\Model\ModelResponseDTO;

class ModelCreateUseCaseHandler
{
    private ModelCreateUseCase $useCase;

    public function __construct(ModelCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ModelRequestDTO $requestDTO): ModelResponseDTO
    {
        return $this->useCase->execute($requestDTO);
    }
}

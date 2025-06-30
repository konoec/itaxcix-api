<?php

namespace itaxcix\Core\Handler\Model;

use itaxcix\Core\UseCases\Model\ModelUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Model\ModelRequestDTO;
use itaxcix\Shared\DTO\useCases\Model\ModelResponseDTO;

class ModelUpdateUseCaseHandler
{
    private ModelUpdateUseCase $useCase;

    public function __construct(ModelUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, ModelRequestDTO $requestDTO): ModelResponseDTO
    {
        return $this->useCase->execute($id, $requestDTO);
    }
}

<?php

namespace itaxcix\Core\Handler\Model;

use itaxcix\Core\UseCases\Model\ModelListUseCase;
use itaxcix\Shared\DTO\useCases\Model\ModelPaginationRequestDTO;

class ModelListUseCaseHandler
{
    private ModelListUseCase $useCase;

    public function __construct(ModelListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ModelPaginationRequestDTO $requestDTO): array
    {
        return $this->useCase->execute($requestDTO);
    }
}

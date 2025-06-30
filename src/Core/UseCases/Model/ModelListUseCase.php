<?php

namespace itaxcix\Core\UseCases\Model;

use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Model\ModelPaginationRequestDTO;

class ModelListUseCase
{
    private ModelRepositoryInterface $modelRepository;

    public function __construct(ModelRepositoryInterface $modelRepository)
    {
        $this->modelRepository = $modelRepository;
    }

    public function execute(ModelPaginationRequestDTO $requestDTO): array
    {
        $filters = $requestDTO->getFilters();

        $models = $this->modelRepository->findAll(
            $requestDTO->page,
            $requestDTO->perPage,
            $filters,
            $requestDTO->sortBy,
            $requestDTO->sortOrder
        );

        $total = $this->modelRepository->countTotal($filters);

        return [
            'data' => $models,
            'pagination' => [
                'page' => $requestDTO->page,
                'perPage' => $requestDTO->perPage,
                'total' => $total,
                'totalPages' => ceil($total / $requestDTO->perPage)
            ]
        ];
    }
}

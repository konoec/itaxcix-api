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

        // Mapear los modelos al formato requerido
        $data = array_map(function($model) {
            return [
                'id' => $model->getId(),
                'name' => $model->getName(),
                'brandId' => $model->getBrand()->getId(),
                'brandName' => $model->getBrand()->getName(),
                'active' => $model->isActive(),
            ];
        }, $models);

        return [
            'data' => $data,
            'pagination' => [
                'page' => $requestDTO->page,
                'perPage' => $requestDTO->perPage,
                'total' => $total,
                'totalPages' => ceil($total / $requestDTO->perPage)
            ]
        ];
    }
}

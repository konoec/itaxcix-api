<?php

namespace itaxcix\Core\UseCases\District;

use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Shared\DTO\useCases\District\DistrictPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\District\DistrictResponseDTO;

class DistrictListUseCase
{
    private DistrictRepositoryInterface $districtRepository;

    public function __construct(DistrictRepositoryInterface $districtRepository)
    {
        $this->districtRepository = $districtRepository;
    }

    public function execute(DistrictPaginationRequestDTO $dto): array
    {
        $result = $this->districtRepository->findAll(
            page: $dto->getPage(),
            perPage: $dto->getPerPage(),
            search: $dto->getSearch(),
            name: $dto->getName(),
            provinceId: $dto->getProvinceId(),
            ubigeo: $dto->getUbigeo(),
            sortBy: $dto->getSortBy(),
            sortDirection: $dto->getSortDirection()
        );

        // Convertir modelos a DTOs de respuesta
        $items = array_map(
            fn($model) => DistrictResponseDTO::fromModel($model),
            $result['items']
        );

        return [
            'items' => $items,
            'meta' => [
                'total' => $result['total'],
                'perPage' => $result['perPage'],
                'currentPage' => $result['page'],
                'lastPage' => $result['lastPage'],
                'search' => $dto->getSearch(),
                'filters' => [
                    'name' => $dto->getName(),
                    'provinceId' => $dto->getProvinceId(),
                    'ubigeo' => $dto->getUbigeo()
                ],
                'sortBy' => $dto->getSortBy(),
                'sortDirection' => $dto->getSortDirection()
            ]
        ];
    }
}

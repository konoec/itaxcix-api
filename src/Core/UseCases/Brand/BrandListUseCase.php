<?php

namespace itaxcix\Core\UseCases\Brand;

use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Brand\BrandPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Brand\BrandResponseDTO;

class BrandListUseCase
{
    private BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function execute(BrandPaginationRequestDTO $dto): array
    {
        $brands = $this->brandRepository->findAll(
            page: $dto->getPage(),
            perPage: $dto->getPerPage(),
            search: $dto->getSearch(),
            name: $dto->getName(),
            active: $dto->getActive(),
            sortBy: $dto->getSortBy(),
            sortDirection: $dto->getSortDirection(),
            onlyActive: $dto->isOnlyActive()
        );

        $total = $this->brandRepository->count(
            search: $dto->getSearch(),
            name: $dto->getName(),
            active: $dto->getActive(),
            onlyActive: $dto->isOnlyActive()
        );

        $brandDTOs = array_map(function($brand) {
            return new BrandResponseDTO(
                id: $brand->getId(),
                name: $brand->getName(),
                active: $brand->isActive()
            );
        }, $brands);

        $lastPage = ceil($total / $dto->getPerPage());

        return [
            'items' => $brandDTOs,
            'meta' => [
                'total' => $total,
                'perPage' => $dto->getPerPage(),
                'currentPage' => $dto->getPage(),
                'lastPage' => $lastPage,
                'search' => $dto->getSearch(),
                'filters' => [
                    'name' => $dto->getName(),
                    'active' => $dto->getActive(),
                    'onlyActive' => $dto->isOnlyActive()
                ],
                'sortBy' => $dto->getSortBy(),
                'sortDirection' => $dto->getSortDirection()
            ]
        ];
    }
}

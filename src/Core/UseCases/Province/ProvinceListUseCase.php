<?php

namespace itaxcix\Core\UseCases\Province;

use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Province\ProvincePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Province\ProvinceResponseDTO;

class ProvinceListUseCase
{
    private ProvinceRepositoryInterface $provinceRepository;

    public function __construct(ProvinceRepositoryInterface $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    public function execute(ProvincePaginationRequestDTO $request): array
    {
        $provinces = $this->provinceRepository->findAll(
            $request->getPage(),
            $request->getPerPage(),
            $request->getFilters(),
            $request->getSearch(),
            $request->getSortBy(),
            $request->getSortOrder()
        );

        $total = $this->provinceRepository->countTotal($request->getFilters(), $request->getSearch());

        $provincesData = array_map(function($province) {
            return ProvinceResponseDTO::fromModel($province)->toArray();
        }, $provinces);

        return [
            'data' => $provincesData,
            'pagination' => [
                'page' => $request->getPage(),
                'perPage' => $request->getPerPage(),
                'total' => $total,
                'totalPages' => ceil($total / $request->getPerPage())
            ]
        ];
    }
}

<?php

namespace itaxcix\Core\UseCases\Department;

use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Department\DepartmentPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Department\DepartmentResponseDTO;

class DepartmentListUseCase
{
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(DepartmentPaginationRequestDTO $request): array
    {
        $departments = $this->departmentRepository->findAll(
            $request->page,
            $request->limit,
            $request->search,
            $request->orderBy,
            $request->orderDirection
        );

        $total = $this->departmentRepository->count($request->search);
        $totalPages = ceil($total / $request->limit);

        $departmentDTOs = array_map(function($department) {
            return new DepartmentResponseDTO(
                $department->getId(),
                $department->getName(),
                $department->getUbigeo()
            );
        }, $departments);

        return [
            'data' => array_map(fn($dto) => $dto->toArray(), $departmentDTOs),
            'pagination' => [
                'page' => $request->page,
                'limit' => $request->limit,
                'total' => $total,
                'totalPages' => $totalPages,
                'hasNext' => $request->page < $totalPages,
                'hasPrev' => $request->page > 1
            ]
        ];
    }
}

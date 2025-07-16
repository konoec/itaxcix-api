<?php

namespace itaxcix\Core\UseCases\ContactType;

use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypePaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeResponseDTO;

class ContactTypeListUseCase
{
    private ContactTypeRepositoryInterface $contactTypeRepository;

    public function __construct(ContactTypeRepositoryInterface $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }

    /**
     * @param ContactTypePaginationRequestDTO $request
     * @return array{data: ContactTypeResponseDTO[], total: int, page: int, limit: int}
     */
    public function execute(ContactTypePaginationRequestDTO $request): array
    {
        $filters = [];
        if ($request->name !== null) {
            $filters['name'] = $request->name;
        }
        if ($request->active !== null) {
            $filters['active'] = $request->active;
        }

        $orderBy = [$request->sortBy => $request->sortDirection];
        $offset = ($request->page - 1) * $request->limit;

        $result = $this->contactTypeRepository->findWithFilters(
            $filters,
            $orderBy,
            $request->limit,
            $offset
        );

        $responseData = array_map(function ($contactType) {
            return new ContactTypeResponseDTO(
                $contactType->getId(),
                $contactType->getName(),
                $contactType->isActive()
            );
        }, $result['data']);

        return [
            'data' => $responseData,
            'total' => $result['total'],
            'page' => $request->page,
            'limit' => $request->limit
        ];
    }
}

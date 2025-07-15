<?php

namespace itaxcix\Core\Handler\HelpCenter;

use itaxcix\Core\Interfaces\help\HelpCenterRepositoryInterface;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterListUseCase;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterPaginationRequestDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterResponseDTO;

class HelpCenterListUseCaseHandler implements HelpCenterListUseCase
{
    private HelpCenterRepositoryInterface $helpCenterRepository;

    public function __construct(HelpCenterRepositoryInterface $helpCenterRepository)
    {
        $this->helpCenterRepository = $helpCenterRepository;
    }

    public function execute(HelpCenterPaginationRequestDTO $dto): PaginationResponseDTO
    {
        // Obtén los datos paginados del repositorio
        $paginationResult = $this->helpCenterRepository->findAllHelpItemsPaginated($dto->page, $dto->perPage);

        $rawItems = $paginationResult->items
        $meta = $paginationResult->meta ?? null;

        $items = array_map(
            fn($item) => HelpCenterResponseDTO::fromModel($item),
            $rawItems
        );

        return new PaginationResponseDTO(
            $items,
            $meta
        );
    }
}

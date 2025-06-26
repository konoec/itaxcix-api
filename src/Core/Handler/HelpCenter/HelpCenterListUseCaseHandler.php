<?php

namespace itaxcix\Core\Handler\HelpCenter;

use itaxcix\Core\Interfaces\help\HelpCenterRepositoryInterface;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterListUseCase;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterPaginationRequestDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

class HelpCenterListUseCaseHandler implements HelpCenterListUseCase
{
    private HelpCenterRepositoryInterface $helpCenterRepository;

    public function __construct(HelpCenterRepositoryInterface $helpCenterRepository)
    {
        $this->helpCenterRepository = $helpCenterRepository;
    }

    public function execute(HelpCenterPaginationRequestDTO $dto): PaginationResponseDTO
    {
        return $this->helpCenterRepository->findAllHelpItemsPaginated($dto->page, $dto->perPage);
    }
}

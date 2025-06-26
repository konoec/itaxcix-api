<?php

namespace itaxcix\Core\Handler\HelpCenter;

use itaxcix\Core\Interfaces\help\HelpCenterRepositoryInterface;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterPublicListUseCase;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterResponseDTO;

class HelpCenterPublicListUseCaseHandler implements HelpCenterPublicListUseCase
{
    private HelpCenterRepositoryInterface $helpCenterRepository;

    public function __construct(HelpCenterRepositoryInterface $helpCenterRepository)
    {
        $this->helpCenterRepository = $helpCenterRepository;
    }

    public function execute(): array
    {
        $helpItems = $this->helpCenterRepository->findAllActiveHelpItems();

        return array_map(function($item) {
            return new HelpCenterResponseDTO(
                id: $item->getId(),
                title: $item->getTitle(),
                subtitle: $item->getSubtitle(),
                answer: $item->getAnswer(),
                active: $item->isActive()
            );
        }, $helpItems);
    }
}

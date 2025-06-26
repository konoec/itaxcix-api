<?php

namespace itaxcix\Core\Handler\HelpCenter;

use InvalidArgumentException;
use itaxcix\Core\Domain\help\HelpCenterModel;
use itaxcix\Core\Interfaces\help\HelpCenterRepositoryInterface;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterCreateUseCase;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterRequestDTO;

class HelpCenterCreateUseCaseHandler implements HelpCenterCreateUseCase
{
    private HelpCenterRepositoryInterface $helpCenterRepository;

    public function __construct(HelpCenterRepositoryInterface $helpCenterRepository)
    {
        $this->helpCenterRepository = $helpCenterRepository;
    }

    public function execute(HelpCenterRequestDTO $dto): array
    {
        $helpItem = new HelpCenterModel(
            id: null,
            title: $dto->title,
            subtitle: $dto->subtitle,
            answer: $dto->answer,
            active: $dto->active
        );

        $this->helpCenterRepository->saveHelpItem($helpItem);

        return ['message' => 'Elemento del centro de ayuda creado correctamente.'];
    }
}

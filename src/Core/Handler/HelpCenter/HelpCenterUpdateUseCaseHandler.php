<?php

namespace itaxcix\Core\Handler\HelpCenter;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\help\HelpCenterRepositoryInterface;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterUpdateUseCase;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterRequestDTO;

class HelpCenterUpdateUseCaseHandler implements HelpCenterUpdateUseCase
{
    private HelpCenterRepositoryInterface $helpCenterRepository;

    public function __construct(HelpCenterRepositoryInterface $helpCenterRepository)
    {
        $this->helpCenterRepository = $helpCenterRepository;
    }

    public function execute(HelpCenterRequestDTO $dto): array
    {
        if (!$dto->id) {
            throw new InvalidArgumentException('ID es requerido para actualizar.');
        }

        $helpItem = $this->helpCenterRepository->findHelpItemById($dto->id);
        if (!$helpItem) {
            throw new InvalidArgumentException('Elemento del centro de ayuda no encontrado.');
        }

        $helpItem->setTitle($dto->title);
        $helpItem->setSubtitle($dto->subtitle);
        $helpItem->setAnswer($dto->answer);
        $helpItem->setActive($dto->active);

        $this->helpCenterRepository->saveHelpItem($helpItem);

        return ['message' => 'Elemento del centro de ayuda actualizado correctamente.'];
    }
}

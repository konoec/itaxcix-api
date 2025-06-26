<?php

namespace itaxcix\Core\Handler\HelpCenter;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\help\HelpCenterRepositoryInterface;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterDeleteUseCase;

class HelpCenterDeleteUseCaseHandler implements HelpCenterDeleteUseCase
{
    private HelpCenterRepositoryInterface $helpCenterRepository;

    public function __construct(HelpCenterRepositoryInterface $helpCenterRepository)
    {
        $this->helpCenterRepository = $helpCenterRepository;
    }

    public function execute(int $id): array
    {
        $success = $this->helpCenterRepository->deleteHelpItem($id);

        if (!$success) {
            throw new InvalidArgumentException('Elemento del centro de ayuda no encontrado.');
        }

        return ['message' => 'Elemento del centro de ayuda eliminado correctamente.'];
    }
}

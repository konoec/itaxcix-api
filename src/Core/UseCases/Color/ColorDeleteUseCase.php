<?php

namespace itaxcix\Core\UseCases\Color;

use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;

class ColorDeleteUseCase
{
    private ColorRepositoryInterface $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el color existe antes de eliminarlo
        $existingColor = $this->colorRepository->findById($id);
        if (!$existingColor) {
            throw new \InvalidArgumentException("Color with ID {$id} not found");
        }

        return $this->colorRepository->delete($id);
    }
}

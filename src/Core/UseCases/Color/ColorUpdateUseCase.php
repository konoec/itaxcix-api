<?php

namespace itaxcix\Core\UseCases\Color;

use itaxcix\Core\Domain\vehicle\ColorModel;
use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Color\ColorRequestDTO;
use itaxcix\Shared\DTO\useCases\Color\ColorResponseDTO;

class ColorUpdateUseCase
{
    private ColorRepositoryInterface $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function execute(int $id, ColorRequestDTO $request): ColorResponseDTO
    {
        $colorModel = new ColorModel(
            id: $id,
            name: trim($request->name),
            active: $request->active
        );

        $updatedColor = $this->colorRepository->update($colorModel);

        return ColorResponseDTO::fromModel($updatedColor);
    }
}

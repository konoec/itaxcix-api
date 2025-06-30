<?php

namespace itaxcix\Core\UseCases\Color;

use itaxcix\Core\Domain\vehicle\ColorModel;
use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Color\ColorRequestDTO;
use itaxcix\Shared\DTO\useCases\Color\ColorResponseDTO;

class ColorCreateUseCase
{
    private ColorRepositoryInterface $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function execute(ColorRequestDTO $request): ColorResponseDTO
    {
        $colorModel = new ColorModel(
            id: null,
            name: trim($request->name),
            active: $request->active
        );

        $createdColor = $this->colorRepository->create($colorModel);

        return ColorResponseDTO::fromModel($createdColor);
    }
}

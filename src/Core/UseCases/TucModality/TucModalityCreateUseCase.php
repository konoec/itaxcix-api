<?php

namespace itaxcix\Core\UseCases\TucModality;

use itaxcix\Core\Domain\vehicle\TucModalityModel;
use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityRequestDTO;
use itaxcix\Shared\DTO\useCases\TucModality\TucModalityResponseDTO;

class TucModalityCreateUseCase
{
    private TucModalityRepositoryInterface $repository;

    public function __construct(TucModalityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(TucModalityRequestDTO $request): TucModalityResponseDTO
    {
        $tucModality = new TucModalityModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $saved = $this->repository->saveTucModality($tucModality);

        return new TucModalityResponseDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            active: $saved->isActive()
        );
    }
}

